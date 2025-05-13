@extends('layouts.iframe')

@section('title', 'Patient Details - ' . $patient->name)

@section('css')
    <style>
        body, html {
            margin: 0;
            padding: 0;
            min-height: fit-content;
            background: #fff;
            overflow-x: hidden;
        }
        .container-fluid {
            padding: 0;
            min-height: fit-content;
            display: flex;
            flex-direction: column;
        }
        .patient-details-card {
            border: none;
            margin: 0;
            border-radius: 0;
            flex: 1;
        }
        .card-body {
            padding: 15px;
        }
        .tab-content {
            padding: 15px 15px 0 15px;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-group:last-child {
            margin-bottom: 0;
        }
        .row {
            margin-right: -8px;
            margin-left: -8px;
        }
        .col-md-4, .col-md-6 {
            padding-right: 8px;
            padding-left: 8px;
        }
        .form-control {
            height: 34px;
            padding: 6px 12px;
        }
        .sensitive-field {
            position: relative;
        }
        .sensitive-field .toggle-visibility {
            position: absolute;
            right: 1px;
            top: 1px;
            bottom: 1px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
            transition: all 0.2s;
        }
        .sensitive-field .toggle-visibility:hover {
            color: #007bff;
            background-color: rgba(0, 0, 0, 0.05);
        }
        .sensitive-field .toggle-visibility i {
            font-size: 14px;
        }
        .sensitive-field input {
            padding-right: 35px;
        }
        textarea.form-control {
            height: auto;
            min-height: 60px;
            resize: none;
        }
        .risk-factors-section {
            margin-bottom: 0;
        }
        .risk-factors-section .form-group {
            margin-bottom: 8px;
        }
        .risk-factors-section .btn {
            margin: 8px 0 0 0;
        }
        .custom-control {
            margin-bottom: 6px;
        }
        .custom-control:last-child {
            margin-bottom: 0;
        }
        /* Remove any bottom padding/margin from last elements */
        .tab-pane {
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .card {
            margin-bottom: 0;
        }
        .nav-tabs {
            padding: 0 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .nav-tabs .nav-link {
            padding: 10px 15px;
            border: none;
            border-bottom: 2px solid transparent;
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 2px solid #007bff;
            background: transparent;
            color: #007bff;
        }
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
        select.form-control {
            padding-right: 24px;
        }
    </style>
@endsection

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

    <div class="card patient-details-card">
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
                            <div class="form-group sensitive-field">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" value="{{ str_repeat('•', strlen($patient->name)) }}" data-original="{{ $patient->name }}" readonly>
                                <button type="button" class="toggle-visibility" data-target="#name">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group sensitive-field">
                                <label for="ic">IC/Passport</label>
                                <input type="text" class="form-control" id="ic" value="{{ substr($patient->identity_number, 0, 3) . str_repeat('*', max(0, strlen($patient->identity_number) - 6)) . substr($patient->identity_number, -3) }}" data-original="{{ $patient->identity_number }}" readonly>
                                <button type="button" class="toggle-visibility" data-target="#ic">
                                    <i class="fas fa-eye"></i>
                                </button>
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
                            <div class="form-group sensitive-field">
                                <label for="contact">Contact</label>
                                <input type="text" class="form-control" id="contact" value="{{ substr($patient->phone, 0, 3) . str_repeat('*', max(0, strlen($patient->phone) - 5)) . substr($patient->phone, -2) }}" data-original="{{ $patient->phone }}" readonly>
                                <button type="button" class="toggle-visibility" data-target="#contact">
                                    <i class="fas fa-eye"></i>
                                </button>
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
                            <div class="form-group risk-factors-section">
                                <label>Risk Factors</label>
                                <form id="riskFactorsForm" action="{{ route('admin.beds.wards.patient.updateRiskFactors', ['ward' => $ward->id, 'bedId' => $bed->id]) }}" method="POST" target="_blank">
                                    @csrf
                                    <div class="form-group mb-2">
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
                            
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="m-0">Schedule Movement</h5>
                                </div>
                                <div class="card-body">
                                    <form id="scheduleMovementForm" action="{{ route('admin.beds.wards.patient.scheduleMovement', ['ward' => $ward->id, 'bedId' => $bed->id]) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="to_service_location">Destination</label>
                                            <select class="form-control" id="to_service_location" name="to_service_location" required>
                                                <option value="">Select destination</option>
                                                @foreach($serviceLocations as $location)
                                                    <option value="{{ $location }}">{{ $location }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="scheduled_time">Date & Time</label>
                                            <input type="datetime-local" class="form-control" id="scheduled_time" name="scheduled_time" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Schedule Movement</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="m-0">Movement History</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Destination</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($patientMovements as $movement)
                                                    <tr>
                                                        <td>{{ $movement->formatted_scheduled_time }}</td>
                                                        <td>{{ $movement->to_service_location }}</td>
                                                        <td>
                                                            @if($movement->status == 'scheduled')
                                                                <span class="badge badge-info">Scheduled</span>
                                                            @elseif($movement->status == 'sent')
                                                                <span class="badge badge-warning">Out of Ward</span>
                                                            @elseif($movement->status == 'returned')
                                                                <span class="badge badge-success">Returned</span>
                                                            @elseif($movement->status == 'cancelled')
                                                                <span class="badge badge-danger">Cancelled</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($movement->status == 'scheduled')
                                                                <form method="POST" action="{{ route('admin.movements.send', $movement->id) }}" class="movement-form" data-action="send">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-xs btn-warning">
                                                                        <i class="fas fa-sign-out-alt"></i> Send
                                                                    </button>
                                                                </form>
                                                                <form method="POST" action="{{ route('admin.movements.cancel', $movement->id) }}" class="movement-form" data-action="cancel">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-xs btn-danger">
                                                                        <i class="fas fa-times"></i> Cancel
                                                                    </button>
                                                                </form>
                                                            @elseif($movement->status == 'sent')
                                                                <form method="POST" action="{{ route('admin.movements.return', $movement->id) }}" class="movement-form" data-action="return">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-xs btn-success">
                                                                        <i class="fas fa-sign-in-alt"></i> Return
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No movement history found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Patient View Tab -->
                <div class="tab-pane fade" id="patientview" role="tabpanel" aria-labelledby="patientview-tab">
                    <div class="row">
                        <div class="col-12 text-center p-5">
                            <i class="fas fa-mobile-alt fa-5x text-muted mb-3"></i>
                            <h3>Patient View</h3>
                            <p class="lead">This feature will show what information the patient can see on their device.</p>
                            <p>The patient view is currently under development.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Referral Tab -->
                <div class="tab-pane fade" id="referral" role="tabpanel" aria-labelledby="referral-tab">
                    <form action="{{ route('admin.patients.referral.store', $patient->id) }}" method="POST" target="_blank">
                        @csrf
                        <div class="form-group">
                            <label for="specialty_id">Referring To Specialty</label>
                            <select class="form-control" id="specialty_id" name="specialty_id" required>
                                <option value="">Select Specialty</option>
                                @foreach(\App\Models\Specialty::all() as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="consultant_id">Consultant</label>
                            <select class="form-control" id="consultant_id" name="consultant_id" required disabled>
                                <option value="">Select Consultant</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="clinical_question">Clinical Question</label>
                            <textarea class="form-control" id="clinical_question" name="clinical_question" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Referral</button>
                    </form>
                    
                    <hr>
                    
                    <h5 class="mb-3">Referral History</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Specialty</th>
                                    <th>Consultant</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patientReferrals as $referral)
                                    <tr>
                                        <td>{{ $referral->formatted_referral_date }}</td>
                                        <td>{{ $referral->specialty->name }}</td>
                                        <td>{{ $referral->consultant->name ?? 'Not assigned' }}</td>
                                        <td>
                                            @if($referral->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($referral->status == 'accepted')
                                                <span class="badge badge-success">Accepted</span>
                                            @elseif($referral->status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($referral->status == 'completed')
                                                <span class="badge badge-info">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#referralModal{{ $referral->id }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            
                                            <!-- Referral Modal -->
                                            <div class="modal fade" id="referralModal{{ $referral->id }}" tabindex="-1" role="dialog" aria-labelledby="referralModalLabel{{ $referral->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="referralModalLabel{{ $referral->id }}">Referral Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                                                    <p><strong>Specialty:</strong> {{ $referral->specialty->name }}</p>
                                                                    <p><strong>Consultant:</strong> {{ $referral->consultant->name ?? 'Not assigned' }}</p>
                                                                    <p><strong>Status:</strong> 
                                                                        @if($referral->status == 'pending')
                                                                            <span class="badge badge-warning">Pending</span>
                                                                        @elseif($referral->status == 'accepted')
                                                                            <span class="badge badge-success">Accepted</span>
                                                                        @elseif($referral->status == 'rejected')
                                                                            <span class="badge badge-danger">Rejected</span>
                                                                        @elseif($referral->status == 'completed')
                                                                            <span class="badge badge-info">Completed</span>
                                                                        @endif
                                                                    </p>
                                                                    <p><strong>Notes:</strong> {{ $referral->notes ?? 'No notes' }}</p>
                                                                </div>
                                                            </div>
                                                            @if($referral->response)
                                                                <hr>
                                                                <h6>Consultant Response:</h6>
                                                                <p>{{ $referral->response }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No referrals found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Discharge Tab -->
                <div class="tab-pane fade" id="discharge" role="tabpanel" aria-labelledby="discharge-tab">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="m-0"><i class="fas fa-sign-out-alt mr-2"></i> Discharge Patient</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                        <p class="lead">Are you sure you want to discharge this patient?</p>
                                        <p>This will free up the bed for other patients.</p>
                                    </div>
                                    
                                    <a href="{{ route('admin.patients.discharge', $patient->id) }}" class="btn btn-danger btn-block btn-lg" target="_blank">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Proceed to Discharge
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmScheduleModal" tabindex="-1" role="dialog" aria-labelledby="confirmScheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmScheduleModalLabel">Confirm Schedule Movement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to schedule this patient movement?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmScheduleBtn">Confirm</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Toggle visibility of sensitive fields
        $('.toggle-visibility').on('click', function() {
            var target = $(this).data('target');
            var input = $(target);
            var icon = $(this).find('i');
            
            if (input.val() === input.data('original')) {
                // If currently showing original value, mask it
                if (target === '#name') {
                    input.val('•'.repeat(input.data('original').length));
                } else if (target === '#ic') {
                    var original = input.data('original');
                    input.val(original.substring(0, 3) + '•'.repeat(Math.max(0, original.length - 6)) + original.slice(-3));
                } else if (target === '#contact') {
                    var original = input.data('original');
                    input.val(original.substring(0, 3) + '•'.repeat(Math.max(0, original.length - 5)) + original.slice(-2));
                }
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                // If currently masked, show original value
                input.val(input.data('original'));
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });
        
        // Helper function to repeat a string
        function str_repeat(str, count) {
            return str.repeat(count);
        }
        
        // Load consultants when specialty is selected
        $('#specialty_id').on('change', function() {
            var specialtyId = $(this).val();
            if (specialtyId) {
                $.ajax({
                    url: '{{ route("admin.referrals.consultants-by-specialty.direct") }}',
                    type: 'GET',
                    data: { specialty_id: specialtyId },
                    success: function(data) {
                        $('#consultant_id').empty().prop('disabled', false);
                        $('#consultant_id').append('<option value="">Select Consultant</option>');
                        $.each(data, function(key, value) {
                            $('#consultant_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#consultant_id').empty().prop('disabled', true);
                $('#consultant_id').append('<option value="">Select Consultant</option>');
            }
        });

        let scheduleForm = $('#scheduleMovementForm');
        let confirmModal = $('#confirmScheduleModal');
        let confirmBtn = $('#confirmScheduleBtn');
        let formData = null;

        scheduleForm.on('submit', function(e) {
            e.preventDefault();
            formData = scheduleForm.serialize();
            confirmModal.modal('show');
        });

        confirmBtn.on('click', function() {
            confirmModal.modal('hide');
            // Remove previous error highlights/messages
            scheduleForm.find('.is-invalid').removeClass('is-invalid');
            scheduleForm.find('.invalid-feedback').remove();
            $.ajax({
                url: scheduleForm.attr('action'),
                method: 'POST',
                data: formData,
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                success: function(response) {
                    if(response && response.movement_history_html) {
                        $('.card .card-body .table-responsive').html(response.movement_history_html);
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let firstErrorField = null;
                        for (let field in errors) {
                            let input = scheduleForm.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + errors[field][0] + '</div>');
                            if (!firstErrorField) firstErrorField = input;
                        }
                        if (firstErrorField) firstErrorField.focus();
                    } else {
                        alert('Failed to schedule movement. Please check your input.');
                    }
                }
            });
        });

        // Handle movement form submissions
        $('.movement-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const action = form.data('action');
            const url = form.attr('action');
            
            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response && response.movement_history_html) {
                        // Update the movement history table
                        $('.card .card-body .table-responsive').html(response.movement_history_html);
                        // Show success message
                        toastr.success('Patient movement updated successfully');
                    } else {
                        // Fallback to page reload if no HTML response
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update patient movement');
                }
            });
        });
    });
</script>
@endsection 