<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\FoodMenu;
use App\Models\FoodOrder;
use App\Models\PatientAlert;
use App\Models\Medication;
use App\Models\MedicalHistory;
use Illuminate\Http\Request;

class PatientPanelController extends Controller
{
    /**
     * Display the patient panel for a specific patient
     *
     * @param Patient $patient
     * @return \Illuminate\View\View
     */
    public function showPanel(Patient $patient)
    {
        // Get the active admission
        $activeAdmission = $patient->activeAdmission;
        
        // Check if the patient has an active admission
        if (!$activeAdmission) {
            return back()->with('error', 'Patient does not have an active admission.');
        }
        
        // Get the bed and ward
        $bed = $activeAdmission->bed;
        $ward = $activeAdmission->ward;
        
        // Get menu items grouped by meal type
        $menuItems = [
            'Breakfast' => FoodMenu::where('meal_type', 'Breakfast')->where('available', true)->get(),
            'Lunch' => FoodMenu::where('meal_type', 'Lunch')->where('available', true)->get(),
            'Dinner' => FoodMenu::where('meal_type', 'Dinner')->where('available', true)->get(),
            'Snack' => FoodMenu::where('meal_type', 'Snack')->where('available', true)->get(),
        ];
        
        // Get patient's current active food orders
        $activeOrders = FoodOrder::where('patient_id', $patient->id)
            ->where('status', '!=', 'delivered')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get patient's medications
        $medications = $patient->medications()->orderBy('start_date', 'desc')->get();
        
        // Get patient's medical history
        $medicalHistories = $patient->medicalHistories()->orderBy('date_diagnosed', 'desc')->get();
        
        // Get patient's vital signs (latest 10 records)
        $vitalSigns = $patient->vitalSigns()
            ->with('recorder')
            ->latest('recorded_at')
            ->take(10)
            ->get();
        
        return view('admin.patients.panel', compact(
            'patient', 
            'activeAdmission', 
            'bed', 
            'ward', 
            'menuItems', 
            'activeOrders', 
            'medications', 
            'medicalHistories',
            'vitalSigns'
        ));
    }
    
    /**
     * Store a new food order
     *
     * @param Request $request
     * @param Patient $patient
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrder(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'item_name' => 'required|string',
            'meal_type' => 'required|string|in:Breakfast,Lunch,Dinner,Snack',
            'dietary_restriction' => 'nullable|string',
        ]);
        
        // Get the active admission
        $activeAdmission = $patient->activeAdmission;
        
        if (!$activeAdmission) {
            return response()->json(['error' => 'Patient does not have an active admission.'], 400);
        }
        
        // Check if there's already an active order for this meal type
        $existingOrder = FoodOrder::where('patient_id', $patient->id)
            ->where('meal_type', $validated['meal_type'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->first();
            
        if ($existingOrder) {
            // Cancel the existing order
            $existingOrder->status = 'cancelled';
            $existingOrder->save();
        }
        
        // Create new order
        $order = FoodOrder::create([
            'patient_id' => $patient->id,
            'bed_id' => $activeAdmission->bed_id,
            'ward_id' => $activeAdmission->ward_id,
            'item_name' => $validated['item_name'],
            'meal_type' => $validated['meal_type'],
            'dietary_restriction' => $validated['dietary_restriction'],
            'status' => 'pending',
            'order_time' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => $order
        ]);
    }
    
    /**
     * Cancel a food order
     *
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder(Request $request, $orderId)
    {
        $order = FoodOrder::findOrFail($orderId);
        
        // Only allow cancellation if the order is still pending or preparing
        if (in_array($order->status, ['pending', 'preparing'])) {
            $order->status = 'cancelled';
            $order->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'This order cannot be cancelled'
        ], 400);
    }
    
    /**
     * Send an alert from patient panel
     *
     * @param Request $request
     * @param Patient $patient
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendAlert(Request $request, Patient $patient)
    {
        try {
            $validated = $request->validate([
                'alert_type' => 'required|string|in:emergency,pain,assistance,water,bathroom,food',
                'message' => 'nullable|string',
                'is_urgent' => 'nullable|boolean',
            ]);
            
            // Get the active admission
            $activeAdmission = $patient->activeAdmission;
            
            if (!$activeAdmission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient does not have an active admission.'
                ], 400);
            }
            
            // Determine urgency based on alert type if not explicitly provided
            $isUrgent = $request->has('is_urgent') ? $validated['is_urgent'] : ($validated['alert_type'] === 'emergency');
            
            // Create the alert
            $alert = PatientAlert::create([
                'patient_id' => $patient->id,
                'ward_id' => $activeAdmission->ward_id,
                'bed_id' => $activeAdmission->bed_id,
                'alert_type' => $validated['alert_type'],
                'message' => $validated['message'] ?? $this->getDefaultMessage($validated['alert_type']),
                'status' => 'new',
                'is_urgent' => $isUrgent,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Alert sent successfully',
                'alert' => $alert
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending alert: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get default message based on alert type
     *
     * @param string $alertType
     * @return string
     */
    private function getDefaultMessage($alertType)
    {
        $messages = [
            'emergency' => 'Emergency assistance needed!',
            'pain' => 'Patient is experiencing pain and needs assistance',
            'assistance' => 'Patient requests general assistance',
            'water' => 'Patient requests water',
            'bathroom' => 'Patient needs assistance to bathroom',
            'food' => 'Patient requests food or snack',
        ];
        
        return $messages[$alertType] ?? 'Patient requires assistance';
    }
} 