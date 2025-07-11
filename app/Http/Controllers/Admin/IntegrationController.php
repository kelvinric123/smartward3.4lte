<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Consultant;
use App\Models\User;
use App\Models\PatientAdmission;
use App\Models\MedicalHistory;
use App\Models\Hl7MappingProfile;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IntegrationController extends Controller
{
    /**
     * Display the admission form with HL7 mapping fields
     */
    public function admissionForm()
    {
        // Get all necessary data for the form
        $patients = Patient::all();
        $wards = Ward::where('is_active', true)->with('beds')->get();
        $consultants = Consultant::where('is_active', true)->with('specialty')->get();
        $nurses = User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->get();

        // Get form options from the PatientAdmission model
        $dietTypes = PatientAdmission::getDietTypeOptions();
        $patientClasses = PatientAdmission::getPatientClassOptions();
        $fallRiskOptions = PatientAdmission::getFallRiskAlertOptions();
        $isolationOptions = PatientAdmission::getIsolationPrecautionsOptions();

        // Get HL7 mapping profiles
        $hl7Profiles = Hl7MappingProfile::getAllForSelection();
        $activeProfile = Hl7MappingProfile::getActiveProfile();

        return view('admin.integration.admission', compact(
            'patients',
            'wards',
            'consultants',
            'nurses',
            'dietTypes',
            'patientClasses',
            'fallRiskOptions',
            'isolationOptions',
            'hl7Profiles',
            'activeProfile'
        ));
    }

    /**
     * Get patient details including allergies via AJAX
     */
    public function getPatientDetails(Request $request)
    {
        $patient = Patient::with(['medicalHistories' => function($query) {
            $query->where('type', 'allergy')->where('status', 'active');
        }])->find($request->patient_id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Get allergies
        $allergies = $patient->medicalHistories->where('type', 'allergy')->map(function($allergy) {
            return [
                'title' => $allergy->title,
                'description' => $allergy->description,
                'severity' => $allergy->severity,
                'notes' => $allergy->notes
            ];
        });

        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'mrn' => $patient->mrn,
                'identity_number' => $patient->identity_number,
                'identity_type' => $patient->identity_type,
                'age' => $patient->age,
                'gender' => $patient->gender,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'address' => $patient->address,
            ],
            'allergies' => $allergies
        ]);
    }

    /**
     * Get available beds for a specific ward via AJAX
     */
    public function getWardBeds(Request $request)
    {
        $ward = Ward::with(['beds' => function($query) {
            $query->where('status', 'available');
        }])->find($request->ward_id);

        if (!$ward) {
            return response()->json(['error' => 'Ward not found'], 404);
        }

        $beds = $ward->beds->map(function($bed) {
            return [
                'id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'status' => $bed->status
            ];
        });

        return response()->json(['beds' => $beds]);
    }

    /**
     * Process the admission form with HL7 mapping data
     */
    public function storeAdmission(Request $request)
    {
        // Validate the main admission data
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'ward_id' => 'required|exists:wards,id',
            'bed_id' => 'required|exists:beds,id',
            'consultant_id' => 'nullable|exists:consultants,id',
            'nurse_id' => 'nullable|exists:users,id',
            'admission_date' => 'required|date',
            'admission_notes' => 'nullable|string',
            
            // Clinical fields
            'diet_type' => 'nullable|in:REG,NPO,CLF,FLF,LCH,LCS,LNS,DBT,VEG,VGN,HSL,KSH,RST,CAR,SFT,BLD,PUR',
            'patient_class' => 'nullable|in:I,O,A,E,N,R,B,C,U',
            'expected_discharge_date' => 'nullable|date|after:admission_date',
            'expected_length_of_stay' => 'nullable|integer|min:1|max:365',
            'fall_risk_alert' => 'nullable|in:NO,LOW,MOD,HIGH,FR',
            'isolation_precautions' => 'nullable|in:NONE,STD,CON,DROP,AIR,DAC,DC,AC,AD',
            'clinical_alerts' => 'nullable|string|max:1000',
            'risk_factors' => 'nullable|array',
            
            // HL7 mapping codes (store as JSON for each field)
            'hl7_mappings' => 'nullable|array',
        ]);

        // Find the patient
        $patient = Patient::findOrFail($validated['patient_id']);

        // Check if patient is already admitted
        if ($patient->is_admitted) {
            $currentBed = $patient->bed;
            $currentWard = $currentBed ? $currentBed->ward : null;
            
            $errorMessage = 'This patient is already admitted';
            if ($currentBed && $currentWard) {
                $errorMessage .= " to {$currentWard->name} (Bed {$currentBed->bed_number})";
            }
            $errorMessage .= '. Please select another patient.';
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['patient_id' => $errorMessage]);
        }

        // Find the bed and verify it belongs to the specified ward
        $bed = Bed::where('id', $validated['bed_id'])
            ->where('ward_id', $validated['ward_id'])
            ->first();
            
        if (!$bed) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['bed_id' => 'Selected bed does not belong to the specified ward.']);
        }

        // Check if bed is available
        if ($bed->status !== 'available') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['bed_id' => 'This bed is not available for admission.']);
        }

        // Update the bed with admission information
        $bed->update([
            'status' => 'occupied',
            'patient_id' => $validated['patient_id'],
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
            'notes' => $validated['admission_notes'],
        ]);

        // Get the admission date in KL timezone
        $admissionDate = Carbon::parse($validated['admission_date'])->setTimezone('Asia/Kuala_Lumpur');

        // Prepare clinical data
        $clinicalData = [
            'diet_type' => $validated['diet_type'] ?? null,
            'patient_class' => $validated['patient_class'] ?? 'I',
            'expected_discharge_date' => isset($validated['expected_discharge_date']) 
                ? Carbon::parse($validated['expected_discharge_date'])->setTimezone('Asia/Kuala_Lumpur')
                : null,
            'expected_length_of_stay' => $validated['expected_length_of_stay'] ?? null,
            'fall_risk_alert' => $validated['fall_risk_alert'] ?? 'NO',
            'isolation_precautions' => $validated['isolation_precautions'] ?? 'NONE',
            'clinical_alerts' => $validated['clinical_alerts'] ?? null,
            'risk_factors' => $validated['risk_factors'] ?? [],
        ];

        // Create admission record
        $admission = PatientAdmission::create([
            'patient_id' => $validated['patient_id'],
            'ward_id' => $validated['ward_id'],
            'bed_id' => $bed->id,
            'bed_number' => $bed->bed_number,
            'admission_date' => $admissionDate,
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
            'admitted_by' => auth()->id(),
            'admission_notes' => $validated['admission_notes'],
            'is_active' => true,
        ] + $clinicalData);

        // Store HL7 mapping data (you might want to create a separate table for this)
        // For now, we'll store it as JSON in the session or cache for demo purposes
        if (isset($validated['hl7_mappings'])) {
            session(['hl7_mappings_' . $admission->id => $validated['hl7_mappings']]);
        }

        return redirect()->route('admin.integration.admission')
            ->with('success', 'Patient successfully admitted to ' . $bed->ward->name . ' (Bed ' . $bed->bed_number . ') with HL7 mapping configuration saved.');
    }

    /**
     * Save a new HL7 mapping profile
     */
    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'profile_name' => 'required|string|max:255|unique:hl7_mapping_profiles,name',
            'profile_description' => 'nullable|string|max:1000',
            'hl7_mappings' => 'required|array',
        ]);

        $profile = Hl7MappingProfile::createOrUpdateProfile([
            'name' => $validated['profile_name'],
            'description' => $validated['profile_description'],
            'mapping_codes' => $validated['hl7_mappings'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'HL7 mapping profile saved successfully.',
            'profile' => $profile
        ]);
    }

    /**
     * Update an existing HL7 mapping profile
     */
    public function updateProfile(Request $request, $id)
    {
        $profile = Hl7MappingProfile::findOrFail($id);

        $validated = $request->validate([
            'profile_name' => 'required|string|max:255|unique:hl7_mapping_profiles,name,' . $id,
            'profile_description' => 'nullable|string|max:1000',
            'hl7_mappings' => 'required|array',
        ]);

        $profile->update([
            'name' => $validated['profile_name'],
            'description' => $validated['profile_description'],
            'mapping_codes' => $validated['hl7_mappings'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'HL7 mapping profile updated successfully.',
            'profile' => $profile
        ]);
    }

    /**
     * Load an HL7 mapping profile
     */
    public function loadProfile(Request $request, $id)
    {
        $profile = Hl7MappingProfile::findOrFail($id);

        return response()->json([
            'success' => true,
            'profile' => $profile,
            'mapping_codes' => $profile->mapping_codes
        ]);
    }

    /**
     * Set an HL7 mapping profile as active
     */
    public function setActiveProfile(Request $request, $id)
    {
        $profile = Hl7MappingProfile::findOrFail($id);
        $profile->setAsActive();

        return response()->json([
            'success' => true,
            'message' => 'Profile "' . $profile->name . '" is now active.',
            'profile' => $profile
        ]);
    }

    /**
     * Delete an HL7 mapping profile
     */
    public function deleteProfile($id)
    {
        $profile = Hl7MappingProfile::findOrFail($id);

        // Cannot delete the active profile
        if ($profile->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the active profile. Please set another profile as active first.'
            ], 422);
        }

        $profileName = $profile->name;
        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profile "' . $profileName . '" deleted successfully.'
        ]);
    }

    /**
     * Get all HL7 mapping profiles
     */
    public function getProfiles()
    {
        $profiles = Hl7MappingProfile::getAllForSelection();

        return response()->json([
            'success' => true,
            'profiles' => $profiles
        ]);
    }

    /**
     * Create a default HL7 mapping profile if none exists
     */
    public function createDefaultProfile()
    {
        // Check if any profiles exist
        if (Hl7MappingProfile::count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Profiles already exist.'
            ], 422);
        }

        $profile = Hl7MappingProfile::createOrUpdateProfile([
            'name' => 'Default HL7 Profile',
            'description' => 'Default HL7 mapping configuration with standard field mappings.',
            'mapping_codes' => Hl7MappingProfile::getDefaultMappingCodes(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Default HL7 profile created successfully.',
            'profile' => $profile
        ]);
    }
} 