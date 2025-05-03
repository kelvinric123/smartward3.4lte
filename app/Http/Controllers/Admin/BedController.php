<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Consultant;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;

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

        return view('admin.beds.beds.edit', compact('bed', 'wards', 'consultants', 'patients', 'nurses', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:10',
            'status' => 'required|in:' . implode(',', array_keys(Bed::getStatuses())),
            'ward_id' => 'required|exists:wards,id',
            'consultant_id' => 'nullable|exists:consultants,id',
            'nurse_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,id',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
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

        $bed->update($validated);

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
}
