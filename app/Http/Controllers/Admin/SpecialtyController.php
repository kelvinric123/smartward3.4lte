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
} 
