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
     * Quick discharge patient from bed dashboard
     */
    public function quickDischarge(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $bed = Bed::where('patient_id', $patientId)->where('status', 'occupied')->first();
        
        if (!$bed) {
            return redirect()->route('admin.patients.show', $patientId)
                ->with('error', 'This patient is not currently admitted to any bed.');
        }
        
        $request->validate([
            'discharge_type' => 'required|string',
            'discharge_notes' => 'nullable|string',
        ]);
        
        // Create discharge record
        PatientDischarge::create([
            'patient_id' => $patientId,
            'ward_id' => $bed->ward_id,
            'bed_number' => $bed->bed_number,
            'discharge_date' => now()->setTimezone('Asia/Kuala_Lumpur'),
            'discharge_type' => $request->discharge_type,
            'discharged_by' => Auth::id(),
            'discharge_notes' => $request->discharge_notes ?? 'Quick discharge from bed dashboard',
        ]);
        
        // Update bed to available status and remove patient
        $bed->update([
            'status' => 'available',
            'patient_id' => null,
            'consultant_id' => null,
            'nurse_id' => null,
        ]);
        
        // Mark any active admissions as inactive
        \App\Models\PatientAdmission::where('patient_id', $patientId)
            ->where('is_active', true)
            ->update(['is_active' => false]);
        
        // Check if this is an iframe request
        if ($request->has('is_iframe')) {
            // Return a response that will refresh the parent window
            return response()->view('admin.beds.wards.iframe_discharge_success', [
                'ward_id' => $request->ward_id
            ]);
        }
        
        // Redirect back to ward dashboard if requested
        if ($request->has('redirect_to_ward') && $request->ward_id) {
            return redirect()->route('admin.beds.wards.dashboard', $request->ward_id)
                ->with('success', 'Patient discharged successfully.');
        }
        
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
        // Get discharge records first
        $dischargeQuery = PatientDischarge::with(['patient', 'ward', 'dischargedBy']);
        
        // Filter by patient name if provided
        if ($request->filled('search')) {
            $dischargeQuery->whereHas('patient', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('identity_number', 'like', '%' . $request->search . '%')
                  ->orWhere('mrn', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $dischargeQuery->whereDate('discharge_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $dischargeQuery->whereDate('discharge_date', '<=', $request->date_to);
        }
        
        // Get discharge results ordered by most recent
        $discharges = $dischargeQuery->orderBy('discharge_date', 'desc')->get();
        
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
            $discharge->record_type = 'discharge'; // Mark as discharge record
        }
        
        // Get active admissions
        $activeAdmissionsQuery = \App\Models\PatientAdmission::with(['patient', 'ward', 'consultant', 'nurse'])
            ->where('is_active', true);
            
        // Apply the same filters as discharge records    
        if ($request->filled('search')) {
            $activeAdmissionsQuery->whereHas('patient', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('identity_number', 'like', '%' . $request->search . '%')
                  ->orWhere('mrn', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('date_from')) {
            $activeAdmissionsQuery->whereDate('admission_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $activeAdmissionsQuery->whereDate('admission_date', '<=', $request->date_to);
        }
        
        $activeAdmissions = $activeAdmissionsQuery->orderBy('admission_date', 'desc')->get();
        
        // Mark active admissions
        foreach ($activeAdmissions as $admission) {
            $admission->record_type = 'active_admission'; // Mark as active admission
        }
        
        // Merge the collections
        $allRecords = $discharges->concat($activeAdmissions);
        
        // Sort by date (admission or discharge date) - most recent first
        $allRecords = $allRecords->sortByDesc(function ($record) {
            return $record->record_type === 'discharge' 
                ? $record->discharge_date 
                : $record->admission_date;
        });
        
        // Paginate the combined results
        $perPage = 15;
        $page = $request->input('page', 1);
        $total = $allRecords->count();
        
        $allRecords = $allRecords->slice(($page - 1) * $perPage, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRecords, 
            $total, 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );
            
        return view('admin.patients.admission_history', [
            'discharges' => $paginator
        ]);
    }
}
