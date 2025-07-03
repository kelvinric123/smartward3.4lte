@extends('adminlte::page')

@section('title', $ward->name . ' Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <h1 class="mr-2">{{ $ward->name }}</h1>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="wardViewDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-exchange-alt"></i> Change Ward
                        </button>
                        <div class="dropdown-menu" aria-labelledby="wardViewDropdown">
                            @foreach($allWards as $availableWard)
                                <a class="dropdown-item {{ $ward->id == $availableWard->id ? 'active' : '' }}" 
                                   href="{{ route('admin.beds.wards.dashboard', $availableWard) }}{{ request()->has('fullscreen') ? '?fullscreen=true' : '' }}">
                                    {{ $availableWard->name }} <small class="text-muted">({{ $availableWard->specialty->name }})</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <span class="badge badge-secondary">{{ $ward->specialty->name }} Ward</span>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">Ward Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content_top_nav_right')
    <li class="nav-item d-none" id="current-datetime">
        <span class="nav-link">
            <i class="far fa-clock mr-1"></i> <span id="current-date-time-display"></span>
        </span>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" id="fullscreen-toggle" role="button">
            <i class="fas fa-expand"></i>
        </a>
    </li>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Bed Grid -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Beds Layout</h3>
                            <div class="btn-group">
                                                <button type="button" class="btn btn-warning" id="notification-btn" onclick="showNotificationsPanel()">
                                    <i class="fas fa-bell"></i> Notifications 
                                    @if(isset($patientAlerts) && $patientAlerts->count() > 0)
                                        <span class="badge badge-light" id="new-alerts-count">{{ $patientAlerts->count() }}</span>
                                    @else
                                        <span class="badge badge-light d-none" id="new-alerts-count">0</span>
                                    @endif
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="test-sound-btn" title="Test notification sound">
                                    <i class="fas fa-volume-up"></i> Test Sound
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Sound Settings">
                                        <i class="fas fa-cog"></i>
                                        <span class="badge badge-success ml-1" id="audio-status" style="display: none;">🔊</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right p-3" style="min-width: 250px;">
                                        <h6 class="dropdown-header">Sound Settings</h6>
                                        <div class="form-group mb-2">
                                            <label for="volume-control" class="small">Volume</label>
                                            <input type="range" class="form-control-range" id="volume-control" min="0" max="100" value="85">
                                            <small class="text-muted">Current: <span id="volume-display">85</span>%</small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable-sound" checked>
                                            <label class="form-check-label small" for="enable-sound">
                                                Enable notification sounds
                                            </label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="text-center mb-2">
                                            <button class="btn btn-sm btn-primary" id="simulate-alert-btn">
                                                <i class="fas fa-bell"></i> Simulate New Alert
                                            </button>
                                        </div>
                                        <div class="text-center">
                                            <small class="text-muted">
                                                <span id="audio-status-text">Click anywhere to enable audio</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="beds-container">
                            @forelse($ward->beds as $bed)
                                @php
                                    // Default border color based on bed status
                                    // Change default color for occupied beds from 'danger' to 'success'
                                    $borderColor = $bed->status === 'available' ? 'success' : ($bed->status === 'occupied' ? 'success' : ($bed->status === 'reserved' ? 'warning' : 'secondary'));
                                    
                                    // Check for abnormal EWS in latest vital signs
                                    if ($bed->status === 'occupied' && $bed->patient && $bed->patient->latestVitalSigns) {
                                        $latestEws = $bed->patient->latestVitalSigns->total_ews;
                                        if ($latestEws >= 7) {
                                            $borderColor = 'danger'; // Red for critical (EWS >= 7)
                                        } elseif ($latestEws >= 5) {
                                            $borderColor = 'warning'; // Orange for high risk (EWS 5-6)
                                        } elseif ($latestEws >= 3) {
                                            $borderColor = 'info'; // Blue for medium risk (EWS 3-4)
                                        }
                                        // If EWS is normal (0-2), keep the green border
                                    }
                                @endphp
                                <div class="col-lg-4 col-md-6 mb-4 bed-box">
                                    <div class="card border-{{ $borderColor }} mb-0">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center p-2">
                                            <h5 class="m-0">Bed {{ $bed->bed_number }} 
                                                @if($bed->status === 'occupied')
                                                    <span class="badge badge-success">1</span>
                                                @endif
                                            </h5>
                                            @if($bed->patient)
                                                @if(!isset($activeMovements[$bed->patient_id]))
                                                    <span>
                                                        MRN: {{ $bed->patient->mrn ?: 'Not available' }}
                                                    </span>
                                                @else
                                                    <span style="color: #20B2AA;">
                                                        <i class="fas fa-external-link-alt"></i> At service location
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">No Patient</span>
                                            @endif
                                        </div>
                                        <div class="card-body p-3">
                                            @if($bed->patient)
                                                @if(!isset($activeMovements[$bed->patient_id]))
                                                    <p class="mb-1 d-flex align-items-center justify-content-between">
                                                        <span>
                                                            <i class="fas fa-user"></i> 
                                                            @php
                                                                if ($bed->patient->name) {
                                                                    $fullName = $bed->patient->name;
                                                                    $nameLength = mb_strlen($fullName);
                                                                    $halfLength = intval($nameLength / 2);
                                                                    $visiblePart = mb_substr($fullName, 0, $halfLength);
                                                                    $hiddenPart = str_repeat('*', $nameLength - $halfLength);
                                                                    echo $visiblePart . $hiddenPart;
                                                                } else {
                                                                    echo 'No name';
                                                                }
                                                            @endphp
                                                        </span>
                                                        @if($bed->patient->activeAdmission && !empty($bed->patient->activeAdmission->risk_factors))
                                                            <span class="risk-factor-grid">
                                                                @foreach($bed->patient->activeAdmission->risk_factors as $risk)
                                                                    @if($risk === 'fallrisk')
                                                                        <span class="risk-icon" title="Fall Risk">
                                                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                                                        </span>
                                                                    @elseif($risk === 'dnr')
                                                                        <span class="risk-icon" title="DNR">
                                                                            <i class="fas fa-heart text-danger"></i>
                                                                        </span>
                                                                    @elseif($risk === 'intubated')
                                                                        <span class="risk-icon" title="Intubated">
                                                                            <i class="fas fa-lungs text-primary"></i>
                                                                        </span>
                                                                    @elseif($risk === 'isolation')
                                                                        <span class="risk-icon" title="Isolation">
                                                                            <i class="fas fa-shield-virus text-info"></i>
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </span>
                                                        @endif
                                                    </p>
                                                    
                                                    @if($bed->consultant)
                                                        <p class="mb-1">
                                                            <i class="fas fa-user-md"></i> {{ $bed->consultant->name }}
                                                        </p>
                                                    @else
                                                        <p class="mb-1">
                                                            <i class="fas fa-user-md"></i> Not assigned
                                                        </p>
                                                    @endif
                                                @endif
                                            @endif
                                            
                                            @if($bed->status == 'occupied' && $bed->patient)
                                                @if(isset($activeMovements[$bed->patient_id]))
                                                    <div class="alert p-0 mb-3">
                                                        <div class="btn btn-info btn-block mb-0 text-left" style="cursor: default; background-color: #20B2AA; border-color: #20B2AA;">
                                                            <i class="fas fa-external-link-alt"></i> At {{ $activeMovements[$bed->patient_id]->to_service_location }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                                                        <button type="button" class="btn btn-success btn-sm return-patient-btn" 
                                                                data-movement-id="{{ $activeMovements[$bed->patient_id]->id }}"
                                                                data-patient-name="{{ $bed->patient->name }}">
                                                            <i class="fas fa-sign-in-alt mr-1"></i> Patient Returned
                                                        </button>
                                                        <small class="text-muted ml-2">
                                                            <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($activeMovements[$bed->patient_id]->sent_time)->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    
                                                    <!-- Add some space to push buttons to bottom -->
                                                    <div style="min-height: 30px;"></div>
                                                @else
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-clock"></i> 
                                                        @php
                                                            // Get actual admission duration from the patient's active admission
                                                            $activeAdmission = $bed->patient->activeAdmission;
                                                            echo $activeAdmission ? $activeAdmission->getStayDurationAttribute() : 'Just admitted';
                                                        @endphp
                                                    </p>
                                                    
                                                    @if($bed->patient && $bed->patient->latestVitalSigns && $bed->patient->latestVitalSigns->total_ews >= 3)
                                                        <p class="mb-1">
                                                            <i class="fas fa-heartbeat"></i> 
                                                            <span class="badge badge-{{ $bed->patient->latestVitalSigns->status_color }}">
                                                                EWS: {{ $bed->patient->latestVitalSigns->total_ews }}
                                                            </span>
                                                        </p>
                                                    @elseif($bed->patient && $bed->patient->latestVitalSigns)
                                                        <p class="mb-1">
                                                            <i class="fas fa-heartbeat"></i> 
                                                            <span class="badge badge-success">
                                                                EWS: {{ $bed->patient->latestVitalSigns->total_ews }}
                                                            </span>
                                                        </p>
                                                    @elseif($bed->patient)
                                                        <p class="mb-1">
                                                            <i class="fas fa-heartbeat"></i> 
                                                            <span class="badge badge-secondary">
                                                                No vital signs
                                                            </span>
                                                        </p>
                                                    @endif
                                                @endif
                                                
                                                <div class="btn-group btn-group-sm w-100 mt-2">
                                                    <a href="{{ route('admin.beds.wards.patient.iframe', ['ward' => $ward, 'bedId' => $bed->id]) }}{{ request()->has('fullscreen') ? '?fullscreen=true' : '' }}" class="btn btn-outline-secondary btn-patient-details" data-patient-name="{{ $bed->patient->name }}">
                                                        <i class="fas fa-list"></i>
                                                    </a>
                                                    <a href="{{ route('admin.vital-signs.iframe-trend', $bed->patient->id) }}{{ request()->has('fullscreen') ? '?fullscreen=true' : '' }}" class="btn btn-outline-secondary text-danger btn-vital-signs" data-patient-name="{{ $bed->patient->name }}">
                                                        <i class="fas fa-heart"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.wards.patient.iframe', ['ward' => $ward, 'bedId' => $bed->id]) }}{{ request()->has('fullscreen') ? '?fullscreen=true' : '' }}" class="btn btn-outline-secondary btn-patient-details" data-patient-name="{{ $bed->patient->name }}">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                </div>
                                            @elseif($bed->status == 'available')
                                                <p class="mb-1">
                                                    <i class="fas fa-bed"></i> No Patient
                                                </p>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-success btn-block" onclick="openAdmitPatientModal('{{ route('admin.beds.wards.admit.iframe', ['ward' => $ward, 'bedId' => $bed->id]) }}')">
                                                        <i class="fas fa-user-plus"></i> Admit Patient
                                                    </button>
                                                </div>
                                            @else
                                                <p class="mb-1">
                                                    <i class="fas fa-bed"></i> No Patient Admitted
                                                </p>
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-clock"></i> Bed is available
                                                </p>
                                                <div class="btn-group btn-group-sm w-100 mt-2">
                                                    <a href="{{ route('admin.beds.beds.show', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.beds.edit', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">
                                    <p>No beds available in this ward. <a href="{{ route('admin.beds.beds.create') }}">Add beds</a>.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Footer with Stats -->
        <div class="row stats-footer-row">
            <div class="col-12">
                <div class="card bg-dark mb-0">
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-md-2 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">AVAILABLE BEDS</div>
                                <div class="h5 mb-0 text-success">
                                    <i class="fas fa-bed"></i> {{ $availableBeds }}
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">NURSES ON DUTY</div>
                                <div class="h5 mb-0 text-primary">
                                    <i class="fas fa-user-nurse"></i> {{ $nursesOnDuty }}
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">PATIENTS</div>
                                <div class="h5 mb-0 text-warning">
                                    <i class="fas fa-user-injured"></i> {{ $occupiedBeds }}
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">NURSE-PATIENT RATIO</div>
                                <div class="h5 mb-0 text-info">
                                    <i class="fas fa-balance-scale"></i> {{ $nursePatientRatio }}:1
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-8 col-12 text-center">
                                <div class="text-muted mb-1">OCCUPANCY RATE</div>
                                <div class="h5 mb-0 text-danger">
                                    <i class="fas fa-chart-pie"></i> {{ $occupancyRate }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Vital Signs Modal -->
    <div class="modal fade" id="vitalSignsModal" tabindex="-1" role="dialog" aria-labelledby="vitalSignsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vitalSignsModalLabel">Patient Vital Signs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="vitalSignsFrame" src="" style="width: 100%; height: 80vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Patient Details Modal -->
    <div class="modal fade" id="patientDetailsModal" tabindex="-1" role="dialog" aria-labelledby="patientDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="patientDetailsModalLabel">Patient Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="patientDetailsFrame" src="" style="width: 100%; height: 80vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Simple Notifications Panel Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="fas fa-bell"></i> Patient Notifications
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Statistics Row -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">New Alerts</span>
                                    <span class="info-box-number" id="modal-new-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-bell"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Active</span>
                                    <span class="info-box-number" id="modal-total-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-reply"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Responses</span>
                                    <span class="info-box-number" id="modal-responses-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="info-box bg-secondary">
                                <span class="info-box-icon"><i class="fas fa-history"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">History</span>
                                    <span class="info-box-number" id="modal-history-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success btn-block" id="refresh-notifications">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                            <button type="button" class="btn btn-primary btn-block mt-2" id="create-test-alert">
                                <i class="fas fa-plus"></i> Test Alert
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-3" id="notification-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="active-alerts-tab" data-bs-toggle="tab" data-target="#active-alerts" role="tab" aria-controls="active-alerts" aria-selected="true">
                                <i class="fas fa-bell"></i> Active Alerts
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="history-tab" data-bs-toggle="tab" data-target="#history" role="tab" aria-controls="history" aria-selected="false">
                                <i class="fas fa-history"></i> History
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="notification-tab-content">
                        <!-- Active Alerts Tab -->
                        <div class="tab-pane fade show active" id="active-alerts" role="tabpanel" aria-labelledby="active-alerts-tab">
                            <div id="notifications-list">
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Loading notifications...</p>
                                </div>
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            <div id="history-list">
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Loading history...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="responseModalLabel">
                        <i class="fas fa-reply"></i> Respond to Patient Alert
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Patient:</strong> <span id="response-patient-name"></span><br>
                        <strong>Bed:</strong> <span id="response-bed-number"></span><br>
                        <strong>Alert:</strong> <span id="response-alert-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="response-message">Your Response Message:</label>
                        <textarea class="form-control" id="response-message" rows="3" 
                                  placeholder="Enter your response to the patient...">Your alert has been acknowledged by nursing staff. We are taking care of your request.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="send-response">
                        <i class="fas fa-paper-plane"></i> Send Response
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .text-pink {
            color: #e83e8c !important;
        }
        
        .border-right {
            border-right: 1px solid #444 !important;
        }
        
        /* Enhanced borders for EWS status */
        .border-warning {
            border-width: 3px !important;
            border-color: #ffc107 !important;
        }
        
        .border-danger {
            border-width: 3px !important;
            border-color: #dc3545 !important;
        }
        
        .border-info {
            border-width: 2px !important;
            border-color: #17a2b8 !important;
        }
        
        @media (max-width: 768px) {
            .border-right {
                border-right: none !important;
                border-bottom: 1px solid #444 !important;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
            }
        }
        
        /* Fullscreen mode styles */
        body.fullscreen-mode {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        
        body.fullscreen-mode .main-sidebar {
            display: none !important;
        }
        
        body.fullscreen-mode .content-wrapper {
            margin-left: 0 !important;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        body.fullscreen-mode .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        body.fullscreen-mode .container-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 0;
        }
        
        /* Fixed header in fullscreen mode */
        body.fullscreen-mode .content-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #fff;
            margin: 0;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        /* Scrollable bed grid container */
        body.fullscreen-mode .card {
            margin: 0;
            border-radius: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        body.fullscreen-mode .card-header {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #fff;
        }
        
        body.fullscreen-mode .card-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        
        /* Fixed footer in fullscreen mode */
        body.fullscreen-mode .stats-footer-row {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0;
            z-index: 1000;
            background: #343a40;
            box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
        }
        
        body.fullscreen-mode #fullscreen-toggle i {
            transform: rotate(180deg);
        }
        
        body.fullscreen-mode #current-datetime {
            display: block !important;
        }
        
        /* Hide pushmenu toggle button in fullscreen mode */
        body.fullscreen-mode [data-widget="pushmenu"] {
            display: none !important;
        }
        
        /* 5 beds horizontal in fullscreen mode */
        body.fullscreen-mode .bed-box {
            flex: 0 0 20%;
            max-width: 20%;
        }
        
        /* Modal iframe styles */
        #vitalSignsModal .modal-content {
            height: 90vh;
        }
        
        #vitalSignsModal .modal-body {
            padding: 0;
            overflow: hidden;
        }
        
        #vitalSignsFrame {
            width: 100%;
            height: calc(90vh - 60px); /* Subtract header height */
            border: none;
        }
        
        .risk-factor-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 2px;
            width: 36px;
            height: 36px;
            margin-left: 8px;
        }
        .risk-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.07);
            font-size: 1.1em;
            width: 16px;
            height: 16px;
        }
        
        /* Pulse animation for notification button when sound is playing */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); box-shadow: 0 0 20px rgba(220, 53, 69, 0.6); }
            100% { transform: scale(1); }
        }
        
        /* Enhanced notification button styles */
        #notification-btn.btn-danger {
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.5);
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // EARLY INITIALIZATION: Prevent AdminLTE iframe conflicts before anything else
            try {
                // Disable AdminLTE iframe auto-initialization globally for our notification iframe
                if (typeof window.AdminLTE !== 'undefined' && window.AdminLTE.IFrame) {
                    console.log('Early AdminLTE iframe protection initialized');
                    
                    // Store the original methods immediately
                    if (!window._originalAdminLTE) {
                        window._originalAdminLTE = {
                            _initFrameElement: window.AdminLTE.IFrame._initFrameElement,
                            _jQueryInterface: window.AdminLTE.IFrame._jQueryInterface
                        };
                    }
                    
                    // Immediately override to prevent early errors
                    const originalInitFrameElement = window.AdminLTE.IFrame._initFrameElement;
                    window.AdminLTE.IFrame._initFrameElement = function(element) {
                        // Safety check for null elements
                        if (!element) {
                            console.log('AdminLTE iframe init blocked - null element');
                            return;
                        }
                        
                        // Check if element has getAttribute method
                        if (!element.getAttribute) {
                            console.log('AdminLTE iframe init blocked - no getAttribute method');
                            return;
                        }
                        
                        // Check if this is our notification iframe
                        if (element.id === 'notification-iframe' || 
                            (element.closest && element.closest('#notificationModal'))) {
                            console.log('AdminLTE iframe init blocked - notification iframe detected');
                            return;
                        }
                        
                        // Check for autoIframeMode attribute before accessing it
                        try {
                            const autoMode = element.getAttribute('data-auto-iframe-mode');
                            if (autoMode === 'false') {
                                console.log('AdminLTE iframe init blocked - auto mode disabled');
                                return;
                            }
                        } catch (e) {
                            console.log('Error checking autoIframeMode:', e);
                            return;
                        }
                        
                        // Call original function with safety
                        if (originalInitFrameElement) {
                            return originalInitFrameElement.call(this, element);
                        }
                    };
                }
            } catch (e) {
                console.log('Early AdminLTE protection failed:', e);
            }
            
            // Fullscreen toggle functionality
            const fullscreenToggle = $('#fullscreen-toggle');
            const body = $('body');
            const currentDateTime = $('#current-datetime');
            const dateTimeDisplay = $('#current-date-time-display');
            
            // Update current date and time
            function updateDateTime() {
                const now = new Date();
                const formattedDateTime = now.toLocaleString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                dateTimeDisplay.text(formattedDateTime);
            }
            
            // Initial update
            updateDateTime();
            
            // Update every second
            setInterval(updateDateTime, 1000);
            
            // Function to set fullscreen mode
            function setFullscreenMode(isFullscreen) {
                if (isFullscreen) {
                    body.addClass('fullscreen-mode');
                    fullscreenToggle.find('i').removeClass('fa-expand').addClass('fa-compress');
                    localStorage.setItem('wardDashboardFullscreen', 'true');
                } else {
                    body.removeClass('fullscreen-mode');
                    fullscreenToggle.find('i').removeClass('fa-compress').addClass('fa-expand');
                    localStorage.removeItem('wardDashboardFullscreen');
                }
            }
            
            // Toggle fullscreen mode
            fullscreenToggle.on('click', function(e) {
                e.preventDefault();
                const isCurrentlyFullscreen = body.hasClass('fullscreen-mode');
                setFullscreenMode(!isCurrentlyFullscreen);
            });
            
            // Check localStorage for fullscreen state
            if (localStorage.getItem('wardDashboardFullscreen') === 'true') {
                setFullscreenMode(true);
            }
            
            // Vital Signs Modal functionality
            $('.btn-vital-signs').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                $('#vitalSignsFrame').attr('src', url);
                $('#vitalSignsModalLabel').text('Vital Signs: ' + $(this).data('patient-name'));
                $('#vitalSignsModal').modal('show');
            });
            
            // Clear iframe when modal is closed to prevent memory issues
            $('#vitalSignsModal').on('hidden.bs.modal', function () {
                $('#vitalSignsFrame').attr('src', '');
            });
            
            // Patient Details Modal functionality
            $('.btn-patient-details').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                $('#patientDetailsFrame').attr('src', url);
                $('#patientDetailsModalLabel').text('Patient Details: ' + $(this).data('patient-name'));
                $('#patientDetailsModal').modal('show');
            });
            
            // Clear iframe when modal is closed to prevent memory issues
            $('#patientDetailsModal').on('hidden.bs.modal', function () {
                $('#patientDetailsFrame').attr('src', '');
            });
            
            // Function for handling new alerts
            window.newAlertReceived = function(count) {
                // Update the notification badge count
                const countBadge = $('#new-alerts-count');
                
                // If there are new alerts, show the badge with count
                if (count > 0) {
                    countBadge.text(count).removeClass('d-none');
                    
                    // Flash the notification button to draw attention
                    const notificationBtn = $('#notification-btn');
                    notificationBtn.addClass('btn-danger').removeClass('btn-warning');
                    
                    // Reset button color after 2 seconds
                    setTimeout(function() {
                        notificationBtn.addClass('btn-warning').removeClass('btn-danger');
                    }, 2000);
                } else {
                    countBadge.text('0').addClass('d-none');
                }
            };

            // Real-time alert polling
            function pollForNewAlerts() {
                $.ajax({
                    url: '{{ route("admin.beds.wards.alerts", $ward->id) }}',
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            const newAlertsCount = response.new_alerts_count;
                            
                            // Log for debugging
                            console.log('Polling result - New alerts count:', newAlertsCount);
                            
                            // Update the notification badge
                            window.newAlertReceived(newAlertsCount);
                            
                            // If there are new alerts, auto-play notification sound and show browser notification
                            if (newAlertsCount > 0) {
                                console.log('Auto-playing notification sound for', newAlertsCount, 'new alerts');
                                
                                // Ensure audio is initialized
                                initializeAudio();
                                
                                // Play notification sound automatically
                                playNotificationSound();
                                
                                // Show browser notification if permission is granted
                                if (Notification.permission === 'granted') {
                                    new Notification('🚨 New Patient Alert', {
                                        body: `${newAlertsCount} new patient alert(s) received - Click to view`,
                                        requireInteraction: true,
                                        tag: 'patient-alert',
                                        vibrate: [200, 100, 200] // Vibration pattern for mobile devices
                                    });
                                }
                                
                                // Additional visual feedback - make the entire notification button more prominent
                                const notificationBtn = $('#notification-btn');
                                notificationBtn.addClass('btn-danger').removeClass('btn-warning');
                                notificationBtn.css({
                                    'animation': 'pulse 0.8s infinite',
                                    'transform': 'scale(1.1)'
                                });
                                
                                // Reset after a longer duration to ensure nurses notice
                                setTimeout(() => {
                                    notificationBtn.css({
                                        'animation': '',
                                        'transform': ''
                                    });
                                    notificationBtn.addClass('btn-warning').removeClass('btn-danger');
                                }, 5000); // 5 seconds of visual emphasis
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('Error polling for alerts:', xhr);
                    }
                });
            }

            // Function to play notification sound
            function playNotificationSound() {
                try {
                    // Check if sound is enabled
                    if (!$('#enable-sound').is(':checked')) {
                        return;
                    }
                    
                    // Visual feedback - make notification button pulsate while sound plays
                    const notificationBtn = $('#notification-btn');
                    notificationBtn.addClass('btn-danger').removeClass('btn-warning');
                    notificationBtn.css('animation', 'pulse 0.5s infinite');
                    
                    // Stop visual animation after sound duration (approximately 2.5 seconds)
                    setTimeout(() => {
                        notificationBtn.css('animation', '');
                        notificationBtn.addClass('btn-warning').removeClass('btn-danger');
                    }, 2500);
                    
                    // Get volume setting (boost it by 20% for better audibility)
                    const volumeLevel = Math.min(($('#volume-control').val() / 100) * 1.2, 1.0);
                    
                    // Try Web Audio API first for the best "ding dong" sound
                    if (window.AudioContext || window.webkitAudioContext) {
                        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        createDingDongChime(audioContext, volumeLevel);
                        return;
                    }
                    
                    // Fallback to external files if Web Audio API fails
                    tryExternalSounds(volumeLevel);
                    
                } catch (error) {
                    console.log('Error playing notification sound:', error);
                    // Final fallback to system beep
                    playSystemBeep();
                }
            }
            
            // Create a notification beep using Web Audio API and return as data URI
            function createNotificationBeep() {
                try {
                    // Create a simple beep sound using Web Audio API
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const sampleRate = audioContext.sampleRate;
                    const duration = 0.3; // 300ms
                    const frequency = 800; // Hz
                    
                    const length = sampleRate * duration;
                    const buffer = audioContext.createBuffer(1, length, sampleRate);
                    const data = buffer.getChannelData(0);
                    
                    for (let i = 0; i < length; i++) {
                        const t = i / sampleRate;
                        data[i] = Math.sin(2 * Math.PI * frequency * t) * Math.exp(-t * 3);
                    }
                    
                    // Convert buffer to data URI (simplified - we'll use a pre-made data URI instead)
                    return 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+Tr'; // Short beep data URI
                } catch (e) {
                    // Fallback data URI for a simple beep
                    return 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+Tr';
                }
            }
            
            // Try external sound files as fallback
            function tryExternalSounds(volumeLevel) {
                const audio = new Audio();
                audio.volume = volumeLevel;
                
                // Try different audio formats for browser compatibility
                if (audio.canPlayType('audio/mp3')) {
                    audio.src = '/sounds/notification.mp3';
                } else if (audio.canPlayType('audio/wav')) {
                    audio.src = '/sounds/notification.wav';
                } else if (audio.canPlayType('audio/ogg')) {
                    audio.src = '/sounds/notification.ogg';
                } else {
                    // Fallback to system beep using Web Audio API
                    playSystemBeep();
                    return;
                }
                
                audio.play().catch(error => {
                    console.log('External audio file failed:', error);
                    playSystemBeep();
                });
            }
            
            // Fallback function to create a system beep using Web Audio API
            function playSystemBeep() {
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Get volume setting
                    const volumeLevel = $('#volume-control').val() / 100;
                    
                    // Create a "ding dong" chime sound - two tones
                    createDingDongChime(audioContext, volumeLevel);
                    
                } catch (error) {
                    console.log('Web Audio API not supported:', error);
                    // Final fallback - try system alert
                    try {
                        // Repeat the speech alert to make it more obvious
                        const utterance = new SpeechSynthesisUtterance('Alert! Patient needs assistance!');
                        utterance.volume = 0.8;
                        utterance.rate = 1.2;
                        utterance.pitch = 1.2;
                        speechSynthesis.speak(utterance);
                    } catch (speechError) {
                        console.log('No audio notification available');
                    }
                }
            }
            
            // Create a "ding dong" hospital chime using Web Audio API
            function createDingDongChime(audioContext, volume) {
                const masterGain = audioContext.createGain();
                masterGain.connect(audioContext.destination);
                masterGain.gain.setValueAtTime(volume * 0.9, audioContext.currentTime); // Make it even louder
                
                // First tone (higher pitch "ding") - longer and more prominent
                setTimeout(() => {
                    const oscillator1 = audioContext.createOscillator();
                    const gainNode1 = audioContext.createGain();
                    
                    oscillator1.connect(gainNode1);
                    gainNode1.connect(masterGain);
                    
                    oscillator1.frequency.setValueAtTime(880, audioContext.currentTime); // Higher pitch for attention
                    oscillator1.type = 'sine';
                    
                    gainNode1.gain.setValueAtTime(0.9, audioContext.currentTime);
                    gainNode1.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.8);
                    
                    oscillator1.start(audioContext.currentTime);
                    oscillator1.stop(audioContext.currentTime + 0.8);
                }, 0);
                
                // Second tone (lower pitch "dong") - delayed, longer, and deeper
                setTimeout(() => {
                    const oscillator2 = audioContext.createOscillator();
                    const gainNode2 = audioContext.createGain();
                    
                    oscillator2.connect(gainNode2);
                    gainNode2.connect(masterGain);
                    
                    oscillator2.frequency.setValueAtTime(550, audioContext.currentTime); // Lower pitch for "dong"
                    oscillator2.type = 'sine';
                    
                    gainNode2.gain.setValueAtTime(0.9, audioContext.currentTime);
                    gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 1.0);
                    
                    oscillator2.start(audioContext.currentTime);
                    oscillator2.stop(audioContext.currentTime + 1.0);
                }, 500); // 500ms delay for "dong"
                
                // Third tone - another "ding" for urgency
                setTimeout(() => {
                    const oscillator3 = audioContext.createOscillator();
                    const gainNode3 = audioContext.createGain();
                    
                    oscillator3.connect(gainNode3);
                    gainNode3.connect(masterGain);
                    
                    oscillator3.frequency.setValueAtTime(800, audioContext.currentTime); // High attention tone
                    oscillator3.type = 'sine';
                    
                    gainNode3.gain.setValueAtTime(0.7, audioContext.currentTime);
                    gainNode3.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.6);
                    
                    oscillator3.start(audioContext.currentTime);
                    oscillator3.stop(audioContext.currentTime + 0.6);
                }, 1200); // 1.2 second delay for final urgent chime
                
                // Fourth tone - final emphasis (optional, can be removed if too much)
                setTimeout(() => {
                    const oscillator4 = audioContext.createOscillator();
                    const gainNode4 = audioContext.createGain();
                    
                    oscillator4.connect(gainNode4);
                    gainNode4.connect(masterGain);
                    
                    oscillator4.frequency.setValueAtTime(660, audioContext.currentTime); // Medium tone
                    oscillator4.type = 'sine';
                    
                    gainNode4.gain.setValueAtTime(0.5, audioContext.currentTime);
                    gainNode4.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.8);
                    
                    oscillator4.start(audioContext.currentTime);
                    oscillator4.stop(audioContext.currentTime + 0.8);
                }, 1700); // 1.7 second delay for final chime
            }
            
            // Initialize audio on first user interaction (required by modern browsers)
            let audioInitialized = false;
            let audioContext = null;
            
            function initializeAudio() {
                if (!audioInitialized) {
                    try {
                        // Create audio context
                        if (window.AudioContext || window.webkitAudioContext) {
                            audioContext = new (window.AudioContext || window.webkitAudioContext)();
                            
                            // Resume audio context if it's suspended
                            if (audioContext.state === 'suspended') {
                                audioContext.resume().then(() => {
                                    console.log('Audio context resumed successfully');
                                    updateAudioStatus(true);
                                });
                            } else {
                                updateAudioStatus(true);
                            }
                        }
                        
                        // Create a silent audio to initialize the audio system
                        const silentAudio = new Audio();
                        silentAudio.src = 'data:audio/wav;base64,UklGRigAAABXQVZFZm10IBIAAAABAAEARKwAAIhYAQACABAAAABkYXRhAgAAAAEA';
                        silentAudio.volume = 0;
                        silentAudio.play().then(() => {
                            console.log('Audio system initialized for auto-play');
                            updateAudioStatus(true);
                        }).catch(e => {
                            console.log('Initial audio play failed (expected):', e.message);
                            updateAudioStatus(false);
                        });
                        
                        audioInitialized = true;
                        console.log('Audio initialization completed');
                    } catch (error) {
                        console.log('Audio initialization error:', error);
                        updateAudioStatus(false);
                    }
                }
            }
            
            // Update audio status indicator
            function updateAudioStatus(isReady) {
                const statusBadge = $('#audio-status');
                const statusText = $('#audio-status-text');
                
                if (isReady) {
                    statusBadge.show();
                    statusText.text('🔊 Audio ready for notifications');
                    statusText.removeClass('text-muted').addClass('text-success');
                } else {
                    statusBadge.hide();
                    statusText.text('⚠️ Click to enable audio notifications');
                    statusText.removeClass('text-success').addClass('text-warning');
                }
            }
            
            // Initialize audio on any user interaction (more comprehensive)
            $(document).one('click keydown touchstart mousedown', function() {
                initializeAudio();
                console.log('Audio initialized on user interaction');
            });
            
            // Also try to initialize when page becomes visible (for tab switching)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && !audioInitialized) {
                    initializeAudio();
                }
            });
            
            // Test sound button functionality
            $('#test-sound-btn').on('click', function() {
                initializeAudio(); // Ensure audio is initialized
                playNotificationSound();
                
                // Show a brief feedback message
                const btn = $(this);
                const originalText = btn.html();
                btn.html('<i class="fas fa-check"></i> Sound Played').addClass('btn-success').removeClass('btn-outline-secondary');
                
                setTimeout(function() {
                    btn.html(originalText).removeClass('btn-success').addClass('btn-outline-secondary');
                }, 2000);
            });
            
            // Volume control functionality
            $('#volume-control').on('input', function() {
                const volume = $(this).val();
                $('#volume-display').text(volume);
                localStorage.setItem('wardDashboard_volume', volume);
            });
            
            // Sound enable/disable functionality
            $('#enable-sound').on('change', function() {
                const enabled = $(this).is(':checked');
                localStorage.setItem('wardDashboard_soundEnabled', enabled);
                
                // Update test button state
                if (enabled) {
                    $('#test-sound-btn').removeClass('disabled').prop('disabled', false);
                } else {
                    $('#test-sound-btn').addClass('disabled').prop('disabled', true);
                }
            });
            
            // Load saved settings from localStorage
            function loadSoundSettings() {
                const savedVolume = localStorage.getItem('wardDashboard_volume');
                const savedSoundEnabled = localStorage.getItem('wardDashboard_soundEnabled');
                
                if (savedVolume !== null) {
                    $('#volume-control').val(savedVolume);
                    $('#volume-display').text(savedVolume);
                }
                
                if (savedSoundEnabled !== null) {
                    const isEnabled = savedSoundEnabled === 'true';
                    $('#enable-sound').prop('checked', isEnabled);
                    
                    if (!isEnabled) {
                        $('#test-sound-btn').addClass('disabled').prop('disabled', true);
                    }
                }
            }
            
            // Load settings on page load
            loadSoundSettings();
            
            // Simulate alert button for testing auto-play
            $('#simulate-alert-btn').on('click', function() {
                console.log('Simulating new alert notification...');
                
                // Ensure audio is initialized
                initializeAudio();
                
                // Simulate receiving a new alert
                window.newAlertReceived(1);
                
                // Play the notification sound automatically (like a real alert)
                playNotificationSound();
                
                // Show browser notification
                if (Notification.permission === 'granted') {
                    new Notification('🧪 Test Alert', {
                        body: 'This is a simulated patient alert for testing',
                        requireInteraction: false,
                        tag: 'test-alert'
                    });
                }
                
                // Provide feedback
                const btn = $(this);
                const originalText = btn.html();
                btn.html('<i class="fas fa-check"></i> Alert Simulated!').prop('disabled', true);
                
                setTimeout(() => {
                    btn.html(originalText).prop('disabled', false);
                    // Reset the alert count after test
                    window.newAlertReceived(0);
                }, 3000);
            });
            
            // Start polling for alerts every 10 seconds (faster response)
            setInterval(pollForNewAlerts, 10000);
            
            // Initial poll on page load
            pollForNewAlerts();
            
            // Request notification permission on page load
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    console.log('Notification permission:', permission);
                });
            }

            // Simple Notification System - No iframe needed!
            let currentAlertId = null;

            // Show notifications panel function
            window.showNotificationsPanel = function() {
                $('#notificationModal').modal('show');
                loadNotifications();
            };

            // Load notifications via AJAX
            function loadNotifications() {
                $('#notifications-list').html(`
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">Loading notifications...</p>
                    </div>
                `);

                $.ajax({
                    url: '{{ route("admin.beds.wards.alerts", $ward->id) }}',
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.alerts) {
                            displayNotifications(response.alerts);
                            updateModalStats(response);
                        } else {
                            showNoNotifications();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load notifications:', xhr.responseText);
                        $('#notifications-list').html(`
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                <p class="mt-2 text-danger">Failed to load notifications</p>
                                <button class="btn btn-outline-primary" onclick="loadNotifications()">Try Again</button>
                            </div>
                        `);
                    }
                });
            }

            // Display notifications
            function displayNotifications(alerts) {
                const container = $('#notifications-list');
                container.empty();

                if (alerts.length === 0) {
                    showNoNotifications();
                    return;
                }

                alerts.forEach(function(alert) {
                    const alertCard = createNotificationCard(alert);
                    container.append(alertCard);
                });
            }

            // Show no notifications message
            function showNoNotifications() {
                $('#notifications-list').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                        <h4 class="mt-3">No Active Alerts</h4>
                        <p class="text-muted">All patients are doing well!</p>
                    </div>
                `);
            }

            // Create notification card
            function createNotificationCard(alert) {
                const icons = {
                    'emergency': 'fa-exclamation-triangle',
                    'pain': 'fa-heartbeat',
                    'assistance': 'fa-hands-helping',
                    'water': 'fa-tint',
                    'bathroom': 'fa-toilet',
                    'food': 'fa-utensils',
                    'ews_critical': 'fa-exclamation-triangle',
                    'ews_high_risk': 'fa-chart-line',
                    'ews_warning': 'fa-chart-line'
                };
                
                const icon = icons[alert.alert_type] || 'fa-bell';
                const urgentClass = alert.is_urgent ? 'border-danger' : 'border-primary';
                const badgeClass = alert.is_urgent ? 'badge-danger' : 'badge-primary';
                const statusBadge = alert.status === 'new' ? 'badge-warning' : 'badge-secondary';
                
                return $(`
                    <div class="card mb-3 ${urgentClass}" data-alert-id="${alert.id}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-1 text-center">
                                    <span class="badge ${badgeClass} p-2">
                                        <i class="fas ${icon} fa-lg"></i>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        <strong>${alert.patient.name}</strong>
                                        <span class="text-muted">(Bed ${alert.bed.bed_number})</span>
                                    </h6>
                                    <p class="mb-1">${alert.message}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> ${formatTimeAgo(alert.created_at)}
                                        ${alert.is_urgent ? '<span class="badge badge-danger ml-2">URGENT</span>' : ''}
                                    </small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <span class="badge ${statusBadge}">
                                        ${alert.status.charAt(0).toUpperCase() + alert.status.slice(1)}
                                    </span>
                                    <br><small class="text-muted">${alert.alert_type.charAt(0).toUpperCase() + alert.alert_type.slice(1)}</small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <button type="button" class="btn btn-success btn-sm respond-to-alert" 
                                            data-alert-id="${alert.id}"
                                            data-patient-name="${alert.patient.name}"
                                            data-bed-number="${alert.bed.bed_number}"
                                            data-alert-message="${alert.message}">
                                        <i class="fas fa-reply"></i> Respond
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

            // Format time ago
            function formatTimeAgo(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                
                if (diffMins < 1) return 'Just now';
                if (diffMins < 60) return `${diffMins} min ago`;
                if (diffMins < 1440) return `${Math.floor(diffMins / 60)} hour ago`;
                return `${Math.floor(diffMins / 1440)} day ago`;
            }

            // Update modal statistics
            function updateModalStats(data) {
                const alerts = data.alerts || [];
                const newCount = alerts.filter(a => a.status === 'new').length;
                const totalCount = alerts.length;
                
                $('#modal-new-count').text(newCount);
                $('#modal-total-count').text(totalCount);
                $('#modal-responses-count').text(data.responses_count || 0);
                $('#modal-history-count').text(data.resolved_alerts_count || 0);
            }

            // Load alert history
            function loadHistory() {
                $('#history-list').html(`
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">Loading history...</p>
                    </div>
                `);

                $.ajax({
                    url: '{{ route("admin.beds.wards.alerts.history", $ward->id) }}',
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.history) {
                            displayHistory(response.history);
                        } else {
                            showNoHistory();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load history:', xhr.responseText);
                        $('#history-list').html(`
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                <p class="mt-2 text-danger">Failed to load history</p>
                                <button class="btn btn-outline-primary" onclick="loadHistory()">Try Again</button>
                            </div>
                        `);
                    }
                });
            }

            // Display history
            function displayHistory(alerts) {
                const container = $('#history-list');
                container.empty();

                if (alerts.length === 0) {
                    showNoHistory();
                    return;
                }

                alerts.forEach(function(alert) {
                    const historyCard = createHistoryCard(alert);
                    container.append(historyCard);
                });
            }

            // Show no history message
            function showNoHistory() {
                $('#history-list').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted"></i>
                        <h4 class="mt-3">No History</h4>
                        <p class="text-muted">No resolved alerts found.</p>
                    </div>
                `);
            }

            // Create history card
            function createHistoryCard(alert) {
                const icons = {
                    'emergency': 'fa-exclamation-triangle',
                    'pain': 'fa-heartbeat',
                    'assistance': 'fa-hands-helping',
                    'water': 'fa-tint',
                    'bathroom': 'fa-toilet',
                    'food': 'fa-utensils',
                    'ews_critical': 'fa-exclamation-triangle',
                    'ews_high_risk': 'fa-chart-line',
                    'ews_warning': 'fa-chart-line'
                };
                
                const icon = icons[alert.alert_type] || 'fa-bell';
                const badgeClass = alert.is_urgent ? 'badge-danger' : 'badge-primary';
                
                // Get response info
                const response = alert.responses && alert.responses.length > 0 ? alert.responses[0] : null;
                const nurseName = response && response.nurse ? response.nurse.name : 'Unknown';
                const responseTime = response ? formatTimeAgo(response.created_at) : 'N/A';
                
                return $(`
                    <div class="card mb-3 border-success">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-1 text-center">
                                    <span class="badge ${badgeClass} p-2">
                                        <i class="fas ${icon} fa-lg"></i>
                                    </span>
                                </div>
                                <div class="col-md-5">
                                    <h6 class="mb-1">
                                        <strong>${alert.patient.name}</strong>
                                        <span class="text-muted">(Bed ${alert.bed.bed_number})</span>
                                    </h6>
                                    <p class="mb-1">${alert.message}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> Alert: ${formatTimeAgo(alert.created_at)}
                                        ${alert.is_urgent ? '<span class="badge badge-danger ml-2">URGENT</span>' : ''}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge badge-success">Resolved</span>
                                    <br><small class="text-muted">${alert.alert_type.charAt(0).toUpperCase() + alert.alert_type.slice(1)}</small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-success">
                                        <i class="fas fa-user-nurse"></i> ${nurseName}<br>
                                        <i class="fas fa-reply"></i> Responded ${responseTime}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

            // Tab switching handlers
            $('#active-alerts-tab').click(function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                $('.tab-pane').removeClass('show active');
                $('#active-alerts').addClass('show active');
                
                // Load active alerts if not already loaded
                if ($('#notifications-list').children().length === 0 || $('#notifications-list').find('.text-center').length > 0) {
                    loadNotifications();
                }
            });

            $('#history-tab').click(function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                $('.tab-pane').removeClass('show active');
                $('#history').addClass('show active');
                
                // Load history when tab is clicked
                loadHistory();
            });

            // Event handlers for new notification system
            $(document).on('click', '.respond-to-alert', function() {
                currentAlertId = $(this).data('alert-id');
                const patientName = $(this).data('patient-name');
                const bedNumber = $(this).data('bed-number');
                const alertMessage = $(this).data('alert-message');
                
                $('#response-patient-name').text(patientName);
                $('#response-bed-number').text(bedNumber);
                $('#response-alert-message').text(alertMessage);
                $('#response-message').val('Your alert has been acknowledged by nursing staff. We are taking care of your request.');
                
                $('#responseModal').modal('show');
            });

            // Send response handler
            $('#send-response').click(function() {
                if (!currentAlertId) return;
                
                const btn = $(this);
                const originalHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);
                
                const responseMessage = $('#response-message').val().trim();
                if (!responseMessage) {
                    alert('Please enter a response message');
                    btn.html(originalHtml).prop('disabled', false);
                    return;
                }
                
                $.ajax({
                    url: '{{ route("admin.beds.wards.alerts.respond", ":alertId") }}'.replace(':alertId', currentAlertId),
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        response_message: responseMessage
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Remove the alert card with animation
                            $(`[data-alert-id="${currentAlertId}"]`).fadeOut(400, function() {
                                $(this).remove();
                                // Reload notifications to update counts
                                loadNotifications();
                                // If history tab exists and has been loaded, refresh it too
                                if ($('#history-list').children().length > 0) {
                                    loadHistory();
                                }
                            });
                            
                            $('#responseModal').modal('hide');
                            
                            // Show success feedback
                            showToast('Response sent successfully! Patient has been notified.', 'success');
                            
                            // Update main dashboard alert count
                            pollForNewAlerts();
                            
                        } else {
                            showToast('Failed to send response', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Response failed:', xhr.responseText);
                        showToast('Failed to send response. Please try again.', 'error');
                    },
                    complete: function() {
                        btn.html(originalHtml).prop('disabled', false);
                        currentAlertId = null;
                    }
                });
            });

            // Refresh notifications
            $('#refresh-notifications').click(function() {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...').prop('disabled', true);
                
                // Refresh active alerts
                loadNotifications();
                
                // If history tab is active, also refresh history
                if ($('#history-tab').hasClass('active')) {
                    loadHistory();
                }
                
                setTimeout(() => {
                    btn.html(originalHtml).prop('disabled', false);
                }, 1000);
            });

            // Create test alert
            $('#create-test-alert').click(function() {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Creating...').prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("admin.beds.wards.create-test-alert", $ward->id) }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Test alert created successfully!', 'success');
                            loadNotifications(); // Refresh the list
                            pollForNewAlerts(); // Update main dashboard
                        } else {
                            showToast('Failed to create test alert', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Test alert creation failed:', xhr.responseText);
                        showToast('Failed to create test alert', 'error');
                    },
                    complete: function() {
                        btn.html(originalHtml).prop('disabled', false);
                    }
                });
            });

            // Simple toast notification function
            function showToast(message, type = 'info') {
                const toastClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
                const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle');
                
                const toast = $(`
                    <div class="alert ${toastClass} alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                        <i class="fas ${icon} mr-2"></i>
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
                
                $('body').append(toast);
                setTimeout(() => {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 4000);
            }
            
            // Prevent AdminLTE iframe conflicts - Enhanced version
            $('#notificationModal').on('show.bs.modal', function () {
                const modal = $(this);
                const iframe = modal.find('iframe');
                
                // Set iframe attributes before AdminLTE can access them
                iframe.attr('data-auto-iframe-mode', 'false');
                iframe.attr('data-iframe-mode', 'false');
                
                // Completely disable AdminLTE iframe handling for this modal
                if (typeof window.AdminLTE !== 'undefined' && window.AdminLTE.IFrame) {
                    console.log('Disabling AdminLTE iframe handling...');
                    
                    // Store original functions to restore later
                    if (!window._originalAdminLTE) {
                        window._originalAdminLTE = {
                            _initFrameElement: window.AdminLTE.IFrame._initFrameElement,
                            _jQueryInterface: window.AdminLTE.IFrame._jQueryInterface
                        };
                    }
                    
                    // Override the problematic functions to prevent null reference errors
                    window.AdminLTE.IFrame._initFrameElement = function(element) {
                        // Check if element exists and has the required properties
                        if (!element || !element.getAttribute) {
                            console.log('AdminLTE iframe init blocked - invalid element');
                            return;
                        }
                        
                        // Only process elements that are not our notification iframe
                        if (element.id === 'notification-iframe' || element.closest('#notificationModal')) {
                            console.log('AdminLTE iframe init blocked - notification iframe');
                            return;
                        }
                        
                        // Call original function for other iframes
                        if (window._originalAdminLTE && window._originalAdminLTE._initFrameElement) {
                            return window._originalAdminLTE._initFrameElement.call(this, element);
                        }
                    };
                    
                    // Override jQuery interface to prevent errors
                    window.AdminLTE.IFrame._jQueryInterface = function(config) {
                        const element = this[0];
                        if (!element || element.id === 'notification-iframe' || element.closest('#notificationModal')) {
                            console.log('AdminLTE jQuery interface blocked for notification iframe');
                            return this;
                        }
                        
                        // Call original function for other elements
                        if (window._originalAdminLTE && window._originalAdminLTE._jQueryInterface) {
                            return window._originalAdminLTE._jQueryInterface.call(this, config);
                        }
                        return this;
                    };
                }
                
                // Additional safety: remove any iframe-related classes that AdminLTE might target
                iframe.removeClass('iframe-mode').removeClass('auto-iframe-mode');
                
                // Prevent any existing AdminLTE event handlers on this iframe
                iframe.off('.adminlte');
            });
            
            // Separate iframe refresh handler
            $('#notificationModal').on('shown.bs.modal', function () {
                // Refresh iframe to get latest alerts
                const iframe = $(this).find('iframe');
                const originalSrc = '{{ url('/admin/beds/wards/'.$ward->id.'/notification-demo') }}';
                
                // Small delay to ensure modal is fully rendered
                setTimeout(() => {
                    iframe.attr('src', originalSrc + '?t=' + Date.now()); // Add timestamp to force refresh
                    console.log('Notification iframe refreshed');
                }, 100);
            });
            
            // Global error handler for iframe-related errors
            window.addEventListener('error', function(event) {
                if (event.message && event.message.includes('autoIframeMode')) {
                    console.log('Caught AdminLTE iframe error, preventing it from bubbling up');
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            });
            
            // Additional protection: Override problematic jQuery events
            $(document).on('DOMContentLoaded', function() {
                // Prevent AdminLTE from auto-initializing on our notification iframe
                if (typeof $.fn.IFrame !== 'undefined') {
                    const originalIFrame = $.fn.IFrame;
                    $.fn.IFrame = function(option) {
                        // Skip our notification iframe
                        if (this.attr('id') === 'notification-iframe' || this.closest('#notificationModal').length > 0) {
                            console.log('Skipping AdminLTE IFrame initialization for notification iframe');
                            return this;
                        }
                        // Call original for other elements
                        return originalIFrame.call(this, option);
                    };
                }
            });
        });

        function openAdmitPatientModal(url) {
            // Create modal if it doesn't exist
            if (!$('#admitPatientModal').length) {
                $('body').append(`
                    <div class="modal fade" id="admitPatientModal" tabindex="-1" role="dialog" aria-labelledby="admitPatientModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="admitPatientModalLabel">Admit Patient</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <iframe id="admitPatientIframe" style="width: 100%; height: 600px; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

            // Set iframe source and show modal
            $('#admitPatientIframe').attr('src', url);
            $('#admitPatientModal').modal('show');
        }

        // Function to close the modal (called from iframe)
        window.closeAdmitPatientModal = function() {
            $('#admitPatientModal').modal('hide');
        };

        // Clear iframe src when modal is hidden
        $('#admitPatientModal').on('hidden.bs.modal', function () {
            $('#admitPatientIframe').attr('src', 'about:blank');
        });

        // Enable tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            
            // Set current date and time
            function updateDateTime() {
                var now = new Date();
                var options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                var formattedDate = now.toLocaleDateString('en-US', options);
                $('#current-date-time-display').text(formattedDate);
                $('#current-datetime').removeClass('d-none');
            }
            
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            // Return patient button click handler
            $('.return-patient-btn').on('click', function() {
                var movementId = $(this).data('movement-id');
                var patientName = $(this).data('patient-name');
                var btn = $(this);
                
                if (confirm('Are you sure you want to return the patient back to this bed?')) {
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');
                    
                    $.ajax({
                        url: '/movements/' + movementId + '/return',
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Show success message
                            toastr.success('Patient has been returned successfully');
                            // Reload the page to refresh the bed status
                            location.reload();
                        },
                        error: function(xhr) {
                            // Show error message
                            toastr.error('Failed to return patient. Please try again.');
                            btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt mr-1"></i> Return Patient');
                        }
                    });
                }
            });
        });
    </script>
@stop 