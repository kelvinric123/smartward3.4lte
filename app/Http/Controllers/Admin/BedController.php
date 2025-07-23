<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Consultant;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BedController extends Controller
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
        $beds = Bed::with(['ward.hospital', 'ward.specialty', 'consultant', 'nurse', 'patient'])
            ->when($request->filled('search'), function($query) use ($request) {
                return $query->where('bed_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('ward', function($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            })
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('ward_id'), function($query) use ($request) {
                return $query->where('ward_id', $request->ward_id);
            })
            ->paginate(10)
            ->appends($request->except('page'));

        $wards = Ward::where('is_active', true)->get();
        $statuses = Bed::getStatuses();

        return view('admin.beds.beds.index', compact('beds', 'wards', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wards = Ward::where('is_active', true)->get();
        $consultants = Consultant::where('is_active', true)->get();
        $patients = Patient::all();
        $nurses = User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->get();
        $statuses = Bed::getStatuses();

        return view('admin.beds.beds.create', compact('wards', 'consultants', 'patients', 'nurses', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:20|unique:beds',
            'bed_number' => 'required|string|max:10',
            'status' => 'required|in:' . implode(',', array_keys(Bed::getStatuses())),
            'ward_id' => 'required|exists:wards,id',
            'consultant_id' => 'nullable|exists:consultants,id',
            'nurse_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check for uniqueness of bed number in ward
        $bedExists = Bed::where('ward_id', $request->ward_id)
            ->where('bed_number', $request->bed_number)
            ->exists();

        if ($bedExists) {
            return back()->withInput()->withErrors([
                'bed_number' => 'This bed number already exists in the selected ward.'
            ]);
        }

        // If status is available, clear any assigned consultant/patient/nurse
        if ($request->status === Bed::STATUS_AVAILABLE) {
            $validated['consultant_id'] = null;
            $validated['nurse_id'] = null;
            $validated['patient_id'] = null;
        }

        // If status is occupied, ensure a patient is assigned
        if ($request->status === Bed::STATUS_OCCUPIED && empty($request->patient_id)) {
            return back()->withInput()->withErrors([
                'patient_id' => 'A patient must be assigned when the bed status is Occupied.'
            ]);
        }

        $bed = Bed::create($validated);

        return redirect()->route('admin.beds.beds.index')
            ->with('success', 'Bed created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bed $bed)
    {
        $bed->load(['ward.hospital', 'ward.specialty', 'consultant', 'nurse', 'patient']);
        return view('admin.beds.beds.show', compact('bed'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Bed $bed)
    {
        $wards = Ward::where('is_active', true)->get();
        $consultants = Consultant::where('is_active', true)->get();
        $patients = Patient::all();
        $nurses = User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->get();
        $statuses = Bed::getStatuses();
        
        // Pre-select status if it's provided in the request
        if ($request->has('status') && array_key_exists($request->status, Bed::getStatuses())) {
            $bed->status = $request->status;
        }

        // If this is an admission request (patient is being added to bed)
        $isAdmitting = $request->has('status') && $request->status === Bed::STATUS_OCCUPIED && !$bed->patient_id;
        $admissionDate = now()->format('Y-m-d\TH:i');

        return view('admin.beds.beds.edit', compact('bed', 'wards', 'consultants', 'patients', 'nurses', 'statuses', 'isAdmitting', 'admissionDate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:20|unique:beds,code,' . $bed->id,
            'bed_number' => 'required|string|max:10',
            'status' => 'required|in:' . implode(',', array_keys(Bed::getStatuses())),
            'ward_id' => 'required|exists:wards,id',
            'consultant_id' => 'nullable|exists:consultants,id',
            'nurse_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'admission_date' => 'nullable|date',
        ]);

        // Check for uniqueness of bed number in ward (if changed)
        if ($request->ward_id != $bed->ward_id || $request->bed_number != $bed->bed_number) {
            $bedExists = Bed::where('ward_id', $request->ward_id)
                ->where('bed_number', $request->bed_number)
                ->where('id', '!=', $bed->id)
                ->exists();

            if ($bedExists) {
                return back()->withInput()->withErrors([
                    'bed_number' => 'This bed number already exists in the selected ward.'
                ]);
            }
        }

        // If status is available, clear any assigned consultant/patient/nurse
        if ($request->status === Bed::STATUS_AVAILABLE) {
            $validated['consultant_id'] = null;
            $validated['nurse_id'] = null;
            $validated['patient_id'] = null;
        }

        // If status is occupied, ensure a patient is assigned
        if ($request->status === Bed::STATUS_OCCUPIED && empty($request->patient_id)) {
            return back()->withInput()->withErrors([
                'patient_id' => 'A patient must be assigned when the bed status is Occupied.'
            ]);
        }
        
        // Check if this is a new patient admission
        $isNewAdmission = $request->status === Bed::STATUS_OCCUPIED && 
                         (empty($bed->patient_id) || $bed->patient_id != $request->patient_id);
        
        // Update the bed
        $bed->update($validated);
        
        // Create admission record if this is a new admission
        if ($isNewAdmission) {
            \App\Models\PatientAdmission::create([
                'patient_id' => $validated['patient_id'],
                'ward_id' => $validated['ward_id'],
                'bed_id' => $bed->id,
                'bed_number' => $validated['bed_number'],
                'admission_date' => $request->filled('admission_date') 
                    ? Carbon::parse($request->admission_date)->setTimezone('Asia/Kuala_Lumpur')
                    : now(),
                'consultant_id' => $validated['consultant_id'],
                'nurse_id' => $validated['nurse_id'],
                'admitted_by' => auth()->id(),
                'admission_notes' => $validated['notes'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.beds.beds.index')
            ->with('success', 'Bed updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bed $bed)
    {
        $bed->delete();

        return redirect()->route('admin.beds.beds.index')
            ->with('success', 'Bed deleted successfully');
    }
    
    /**
     * Show form to admit a patient to a specific bed.
     */
    public function admitPatient(Bed $bed)
    {
        // Redirect to edit with status parameter
        return redirect()->route('admin.beds.beds.edit', [
            'bed' => $bed, 
            'status' => 'occupied'
        ]);
    }
    
    /**
     * Discharge a patient from the bed
     */
    public function discharge(Request $request, Bed $bed)
    {
        // Check if the bed has a patient
        if (!$bed->patient_id) {
            return redirect()->route('admin.beds.beds.show', $bed)
                ->with('error', 'No patient assigned to this bed.');
        }
        
        // Get data needed for discharge record
        $patientId = $bed->patient_id;
        $wardId = $bed->ward_id;
        $bedNumber = $bed->bed_number;
        
        // Mark any active admissions as inactive
        \App\Models\PatientAdmission::where('patient_id', $patientId)
            ->where('is_active', true)
            ->update(['is_active' => false]);
        
        // Create discharge record
        \App\Models\PatientDischarge::create([
            'patient_id' => $patientId,
            'ward_id' => $wardId,
            'bed_number' => $bedNumber,
            'discharge_date' => now()->setTimezone('Asia/Kuala_Lumpur'),
            'discharge_type' => 'regular', // Default discharge type
            'discharged_by' => auth()->id(),
            'discharge_notes' => $request->has('notes') ? $request->notes : 'Discharged from bed ' . $bedNumber,
        ]);
        
        // Update bed status to cleaning needed and remove patient assignment
        $bed->update([
            'status' => Bed::STATUS_CLEANING_NEEDED,
            'patient_id' => null,
            'consultant_id' => null,
            'nurse_id' => null,
        ]);
        
        return redirect()->route('admin.beds.beds.show', $bed)
            ->with('success', 'Patient discharged successfully.');
    }
    
    /**
     * Mark bed cleaning as done
     */
    public function markCleaningDone(Request $request, Bed $bed)
    {
        // Check if the bed status is cleaning_needed
        if ($bed->status !== Bed::STATUS_CLEANING_NEEDED) {
            return redirect()->back()
                ->with('error', 'This bed is not in cleaning needed status.');
        }
        
        // Update bed status to available
        $bed->update([
            'status' => Bed::STATUS_AVAILABLE,
        ]);
        
        // Check if this is an iframe request from ward dashboard
        if ($request->has('is_iframe')) {
            // Return a response that will refresh the parent window
            return response()->json([
                'success' => true,
                'message' => 'Bed cleaning completed successfully.',
                'refresh' => true
            ]);
        }
        
        // Redirect back to ward dashboard if requested
        if ($request->has('redirect_to_ward') && $request->ward_id) {
            return redirect()->route('admin.beds.wards.dashboard', $request->ward_id)
                ->with('success', 'Bed cleaning completed successfully.');
        }
        
        return redirect()->route('admin.beds.beds.show', $bed)
            ->with('success', 'Bed cleaning completed successfully.');
    }
}
