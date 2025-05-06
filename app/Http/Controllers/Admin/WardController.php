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
        return view('admin.beds.wards.show', compact('ward'));
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
    public function dashboard(Ward $ward)
    {
        // Load the ward with its relationships
        $ward->load(['hospital', 'specialty', 'beds.consultant', 'beds.nurse', 'beds.patient']);
        
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
        
        return view('admin.beds.wards.dashboard', compact(
            'ward', 
            'availableBeds', 
            'nursesOnDuty',
            'occupiedBeds',
            'nursePatientRatio',
            'occupancyRate',
            'activeMovements'
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
        
        return redirect()->route('admin.beds.wards.dashboard', $ward)
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
                return redirect()->route('admin.beds.wards.dashboard', $ward)
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
            $patientReferrals = $patient->referrals;
            
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
            return redirect()->route('admin.beds.wards.dashboard', $ward)
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
            return redirect()->route('admin.beds.wards.dashboard', $ward)
                ->with('error', 'This bed does not have a patient assigned.');
        }
        
        // Get the patient
        $patient = $bed->patient;
        
        // Get the active admission
        $activeAdmission = $patient->activeAdmission;
        
        if (!$activeAdmission) {
            return redirect()->route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bed->id])
                ->with('error', 'No active admission found for this patient.');
        }
        
        // Update risk factors
        $riskFactors = $request->has('risk_factors') ? $request->risk_factors : [];
        $activeAdmission->update([
            'risk_factors' => $riskFactors,
        ]);
        
        return redirect()->route('admin.beds.wards.patient.details', ['ward' => $ward->id, 'bedId' => $bed->id])
            ->with('success', 'Risk factors updated successfully.');
    }
}
