<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\PatientAlert;
use Illuminate\Http\Request;

class PacController extends Controller
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
     * Display the PAC dashboard with ward and bed status overview.
     */
    public function dashboard(Request $request)
    {
        // Get all active hospitals for filtering
        $hospitals = Hospital::where('is_active', true)->with('wards.beds')->get();
        
        // Get filter parameters
        $selectedHospitalId = $request->get('hospital_id');
        $selectedWardId = $request->get('ward_id');
        $statusFilter = $request->get('status_filter', 'all');
        
        // Build the query for wards
        $wardsQuery = Ward::with(['hospital', 'specialty', 'beds.patient.latestVitalSigns', 'beds.consultant', 'beds.nurse'])
            ->where('is_active', true);
        
        // Apply hospital filter
        if ($selectedHospitalId) {
            $wardsQuery->where('hospital_id', $selectedHospitalId);
        }
        
        // Apply ward filter
        if ($selectedWardId) {
            $wardsQuery->where('id', $selectedWardId);
        }
        
        $wards = $wardsQuery->get();
        
        // Calculate summary statistics
        $totalBeds = 0;
        $availableBeds = 0;
        $occupiedBeds = 0;
        $cleaningNeededBeds = 0;
        $maintenanceBeds = 0;
        $totalAlerts = 0;
        
        // Ward statistics for PAC overview
        $wardStats = collect();
        
        foreach ($wards as $ward) {
            $wardAvailable = 0;
            $wardOccupied = 0;
            $wardCleaning = 0;
            $wardMaintenance = 0;
            $wardAlerts = 0;
            
            foreach ($ward->beds as $bed) {
                $totalBeds++;
                
                switch ($bed->status) {
                    case 'available':
                        $availableBeds++;
                        $wardAvailable++;
                        break;
                    case 'occupied':
                        $occupiedBeds++;
                        $wardOccupied++;
                        break;
                    case 'cleaning_needed':
                        $cleaningNeededBeds++;
                        $wardCleaning++;
                        break;
                    case 'maintenance':
                        $maintenanceBeds++;
                        $wardMaintenance++;
                        break;
                }
            }
            
            // Get alerts for this ward
            $wardAlerts = PatientAlert::where('ward_id', $ward->id)
                ->whereIn('status', ['new', 'seen'])
                ->count();
            $totalAlerts += $wardAlerts;
            
            // Apply status filter to beds if needed
            $filteredBeds = $ward->beds;
            if ($statusFilter !== 'all') {
                $filteredBeds = $ward->beds->filter(function ($bed) use ($statusFilter) {
                    return $bed->status === $statusFilter;
                });
            }
            
            $wardStats->push([
                'ward' => $ward,
                'available' => $wardAvailable,
                'occupied' => $wardOccupied,
                'cleaning_needed' => $wardCleaning,
                'maintenance' => $wardMaintenance,
                'total' => $ward->beds->count(),
                'alerts' => $wardAlerts,
                'occupancy_rate' => $ward->beds->count() > 0 ? round(($wardOccupied / $ward->beds->count()) * 100) : 0,
                'filtered_beds' => $filteredBeds
            ]);
        }
        
        // Calculate overall occupancy rate
        $overallOccupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0;
        
        // Get recent admissions (last 24 hours)
        $recentAdmissions = PatientAdmission::with(['patient', 'bed.ward', 'consultant'])
            ->where('admission_date', '>=', now()->subDay())
            ->orderBy('admission_date', 'desc')
            ->take(10)
            ->get();
        
        // Get patients waiting for admission (patients without current bed assignment)
        $waitingPatients = Patient::whereDoesntHave('bed')
        ->orderBy('created_at', 'asc')
        ->take(10)
        ->get();
        
        return view('admin.pac.dashboard', compact(
            'hospitals',
            'wards',
            'wardStats',
            'totalBeds',
            'availableBeds',
            'occupiedBeds',
            'cleaningNeededBeds',
            'maintenanceBeds',
            'totalAlerts',
            'overallOccupancyRate',
            'recentAdmissions',
            'waitingPatients',
            'selectedHospitalId',
            'selectedWardId',
            'statusFilter'
        ));
    }
    
    /**
     * Get available beds for admission (AJAX endpoint)
     */
    public function getAvailableBeds(Request $request)
    {
        $hospitalId = $request->get('hospital_id');
        $specialtyId = $request->get('specialty_id');
        
        $bedsQuery = Bed::with(['ward.hospital', 'ward.specialty'])
            ->where('status', 'available');
        
        if ($hospitalId) {
            $bedsQuery->whereHas('ward', function($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            });
        }
        
        if ($specialtyId) {
            $bedsQuery->whereHas('ward', function($query) use ($specialtyId) {
                $query->where('specialty_id', $specialtyId);
            });
        }
        
        $beds = $bedsQuery->get();
        
        return response()->json($beds);
    }
    
    /**
     * Show form to admit a patient to a ward (with bed selection)
     */
    public function admitPatient($wardId)
    {
        // Find the ward
        $ward = Ward::with(['hospital', 'specialty'])->findOrFail($wardId);
        
        // Get all available beds in this ward
        $availableBeds = Bed::where('ward_id', $ward->id)
            ->where('status', 'available')
            ->orderBy('bed_number')
            ->get();
        
        // Use the first available bed as default for the form
        $defaultBed = $availableBeds->first();
        
        if (!$defaultBed) {
            return redirect()->route('admin.pac.dashboard')
                ->with('error', 'No available beds in ' . $ward->name);
        }
        
        // Load relationships needed for the form
        $consultants = \App\Models\Consultant::where('is_active', true)->get();
        $patients = \App\Models\Patient::whereDoesntHave('bed')->get();
        $nurses = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->get();
        
        return view('admin.pac.admit_patient', compact('ward', 'defaultBed', 'availableBeds', 'consultants', 'patients', 'nurses'));
    }
    
    /**
     * Process the admission form submission from PAC
     */
    public function storeAdmission(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'bed_id' => 'required|exists:beds,id',
            'ward_id' => 'required|exists:wards,id',
            'consultant_id' => 'nullable|exists:consultants,id',
            'nurse_id' => 'nullable|exists:users,id',
            'admission_notes' => 'nullable|string',
            'admission_date' => 'nullable|date',
        ]);
        
        // Find the patient
        $patient = \App\Models\Patient::findOrFail($validated['patient_id']);
        
        // Check if patient is already admitted
        if ($patient->is_admitted) {
            // Get the current bed and ward
            $currentBed = $patient->bed;
            $currentWard = $currentBed ? $currentBed->ward : null;
            
            $errorMessage = 'This patient is already admitted';
            if ($currentBed && $currentWard) {
                $errorMessage .= " to {$currentWard->name} (Bed {$currentBed->bed_number})";
            }
            $errorMessage .= '. Please select another patient.';
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['patient_id' => $errorMessage]);
        }
        
        // Find the bed and verify it belongs to the specified ward
        $bed = Bed::where('id', $validated['bed_id'])
            ->where('ward_id', $validated['ward_id'])
            ->first();
            
        if (!$bed) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['bed_id' => 'Selected bed does not belong to the specified ward.']);
        }
        
        // Check if bed is available
        if ($bed->status !== 'available') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['bed_id' => 'This bed is not available for admission.']);
        }
        
        // Update the bed with admission information
        $bed->update([
            'status' => 'occupied',
            'patient_id' => $validated['patient_id'],
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
            'notes' => $validated['admission_notes'],
        ]);
        
        // Get the admission date, ensuring we use KL timezone
        $admissionDate = $request->filled('admission_date') 
            ? \Carbon\Carbon::parse($request->admission_date)->setTimezone('Asia/Kuala_Lumpur')
            : now();
        
        // Create admission record
        \App\Models\PatientAdmission::create([
            'patient_id' => $validated['patient_id'],
            'ward_id' => $bed->ward_id,
            'bed_id' => $bed->id,
            'bed_number' => $bed->bed_number,
            'admission_date' => $admissionDate,
            'consultant_id' => $validated['consultant_id'],
            'nurse_id' => $validated['nurse_id'],
            'admitted_by' => auth()->id(),
            'admission_notes' => $validated['admission_notes'],
            'is_active' => true,
        ]);
        
        return redirect()->route('admin.pac.dashboard')
            ->with('success', 'Patient admitted successfully to ' . $bed->ward->name . ' (Bed ' . $bed->bed_number . ')');
    }
    
    /**
     * Get consultants by specialty (AJAX endpoint)
     */
    public function getConsultantsBySpecialty(Request $request)
    {
        $specialtyId = $request->input('specialty_id');
        
        if (!$specialtyId) {
            return response()->json(['error' => 'Specialty ID is required'], 400);
        }
        
        $consultants = \App\Models\Consultant::where('specialty_id', $specialtyId)
            ->where('is_active', true)
            ->get(['id', 'name', 'specialty_id']);
            
        return response()->json($consultants);
    }
} 