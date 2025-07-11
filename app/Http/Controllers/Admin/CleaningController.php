<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Hospital;
use App\Models\PatientAlert;
use Illuminate\Http\Request;

class CleaningController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin']);
    }

    /**
     * Display the cleaning dashboard with beds that need cleaning.
     */
    public function dashboard(Request $request)
    {
        // Get filter parameters
        $selectedHospitalId = $request->get('hospital_id');
        $selectedWardId = $request->get('ward_id');
        $cleaningStatus = $request->get('cleaning_status', 'all');
        
        // Build the query for beds that need cleaning
        $bedsQuery = Bed::with(['ward.hospital', 'ward.specialty', 'patient', 'nurse'])
            ->where('status', 'cleaning_needed');
        
        // Apply hospital filter
        if ($selectedHospitalId) {
            $bedsQuery->whereHas('ward', function($query) use ($selectedHospitalId) {
                $query->where('hospital_id', $selectedHospitalId);
            });
        }
        
        // Apply ward filter
        if ($selectedWardId) {
            $bedsQuery->where('ward_id', $selectedWardId);
        }
        
        $bedsNeedingCleaning = $bedsQuery->orderBy('updated_at', 'desc')->get();
        
        // Get all active hospitals for filtering
        $hospitals = Hospital::where('is_active', true)->get();
        
        // Get all active wards for filtering
        $wardsQuery = Ward::where('is_active', true);
        if ($selectedHospitalId) {
            $wardsQuery->where('hospital_id', $selectedHospitalId);
        }
        $wards = $wardsQuery->get();
        
        // Calculate summary statistics
        $totalBedsNeedingCleaning = $bedsNeedingCleaning->count();
        $totalBedsInSystem = Bed::count();
        
        // Group beds by ward for better organization
        $bedsByWard = $bedsNeedingCleaning->groupBy('ward_id');
        
        // Get recent cleaning activities (beds that were recently marked as cleaned)
        $recentlyCleanedBeds = Bed::with(['ward.hospital', 'ward.specialty'])
            ->where('status', 'available')
            ->where('updated_at', '>=', now()->subDay())
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
        
        // Get recent notifications (demo data for now)
        $recentNotifications = collect([
            [
                'id' => 1,
                'type' => 'whatsapp',
                'message' => 'Cleaning notification sent for Bed 101 in Cardiology Ward',
                'recipient' => '+60123456789',
                'status' => 'sent',
                'created_at' => now()->subMinutes(15)
            ],
            [
                'id' => 2,
                'type' => 'whatsapp',
                'message' => 'Cleaning notification sent for Bed 203 in ICU Ward',
                'recipient' => '+60123456789',
                'status' => 'sent',
                'created_at' => now()->subMinutes(30)
            ],
            [
                'id' => 3,
                'type' => 'whatsapp',
                'message' => 'Cleaning notification sent for Bed 305 in Emergency Ward',
                'recipient' => '+60123456789',
                'status' => 'sent',
                'created_at' => now()->subHour()
            ]
        ]);
        
        return view('admin.cleaning.dashboard', compact(
            'bedsNeedingCleaning',
            'bedsByWard',
            'hospitals',
            'wards',
            'totalBedsNeedingCleaning',
            'totalBedsInSystem',
            'recentlyCleanedBeds',
            'recentNotifications',
            'selectedHospitalId',
            'selectedWardId',
            'cleaningStatus'
        ));
    }
    
    /**
     * Mark a bed as cleaned (AJAX endpoint)
     */
    public function markAsCleaned(Request $request, $bedId)
    {
        try {
            $bed = Bed::findOrFail($bedId);
            
            // Check if bed actually needs cleaning
            if ($bed->status !== 'cleaning_needed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This bed does not need cleaning.'
                ], 400);
            }
            
            // Update bed status to available
            $bed->update([
                'status' => 'available',
                'notes' => $bed->notes ? $bed->notes . "\n[Cleaned on " . now()->format('Y-m-d H:i:s') . " by " . auth()->user()->name . "]" : "[Cleaned on " . now()->format('Y-m-d H:i:s') . " by " . auth()->user()->name . "]"
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Bed ' . $bed->bed_number . ' marked as cleaned successfully.',
                'bed_id' => $bed->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark multiple beds as cleaned (bulk action)
     */
    public function markMultipleAsCleaned(Request $request)
    {
        $bedIds = $request->input('bed_ids', []);
        
        if (empty($bedIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No beds selected.'
            ], 400);
        }
        
        try {
            $beds = Bed::whereIn('id', $bedIds)
                ->where('status', 'cleaning_needed')
                ->get();
            
            $cleanedCount = 0;
            foreach ($beds as $bed) {
                $bed->update([
                    'status' => 'available',
                    'notes' => $bed->notes ? $bed->notes . "\n[Cleaned on " . now()->format('Y-m-d H:i:s') . " by " . auth()->user()->name . "]" : "[Cleaned on " . now()->format('Y-m-d H:i:s') . " by " . auth()->user()->name . "]"
                ]);
                $cleanedCount++;
            }
            
            return response()->json([
                'success' => true,
                'message' => $cleanedCount . ' bed(s) marked as cleaned successfully.',
                'cleaned_count' => $cleanedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send WhatsApp notification for cleaning (demo)
     */
    public function sendWhatsAppNotification(Request $request)
    {
        $bedId = $request->input('bed_id');
        $recipient = $request->input('recipient', '+60123456789');
        
        try {
            $bed = Bed::with(['ward.specialty', 'ward.hospital'])->findOrFail($bedId);
            
            // Demo WhatsApp notification message
            $message = "🚨 CLEANING ALERT 🚨\n\n";
            $message .= "Bed: " . $bed->bed_number . "\n";
            $message .= "Ward: " . $bed->ward->name . "\n";
            $message .= "Specialty: " . $bed->ward->specialty->name . "\n";
            $message .= "Hospital: " . $bed->ward->hospital->name . "\n";
            $message .= "Status: Needs Cleaning\n\n";
            $message .= "Please clean this bed as soon as possible.\n";
            $message .= "Sent at: " . now()->format('Y-m-d H:i:s');
            
            // In a real implementation, you would integrate with WhatsApp API here
            // For demo purposes, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'WhatsApp notification sent successfully to ' . $recipient,
                'notification' => [
                    'id' => time(),
                    'type' => 'whatsapp',
                    'message' => 'Cleaning notification sent for Bed ' . $bed->bed_number . ' in ' . $bed->ward->name . ' Ward',
                    'recipient' => $recipient,
                    'status' => 'sent',
                    'created_at' => now()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
} 