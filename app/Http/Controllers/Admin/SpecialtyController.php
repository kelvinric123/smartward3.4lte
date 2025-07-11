<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::with('hospital')->paginate(10);
        return view('admin.specialties.index', compact('specialties'));
    }

    public function create()
    {
        $hospitals = Hospital::where('is_active', true)->get();
        return view('admin.specialties.create', compact('hospitals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hospital_id' => 'required|exists:hospitals,id',
            'is_active' => 'boolean',
        ]);

        Specialty::create($validated);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Specialty created successfully.');
    }

    public function edit(Specialty $specialty)
    {
        $hospitals = Hospital::where('is_active', true)->get();
        return view('admin.specialties.edit', compact('specialty', 'hospitals'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hospital_id' => 'required|exists:hospitals,id',
            'is_active' => 'boolean',
        ]);

        $specialty->update($validated);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Specialty updated successfully.');
    }

    public function destroy(Specialty $specialty)
    {
        $specialty->delete();

        return redirect()->route('admin.specialties.index')
            ->with('success', 'Specialty deleted successfully.');
    }

    /**
     * Display the specified specialty.
     *
     * @param  \App\Models\Specialty  $specialty
     * @return \Illuminate\Http\Response
     */
    public function show(Specialty $specialty)
    {
        $specialty->load(['consultants' => function($query) {
            $query->orderBy('name');
        }]);
        
        return view('admin.specialties.show', compact('specialty'));
    }

    /**
     * Get specialties by hospital
     */
    public function getSpecialtiesByHospital(Request $request)
    {
        $hospitalId = $request->input('hospital_id');
        $specialties = Specialty::where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->get();
            
        return response()->json($specialties);
    }
    
    /**
     * Alternative method for getting specialties by hospital
     * Used as a direct route
     */
    public function getSpecialtiesByHospitalDirect(Request $request)
    {
        try {
            $hospitalId = $request->input('hospital_id');
            
            if (!$hospitalId) {
                return response()->json(['error' => 'Hospital ID is required'], 400);
            }
            
            $specialties = Specialty::where('hospital_id', $hospitalId)
                ->where('is_active', true)
                ->get(['id', 'name', 'hospital_id']);
                
            return response()->json($specialties);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 
