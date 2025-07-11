<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NurseController extends Controller
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
        // Get the nurse role
        $nurseRole = Role::where('slug', 'nurse')->first();
        
        if (!$nurseRole) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Nurse role not found. Please create it first.');
        }
        
        // Get all users with nurse role
        $nurses = User::whereHas('roles', function($query) use ($nurseRole) {
            $query->where('roles.id', $nurseRole->id);
        })
        ->when($request->filled('search'), function($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        })
        ->orderBy('name')
        ->paginate(10)
        ->appends($request->except('page'));
        
        return view('admin.nurses.index', compact('nurses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hospitals = Hospital::where('is_active', true)->get();
        return view('admin.nurses.create', compact('hospitals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'hospital_id' => 'required|exists:hospitals,id',
            'active' => 'boolean',
        ]);
        
        // Set default password if not provided
        if (!isset($validated['password'])) {
            $validated['password'] = '12345678';
        }
        
        // Hash the password
        $validated['password'] = Hash::make($validated['password']);
        
        // Set active status
        $validated['active'] = $request->has('active') ? true : false;
        
        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'active' => $validated['active'],
        ]);
        
        // Assign nurse role
        $nurseRole = Role::where('slug', 'nurse')->first();
        if ($nurseRole) {
            $user->roles()->attach($nurseRole);
        }
        
        // Add hospital_id as user_meta
        $user->hospital_id = $validated['hospital_id'];
        $user->save();
        
        return redirect()->route('admin.nurses.index')
            ->with('success', 'Nurse created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $nurse)
    {
        // Verify this user has the nurse role
        if (!$nurse->hasRole('nurse')) {
            return redirect()->route('admin.nurses.index')
                ->with('error', 'User is not a nurse.');
        }
        
        // Get hospital
        $hospital = Hospital::find($nurse->hospital_id);
        
        return view('admin.nurses.show', compact('nurse', 'hospital'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $nurse)
    {
        // Verify this user has the nurse role
        if (!$nurse->hasRole('nurse')) {
            return redirect()->route('admin.nurses.index')
                ->with('error', 'User is not a nurse.');
        }
        
        $hospitals = Hospital::where('is_active', true)->get();
        
        return view('admin.nurses.edit', compact('nurse', 'hospitals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $nurse)
    {
        // Verify this user has the nurse role
        if (!$nurse->hasRole('nurse')) {
            return redirect()->route('admin.nurses.index')
                ->with('error', 'User is not a nurse.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $nurse->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'hospital_id' => 'required|exists:hospitals,id',
            'active' => 'boolean',
        ]);
        
        // Update user fields
        $nurse->name = $validated['name'];
        $nurse->email = $validated['email'];
        $nurse->phone = $validated['phone'] ?? $nurse->phone;
        $nurse->active = $request->has('active') ? true : false;
        
        // Update password if provided
        if (isset($validated['password'])) {
            $nurse->password = Hash::make($validated['password']);
        }
        
        // Update hospital_id
        $nurse->hospital_id = $validated['hospital_id'];
        
        $nurse->save();
        
        return redirect()->route('admin.nurses.index')
            ->with('success', 'Nurse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $nurse)
    {
        // Verify this user has the nurse role
        if (!$nurse->hasRole('nurse')) {
            return redirect()->route('admin.nurses.index')
                ->with('error', 'User is not a nurse.');
        }
        
        // Check if nurse is assigned to any beds
        $bedsAssigned = $nurse->beds()->count();
        if ($bedsAssigned > 0) {
            return redirect()->route('admin.nurses.index')
                ->with('error', 'Cannot delete nurse as they are assigned to ' . $bedsAssigned . ' beds.');
        }
        
        // Remove nurse role
        $nurseRole = Role::where('slug', 'nurse')->first();
        if ($nurseRole) {
            $nurse->roles()->detach($nurseRole);
        }
        
        // Delete the user
        $nurse->delete();
        
        return redirect()->route('admin.nurses.index')
            ->with('success', 'Nurse deleted successfully.');
    }
} 