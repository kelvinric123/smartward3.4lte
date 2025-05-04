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
            'discharge_date' => \Carbon\Carbon::parse($request->discharge_date)->setTimezone('Asia/Kuala_Lumpur'),
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
    
    /**
     * Show admission and discharge history for all patients
     */
    public function admissionHistory(Request $request)
    {
        $query = PatientDischarge::with(['patient', 'ward', 'dischargedBy']);
        
        // Filter by patient name if provided
        if ($request->filled('search')) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('identity_number', 'like', '%' . $request->search . '%')
                  ->orWhere('mrn', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('discharge_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('discharge_date', '<=', $request->date_to);
        }
        
        // Get paginated results ordered by most recent
        $discharges = $query->orderBy('discharge_date', 'desc')
            ->paginate(15)
            ->appends($request->except('page'));
        
        // For each discharge, find the matching admission
        foreach ($discharges as $discharge) {
            $admission = \App\Models\PatientAdmission::where('patient_id', $discharge->patient_id)
                ->where('ward_id', $discharge->ward_id)
                ->where('bed_number', $discharge->bed_number)
                ->where('is_active', false)
                ->where('admission_date', '<', $discharge->discharge_date)
                ->orderBy('admission_date', 'desc')
                ->first();
            
            $discharge->admission = $admission;
        }
            
        return view('admin.patients.admission_history', compact('discharges'));
    }
}
