<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientMovement;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientMovementController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Schedule a new patient movement
     */
    public function scheduleMovement(Request $request, Ward $ward, $bedId)
    {
        // Validate the request
        $validated = $request->validate([
            'to_service_location' => 'required|string|max:255',
            'scheduled_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);
        
        // Find the bed within this ward
        $bed = $ward->beds()->findOrFail($bedId);
        
        // Check if the bed has a patient
        if (!$bed->patient_id) {
            return redirect()->route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bedId])
                ->with('error', 'This bed does not have a patient assigned.');
        }
        
        // Get the patient
        $patient = $bed->patient;
        
        // Get the active admission
        $activeAdmission = $patient->activeAdmission;
        
        if (!$activeAdmission) {
            return redirect()->route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bedId])
                ->with('error', 'No active admission found for this patient.');
        }
        
        // Create the patient movement
        PatientMovement::create([
            'patient_id' => $patient->id,
            'admission_id' => $activeAdmission->id,
            'from_ward_id' => $ward->id,
            'from_bed_id' => $bed->id,
            'to_service_location' => $validated['to_service_location'],
            'scheduled_time' => Carbon::parse($validated['scheduled_time']),
            'status' => 'scheduled',
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bedId])
            ->with('success', 'Patient movement scheduled successfully.');
    }
    
    /**
     * Mark a patient as sent to the service location
     */
    public function sendPatient(Request $request, PatientMovement $movement)
    {
        if ($movement->status !== 'scheduled') {
            if ($request->ajax()) {
                return response()->json(['error' => 'This movement is not in scheduled status.'], 422);
            }
            return redirect()->back()->with('error', 'This movement is not in scheduled status.');
        }
        
        // Update the movement
        $movement->update([
            'sent_time' => now(),
            'status' => 'sent'
        ]);
        
        if ($request->ajax()) {
            // Get updated movement history HTML
            $patientMovements = PatientMovement::where('patient_id', $movement->patient_id)
                ->orderBy('scheduled_time', 'desc')
                ->get();
            
            $movementHistoryHtml = view('admin.beds.wards.partials.movement_history_table', [
                'patientMovements' => $patientMovements
            ])->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Patient has been sent to the service location.',
                'movement_history_html' => $movementHistoryHtml
            ]);
        }
        
        return redirect()->back()->with('success', 'Patient has been sent to the service location.');
    }
    
    /**
     * Mark a patient as returned from the service location
     */
    public function returnPatient(Request $request, PatientMovement $movement)
    {
        if ($movement->status !== 'sent') {
            if ($request->ajax()) {
                return response()->json(['error' => 'This movement is not in sent status.'], 422);
            }
            return redirect()->back()->with('error', 'This movement is not in sent status.');
        }
        
        // Update the movement
        $movement->update([
            'return_time' => now(),
            'status' => 'returned'
        ]);
        
        if ($request->ajax()) {
            // Get updated movement history HTML
            $patientMovements = PatientMovement::where('patient_id', $movement->patient_id)
                ->orderBy('scheduled_time', 'desc')
                ->get();
            
            $movementHistoryHtml = view('admin.beds.wards.partials.movement_history_table', [
                'patientMovements' => $patientMovements
            ])->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Patient has returned from the service location.',
                'movement_history_html' => $movementHistoryHtml
            ]);
        }
        
        return redirect()->back()->with('success', 'Patient has returned from the service location.');
    }
    
    /**
     * Cancel a scheduled movement
     */
    public function cancelMovement(Request $request, PatientMovement $movement)
    {
        if ($movement->status !== 'scheduled') {
            if ($request->ajax()) {
                return response()->json(['error' => 'This movement is not in scheduled status.'], 422);
            }
            return redirect()->back()->with('error', 'This movement is not in scheduled status.');
        }
        
        // Update the movement
        $movement->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id()
        ]);
        
        if ($request->ajax()) {
            // Get updated movement history HTML
            $patientMovements = PatientMovement::where('patient_id', $movement->patient_id)
                ->orderBy('scheduled_time', 'desc')
                ->get();
            
            $movementHistoryHtml = view('admin.beds.wards.partials.movement_history_table', [
                'patientMovements' => $patientMovements
            ])->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Movement has been cancelled.',
                'movement_history_html' => $movementHistoryHtml
            ]);
        }
        
        return redirect()->back()->with('success', 'Movement has been cancelled.');
    }
} 