<?php

namespace App\Http\Controllers\WardAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:ward-admin,super-admin');
    }

    /**
     * Render the ward dashboard in fullscreen mode.
     *
     * @param  int  $wardId
     * @return \Illuminate\Http\Response
     */
    public function dashboard($wardId)
    {
        $ward = Ward::with(['hospital', 'specialty', 'beds.consultant', 'beds.nurse', 'beds.patient.latestVitalSigns'])->findOrFail($wardId);
        
        // Get bed status counts for dashboard stats
        $availableBeds = $ward->beds->where('status', 'available')->count();
        $totalBeds = $ward->beds->count();
        $occupiedBeds = $ward->beds->where('status', 'occupied')->count();
        
        // Get unique nurses assigned to this ward's beds
        $nursesOnDuty = $ward->beds->pluck('nurse_id')->filter()->unique()->count();
        
        // Calculate occupancy rate
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0;
        
        // Calculate nurse-patient ratio (if there are nurses)
        $nursePatientRatio = $nursesOnDuty > 0 ? round($occupiedBeds / $nursesOnDuty, 1) : 0;
        
        // Get patients who are currently sent to service locations
        $patientIdsInBeds = $ward->beds->pluck('patient_id')->filter()->toArray();
        
        // Get active movements where patients are currently out for service
        $activeMovements = \App\Models\PatientMovement::whereIn('patient_id', $patientIdsInBeds)
            ->where('status', 'sent')
            ->get()
            ->keyBy('patient_id');
        
        // Set fullscreen mode flag
        $fullscreen_mode = true;
        
        return view('admin.beds.wards.dashboard', compact(
            'ward', 
            'availableBeds', 
            'nursesOnDuty',
            'occupiedBeds',
            'nursePatientRatio',
            'occupancyRate',
            'activeMovements',
            'fullscreen_mode'
        ));
    }
    
    /**
     * Show form to admit a patient to a bed from the ward dashboard.
     */
    public function admitPatient(Ward $ward, $bedId)
    {
        // Find the bed within this ward
        $bed = $ward->beds()->findOrFail($bedId);
        
        // Load relationships needed for the form
        $consultants = \App\Models\Consultant::where('is_active', true)->get();
        $patients = \App\Models\Patient::all();
        $nurses = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->get();
        
        // Set bed status to occupied for admission form
        $bed->status = 'occupied';
        
        // Set fullscreen mode flag
        $fullscreen_mode = true;
        
        return view('admin.beds.wards.admit_patient', compact('ward', 'bed', 'consultants', 'patients', 'nurses', 'fullscreen_mode'));
    }
    
    /**
     * Process the admission form submission.
     */
    public function storeAdmission(Request $request, Ward $ward, $bedId)
    {
        // Validate the request
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'consultant_id' => 'nullable|exists:consultants,id',
            'nurse_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'admission_date' => 'nullable|date',
        ]);
        
        // Find the bed
        $bed = $ward->beds()->findOrFail($bedId);
        
        // Update the bed with admission information
        $bed->update([
            'status' => 'occupied',
            'patient_id' => $validated['patient_id'],
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
        ]);
        
        // Create admission record
        \App\Models\PatientAdmission::create([
            'patient_id' => $validated['patient_id'],
            'ward_id' => $ward->id,
            'bed_id' => $bed->id,
            'consultant_id' => $validated['consultant_id'],
            'admission_date' => $validated['admission_date'] ?? now(),
            'admission_notes' => $validated['notes'],
            'status' => 'active',
        ]);
        
        return redirect()->route('admin.beds.wards.dashboard.direct', ['ward' => $ward->id])
            ->with('success', 'Patient admitted successfully');
    }
    
    /**
     * Display patient details in ward.
     */
    public function patientDetails(Ward $ward, $bedId)
    {
        // Find the bed
        $bed = $ward->beds()->with([
            'patient.vitalSigns' => function ($query) {
                $query->orderBy('recorded_at', 'desc');
            },
            'patient.referrals.consultant',
            'patient.referrals.specialty',
            'consultant',
            'nurse'
        ])->findOrFail($bedId);
        
        // Check if bed has a patient
        if (!$bed->patient) {
            return redirect()->route('admin.beds.wards.dashboard.direct', ['ward' => $ward->id])
                ->with('error', 'No patient assigned to this bed');
        }
        
        // Get admission record for this patient
        $admission = \App\Models\PatientAdmission::where('patient_id', $bed->patient->id)
            ->where('is_active', true)
            ->first();
            
        // Get patient movements
        $patientMovements = \App\Models\PatientMovement::where('patient_id', $bed->patient->id)
            ->orderBy('scheduled_time', 'desc')
            ->get();
            
        // Get service locations for movement form
        $serviceLocations = [
            'X-Ray',
            'CT Scan',
            'MRI',
            'Ultrasound',
            'ECG',
            'EEG',
            'Physiotherapy',
            'Laboratory',
            'Dialysis',
            'Operating Theatre',
            'ICU',
            'CCU',
            'Emergency Department'
        ];
        
        // Set variables for view
        $patient = $bed->patient;
        
        // Set fullscreen mode flag
        $fullscreen_mode = true;
        
        return view('admin.beds.wards.patient_details', compact(
            'ward', 
            'bed', 
            'patient',
            'admission', 
            'patientMovements',
            'serviceLocations',
            'fullscreen_mode'
        ));
    }
    
    /**
     * Update risk factors for a patient.
     */
    public function updateRiskFactors(Request $request, Ward $ward, $bedId)
    {
        // Find the bed
        $bed = $ward->beds()->with('patient')->findOrFail($bedId);
        
        // Check if bed has a patient
        if (!$bed->patient) {
            return redirect()->route('admin.beds.wards.dashboard.direct', ['ward' => $ward->id])
                ->with('error', 'No patient assigned to this bed');
        }
        
        // Validate the request
        $validated = $request->validate([
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'fall_risk' => 'nullable|boolean',
            'isolation_required' => 'nullable|boolean',
            'mobility_status' => 'nullable|string',
            'special_precautions' => 'nullable|string',
        ]);
        
        // Update patient with risk factors
        $bed->patient->update($validated);
        
        return redirect()->route('admin.beds.wards.patient.details.direct', ['ward' => $ward->id, 'bedId' => $bed->id])
            ->with('success', 'Risk factors updated successfully');
    }
} 