<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientAlert;
use App\Models\VitalSign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Check if table view is specifically requested, otherwise default to card view
        if ($request->has('view') && $request->view === 'table') {
            // Show list of patients with vital signs (table view)
            $patientsQuery = Patient::select('patients.*')
                ->join('vital_signs', 'patients.id', '=', 'vital_signs.patient_id')
                ->groupBy('patients.id', 'patients.name', 'patients.mrn', 'patients.identity_number', 'patients.identity_type', 'patients.age', 'patients.gender', 'patients.email', 'patients.phone', 'patients.address', 'patients.created_at', 'patients.updated_at')
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
                
            $patients = $patientsQuery->orderBy('patients.name')->paginate(15);
                
            return view('admin.vital_signs.index', compact('patients'));
        }
        
        // Default to card view
        if (!$request->has('view') || $request->view === 'card') {
            // Show card view of patients with their latest vital signs
            $patientsQuery = Patient::select('patients.*')
                ->join('vital_signs', 'patients.id', '=', 'vital_signs.patient_id')
                ->groupBy('patients.id', 'patients.name', 'patients.mrn', 'patients.identity_number', 'patients.identity_type', 'patients.age', 'patients.gender', 'patients.email', 'patients.phone', 'patients.address', 'patients.created_at', 'patients.updated_at')
                ->withCount(['vitalSigns'])
                ->with(['latestVitalSigns' => function($query) {
                    $query->with('recorder');
                }]);
                
            // Handle search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $patientsQuery->where(function($query) use ($search) {
                    $query->where('patients.name', 'like', "%{$search}%")
                        ->orWhere('patients.mrn', 'like', "%{$search}%")
                        ->orWhere('patients.identity_number', 'like', "%{$search}%");
                });
            }
                
            $patients = $patientsQuery->orderBy('patients.name')->paginate(25);
                
            return view('admin.vital_signs.card_index', compact('patients'));
        }
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
            'gcs_total' => 'nullable|integer|between:3,15',
            'gcs_eye' => 'nullable|integer|between:1,4',
            'gcs_verbal' => 'nullable|integer|between:1,5',
            'gcs_motor' => 'nullable|integer|between:1,6',
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
        
        // Check for EWS-based alert triggers
        $this->checkEWSAlerts($vitalSign);
        
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
            'gcs_total' => 'nullable|integer|between:3,15',
            'gcs_eye' => 'nullable|integer|between:1,4',
            'gcs_verbal' => 'nullable|integer|between:1,5',
            'gcs_motor' => 'nullable|integer|between:1,6',
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
        
        // Check for EWS-based alert triggers after update
        $this->checkEWSAlerts($vitalSign->fresh());
        
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
        $vitalSigns = $patient->vitalSigns()
                             ->orderBy('recorded_at', 'asc')
                             ->get();
                             
        foreach ($vitalSigns as $vitalSign) {
            $vitalSign->formatted_recorded_at = $vitalSign->recorded_at->format('M j, Y H:i');
        }
        
        return view('admin.vital_signs.flipbox_trend', compact('patient', 'vitalSigns'));
    }

    /**
     * Display the specified vital sign trend in iframe.
     *
     * @param  int  $patientId
     * @return \Illuminate\Http\Response
     */
    public function iframeTrend($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $query = $patient->vitalSigns()->orderBy('recorded_at', 'asc');
        
        // Handle date filtering
        if (request()->has('start') && request()->has('end')) {
            $startDate = request('start');
            $endDate = request('end') . ' 23:59:59';
            $query->whereBetween('recorded_at', [$startDate, $endDate]);
        }
        
        $vitalSigns = $query->get();
        
        foreach ($vitalSigns as $vitalSign) {
            $vitalSign->formatted_recorded_at = $vitalSign->recorded_at->format('M j, Y H:i');
        }
        
        return view('admin.vital_signs.iframe_trend', compact('patient', 'vitalSigns'));
    }
    /**
     * Get vital signs data for chart (AJAX endpoint)
     */
    public function trendData($patientId)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            
            $vitalSigns = VitalSign::where('patient_id', $patientId)
                ->orderBy('recorded_at', 'desc')
                ->take(30) // Get last 30 records for better chart performance
                ->get()
                ->reverse() // Reverse to get chronological order
                ->values(); // Re-index the collection

            return response()->json([
                'success' => true,
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'mrn' => $patient->mrn
                ],
                'vitals' => $vitalSigns->map(function($vital) {
                    return [
                        'id' => $vital->id,
                        'recorded_at' => $vital->recorded_at->toISOString(),
                        'temperature' => $vital->temperature,
                        'heart_rate' => $vital->heart_rate,
                        'systolic_pressure' => $vital->systolic_bp,
                        'diastolic_pressure' => $vital->diastolic_bp,
                        'respiratory_rate' => $vital->respiratory_rate,
                        'oxygen_saturation' => $vital->oxygen_saturation,
                        'pain_score' => $vital->pain_score,
                        'blood_glucose' => $vital->blood_glucose,
                        'total_ews' => $vital->total_ews
                    ];
                }),
                'total_records' => VitalSign::where('patient_id', $patientId)->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching vital signs trend data', [
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading vital signs data',
                'vitals' => []
            ], 500);
        }
    }

    /**
     * Check for vital signs updates (AJAX endpoint)
     */
    public function checkUpdates(Request $request)
    {
        try {
            // Get patients with vital signs for comparison
            $patientsQuery = Patient::select('patients.*')
                ->join('vital_signs', 'patients.id', '=', 'vital_signs.patient_id')
                ->groupBy('patients.id', 'patients.name', 'patients.mrn', 'patients.identity_number', 'patients.identity_type', 'patients.age', 'patients.gender', 'patients.email', 'patients.phone', 'patients.address', 'patients.created_at', 'patients.updated_at')
                ->withCount(['vitalSigns'])
                ->with(['latestVitalSigns' => function($query) {
                    $query->with('recorder');
                }]);
                
            // Handle search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $patientsQuery->where(function($query) use ($search) {
                    $query->where('patients.name', 'like', "%{$search}%")
                        ->orWhere('patients.mrn', 'like', "%{$search}%")
                        ->orWhere('patients.identity_number', 'like', "%{$search}%");
                });
            }
                
            $patients = $patientsQuery->orderBy('patients.name')->get();
            
            // Format the response data
            $responseData = $patients->map(function($patient) {
                $latestVital = $patient->latestVitalSigns;
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'mrn' => $patient->mrn,
                    'latest_vital_signs' => $latestVital ? [
                        'id' => $latestVital->id,
                        'recorded_at' => $latestVital->recorded_at->toISOString(),
                        'temperature' => $latestVital->temperature,
                        'heart_rate' => $latestVital->heart_rate,
                        'respiratory_rate' => $latestVital->respiratory_rate,
                        'systolic_bp' => $latestVital->systolic_bp,
                        'diastolic_bp' => $latestVital->diastolic_bp,
                        'oxygen_saturation' => $latestVital->oxygen_saturation,
                        'consciousness' => $latestVital->consciousness,
                        'total_ews' => $latestVital->total_ews,
                        'recorder' => $latestVital->recorder ? [
                            'id' => $latestVital->recorder->id,
                            'name' => $latestVital->recorder->name
                        ] : null
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'patients' => $responseData,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking vital signs updates', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking for updates',
                'patients' => []
            ], 500);
        }
    }

    /**
     * Check EWS levels and create alerts for yellow/red warnings
     * 
     * @param VitalSign $vitalSign
     * @return void
     */
    private function checkEWSAlerts(VitalSign $vitalSign)
    {
        try {
            // Only trigger alerts for EWS >= 5 (High Risk/Critical)
            if ($vitalSign->total_ews < 5) {
                return;
            }

            // Get patient and their current bed information
            $patient = $vitalSign->patient;
            if (!$patient) {
                return;
            }

            // Find the patient's current bed and ward
            $currentBed = $patient->bed; // Get the bed this patient is currently assigned to
            if (!$currentBed || !$currentBed->ward_id) {
                // If patient is not in a bed, try to find through active admission
                $activeAdmission = $patient->activeAdmission;
                if (!$activeAdmission || !$activeAdmission->bed_id) {
                    return; // No bed/ward information, can't create alert
                }
                $currentBed = \App\Models\Bed::find($activeAdmission->bed_id);
                if (!$currentBed) {
                    return;
                }
            }

            // Determine alert message and urgency based on EWS level
            $isUrgent = false;
            $alertType = 'ews_warning';
            $message = '';

            if ($vitalSign->total_ews >= 7) {
                // Critical - Red
                $isUrgent = true;
                $alertType = 'ews_critical';
                $message = "CRITICAL: Patient has EWS score of {$vitalSign->total_ews} - Immediate medical attention required!";
            } elseif ($vitalSign->total_ews >= 5) {
                // High Risk - Yellow  
                $isUrgent = true;
                $alertType = 'ews_high_risk';
                $message = "HIGH RISK: Patient has EWS score of {$vitalSign->total_ews} - Medical review required.";
            }

            // Check if there's already a recent EWS alert for this patient to avoid spam
            $recentAlert = PatientAlert::where('patient_id', $patient->id)
                ->where('ward_id', $currentBed->ward_id)
                ->where('alert_type', $alertType)
                ->where('status', '!=', 'resolved')
                ->where('created_at', '>=', Carbon::now()->subMinutes(30)) // Don't spam within 30 minutes
                ->first();

            if ($recentAlert) {
                // Update the existing alert with new EWS score instead of creating a new one
                $recentAlert->update([
                    'message' => $message,
                    'is_urgent' => $isUrgent,
                    'updated_at' => now()
                ]);
                return;
            }

            // Create new EWS-based alert
            PatientAlert::create([
                'patient_id' => $patient->id,
                'ward_id' => $currentBed->ward_id,
                'bed_id' => $currentBed->id,
                'alert_type' => $alertType,
                'message' => $message,
                'status' => 'new',
                'is_urgent' => $isUrgent,
            ]);

            \Log::info('EWS Alert Created', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->name,
                'ews_score' => $vitalSign->total_ews,
                'alert_type' => $alertType,
                'bed_number' => $currentBed->bed_number,
                'ward_id' => $currentBed->ward_id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating EWS alert', [
                'vital_sign_id' => $vitalSign->id,
                'patient_id' => $vitalSign->patient_id,
                'ews_score' => $vitalSign->total_ews,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 