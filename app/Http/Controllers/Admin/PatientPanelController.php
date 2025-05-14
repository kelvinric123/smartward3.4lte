<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientPanelController extends Controller
{
    /**
     * Display the patient panel for a specific patient
     *
     * @param Patient $patient
     * @return \Illuminate\View\View
     */
    public function showPanel(Patient $patient)
    {
        // Get the active admission
        $activeAdmission = $patient->activeAdmission;
        
        // Check if the patient has an active admission
        if (!$activeAdmission) {
            return back()->with('error', 'Patient does not have an active admission.');
        }
        
        // Get the bed and ward
        $bed = $activeAdmission->bed;
        $ward = $activeAdmission->ward;
        
        return view('admin.patients.panel', compact('patient', 'activeAdmission', 'bed', 'ward'));
    }
} 