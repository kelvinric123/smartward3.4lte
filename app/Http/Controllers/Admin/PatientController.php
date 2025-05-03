<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the patients.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Patient::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('mrn', 'like', "%{$searchTerm}%")
                  ->orWhere('identity_number', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by identity type
        if ($request->filled('identity_type')) {
            $query->where('identity_type', $request->identity_type);
        }
        
        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        // Filter by age range
        if ($request->filled('age_range')) {
            $ageRange = $request->age_range;
            switch ($ageRange) {
                case '0-18':
                    $query->where('age', '<', 18);
                    break;
                case '18-30':
                    $query->whereBetween('age', [18, 30]);
                    break;
                case '31-50':
                    $query->whereBetween('age', [31, 50]);
                    break;
                case '51-65':
                    $query->whereBetween('age', [51, 65]);
                    break;
                case '65+':
                    $query->where('age', '>', 65);
                    break;
            }
        }
        
        // Get paginated results
        $patients = $query->latest()->paginate(10);
        
        return view('admin.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Store a newly created patient in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mrn' => 'nullable|string|max:50',
            'identity_number' => 'required|string|max:255|unique:patients',
            'identity_type' => 'required|in:ic,passport',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:male,female,other',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Auto calculate age and gender from IC if identity type is IC
        if ($request->identity_type == 'ic') {
            // Calculate age from IC
            if (!$request->filled('age')) {
                $data['age'] = Patient::calculateAgeFromIC($request->identity_number);
            }
            
            // Determine gender from IC
            if (!$request->filled('gender')) {
                $data['gender'] = Patient::determineGenderFromIC($request->identity_number);
            }
        }
        
        // Generate MRN if not provided
        if (!$request->filled('mrn')) {
            // Format: MRN-YYYYMMDD-XXXX where XXXX is a random number
            $data['mrn'] = 'MRN-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        Patient::create($data);
        
        Session::flash('success', 'Patient created successfully!');
        return redirect()->route('admin.patients.index');
    }

    /**
     * Display the specified patient.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mrn' => 'nullable|string|max:50',
            'identity_number' => 'required|string|max:255|unique:patients,identity_number,' . $id,
            'identity_type' => 'required|in:ic,passport',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:male,female,other',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Auto calculate age and gender from IC if identity type is IC
        if ($request->identity_type == 'ic') {
            // Calculate age from IC
            if (!$request->filled('age')) {
                $data['age'] = Patient::calculateAgeFromIC($request->identity_number);
            }
            
            // Determine gender from IC
            if (!$request->filled('gender')) {
                $data['gender'] = Patient::determineGenderFromIC($request->identity_number);
            }
        }
        
        // Generate MRN if not provided and not already set
        if (!$request->filled('mrn') && empty($patient->mrn)) {
            // Format: MRN-YYYYMMDD-XXXX where XXXX is a random number
            $data['mrn'] = 'MRN-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        $patient->update($data);
        
        Session::flash('success', 'Patient updated successfully!');
        return redirect()->route('admin.patients.index');
    }

    /**
     * Remove the specified patient from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        
        Session::flash('success', 'Patient deleted successfully!');
        return redirect()->route('admin.patients.index');
    }
} 