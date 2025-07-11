@extends('adminlte::page')

@section('title', 'Integration - Patient Admission')

@section('content_header')
    <h1>
        <i class="fas fa-exchange-alt"></i> Integration - Patient Admission
        <small class="text-muted">Complete admission form with HL7 mapping</small>
    </h1>
@stop

@section('content')
<!-- HL7 Profile Management Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i> HL7 Mapping Profile Management
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="profile_selector">Select HL7 Profile:</label>
                            <select class="form-control" id="profile_selector">
                                <option value="">Choose a profile...</option>
                                @foreach($hl7Profiles as $profile)
                                    <option value="{{ $profile->id }}" 
                                            {{ $activeProfile && $activeProfile->id == $profile->id ? 'selected' : '' }}
                                            data-description="{{ $profile->description }}">
                                        {{ $profile->name }} 
                                        {{ $profile->is_active ? '(Active)' : '' }}
                                        {{ $profile->is_default ? '(Default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="loadProfileBtn" disabled>
                                <i class="fas fa-download"></i> Load Profile
                            </button>
                            <button type="button" class="btn btn-success" id="setActiveBtn" disabled>
                                <i class="fas fa-check-circle"></i> Set Active
                            </button>
                            <button type="button" class="btn btn-warning" id="saveProfileBtn">
                                <i class="fas fa-save"></i> Save Current as New
                            </button>
                            <button type="button" class="btn btn-info" id="updateProfileBtn" disabled>
                                <i class="fas fa-edit"></i> Update Selected
                            </button>
                            <button type="button" class="btn btn-danger" id="deleteProfileBtn" disabled>
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-12">
                        <div id="profileDescription" class="alert alert-info" style="display: none;">
                            <strong>Profile Description:</strong> <span id="profileDescriptionText"></span>
                        </div>
                    </div>
                </div>
                
                @if($hl7Profiles->isEmpty())
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <strong>No HL7 profiles found.</strong> 
                                <button type="button" class="btn btn-sm btn-primary ml-2" id="createDefaultProfileBtn">
                                    <i class="fas fa-plus"></i> Create Default Profile
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus"></i> Patient Admission Form
                </h3>
            </div>
            
            <form action="{{ route('admin.integration.admission.store') }}" method="POST" id="admissionForm">
                @csrf
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Patient Selection Section -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-primary"><i class="fas fa-user"></i> Patient Selection</h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                <select class="form-control @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }} (MRN: {{ $patient->mrn }}) - {{ $patient->identity_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hl7_patient_id">HL7 Mapping Code</label>
                                <input type="text" class="form-control" id="hl7_patient_id" name="hl7_mappings[patient_id]" 
                                       placeholder="e.g., PID.3" value="{{ old('hl7_mappings.patient_id') }}">
                                <small class="text-muted">Patient identifier mapping</small>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Details Display (Auto-populated) -->
                    <div id="patientDetailsSection" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-info"><i class="fas fa-info-circle"></i> Patient Details</h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Patient Name -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Patient Name</label>
                                    <input type="text" class="form-control" id="display_patient_name" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code</label>
                                    <input type="text" class="form-control" name="hl7_mappings[patient_name]" 
                                           placeholder="PID.5" value="{{ old('hl7_mappings.patient_name') }}">
                                </div>
                            </div>
                            
                            <!-- MRN -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Medical Record Number (MRN)</label>
                                    <input type="text" class="form-control" id="display_mrn" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code</label>
                                    <input type="text" class="form-control" name="hl7_mappings[mrn]" 
                                           placeholder="PID.3.1" value="{{ old('hl7_mappings.mrn') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Identity Number -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Identity Number (IC/Passport)</label>
                                    <input type="text" class="form-control" id="display_identity_number" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code</label>
                                    <input type="text" class="form-control" name="hl7_mappings[identity_number]" 
                                           placeholder="PID.3.4" value="{{ old('hl7_mappings.identity_number') }}">
                                </div>
                            </div>
                            
                            <!-- Age -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Age</label>
                                    <input type="text" class="form-control" id="display_age" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code</label>
                                    <input type="text" class="form-control" name="hl7_mappings[age]" 
                                           placeholder="PID.7" value="{{ old('hl7_mappings.age') }}">
                                </div>
                            </div>
                            
                            <!-- Gender -->
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <input type="text" class="form-control" id="display_gender" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code (Gender)</label>
                                    <input type="text" class="form-control" name="hl7_mappings[gender]" 
                                           placeholder="PID.8" value="{{ old('hl7_mappings.gender') }}">
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" id="display_email" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code</label>
                                    <input type="text" class="form-control" name="hl7_mappings[email]" 
                                           placeholder="PID.13.4" value="{{ old('hl7_mappings.email') }}">
                                </div>
                            </div>
                            
                            <!-- Phone -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" id="display_phone" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code (Phone)</label>
                                    <input type="text" class="form-control" name="hl7_mappings[phone]" 
                                           placeholder="PID.13.1" value="{{ old('hl7_mappings.phone') }}">
                                </div>
                            </div>
                            
                            <!-- Address -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" id="display_address" rows="2" readonly></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code</label>
                                    <input type="text" class="form-control" name="hl7_mappings[address]" 
                                           placeholder="PID.11" value="{{ old('hl7_mappings.address') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Allergies Section -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Known Allergies</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div id="allergiesDisplay">
                                    <p class="text-muted">Select a patient to view allergy information</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>HL7 Code (Allergies)</label>
                                    <input type="text" class="form-control" name="hl7_mappings[allergies]" 
                                           placeholder="AL1.3" value="{{ old('hl7_mappings.allergies') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ward and Bed Selection -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="text-primary"><i class="fas fa-bed"></i> Ward and Bed Assignment</h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="ward_id">Ward <span class="text-danger">*</span></label>
                                <select class="form-control @error('ward_id') is-invalid @enderror" id="ward_id" name="ward_id" required>
                                    <option value="">Select Ward</option>
                                    @foreach($wards as $ward)
                                        <option value="{{ $ward->id }}" {{ old('ward_id') == $ward->id ? 'selected' : '' }}>
                                            {{ $ward->name }} ({{ $ward->beds->where('status', 'available')->count() }} available beds)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[ward_id]" 
                                       placeholder="PV1.3.1" value="{{ old('hl7_mappings.ward_id') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bed_id">Bed <span class="text-danger">*</span></label>
                                <select class="form-control @error('bed_id') is-invalid @enderror" id="bed_id" name="bed_id" required disabled>
                                    <option value="">Select Ward First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[bed_id]" 
                                       placeholder="PV1.3.3" value="{{ old('hl7_mappings.bed_id') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Assignment -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="text-primary"><i class="fas fa-user-md"></i> Clinical Assignment</h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="consultant_id">Attending Consultant</label>
                                <select class="form-control @error('consultant_id') is-invalid @enderror" id="consultant_id" name="consultant_id">
                                    <option value="">Select Consultant</option>
                                    @foreach($consultants as $consultant)
                                        <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                            {{ $consultant->name }} - {{ $consultant->specialty->name ?? 'No Specialty' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[consultant_id]" 
                                       placeholder="PV1.7" value="{{ old('hl7_mappings.consultant_id') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nurse_id">Assigned Nurse</label>
                                <select class="form-control @error('nurse_id') is-invalid @enderror" id="nurse_id" name="nurse_id">
                                    <option value="">Select Nurse</option>
                                    @foreach($nurses as $nurse)
                                        <option value="{{ $nurse->id }}" {{ old('nurse_id') == $nurse->id ? 'selected' : '' }}>
                                            {{ $nurse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[nurse_id]" 
                                       placeholder="PV1.8" value="{{ old('hl7_mappings.nurse_id') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Admission Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="text-primary"><i class="fas fa-calendar-plus"></i> Admission Information</h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="admission_date">Admission Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('admission_date') is-invalid @enderror" 
                                       id="admission_date" name="admission_date" value="{{ old('admission_date', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[admission_date]" 
                                       placeholder="PV1.44" value="{{ old('hl7_mappings.admission_date') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patient_class">Patient Class</label>
                                <select class="form-control @error('patient_class') is-invalid @enderror" id="patient_class" name="patient_class">
                                    @foreach($patientClasses as $code => $description)
                                        <option value="{{ $code }}" {{ old('patient_class', 'I') == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[patient_class]" 
                                       placeholder="PV1.2" value="{{ old('hl7_mappings.patient_class') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="text-primary"><i class="fas fa-notes-medical"></i> Clinical Information</h4>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="diet_type">Diet Type</label>
                                <select class="form-control @error('diet_type') is-invalid @enderror" id="diet_type" name="diet_type">
                                    <option value="">Select Diet Type</option>
                                    @foreach($dietTypes as $code => $description)
                                        <option value="{{ $code }}" {{ old('diet_type') == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[diet_type]" 
                                       placeholder="ORC.7" value="{{ old('hl7_mappings.diet_type') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fall_risk_alert">Fall Risk Assessment</label>
                                <select class="form-control @error('fall_risk_alert') is-invalid @enderror" id="fall_risk_alert" name="fall_risk_alert">
                                    @foreach($fallRiskOptions as $code => $description)
                                        <option value="{{ $code }}" {{ old('fall_risk_alert', 'NO') == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[fall_risk_alert]" 
                                       placeholder="ZAT.1" value="{{ old('hl7_mappings.fall_risk_alert') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="isolation_precautions">Isolation Precautions</label>
                                <select class="form-control @error('isolation_precautions') is-invalid @enderror" id="isolation_precautions" name="isolation_precautions">
                                    @foreach($isolationOptions as $code => $description)
                                        <option value="{{ $code }}" {{ old('isolation_precautions', 'NONE') == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[isolation_precautions]" 
                                       placeholder="ZIT.1" value="{{ old('hl7_mappings.isolation_precautions') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="expected_length_of_stay">Expected Length of Stay (days)</label>
                                <input type="number" class="form-control @error('expected_length_of_stay') is-invalid @enderror" 
                                       id="expected_length_of_stay" name="expected_length_of_stay" 
                                       min="1" max="365" value="{{ old('expected_length_of_stay') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[expected_length_of_stay]" 
                                       placeholder="PV1.48" value="{{ old('hl7_mappings.expected_length_of_stay') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="expected_discharge_date">Expected Discharge Date</label>
                                <input type="datetime-local" class="form-control @error('expected_discharge_date') is-invalid @enderror" 
                                       id="expected_discharge_date" name="expected_discharge_date" value="{{ old('expected_discharge_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[expected_discharge_date]" 
                                       placeholder="PV1.45" value="{{ old('hl7_mappings.expected_discharge_date') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="clinical_alerts">Clinical Alerts & Special Instructions</label>
                                <textarea class="form-control @error('clinical_alerts') is-invalid @enderror" 
                                          id="clinical_alerts" name="clinical_alerts" rows="3" 
                                          placeholder="Enter any additional clinical alerts, special instructions, or important notes...">{{ old('clinical_alerts') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code (Clinical Alerts)</label>
                                <input type="text" class="form-control" name="hl7_mappings[clinical_alerts]" 
                                       placeholder="NTE.3" value="{{ old('hl7_mappings.clinical_alerts') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="admission_notes">Admission Notes</label>
                                <textarea class="form-control @error('admission_notes') is-invalid @enderror" 
                                          id="admission_notes" name="admission_notes" rows="3" 
                                          placeholder="Enter admission notes and diagnosis...">{{ old('admission_notes') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>HL7 Code</label>
                                <input type="text" class="form-control" name="hl7_mappings[admission_notes]" 
                                       placeholder="DG1.4" value="{{ old('hl7_mappings.admission_notes') }}">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Submit Admission
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .form-group label {
        font-weight: 600;
    }
    .card-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
    }
    .text-primary {
        color: #007bff !important;
    }
    hr {
        border-top: 2px solid #007bff;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
    }
    #allergiesDisplay .alert {
        margin-bottom: 0.5rem;
    }
    .allergy-item {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 0.25rem;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .allergy-severe {
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    .allergy-moderate {
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }
    .allergy-mild {
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    
    // HL7 Profile Management Functions
    var currentProfileId = null;
    
    // Profile selector change handler
    $('#profile_selector').change(function() {
        var profileId = $(this).val();
        var description = $(this).find('option:selected').data('description');
        
        if (profileId) {
            currentProfileId = profileId;
            $('#loadProfileBtn, #setActiveBtn, #updateProfileBtn, #deleteProfileBtn').prop('disabled', false);
            
            if (description) {
                $('#profileDescriptionText').text(description);
                $('#profileDescription').show();
            } else {
                $('#profileDescription').hide();
            }
        } else {
            currentProfileId = null;
            $('#loadProfileBtn, #setActiveBtn, #updateProfileBtn, #deleteProfileBtn').prop('disabled', true);
            $('#profileDescription').hide();
        }
    });
    
    // Load profile button handler
    $('#loadProfileBtn').click(function() {
        if (!currentProfileId) return;
        
        $.ajax({
            url: '{{ route("admin.integration.profiles.load", ":id") }}'.replace(':id', currentProfileId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    loadMappingCodes(response.mapping_codes);
                    showAlert('success', 'Profile loaded successfully!');
                }
            },
            error: function() {
                showAlert('error', 'Error loading profile.');
            }
        });
    });
    
    // Set active profile button handler
    $('#setActiveBtn').click(function() {
        if (!currentProfileId) return;
        
        $.ajax({
            url: '{{ route("admin.integration.profiles.set-active", ":id") }}'.replace(':id', currentProfileId),
            type: 'PUT',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    location.reload(); // Reload to update the UI
                }
            },
            error: function() {
                showAlert('error', 'Error setting profile as active.');
            }
        });
    });
    
    // Save profile button handler
    $('#saveProfileBtn').click(function() {
        var profileName = prompt('Enter a name for the new profile:');
        if (!profileName) return;
        
        var profileDescription = prompt('Enter a description for the profile (optional):');
        var mappingCodes = collectMappingCodes();
        
        $.ajax({
            url: '{{ route("admin.integration.profiles.save") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                profile_name: profileName,
                profile_description: profileDescription,
                hl7_mappings: mappingCodes
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    location.reload(); // Reload to update the profile list
                }
            },
            error: function(xhr) {
                var message = 'Error saving profile.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                showAlert('error', message);
            }
        });
    });
    
    // Update profile button handler
    $('#updateProfileBtn').click(function() {
        if (!currentProfileId) return;
        
        var profileName = prompt('Enter the updated name for the profile:');
        if (!profileName) return;
        
        var profileDescription = prompt('Enter the updated description for the profile (optional):');
        var mappingCodes = collectMappingCodes();
        
        $.ajax({
            url: '{{ route("admin.integration.profiles.update", ":id") }}'.replace(':id', currentProfileId),
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                profile_name: profileName,
                profile_description: profileDescription,
                hl7_mappings: mappingCodes
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    location.reload(); // Reload to update the profile list
                }
            },
            error: function(xhr) {
                var message = 'Error updating profile.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                showAlert('error', message);
            }
        });
    });
    
    // Delete profile button handler
    $('#deleteProfileBtn').click(function() {
        if (!currentProfileId) return;
        
        var profileName = $('#profile_selector option:selected').text();
        if (!confirm('Are you sure you want to delete the profile "' + profileName + '"?')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.integration.profiles.delete", ":id") }}'.replace(':id', currentProfileId),
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    location.reload(); // Reload to update the profile list
                }
            },
            error: function(xhr) {
                var message = 'Error deleting profile.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showAlert('error', message);
            }
        });
    });
    
    // Create default profile button handler
    $('#createDefaultProfileBtn').click(function() {
        $.ajax({
            url: '{{ route("admin.integration.profiles.create-default") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    location.reload(); // Reload to show the new profile
                }
            },
            error: function() {
                showAlert('error', 'Error creating default profile.');
            }
        });
    });
    
    // Helper function to collect current mapping codes from the form
    function collectMappingCodes() {
        var mappingCodes = {};
        $('input[name^="hl7_mappings["]').each(function() {
            var name = $(this).attr('name');
            var match = name.match(/hl7_mappings\[(.+)\]/);
            if (match) {
                mappingCodes[match[1]] = $(this).val();
            }
        });
        return mappingCodes;
    }
    
    // Helper function to load mapping codes into the form
    function loadMappingCodes(mappingCodes) {
        Object.keys(mappingCodes).forEach(function(field) {
            $('input[name="hl7_mappings[' + field + ']"]').val(mappingCodes[field]);
        });
    }
    
    // Helper function to show alerts
    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible">' +
                       '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                       '<i class="fas fa-' + (type === 'success' ? 'check' : 'exclamation-triangle') + '"></i> ' + message +
                       '</div>';
        
        // Remove existing alerts
        $('.alert-success, .alert-danger').remove();
        
        // Add new alert at the top of the form
        $('.card-body').first().prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    // Initialize profile selector
    $('#profile_selector').trigger('change');
    
    // Load active profile on page load
    @if($activeProfile)
        loadMappingCodes({!! json_encode($activeProfile->mapping_codes) !!});
    @endif
    // Patient selection handler
    $('#patient_id').change(function() {
        var patientId = $(this).val();
        
        if (patientId) {
            $.ajax({
                url: '{{ route("admin.integration.patient-details") }}',
                type: 'GET',
                data: { patient_id: patientId },
                success: function(response) {
                    // Populate patient details
                    $('#display_patient_name').val(response.patient.name);
                    $('#display_mrn').val(response.patient.mrn);
                    $('#display_identity_number').val(response.patient.identity_number);
                    $('#display_age').val(response.patient.age);
                    $('#display_gender').val(response.patient.gender);
                    $('#display_email').val(response.patient.email);
                    $('#display_phone').val(response.patient.phone);
                    $('#display_address').val(response.patient.address);
                    
                    // Display allergies
                    var allergiesHtml = '';
                    if (response.allergies && response.allergies.length > 0) {
                        response.allergies.forEach(function(allergy) {
                            var severityClass = 'allergy-' + allergy.severity;
                            allergiesHtml += '<div class="allergy-item ' + severityClass + '">';
                            allergiesHtml += '<strong>' + allergy.title + '</strong>';
                            if (allergy.severity) {
                                allergiesHtml += ' <span class="badge badge-' + getSeverityBadgeClass(allergy.severity) + '">' + allergy.severity.toUpperCase() + '</span>';
                            }
                            if (allergy.description) {
                                allergiesHtml += '<br><small>' + allergy.description + '</small>';
                            }
                            allergiesHtml += '</div>';
                        });
                    } else {
                        allergiesHtml = '<div class="alert alert-success"><i class="fas fa-check"></i> No known allergies</div>';
                    }
                    $('#allergiesDisplay').html(allergiesHtml);
                    
                    // Show patient details section
                    $('#patientDetailsSection').slideDown();
                },
                error: function() {
                    alert('Error loading patient details');
                }
            });
        } else {
            $('#patientDetailsSection').slideUp();
        }
    });
    
    // Ward selection handler
    $('#ward_id').change(function() {
        var wardId = $(this).val();
        var bedSelect = $('#bed_id');
        
        bedSelect.prop('disabled', true).html('<option value="">Loading...</option>');
        
        if (wardId) {
            $.ajax({
                url: '{{ route("admin.integration.ward-beds") }}',
                type: 'GET',
                data: { ward_id: wardId },
                success: function(response) {
                    var options = '<option value="">Select Bed</option>';
                    response.beds.forEach(function(bed) {
                        options += '<option value="' + bed.id + '">Bed ' + bed.bed_number + '</option>';
                    });
                    bedSelect.html(options).prop('disabled', false);
                },
                error: function() {
                    bedSelect.html('<option value="">Error loading beds</option>');
                }
            });
        } else {
            bedSelect.html('<option value="">Select Ward First</option>');
        }
    });
    
    // Helper function for severity badge class
    function getSeverityBadgeClass(severity) {
        switch(severity) {
            case 'severe': return 'danger';
            case 'moderate': return 'warning';
            case 'mild': return 'info';
            default: return 'secondary';
        }
    }
    
    // Form validation
    $('#admissionForm').submit(function(e) {
        var patientId = $('#patient_id').val();
        var wardId = $('#ward_id').val();
        var bedId = $('#bed_id').val();
        
        if (!patientId || !wardId || !bedId) {
            e.preventDefault();
            alert('Please select patient, ward, and bed before submitting.');
            return false;
        }
    });
});
</script>
@stop 