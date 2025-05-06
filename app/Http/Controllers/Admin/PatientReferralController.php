<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientReferral;
use App\Models\Specialty;
use App\Models\Consultant;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientReferralController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create a new patient referral
     */
    public function createReferral(Request $request, Ward $ward, $bedId)
    {
        // Validate the request
        $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'to_specialty_id' => 'required|exists:specialties,id',
            'to_consultant_id' => 'required|exists:consultants,id',
            'reason' => 'required|string|max:255',
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
        
        // Create the patient referral
        PatientReferral::create([
            'patient_id' => $patient->id,
            'admission_id' => $activeAdmission->id,
            'from_ward_id' => $ward->id,
            'from_consultant_id' => $bed->consultant_id,
            'to_specialty_id' => $validated['to_specialty_id'],
            'to_consultant_id' => $validated['to_consultant_id'],
            'referral_date' => now(),
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'referred_by' => auth()->id(),
        ]);
        
        return redirect()->route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bedId])
            ->with('success', 'Patient referred successfully.');
    }
    
    /**
     * Update the status of a patient referral
     */
    public function updateStatus(Request $request, PatientReferral $referral)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,declined,completed',
            'response_notes' => 'nullable|string',
        ]);
        
        // Update the referral
        $referral->update([
            'status' => $validated['status'],
            'response_notes' => $validated['response_notes'],
        ]);
        
        return redirect()->back()->with('success', 'Referral status updated successfully.');
    }
    
    /**
     * Get consultants by specialty
     */
    public function getConsultantsBySpecialty(Request $request)
    {
        $specialtyId = $request->input('specialty_id');
        $hospitalId = $request->input('hospital_id');
        
        $query = Consultant::where('specialty_id', $specialtyId)
            ->where('is_active', true);
            
        // Filter by hospital if provided
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }
        
        $consultants = $query->get();
            
        return response()->json($consultants);
    }
    
    /**
     * Get consultants by specialty - direct method
     */
    public function getConsultantsBySpecialtyDirect(Request $request)
    {
        try {
            $specialtyId = $request->input('specialty_id');
            $hospitalId = $request->input('hospital_id');
            
            if (!$specialtyId) {
                return response()->json(['error' => 'Specialty ID is required'], 400);
            }
            
            $query = Consultant::where('specialty_id', $specialtyId)
                ->where('is_active', true);
                
            // Filter by hospital if provided
            if ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            }
            
            $consultants = $query->get(['id', 'name', 'specialty_id', 'hospital_id']);
                
            return response()->json($consultants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Store a new patient referral
     */
    public function store(Request $request, $patientId)
    {
        // Validate the request
        $validated = $request->validate([
            'specialty' => 'required|string',
            'consultant' => 'required|string',
            'referral_date' => 'required|date',
            'clinical_question' => 'required|string',
            'reason_for_referral' => 'required|string',
            'relevant_clinical_info' => 'nullable|string',
            'urgency' => 'required|in:routine,urgent,emergency',
            'referring_doctor' => 'required|string',
        ]);
        
        // Find the patient
        $patient = Patient::findOrFail($patientId);
        
        // Get the patient's active admission
        $activeAdmission = $patient->activeAdmission;
        
        if (!$activeAdmission) {
            return redirect()->back()
                ->with('error', 'No active admission found for this patient.');
        }
        
        // Find the bed the patient is assigned to
        $bed = Bed::where('patient_id', $patient->id)->first();
        
        if (!$bed) {
            return redirect()->back()
                ->with('error', 'Patient is not assigned to a bed.');
        }
        
        // Find the ward for this bed
        $ward = $bed->ward;
        
        // Create the patient referral
        PatientReferral::create([
            'patient_id' => $patient->id,
            'admission_id' => $activeAdmission->id,
            'from_ward_id' => $ward->id,
            'from_consultant_id' => $bed->consultant_id ?? null,
            'to_specialty' => $validated['specialty'],
            'to_consultant' => $validated['consultant'],
            'referral_date' => $validated['referral_date'],
            'clinical_question' => $validated['clinical_question'],
            'reason' => $validated['reason_for_referral'],
            'notes' => $validated['relevant_clinical_info'],
            'urgency' => $validated['urgency'],
            'status' => 'pending',
            'referred_by' => auth()->id(),
            'referring_doctor' => $validated['referring_doctor'],
        ]);
        
        return redirect()->back()
            ->with('success', 'Patient referral submitted successfully.');
    }
} 