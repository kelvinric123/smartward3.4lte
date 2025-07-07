<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Ward;
use Illuminate\Http\Request;
use App\Models\PatientMovement;

class WardController extends Controller
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wards = Ward::with(['hospital', 'specialty'])
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->paginate(10)
            ->appends($request->except('page'));
            
        return view('admin.beds.wards.index', compact('wards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hospitals = Hospital::where('is_active', true)->get();
        $specialties = Specialty::where('is_active', true)->get();
        return view('admin.beds.wards.create', compact('hospitals', 'specialties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'hospital_id' => 'required|exists:hospitals,id',
            'specialty_id' => 'required|exists:specialties,id',
            'is_active' => 'boolean',
        ]);

        Ward::create($validated);

        return redirect()->route('admin.beds.wards.index')
            ->with('success', 'Ward created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ward $ward)
    {
        // Get all active wards for the ward selector dropdown
        $allWards = Ward::where('is_active', true)->get();
        
        return view('admin.beds.wards.show', compact('ward', 'allWards'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ward $ward)
    {
        $hospitals = Hospital::where('is_active', true)->get();
        $specialties = Specialty::where('is_active', true)->get();
        return view('admin.beds.wards.edit', compact('ward', 'hospitals', 'specialties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ward $ward)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'hospital_id' => 'required|exists:hospitals,id',
            'specialty_id' => 'required|exists:specialties,id',
            'is_active' => 'boolean',
        ]);

        $ward->update($validated);

        return redirect()->route('admin.beds.wards.index')
            ->with('success', 'Ward updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ward $ward)
    {
        $ward->delete();

        return redirect()->route('admin.beds.wards.index')
            ->with('success', 'Ward deleted successfully');
    }
    
    /**
     * Display the ward dashboard with bed layout.
     */
    public function dashboard(Request $request, Ward $ward)
    {
        // Load the ward with its relationships
        $ward->load(['hospital', 'specialty', 'beds.consultant', 'beds.nurse', 'beds.patient.latestVitalSigns']);
        
        // Get all active wards for the ward selector dropdown
        $allWards = Ward::where('is_active', true)->get();
        
        // Get bed status counts for dashboard stats (always show full counts)
        $availableBeds = $ward->beds->where('status', 'available')->count();
        $cleaningNeededBeds = $ward->beds->where('status', 'cleaning_needed')->count();
        $totalBeds = $ward->beds->count();
        $occupiedBeds = $ward->beds->where('status', 'occupied')->count();
        
        // Get filter parameters
        $filters = $request->get('filters', []);
        $activeFilters = is_array($filters) ? $filters : [];
        
        // Get consultant filter
        $consultantFilter = $request->get('consultant_id');
        
        // If no filters are active, show all beds
        if (empty($activeFilters)) {
            $activeFilters = ['all'];
        }
        
        // Filter beds based on active filters
        $filteredBeds = $ward->beds;
        if (!in_array('all', $activeFilters)) {
            $filteredBeds = $ward->beds->filter(function ($bed) use ($activeFilters) {
                return in_array($bed->status, $activeFilters);
            });
        }
        
        // Apply consultant filter if specified
        if ($consultantFilter) {
            $filteredBeds = $filteredBeds->filter(function ($bed) use ($consultantFilter) {
                return $bed->consultant_id == $consultantFilter;
            });
        }
        
        // Get unique nurses assigned to this ward's beds
        $nursesOnDuty = $ward->beds->pluck('nurse_id')->filter()->unique()->count();
        
        // Get consultants for this ward's specialty
        $consultantsCount = \App\Models\Consultant::where('specialty_id', $ward->specialty_id)
            ->where('is_active', true)
            ->count();
        
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
        
        // Get recent unresolved patient alerts for this ward
        $patientAlerts = \App\Models\PatientAlert::where('ward_id', $ward->id)
            ->whereIn('status', ['new', 'seen'])
            ->with(['patient', 'bed'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.beds.wards.dashboard', compact(
            'ward', 
            'allWards',
            'availableBeds', 
            'cleaningNeededBeds',
            'nursesOnDuty',
            'occupiedBeds',
            'nursePatientRatio',
            'occupancyRate',
            'activeMovements',
            'patientAlerts',
            'filteredBeds',
            'activeFilters',
            'consultantsCount',
            'consultantFilter'
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
        
        return view('admin.beds.wards.admit_patient', compact('ward', 'bed', 'consultants', 'patients', 'nurses'));
    }
    
    /**
     * Show form to admit a patient to a bed from the ward dashboard in an iframe.
     */
    public function iframeAdmitPatient(Ward $ward, $bedId)
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
        
        return view('admin.beds.wards.admit_patient_iframe', compact('ward', 'bed', 'consultants', 'patients', 'nurses'));
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
        
        // Find the patient
        $patient = \App\Models\Patient::findOrFail($validated['patient_id']);
        
        // Check if patient is already admitted
        if ($patient->is_admitted) {
            // Get the current bed and ward
            $currentBed = $patient->bed;
            $currentWard = $currentBed ? $currentBed->ward : null;
            
            $errorMessage = 'This patient is already admitted';
            if ($currentBed && $currentWard) {
                $errorMessage .= " to {$currentWard->name} (Bed {$currentBed->bed_number})";
            }
            $errorMessage .= '. Please select another patient.';
            
            // Return a JSON response for AJAX requests
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage,
                'systemAlert' => true
            ], 422);
        }
        
        // Find the bed
        $bed = $ward->beds()->findOrFail($bedId);
        
        // Update the bed with admission information
        $bed->update([
            'status' => 'occupied',
            'patient_id' => $validated['patient_id'],
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
            'notes' => $validated['notes'],
        ]);
        
        // Get the admission date, ensuring we use KL timezone
        $admissionDate = $request->filled('admission_date') 
            ? \Carbon\Carbon::parse($request->admission_date)->setTimezone('Asia/Kuala_Lumpur')
            : now();
        
        // Create admission record
        \App\Models\PatientAdmission::create([
            'patient_id' => $validated['patient_id'],
            'ward_id' => $ward->id,
            'bed_id' => $bed->id,
            'bed_number' => $bed->bed_number,
            'admission_date' => $admissionDate,
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
            'admitted_by' => auth()->id(),
            'admission_notes' => $validated['notes'],
            'is_active' => true,
        ]);
        
        // Preserve fullscreen mode if it was enabled
        $redirectRoute = route('admin.beds.wards.dashboard', $ward);
        if ($request->has('fullscreen') && $request->fullscreen == 'true') {
            $redirectRoute .= '?fullscreen=true';
        }
        
        return redirect($redirectRoute)
            ->with('success', 'Patient admitted successfully');
    }

    /**
     * Display patient details
     */
    public function patientDetails(Ward $ward, $bedId)
    {
        try {
            // Find the bed within this ward
            $bed = $ward->beds()->findOrFail($bedId);
            
            // Check if the bed has a patient
            if (!$bed->patient_id) {
                // Preserve fullscreen mode if it was enabled
                $redirectRoute = route('admin.beds.wards.dashboard', $ward);
                if (request()->has('fullscreen') && request()->fullscreen == 'true') {
                    $redirectRoute .= '?fullscreen=true';
                }
                
                return redirect($redirectRoute)
                    ->with('error', 'This bed does not have a patient assigned.');
            }
            
            // Get the patient
            $patient = $bed->patient;
            
            // Get the active admission
            $activeAdmission = $patient->activeAdmission;
            
            // Get patient movements (scheduled and past)
            $patientMovements = PatientMovement::where('patient_id', $patient->id)
                ->orderBy('scheduled_time', 'desc')
                ->get();
                
            // Get all service locations for the dropdown
            $serviceLocations = ['Radiology', 'Laboratory', 'Physiotherapy', 'Pharmacy', 'Dialysis', 'Operating Theatre'];
            
            // Load patient referrals
            $patientReferrals = $patient->referrals()->with(['toSpecialty', 'toConsultant', 'fromWard', 'referredBy'])->get();
            
            return view('admin.beds.wards.patient_details', compact(
                'ward', 
                'bed', 
                'patient', 
                'activeAdmission', 
                'patientMovements',
                'serviceLocations',
                'patientReferrals'
            ));
        } catch (\Exception $e) {
            // Preserve fullscreen mode if it was enabled
            $redirectRoute = route('admin.beds.wards.dashboard', $ward);
            if (request()->has('fullscreen') && request()->fullscreen == 'true') {
                $redirectRoute .= '?fullscreen=true';
            }
            
            return redirect($redirectRoute)
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Update risk factors for a patient's admission
     */
    public function updateRiskFactors(Request $request, Ward $ward, $bedId)
    {
        // Find the bed within this ward
        $bed = $ward->beds()->findOrFail($bedId);
        
        // Check if the bed has a patient
        if (!$bed->patient_id) {
            // Preserve fullscreen mode if it was enabled
            $redirectRoute = route('admin.beds.wards.dashboard', $ward);
            if ($request->has('fullscreen') && $request->fullscreen == 'true') {
                $redirectRoute .= '?fullscreen=true';
            }
            if ($request->ajax()) {
                return response()->json(['error' => 'This bed does not have a patient assigned.'], 422);
            }
            return redirect($redirectRoute)
                ->with('error', 'This bed does not have a patient assigned.');
        }
        
        // Get the patient
        $patient = $bed->patient;
        
        // Get the active admission
        $activeAdmission = $patient->activeAdmission;
        
        if (!$activeAdmission) {
            // Preserve fullscreen mode if it was enabled
            $redirectRoute = route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bed->id]);
            if ($request->has('fullscreen') && $request->fullscreen == 'true') {
                $redirectRoute .= '?fullscreen=true';
            }
            if ($request->ajax()) {
                return response()->json(['error' => 'No active admission found for this patient.'], 422);
            }
            return redirect($redirectRoute)
                ->with('error', 'No active admission found for this patient.');
        }
        
        // Update risk factors
        $riskFactors = $request->has('risk_factors') ? $request->risk_factors : [];
        $activeAdmission->update([
            'risk_factors' => $riskFactors,
        ]);
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        // Preserve fullscreen mode if it was enabled
        $redirectRoute = route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bed->id]);
        if ($request->has('fullscreen') && $request->fullscreen == 'true') {
            $redirectRoute .= '?fullscreen=true';
        }
        
        return redirect($redirectRoute)
            ->with('success', 'Risk factors updated successfully.');
    }

    /**
     * Display patient details in iframe
     */
    public function iframePatientDetails(Ward $ward, $bedId)
    {
        try {
            // Find the bed within this ward
            $bed = $ward->beds()->findOrFail($bedId);
            
            // Check if the bed has a patient
            if (!$bed->patient_id) {
                return response()->view('layouts.iframe_error', [
                    'message' => 'This bed does not have a patient assigned.'
                ]);
            }
            
            // Get the patient
            $patient = $bed->patient;
            
            // Get the active admission
            $activeAdmission = $patient->activeAdmission;
            
            // Get patient movements (scheduled and past)
            $patientMovements = \App\Models\PatientMovement::where('patient_id', $patient->id)
                ->orderBy('scheduled_time', 'desc')
                ->get();
                
            // Get all service locations for the dropdown
            $serviceLocations = ['Radiology', 'Laboratory', 'Physiotherapy', 'Pharmacy', 'Dialysis', 'Operating Theatre'];
            
            // Load patient referrals
            $patientReferrals = $patient->referrals()->with(['toSpecialty', 'toConsultant', 'fromWard', 'referredBy'])->get();
            
            // Get all active wards for transfer options
            $allWards = \App\Models\Ward::where('is_active', true)->get();
            
            return view('admin.beds.wards.iframe_patient_details', compact(
                'ward', 
                'bed', 
                'patient', 
                'activeAdmission', 
                'patientMovements',
                'serviceLocations',
                'patientReferrals',
                'allWards'
            ));
        } catch (\Exception $e) {
            return response()->view('layouts.iframe_error', [
                'message' => 'An error occurred while loading patient details: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get patient alerts for a ward
     * Used for AJAX polling to update the notification display
     */
    public function getPatientAlerts(Ward $ward)
    {
        try {
            // Get recent unresolved patient alerts for this ward
            $patientAlerts = \App\Models\PatientAlert::where('ward_id', $ward->id)
                ->whereIn('status', ['new', 'seen'])
                ->with(['patient', 'bed'])
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();
                
            // Count all unresolved alerts (both new and seen are considered active)
            $activeAlertsCount = $patientAlerts->count();
            
            // Get resolved alerts count for history statistics
            $resolvedAlertsCount = \App\Models\PatientAlert::where('ward_id', $ward->id)
                ->where('status', 'resolved')
                ->count();
            
            // Get responses count (number of patient responses sent)
            $responsesCount = \App\Models\PatientResponse::whereHas('patientAlert', function($query) use ($ward) {
                $query->where('ward_id', $ward->id);
            })->count();
            
            return response()->json([
                'success' => true,
                'alerts' => $patientAlerts,
                'new_alerts_count' => $activeAlertsCount,
                'resolved_alerts_count' => $resolvedAlertsCount,
                'responses_count' => $responsesCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get resolved patient alerts for history
     * Used for displaying alert history
     */
    public function getPatientAlertsHistory(Ward $ward)
    {
        try {
            // Get resolved patient alerts for this ward
            $resolvedAlerts = \App\Models\PatientAlert::where('ward_id', $ward->id)
                ->where('status', 'resolved')
                ->with(['patient', 'bed', 'responses.nurse'])
                ->orderBy('updated_at', 'desc')
                ->take(50)
                ->get();
                
            return response()->json([
                'success' => true,
                'history' => $resolvedAlerts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching alert history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark an alert as seen
     */
    public function markAlertAsSeen($alertId)
    {
        try {
            $alert = \App\Models\PatientAlert::findOrFail($alertId);
            $alert->status = 'seen';
            $alert->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Alert marked as seen'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking alert as seen: ' . $e->getMessage()
            ], 500);
        }
    }
    


    /**
     * Mark an alert as resolved and send notification to patient
     */
    public function respondToAlert(Request $request, $alertId)
    {
        try {
            $alert = \App\Models\PatientAlert::findOrFail($alertId);
            
            // Get custom response message or use default
            $responseMessage = $request->input('response_message', 
                'Your alert has been acknowledged by nursing staff. We are taking care of your request.'
            );
            
            // Mark alert as resolved
            $alert->status = 'resolved';
            $alert->save();
            
            // Create a response notification for the patient
            \App\Models\PatientResponse::create([
                'patient_alert_id' => $alert->id,
                'patient_id' => $alert->patient_id,
                'nurse_id' => auth()->id(), // Current logged-in user (nurse)
                'response_message' => $responseMessage,
                'status' => 'sent'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Alert responded to and patient notified'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error responding to alert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer patient to a different bed
     */
    public function transferPatientBed(Request $request, Ward $ward, $bedId)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'target_ward_id' => 'required|exists:wards,id',
                'target_bed_id' => 'required|exists:beds,id',
                'transfer_reason' => 'required|string|max:255',
                'transfer_notes' => 'nullable|string',
            ]);

            // Find the current bed within this ward
            $currentBed = $ward->beds()->findOrFail($bedId);
            
            // Check if the bed has a patient
            if (!$currentBed->patient_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This bed does not have a patient assigned.'
                ], 422);
            }
            
            // Get the patient
            $patient = $currentBed->patient;
            
            // Find the target bed
            $targetBed = \App\Models\Bed::findOrFail($validated['target_bed_id']);
            
            // Validate target bed is in the selected ward
            if ($targetBed->ward_id != $validated['target_ward_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bed does not belong to the selected ward.'
                ], 422);
            }
            
            // Check if target bed is available
            if ($targetBed->status !== 'available' || $targetBed->patient_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target bed is not available.'
                ], 422);
            }
            
            // Get the active admission
            $activeAdmission = $patient->activeAdmission;
            
            if (!$activeAdmission) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active admission found for this patient.'
                ], 422);
            }

            // Start database transaction
            \DB::beginTransaction();

            try {
                // Create a patient transfer record for tracking
                \App\Models\PatientTransfer::create([
                    'patient_id' => $patient->id,
                    'admission_id' => $activeAdmission->id,
                    'from_ward_id' => $currentBed->ward_id,
                    'from_bed_id' => $currentBed->id,
                    'to_ward_id' => $targetBed->ward_id,
                    'to_bed_id' => $targetBed->id,
                    'transfer_date' => now()->setTimezone('Asia/Kuala_Lumpur'),
                    'reason' => $validated['transfer_reason'],
                    'notes' => $validated['transfer_notes'],
                    'transferred_by' => auth()->id(),
                ]);

                // Update the current bed - make it available
                $currentBed->update([
                    'status' => 'available',
                    'patient_id' => null,
                    'consultant_id' => null,
                    'nurse_id' => null,
                ]);

                // Update the target bed - assign patient
                $targetBed->update([
                    'status' => 'occupied',
                    'patient_id' => $patient->id,
                    'consultant_id' => $currentBed->consultant_id, // Keep same consultant
                    'nurse_id' => $currentBed->nurse_id, // Keep same nurse
                ]);

                // Update the active admission with new bed info
                $activeAdmission->update([
                    'ward_id' => $targetBed->ward_id,
                    'bed_id' => $targetBed->id,
                    'bed_number' => $targetBed->bed_number,
                ]);

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Patient transferred successfully to ' . $targetBed->ward->name . ' - Bed ' . $targetBed->bed_number,
                    'redirect_url' => route('admin.beds.wards.dashboard', $ward->id)
                ]);

            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during transfer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available beds for transfer (AJAX endpoint)
     */
    public function getAvailableBeds(Request $request, Ward $ward)
    {
        try {
            $targetWardId = $request->input('ward_id');
            
            if (!$targetWardId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ward ID is required'
                ], 400);
            }

            // Get available beds in the specified ward
            $availableBeds = \App\Models\Bed::where('ward_id', $targetWardId)
                ->where('status', 'available')
                ->whereNull('patient_id')
                ->orderBy('bed_number')
                ->get(['id', 'bed_number']);

            return response()->json([
                'success' => true,
                'beds' => $availableBeds
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching available beds: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show consultants list for the ward's specialty and hospital
     */
    public function showConsultants(Ward $ward)
    {
        // Get consultants from the same specialty as the ward
        $specialtyConsultants = \App\Models\Consultant::where('specialty_id', $ward->specialty_id)
            ->where('is_active', true)
            ->with(['specialty'])
            ->get()
            ->map(function ($consultant) {
                // Count patients for this consultant
                $patientCount = \App\Models\Bed::where('consultant_id', $consultant->id)
                    ->where('status', 'occupied')
                    ->count();
                
                $consultant->patient_count = $patientCount;
                return $consultant;
            });
        
        // Get all other consultants from different specialties
        $otherConsultants = \App\Models\Consultant::where('specialty_id', '!=', $ward->specialty_id)
            ->where('is_active', true)
            ->with(['specialty'])
            ->get()
            ->map(function ($consultant) {
                // Count patients for this consultant
                $patientCount = \App\Models\Bed::where('consultant_id', $consultant->id)
                    ->where('status', 'occupied')
                    ->count();
                
                $consultant->patient_count = $patientCount;
                return $consultant;
            });
        
        return view('admin.beds.wards.consultants_list', compact('ward', 'specialtyConsultants', 'otherConsultants'));
    }
}
