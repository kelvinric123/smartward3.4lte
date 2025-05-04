<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class VitalSignController extends Controller
{
    /**
     * Display a listing of vital signs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Filter by patient if specified
        if ($request->has('patient_id')) {
            $query = VitalSign::with(['patient', 'recorder']);
            $query->where('patient_id', $request->patient_id);
            $patient = Patient::findOrFail($request->patient_id);
            
            return view('admin.vital_signs.index', [
                'vitalSigns' => $query->latest('recorded_at')->paginate(15),
                'patient' => $patient
            ]);
        }
        
        // Show list of patients with vital signs
        $patientsQuery = Patient::select('patients.*')
            ->join('vital_signs', 'patients.id', '=', 'vital_signs.patient_id')
            ->groupBy('patients.id')
            ->withCount(['vitalSigns'])
            ->with('latestVitalSigns');
            
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $patientsQuery->where(function($query) use ($search) {
                $query->where('patients.name', 'like', "%{$search}%")
                    ->orWhere('patients.mrn', 'like', "%{$search}%")
                    ->orWhere('patients.identity_number', 'like', "%{$search}%");
            });
        }
            
        $patients = $patientsQuery->orderBy('name')->paginate(15);
            
        return view('admin.vital_signs.index', compact('patients'));
    }

    /**
     * Show the form for creating a new vital sign.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $patients = Patient::all();
        $patientId = $request->input('patient_id');
        $patient = null;
        
        if ($patientId) {
            $patient = Patient::findOrFail($patientId);
        }
        
        return view('admin.vital_signs.create', compact('patients', 'patient'));
    }

    /**
     * Store a newly created vital sign in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'recorded_at' => 'required|date',
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|numeric|between:0,300',
            'respiratory_rate' => 'nullable|numeric|between:0,100',
            'systolic_bp' => 'nullable|numeric|between:0,300',
            'diastolic_bp' => 'nullable|numeric|between:0,200',
            'oxygen_saturation' => 'nullable|numeric|between:0,100',
            'consciousness' => 'nullable|in:A,V,P,U,Alert,Verbal,Pain,Unresponsive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Set recorder to current user
        $data['recorded_by'] = Auth::id();
        
        // Calculate EWS scores
        $ewsResults = VitalSign::calculateEWS([
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'systolic_bp' => $request->systolic_bp,
            'oxygen_saturation' => $request->oxygen_saturation,
            'consciousness' => $request->consciousness,
        ]);
        
        // Merge EWS scores
        $data = array_merge($data, $ewsResults['scores']);
        $data['total_ews'] = $ewsResults['total_ews'];
        
        // Create the vital sign record
        $vitalSign = VitalSign::create($data);
        
        Session::flash('success', 'Vital signs recorded successfully!');
        
        if ($request->has('redirect') && $request->redirect === 'patient') {
            return redirect()->route('admin.patients.show', $request->patient_id)
                ->with('success', 'Vital signs recorded successfully!');
        }
        
        return redirect()->route('admin.vital-signs.index', ['patient_id' => $request->patient_id])
            ->with('success', 'Vital signs recorded successfully!');
    }

    /**
     * Display the specified vital sign.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vitalSign = VitalSign::with(['patient', 'recorder'])->findOrFail($id);
        return view('admin.vital_signs.show', compact('vitalSign'));
    }

    /**
     * Show the form for editing the specified vital sign.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vitalSign = VitalSign::findOrFail($id);
        $patients = Patient::all();
        return view('admin.vital_signs.edit', compact('vitalSign', 'patients'));
    }

    /**
     * Update the specified vital sign in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $vitalSign = VitalSign::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'recorded_at' => 'required|date',
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|numeric|between:0,300',
            'respiratory_rate' => 'nullable|numeric|between:0,100',
            'systolic_bp' => 'nullable|numeric|between:0,300',
            'diastolic_bp' => 'nullable|numeric|between:0,200',
            'oxygen_saturation' => 'nullable|numeric|between:0,100',
            'consciousness' => 'nullable|in:A,V,P,U,Alert,Verbal,Pain,Unresponsive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Calculate EWS scores
        $ewsResults = VitalSign::calculateEWS([
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'systolic_bp' => $request->systolic_bp,
            'oxygen_saturation' => $request->oxygen_saturation,
            'consciousness' => $request->consciousness,
        ]);
        
        // Merge EWS scores
        $data = array_merge($data, $ewsResults['scores']);
        $data['total_ews'] = $ewsResults['total_ews'];
        
        // Update the record
        $vitalSign->update($data);
        
        Session::flash('success', 'Vital signs updated successfully!');
        
        if ($request->has('redirect') && $request->redirect === 'patient') {
            return redirect()->route('admin.patients.show', $request->patient_id)
                ->with('success', 'Vital signs updated successfully!');
        }
        
        return redirect()->route('admin.vital-signs.index', ['patient_id' => $request->patient_id])
            ->with('success', 'Vital signs updated successfully!');
    }

    /**
     * Remove the specified vital sign from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vitalSign = VitalSign::findOrFail($id);
        $patientId = $vitalSign->patient_id;
        $vitalSign->delete();
        
        Session::flash('success', 'Vital signs deleted successfully!');
        return redirect()->route('admin.vital-signs.index', ['patient_id' => $patientId]);
    }

    /**
     * Display a trend chart for a specific patient
     */
    public function trend($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $vitalSigns = $patient->vitalSigns()->orderBy('recorded_at', 'asc')->get();
        
        return view('admin.vital_signs.trend', compact('patient', 'vitalSigns'));
    }
    
    /**
     * Display a flipbox style trend chart for a specific patient
     */
    public function flipboxTrend($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $vitalSigns = $patient->vitalSigns()->orderBy('recorded_at', 'asc')->get();
        
        return view('admin.vital_signs.flipbox_trend', compact('patient', 'vitalSigns'));
    }
} 