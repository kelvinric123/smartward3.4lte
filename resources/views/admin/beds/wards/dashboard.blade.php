@extends('adminlte::page')

@section('title', $ward->name . ' Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $ward->name }}</h1>
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
                                <a href="{{ route('admin.beds.wards.show', $ward) }}" class="btn btn-default">
                                    <i class="fas fa-info-circle"></i> Ward Details
                                </a>
                                <a href="{{ route('admin.beds.beds.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Bed
                                </a>
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
                                            <span>
                                                MRN: {{ $bed->patient ? ($bed->patient->mrn ?: 'Not available') : 'Not assigned' }}
                                            </span>
                                        </div>
                                        <div class="card-body p-3">
                                            @if($bed->patient)
                                                <p class="mb-1">
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
                                                </p>
                                            @endif
                                            
                                            @if($bed->consultant)
                                                <p class="mb-1">
                                                    <i class="fas fa-user-md"></i> {{ $bed->consultant->name }}
                                                </p>
                                            @else
                                                <p class="mb-1">
                                                    <i class="fas fa-user-md"></i> Not assigned
                                                </p>
                                            @endif
                                            
                                            @if($bed->status == 'occupied' && $bed->patient)
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
                                                
                                                @if(isset($activeMovements[$bed->patient_id]))
                                                    <div class="alert alert-info p-1 mb-2 text-center">
                                                        <small><i class="fas fa-external-link-alt"></i> At {{ $activeMovements[$bed->patient_id]->to_service_location }}</small>
                                                    </div>
                                                @endif
                                                
                                                <div class="btn-group btn-group-sm w-100 mt-2">
                                                    <a href="{{ route('admin.beds.wards.patient.details', ['ward' => $ward, 'bedId' => $bed->id]) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-list"></i>
                                                    </a>
                                                    <a href="{{ route('admin.vital-signs.flipbox-trend', $bed->patient->id) }}" class="btn btn-outline-secondary text-danger">
                                                        <i class="fas fa-heart"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.wards.patient.details', ['ward' => $ward, 'bedId' => $bed->id]) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                </div>
                                            @elseif($bed->status == 'available')
                                                <p class="mb-1">
                                                    <i class="fas fa-bed"></i> No Patient
                                                </p>
                                                <div class="mt-2">
                                                    <a href="{{ route('admin.beds.wards.admit', ['ward' => $ward, 'bedId' => $bed->id]) }}" class="btn btn-success btn-block">
                                                        <i class="fas fa-user-plus"></i> Admit Patient
                                                    </a>
                                                </div>
                                            @else
                                                <p class="mb-1">
                                                    <i class="fas fa-bed"></i> No Patient
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
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark">
                    <div class="card-body">
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
                <div class="mb-3 text-right">
                    <a href="{{ route('admin.beds.wards.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to Wards
                    </a>
                    <a href="{{ route('admin.beds.beds.index') }}" class="btn btn-info">
                        <i class="fas fa-list"></i> View All Beds
                    </a>
                    <button class="btn btn-success">
                        <i class="fas fa-print"></i> Print Ward Report
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
        body.fullscreen-mode .main-sidebar {
            display: none !important;
        }
        
        body.fullscreen-mode .content-wrapper {
            margin-left: 0 !important;
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
            
            // Toggle fullscreen mode
            fullscreenToggle.on('click', function(e) {
                e.preventDefault();
                body.toggleClass('fullscreen-mode');
                
                // Toggle icon
                const icon = $(this).find('i');
                if (body.hasClass('fullscreen-mode')) {
                    icon.removeClass('fa-expand').addClass('fa-compress');
                } else {
                    icon.removeClass('fa-compress').addClass('fa-expand');
                }
            });
            
            // Check URL for fullscreen parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('fullscreen') === 'true') {
                body.addClass('fullscreen-mode');
                fullscreenToggle.find('i').removeClass('fa-expand').addClass('fa-compress');
            }
        });
    </script>
@stop 