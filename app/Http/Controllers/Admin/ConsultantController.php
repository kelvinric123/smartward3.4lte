<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsultantController extends Controller
{
    public function index()
    {
        $consultants = Consultant::with(['specialty.hospital'])->paginate(10);
        return view('admin.consultants.index', compact('consultants'));
    }

    public function create()
    {
        $specialties = Specialty::where('is_active', true)->get();
        return view('admin.consultants.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:20|unique:consultants',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants',
            'phone' => 'nullable|string|max:20',
            'qualification' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'specialty_id' => 'required|exists:specialties,id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('consultants', 'public');
            $validated['photo'] = $path;
        }

        Consultant::create($validated);

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant created successfully.');
    }

    public function edit(Consultant $consultant)
    {
        $specialties = Specialty::where('is_active', true)->get();
        return view('admin.consultants.edit', compact('consultant', 'specialties'));
    }

    public function update(Request $request, Consultant $consultant)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:20|unique:consultants,code,' . $consultant->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email,' . $consultant->id,
            'phone' => 'nullable|string|max:20',
            'qualification' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'specialty_id' => 'required|exists:specialties,id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($consultant->photo) {
                Storage::disk('public')->delete($consultant->photo);
            }
            $path = $request->file('photo')->store('consultants', 'public');
            $validated['photo'] = $path;
        }

        $consultant->update($validated);

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant updated successfully.');
    }

    public function destroy(Consultant $consultant)
    {
        if ($consultant->photo) {
            Storage::disk('public')->delete($consultant->photo);
        }
        
        $consultant->delete();

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant deleted successfully.');
    }
} 
