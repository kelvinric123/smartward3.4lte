@extends('adminlte::page')

@section('title', 'Patient Details')

@section('content_header')
    <div class="container-fluid bg-dark text-white py-2">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.beds.wards.dashboard', $ward) }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Ward
            </a>
            <h1 class="m-0">Patient Details</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">
                            <i class="fas fa-user"></i> Patient Details
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="movement-tab" data-toggle="tab" href="#movement" role="tab" aria-controls="movement" aria-selected="false">
                            <i class="fas fa-exchange-alt"></i> Patient Movement
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="patientview-tab" data-toggle="tab" href="#patientview" role="tab" aria-controls="patientview" aria-selected="false">
                            <i class="fas fa-mobile-alt"></i> Patient View
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="referral-tab" data-toggle="tab" href="#referral" role="tab" aria-controls="referral" aria-selected="false">
                            <i class="fas fa-user-md"></i> Referral
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="discharge-tab" data-toggle="tab" href="#discharge" role="tab" aria-controls="discharge" aria-selected="false">
                            <i class="fas fa-sign-out-alt"></i> Discharge
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="patientTabsContent">
                    <!-- Patient Details Tab -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mrn">MRN</label>
                                    <input type="text" class="form-control" id="mrn" value="{{ $patient->mrn }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="name" value="{{ $patient->name }}" data-original="{{ $patient->name }}" data-masked="{{ preg_replace('/(?<=.{3}).(?=.{0})/', '*', $patient->name) }}" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="#name">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ic">IC/Passport</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="ic" value="{{ $patient->identity_number }}" data-original="{{ $patient->identity_number }}" data-masked="{{ substr($patient->identity_number, 0, 3) . str_repeat('*', max(0, strlen($patient->identity_number) - 6)) . substr($patient->identity_number, -3) }}" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="#ic">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nationality">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" value="Malaysian" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" disabled>
                                        <option value="male" {{ $patient->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $patient->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ $patient->gender == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact">Contact</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="contact" value="{{ $patient->phone }}" data-original="{{ $patient->phone }}" data-masked="{{ substr($patient->phone, 0, 3) . str_repeat('*', max(0, strlen($patient->phone) - 5)) . substr($patient->phone, -2) }}" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-visibility" type="button" data-target="#contact">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="text" class="form-control" id="age" value="{{ $patient->age }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diagnosis">Diagnosis</label>
                                    <textarea class="form-control" id="diagnosis" rows="3" readonly>{{ $bed->notes ?? 'Not specified' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Risk Factors</label>
                                <form id="riskFactorsForm" action="{{ route('admin.beds.wards.patient.updateRiskFactors', ['ward' => $ward->id, 'bedId' => $bed->id]) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="dnr" name="risk_factors[]" value="dnr" {{ isset($activeAdmission->risk_factors) && in_array('dnr', $activeAdmission->risk_factors ?? []) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="dnr">
                                                <i class="fas fa-heart text-danger"></i> DNR
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="fallrisk" name="risk_factors[]" value="fallrisk" {{ isset($activeAdmission->risk_factors) && in_array('fallrisk', $activeAdmission->risk_factors ?? []) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="fallrisk">
                                                <i class="fas fa-exclamation-triangle text-warning"></i> Fall Risk
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="intubated" name="risk_factors[]" value="intubated" {{ isset($activeAdmission->risk_factors) && in_array('intubated', $activeAdmission->risk_factors ?? []) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="intubated">
                                                <i class="fas fa-lungs text-primary"></i> Intubated
                                            </label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="isolation" name="risk_factors[]" value="isolation" {{ isset($activeAdmission->risk_factors) && in_array('isolation', $activeAdmission->risk_factors ?? []) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="isolation">
                                                <i class="fas fa-shield-virus text-info"></i> Isolation
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Update Risk Factors</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Patient Movement Tab -->
                    <div class="tab-pane fade" id="movement" role="tabpanel" aria-labelledby="movement-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="m-0">Current Location</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>
                                            <i class="fas fa-hospital"></i> <strong>Ward:</strong> {{ $ward->name }}
                                        </p>
                                        <p>
                                            <i class="fas fa-bed"></i> <strong>Bed:</strong> {{ $bed->bed_number }}
                                        </p>
                                        <p>
                                            <i class="fas fa-clock"></i> <strong>Since:</strong> 
                                            @if($activeAdmission)
                                                {{ $activeAdmission->formatted_admission_date }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="m-0">Schedule Movement</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.beds.wards.patient.scheduleMovement', ['ward' => $ward->id, 'bedId' => $bed->id]) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="to_service_location">Service Location</label>
                                                <select class="form-control" id="to_service_location" name="to_service_location" required>
                                                    <option value="">Select Service</option>
                                                    @foreach($serviceLocations as $location)
                                                        <option value="{{ $location }}">{{ $location }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="scheduled_date">Scheduled Date</label>
                                                <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" min="{{ now()->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="scheduled_time_slot">Scheduled Time</label>
                                                <select class="form-control" id="scheduled_time_slot" name="scheduled_time_slot" required>
                                                    <option value="">Select Time</option>
                                                    <option value="08:00">08:00 AM</option>
                                                    <option value="08:30">08:30 AM</option>
                                                    <option value="09:00">09:00 AM</option>
                                                    <option value="09:30">09:30 AM</option>
                                                    <option value="10:00">10:00 AM</option>
                                                    <option value="10:30">10:30 AM</option>
                                                    <option value="11:00">11:00 AM</option>
                                                    <option value="11:30">11:30 AM</option>
                                                    <option value="12:00">12:00 PM</option>
                                                    <option value="12:30">12:30 PM</option>
                                                    <option value="13:00">01:00 PM</option>
                                                    <option value="13:30">01:30 PM</option>
                                                    <option value="14:00">02:00 PM</option>
                                                    <option value="14:30">02:30 PM</option>
                                                    <option value="15:00">03:00 PM</option>
                                                    <option value="15:30">03:30 PM</option>
                                                    <option value="16:00">04:00 PM</option>
                                                    <option value="16:30">04:30 PM</option>
                                                    <option value="17:00">05:00 PM</option>
                                                    <option value="17:30">05:30 PM</option>
                                                    <option value="18:00">06:00 PM</option>
                                                    <option value="18:30">06:30 PM</option>
                                                    <option value="19:00">07:00 PM</option>
                                                    <option value="19:30">07:30 PM</option>
                                                    <option value="20:00">08:00 PM</option>
                                                    <option value="20:30">08:30 PM</option>
                                                </select>
                                            </div>
                                            <!-- Hidden input to combine date and time for form submission -->
                                            <input type="hidden" id="scheduled_time" name="scheduled_time">
                                            <div class="form-group">
                                                <label for="notes">Notes</label>
                                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Schedule Movement</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header bg-dark text-white">
                                <h5 class="m-0">Movement History</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Scheduled Time</th>
                                            <th>Sent Time</th>
                                            <th>To</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($patientMovements as $movement)
                                            <tr class="{{ $movement->status == 'sent' ? 'table-info' : '' }}">
                                                <td>{{ $movement->formatted_scheduled_time }}</td>
                                                <td>{{ $movement->formatted_sent_time ?? 'Not sent yet' }}</td>
                                                <td>{{ $movement->to_service_location }}</td>
                                                <td>Service Visit</td>
                                                <td>
                                                    @if($movement->status == 'scheduled')
                                                        <span class="badge badge-warning">Scheduled</span>
                                                    @elseif($movement->status == 'sent')
                                                        <span class="badge badge-primary">Sent</span>
                                                    @elseif($movement->status == 'returned')
                                                        <span class="badge badge-success">Returned</span>
                                                    @elseif($movement->status == 'cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @endif
                                                </td>
                                                <td>{{ $movement->notes ?? 'No notes' }}</td>
                                                <td>
                                                    @if($movement->status == 'scheduled')
                                                        <form class="d-inline" action="{{ route('movements.send', ['movement' => $movement->id]) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-primary">Send</button>
                                                        </form>
                                                        <form class="d-inline" action="{{ route('movements.cancel', ['movement' => $movement->id]) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                                        </form>
                                                    @elseif($movement->status == 'sent')
                                                        <form class="d-inline" action="{{ route('movements.return', ['movement' => $movement->id]) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success">Return</button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">No actions available</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No movement history available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Patient View Tab -->
                    <div class="tab-pane fade" id="patientview" role="tabpanel" aria-labelledby="patientview-tab">
                        <p class="text-muted">Patient view information will be displayed here.</p>
                    </div>
                    
                    <!-- Referral Tab -->
                    <div class="tab-pane fade" id="referral" role="tabpanel" aria-labelledby="referral-tab">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Use this form to refer the patient to other consultants.
                        </div>
                        
                        <form action="{{ route('admin.patients.referral.store', $patient->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="referring_doctor">Referring Doctor</label>
                                        <input type="text" class="form-control" id="referring_doctor" name="referring_doctor" value="{{ auth()->user()->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="referral_date">Referral Date</label>
                                        <input type="datetime-local" class="form-control" id="referral_date" name="referral_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialty">Specialty</label>
                                        <select class="form-control" id="specialty" name="specialty" required>
                                            <option value="">Select Specialty</option>
                                            <option value="cardiology">Cardiology</option>
                                            <option value="neurology">Neurology</option>
                                            <option value="orthopedics">Orthopedics</option>
                                            <option value="gastroenterology">Gastroenterology</option>
                                            <option value="pulmonology">Pulmonology</option>
                                            <option value="nephrology">Nephrology</option>
                                            <option value="endocrinology">Endocrinology</option>
                                            <option value="oncology">Oncology</option>
                                            <option value="rheumatology">Rheumatology</option>
                                            <option value="dermatology">Dermatology</option>
                                            <option value="urology">Urology</option>
                                            <option value="ent">ENT</option>
                                            <option value="ophthalmology">Ophthalmology</option>
                                            <option value="psychiatry">Psychiatry</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="consultant">Consultant</label>
                                        <select class="form-control" id="consultant" name="consultant" required>
                                            <option value="">Select Consultant</option>
                                            <!-- Consultant options will be populated dynamically based on specialty -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="clinical_question">Clinical Question</label>
                                <textarea class="form-control" id="clinical_question" name="clinical_question" rows="2" placeholder="What specific clinical question are you asking?" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="reason_for_referral">Reason for Referral</label>
                                <textarea class="form-control" id="reason_for_referral" name="reason_for_referral" rows="3" placeholder="Describe the reason for this referral" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="relevant_clinical_info">Relevant Clinical Information</label>
                                <textarea class="form-control" id="relevant_clinical_info" name="relevant_clinical_info" rows="4" placeholder="Include any relevant history, examination findings, investigation results, etc."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Urgency</label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="urgency_routine" name="urgency" value="routine" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="urgency_routine">Routine</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="urgency_urgent" name="urgency" value="urgent" class="custom-control-input">
                                    <label class="custom-control-label" for="urgency_urgent">Urgent (within 24-48 hours)</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="urgency_emergency" name="urgency" value="emergency" class="custom-control-input">
                                    <label class="custom-control-label text-danger" for="urgency_emergency">Emergency (immediate)</label>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Referral
                                </button>
                            </div>
                        </form>
                        
                        <hr class="mt-4 mb-4">
                        
                        <h4 class="mb-3"><i class="fas fa-history"></i> Previous Referrals</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Specialty</th>
                                        <th>Consultant</th>
                                        <th>Reason</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patient->referrals ?? [] as $referral)
                                        <tr>
                                            <td>{{ $referral->formatted_referral_date }}</td>
                                            <td>
                                                {{ $referral->to_specialty ?? ($referral->toSpecialty->name ?? 'N/A') }}
                                            </td>
                                            <td>
                                                {{ $referral->to_consultant ?? ($referral->toConsultant->name ?? 'N/A') }}
                                            </td>
                                            <td>{{ Str::limit($referral->reason, 50) }}</td>
                                            <td>
                                                @if($referral->urgency == 'routine')
                                                    <span class="badge badge-success">Routine</span>
                                                @elseif($referral->urgency == 'urgent')
                                                    <span class="badge badge-warning">Urgent</span>
                                                @elseif($referral->urgency == 'emergency')
                                                    <span class="badge badge-danger">Emergency</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($referral->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($referral->status == 'accepted')
                                                    <span class="badge badge-success">Accepted</span>
                                                @elseif($referral->status == 'declined')
                                                    <span class="badge badge-danger">Declined</span>
                                                @elseif($referral->status == 'completed')
                                                    <span class="badge badge-info">Completed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewReferralModal{{ $referral->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- View Referral Modal -->
                                        <div class="modal fade" id="viewReferralModal{{ $referral->id }}" tabindex="-1" role="dialog" aria-labelledby="viewReferralModalLabel{{ $referral->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title" id="viewReferralModalLabel{{ $referral->id }}">Referral Details</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>Date:</strong> {{ $referral->formatted_referral_date }}</p>
                                                                <p><strong>From Ward:</strong> {{ $referral->fromWard->name ?? 'N/A' }}</p>
                                                                <p><strong>Referring Doctor:</strong> {{ $referral->referring_doctor ?? ($referral->referredBy->name ?? 'N/A') }}</p>
                                                                <p><strong>Clinical Question:</strong> {{ $referral->clinical_question ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Status:</strong> 
                                                                    @if($referral->status == 'pending')
                                                                        <span class="badge badge-warning">Pending</span>
                                                                    @elseif($referral->status == 'accepted')
                                                                        <span class="badge badge-success">Accepted</span>
                                                                    @elseif($referral->status == 'declined')
                                                                        <span class="badge badge-danger">Declined</span>
                                                                    @elseif($referral->status == 'completed')
                                                                        <span class="badge badge-info">Completed</span>
                                                                    @endif
                                                                </p>
                                                                <p><strong>Specialty:</strong> {{ $referral->to_specialty ?? ($referral->toSpecialty->name ?? 'N/A') }}</p>
                                                                <p><strong>Consultant:</strong> {{ $referral->to_consultant ?? ($referral->toConsultant->name ?? 'N/A') }}</p>
                                                                <p><strong>Urgency:</strong> 
                                                                    @if($referral->urgency == 'routine')
                                                                        <span class="badge badge-success">Routine</span>
                                                                    @elseif($referral->urgency == 'urgent')
                                                                        <span class="badge badge-warning">Urgent</span>
                                                                    @elseif($referral->urgency == 'emergency')
                                                                        <span class="badge badge-danger">Emergency</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">{{ $referral->urgency ?? 'N/A' }}</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header bg-light">
                                                                        <strong>Reason for Referral</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        {{ $referral->reason }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($referral->notes)
                                                        <div class="row mt-3">
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header bg-light">
                                                                        <strong>Clinical Information</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        {{ $referral->notes }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if($referral->response_notes)
                                                        <div class="row mt-3">
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-header bg-light">
                                                                        <strong>Consultant Response</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        {{ $referral->response_notes }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No previous referrals found for this patient.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Discharge Tab -->
                    <div class="tab-pane fade" id="discharge" role="tabpanel" aria-labelledby="discharge-tab">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Use this form to discharge the patient from the ward.
                        </div>
                        
                        <form action="{{ route('admin.patients.discharge.store', $patient->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="discharge_date">Discharge Date</label>
                                <input type="datetime-local" class="form-control" id="discharge_date" name="discharge_date" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="discharge_type">Discharge Type</label>
                                <select class="form-control" id="discharge_type" name="discharge_type" required>
                                    <option value="normal">Normal Discharge</option>
                                    <option value="ama">Against Medical Advice</option>
                                    <option value="death">Death</option>
                                    <option value="transfer">Transfer to Another Facility</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="discharge_notes">Discharge Notes</label>
                                <textarea class="form-control" id="discharge_notes" name="discharge_notes" rows="3"></textarea>
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-sign-out-alt"></i> Discharge Patient
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header .nav-tabs {
            border-bottom: none;
        }
        .nav-tabs .nav-link {
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 2px solid #007bff;
            color: #007bff;
            background-color: transparent;
        }
        .tab-content {
            padding-top: 20px;
        }
        .text-pink {
            color: #e83e8c !important;
        }
        
        /* Hide datetime picker calendar button */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            opacity: 0.5;
        }
        input[type="datetime-local"] {
            cursor: pointer;
        }
        
        /* Styling for date/time inputs */
        input[type="date"] {
            cursor: pointer;
        }
        select.form-control {
            cursor: pointer;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize masked fields
            $("#name").val($("#name").data('masked'));
            $("#ic").val($("#ic").data('masked'));
            $("#contact").val($("#contact").data('masked'));
            
            // Toggle visibility for sensitive data
            $(".toggle-visibility").on('click', function() {
                const targetId = $(this).data('target');
                const target = $(targetId);
                const icon = $(this).find('i');
                
                if (target.val() === target.data('original')) {
                    // If showing original, switch to masked
                    target.val(target.data('masked'));
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    // If showing masked, switch to original
                    target.val(target.data('original'));
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });
            
            // Set default scheduled time to today's date without specifying time
            function setDefaultScheduledTime() {
                const now = new Date();
                
                // Format for date input (YYYY-MM-DD)
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
                const day = String(now.getDate()).padStart(2, '0');
                
                // Set input to today's date
                const defaultDate = `${year}-${month}-${day}`;
                $("#scheduled_date").val(defaultDate);
                
                console.log("Default date set to:", defaultDate);
            }
            
            // Function to combine date and time for submission
            function updateCombinedDateTime() {
                const date = $("#scheduled_date").val();
                const time = $("#scheduled_time_slot").val();
                
                if (date && time) {
                    const combinedDateTime = `${date}T${time}`;
                    $("#scheduled_time").val(combinedDateTime);
                    console.log("Combined datetime:", combinedDateTime);
                }
            }
            
            // Set initial default date
            setDefaultScheduledTime();
            
            // Update combined value when either date or time changes
            $("#scheduled_date, #scheduled_time_slot").on('change', function() {
                updateCombinedDateTime();
            });
            
            // Add form submit handler to ensure the combined value is set
            $("form").on('submit', function(e) {
                // Only apply this validation to the movement form
                if ($(this).closest('#movement').length > 0) {
                    updateCombinedDateTime();
                    
                    // Validate that both date and time are selected
                    const date = $("#scheduled_date").val();
                    const time = $("#scheduled_time_slot").val();
                    
                    if (!date || !time) {
                        e.preventDefault();
                        alert("Please select both a date and time for the scheduled movement.");
                        return false;
                    }
                }
                
                return true;
            });
            
            // Maintain active tab after page reload
            $('a[data-toggle="tab"]').on('click', function (e) {
                localStorage.setItem('lastActivePatientTab', $(this).attr('href'));
            });
            
            var lastTab = localStorage.getItem('lastActivePatientTab');
            if (lastTab) {
                $('a[href="' + lastTab + '"]').tab('show');
            }
            
            // Specialty to Consultants mapping
            const consultantsBySpecialty = {
                'cardiology': ['Dr. Sarah Johnson', 'Dr. Michael Chen', 'Dr. David Williams'],
                'neurology': ['Dr. Emily Roberts', 'Dr. James Wilson', 'Dr. Lisa Thompson'],
                'orthopedics': ['Dr. Robert Brown', 'Dr. Jennifer Lee', 'Dr. Thomas Anderson'],
                'gastroenterology': ['Dr. Maria Garcia', 'Dr. John Miller', 'Dr. Karen Davis'],
                'pulmonology': ['Dr. Richard Martinez', 'Dr. Susan Taylor', 'Dr. Kevin White'],
                'nephrology': ['Dr. Patricia Lewis', 'Dr. Charles Moore', 'Dr. Elizabeth Wright'],
                'endocrinology': ['Dr. Daniel Clark', 'Dr. Nancy Rodriguez', 'Dr. Mark Harris'],
                'oncology': ['Dr. Linda Hall', 'Dr. Edward Young', 'Dr. Barbara Turner'],
                'rheumatology': ['Dr. Joseph Allen', 'Dr. Carol Walker', 'Dr. Steven King'],
                'dermatology': ['Dr. Helen Scott', 'Dr. George Baker', 'Dr. Donna Green'],
                'urology': ['Dr. Paul Adams', 'Dr. Sandra Nelson', 'Dr. Brian Hill'],
                'ent': ['Dr. Michelle Phillips', 'Dr. Donald Evans', 'Dr. Margaret Campbell'],
                'ophthalmology': ['Dr. Andrew Mitchell', 'Dr. Rebecca Carter', 'Dr. Kenneth Roberts'],
                'psychiatry': ['Dr. Laura Cooper', 'Dr. Timothy Gray', 'Dr. Deborah Morgan'],
                'other': ['Dr. General Consultant']
            };
            
            // Handle specialty change to update consultant options
            $('#specialty').on('change', function() {
                const specialty = $(this).val();
                const consultantSelect = $('#consultant');
                
                // Clear consultant dropdown
                consultantSelect.empty();
                consultantSelect.append('<option value="">Select Consultant</option>');
                
                // Populate consultants based on specialty
                if (specialty && consultantsBySpecialty[specialty]) {
                    consultantsBySpecialty[specialty].forEach(consultant => {
                        consultantSelect.append(`<option value="${consultant}">${consultant}</option>`);
                    });
                }
            });
        });
    </script>
@stop 