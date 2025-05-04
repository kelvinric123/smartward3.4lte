<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Bed;
use App\Models\Ward;
use App\Models\PatientDischarge;
use Illuminate\Support\Facades\Auth;

class PatientDischargeController extends Controller
{
    /**
     * Show discharge form for a patient
     */
    public function create($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $bed = Bed::where('patient_id', $patientId)->where('status', 'occupied')->first();
        
        if (!$bed) {
            return redirect()->route('admin.patients.show', $patientId)
                ->with('error', 'This patient is not currently admitted to any bed.');
        }
        
        return view('admin.patients.discharge', compact('patient', 'bed'));
    }
    
    /**
     * Store a new discharge record
     */
    public function store(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $bed = Bed::where('patient_id', $patientId)->where('status', 'occupied')->first();
        
        if (!$bed) {
            return redirect()->route('admin.patients.show', $patientId)
                ->with('error', 'This patient is not currently admitted to any bed.');
        }
        
        $request->validate([
            'discharge_date' => 'required|date',
            'discharge_type' => 'required|string',
            'discharge_notes' => 'nullable|string',
        ]);
        
        // Create discharge record
        PatientDischarge::create([
            'patient_id' => $patientId,
            'ward_id' => $bed->ward_id,
            'bed_number' => $bed->bed_number,
            'discharge_date' => $request->discharge_date,
            'discharge_type' => $request->discharge_type,
            'discharged_by' => Auth::id(),
            'discharge_notes' => $request->discharge_notes,
        ]);
        
        // Update bed to available status and remove patient
        $bed->update([
            'status' => 'available',
            'patient_id' => null,
        ]);
        
        return redirect()->route('admin.patients.show', $patientId)
            ->with('success', 'Patient discharged successfully.');
    }
    
    /**
     * List discharge history for a patient
     */
    public function history($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $discharges = $patient->discharges()
            ->with(['ward', 'dischargedBy'])
            ->orderBy('discharge_date', 'desc')
            ->paginate(15);
            
        return view('admin.patients.discharge_history', compact('patient', 'discharges'));
    }
}
