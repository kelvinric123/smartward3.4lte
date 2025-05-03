<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Ward;
use Illuminate\Http\Request;

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
        
        return view('admin.beds.wards.dashboard', compact(
            'ward', 
            'availableBeds', 
            'nursesOnDuty',
            'occupiedBeds',
            'nursePatientRatio',
            'occupancyRate'
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
        
        return redirect()->route('admin.beds.wards.dashboard', $ward)
            ->with('success', 'Patient admitted successfully');
    }
}
