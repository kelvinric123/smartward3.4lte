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
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#notificationModal">
                                    <i class="fas fa-bell"></i> Notification
                                </button>
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
                                                    
                                                    <!-- Add empty space to push buttons to bottom, mimicking the layout of other beds -->
                                                    <div style="min-height: 80px;"></div>
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
    
    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Patient to Nurse Notifications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ url('/admin/beds/wards/'.$ward->id.'/notification-demo') }}" style="width: 100%; height: 500px; border: none;"></iframe>
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
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
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
    </script>
@stop 