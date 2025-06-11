@extends('adminlte::page')

@section('title', 'Patient Vital Signs - Card View')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Patient Vital Signs - Card View</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vital Signs</li>
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
        <a class="nav-link" href="#" id="fullscreen-toggle" role="button" title="Toggle Fullscreen">
            <i class="fas fa-expand"></i>
        </a>
    </li>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Control Panel -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-heartbeat mr-1"></i>
                            Patients with Vital Signs
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.vital-signs.index', ['view' => 'table']) }}" class="btn {{ request('view') === 'table' ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm">
                                    <i class="fas fa-list"></i> Table View
                                </a>
                                <a href="{{ route('admin.vital-signs.index', ['view' => 'card']) }}" class="btn {{ !request('view') || request('view') === 'card' ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm">
                                    <i class="fas fa-th-large"></i> Card View
                                </a>
                            </div>
                            <button id="refresh-btn" class="btn btn-info btn-sm ml-2" title="Refresh Data">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                            <button id="sound-toggle" class="btn btn-outline-secondary btn-sm" title="Toggle Sound Notifications">
                                <i class="fas fa-volume-up"></i>
                            </button>
                            <a href="{{ route('admin.vital-signs.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Record New Vital Signs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.vital-signs.index') }}">
                                    <input type="hidden" name="view" value="card">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search by patient name or MRN..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} patients
                                </small>
                                <br>
                                <small id="update-status" class="text-info">
                                    <i class="fas fa-sync-alt fa-spin"></i> Checking for updates...
                                </small>
                            </div>
                        </div>
                        
                        <!-- Patient Cards -->
                        @if($patients->count() > 0)
                            <div class="row">
                                @foreach($patients as $patient)
                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                                        <div class="card h-100 shadow-sm patient-vital-card" 
                                             style="transition: all 0.3s ease;" 
                                             data-patient-id="{{ $patient->id }}"
                                             data-vital-timestamp="{{ $patient->latestVitalSigns ? $patient->latestVitalSigns->recorded_at->toISOString() : '' }}">
                                            <div class="card-header bg-gradient-info text-white text-center py-2">
                                                <h6 class="card-title mb-0" style="font-size: 0.9rem;">
                                                    <i class="fas fa-user-injured mr-1"></i>
                                                    {{ Str::limit($patient->name, 18) }}
                                                </h6>
                                                @if($patient->mrn)
                                                    <small class="text-light" style="font-size: 0.75rem;">MRN: {{ $patient->mrn }}</small>
                                                @endif
                                            </div>
                                            <div class="card-body p-2" style="font-size: 0.8rem;">
                                                @if($patient->latestVitalSigns)
                                                    @php
                                                        $vital = $patient->latestVitalSigns;
                                                        $ewsClass = 'success';
                                                        if($vital->total_ews >= 7) $ewsClass = 'danger';
                                                        elseif($vital->total_ews >= 5) $ewsClass = 'warning';
                                                        elseif($vital->total_ews >= 3) $ewsClass = 'info';
                                                    @endphp
                                                    
                                                    <div class="text-center mb-2">
                                                        <span class="badge badge-{{ $ewsClass }} px-2 py-1">
                                                            EWS: {{ $vital->total_ews }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="vital-signs-grid">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="text-muted">Temp:</span>
                                                            <strong>{{ $vital->temperature ? number_format($vital->temperature, 1) . '°C' : '-' }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="text-muted">HR:</span>
                                                            <strong>{{ $vital->heart_rate ? $vital->heart_rate . ' bpm' : '-' }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="text-muted">RR:</span>
                                                            <strong>{{ $vital->respiratory_rate ? $vital->respiratory_rate . ' bpm' : '-' }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="text-muted">BP:</span>
                                                            <strong>{{ $vital->systolic_bp && $vital->diastolic_bp ? $vital->systolic_bp . '/' . $vital->diastolic_bp : '-' }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="text-muted">SpO2:</span>
                                                            <strong>{{ $vital->oxygen_saturation ? $vital->oxygen_saturation . '%' : '-' }}</strong>
                                                        </div>
                                                        @if($vital->consciousness)
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="text-muted">AVPU:</span>
                                                            <strong>{{ $vital->consciousness }}</strong>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="text-center mt-2 mb-1">
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ $vital->recorded_at->diffForHumans() }}
                                                        </small>
                                                        @if($vital->recorder)
                                                            <br>
                                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                                By: {{ Str::limit($vital->recorder->name, 15) }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-heartbeat fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">No recent vital signs</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-footer p-1">
                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                    <a href="{{ route('admin.vital-signs.index', ['patient_id' => $patient->id]) }}" class="btn btn-outline-info btn-sm" title="View Records">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.vital-signs.trend', $patient->id) }}" class="btn btn-outline-success btn-sm" title="View Trend">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-primary btn-sm" title="Record Vital Signs">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-heartbeat fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No patients with vital signs found</h5>
                                <p class="text-muted">Try adjusting your search criteria or record some vital signs.</p>
                                <a href="{{ route('admin.vital-signs.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Record New Vital Signs
                                </a>
                            </div>
                        @endif
                        
                        <!-- Pagination -->
                        @if($patients->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $patients->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
.patient-vital-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
}

.patient-vital-card .card-header {
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

.vital-signs-grid {
    background: #f8f9fa;
    border-radius: 4px;
    padding: 8px;
}

.patient-vital-card .btn-group .btn {
    border-radius: 0;
    font-size: 0.75rem;
}

.patient-vital-card .btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.patient-vital-card .btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

/* Shake animation for new vital signs */
@keyframes shake {
    0% { transform: translate(1px, 1px) rotate(0deg); }
    10% { transform: translate(-1px, -2px) rotate(-1deg); }
    20% { transform: translate(-3px, 0px) rotate(1deg); }
    30% { transform: translate(3px, 2px) rotate(0deg); }
    40% { transform: translate(1px, -1px) rotate(1deg); }
    50% { transform: translate(-1px, 2px) rotate(-1deg); }
    60% { transform: translate(-3px, 1px) rotate(0deg); }
    70% { transform: translate(3px, 1px) rotate(-1deg); }
    80% { transform: translate(-1px, -1px) rotate(1deg); }
    90% { transform: translate(1px, 2px) rotate(0deg); }
    100% { transform: translate(1px, -2px) rotate(-1deg); }
}

.shake-animation {
    animation: shake 0.5s;
    animation-iteration-count: 10;
    border: 3px solid #ff6b6b !important;
    box-shadow: 0 0 15px rgba(255, 107, 107, 0.5) !important;
}

/* New vital sign indicator */
.new-vital-indicator {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff6b6b;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    z-index: 1000;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Card positioning for the indicator */
.patient-vital-card {
    position: relative;
}

@media (max-width: 576px) {
    .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (min-width: 1400px) {
    .col-lg-2 {
        flex: 0 0 20%;
        max-width: 20%;
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

/* Scrollable content container */
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

/* Optimize card layout in fullscreen mode */
body.fullscreen-mode .patient-vital-card {
    margin-bottom: 1rem;
}

/* 6 cards per row in fullscreen mode for better space utilization */
@media (min-width: 1200px) {
    body.fullscreen-mode .col-lg-2 {
        flex: 0 0 16.666667%;
        max-width: 16.666667%;
    }
}
</style>
@stop

@section('js')
<script>
$(function() {
    // Store initial vital sign timestamps for comparison
    let vitalSignTimestamps = {};
    let soundEnabled = localStorage.getItem('vitalSignSoundEnabled') !== 'false'; // Default to true
    
    // Initialize sound toggle button
    updateSoundToggleButton();
    
    // Initialize timestamps
    $('.patient-vital-card').each(function() {
        const patientId = $(this).data('patient-id');
        const timestamp = $(this).data('vital-timestamp');
        if (patientId && timestamp) {
            vitalSignTimestamps[patientId] = timestamp;
        }
    });
    
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
            fullscreenToggle.attr('title', 'Exit Fullscreen');
            localStorage.setItem('vitalSignsFullscreen', 'true');
        } else {
            body.removeClass('fullscreen-mode');
            fullscreenToggle.find('i').removeClass('fa-compress').addClass('fa-expand');
            fullscreenToggle.attr('title', 'Toggle Fullscreen');
            localStorage.removeItem('vitalSignsFullscreen');
        }
    }
    
    // Toggle fullscreen mode
    fullscreenToggle.on('click', function(e) {
        e.preventDefault();
        const isCurrentlyFullscreen = body.hasClass('fullscreen-mode');
        setFullscreenMode(!isCurrentlyFullscreen);
    });
    
    // Check localStorage for fullscreen state
    if (localStorage.getItem('vitalSignsFullscreen') === 'true') {
        setFullscreenMode(true);
    }
    
    // Countdown variables
    let countdownInterval;
    let currentCountdown = 10;
    
    // Function to start countdown timer
    function startCountdown() {
        currentCountdown = 10;
        updateCountdownDisplay();
        
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        countdownInterval = setInterval(function() {
            currentCountdown--;
            updateCountdownDisplay();
            
            if (currentCountdown <= 0) {
                clearInterval(countdownInterval);
                checkForNewVitalSigns();
            }
        }, 1000);
    }
    
    // Function to update countdown display
    function updateCountdownDisplay() {
        $('#update-status').html(`<i class="fas fa-clock"></i> Next check in ${currentCountdown}s`)
                           .removeClass('text-info text-warning text-danger')
                           .addClass('text-success');
    }
    
    // Function to check for new vital signs
    function checkForNewVitalSigns() {
        // Clear any existing countdown
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        // Update status indicator
        $('#update-status').html('<i class="fas fa-sync-alt fa-spin"></i> Checking for updates...')
                           .removeClass('text-success text-warning text-danger')
                           .addClass('text-info');
        
        $.ajax({
            url: '{{ route("admin.vital-signs.check-updates") }}',
            method: 'GET',
            data: {
                view: 'card',
                search: '{{ request("search") }}'
            },
            success: function(response) {
                if (response.success && response.patients) {
                    let updatesFound = false;
                    response.patients.forEach(function(patient) {
                        const patientId = patient.id;
                        const cardElement = $(`.patient-vital-card[data-patient-id="${patientId}"]`);
                        
                        if (cardElement.length && patient.latest_vital_signs) {
                            const newTimestamp = patient.latest_vital_signs.recorded_at;
                            const oldTimestamp = vitalSignTimestamps[patientId];
                            
                            // Check if there's a new vital sign
                            if (oldTimestamp && newTimestamp !== oldTimestamp) {
                                console.log(`New vital sign detected for patient ${patientId}`);
                                updatesFound = true;
                                
                                // Update the stored timestamp
                                vitalSignTimestamps[patientId] = newTimestamp;
                                
                                // Add new vital indicator
                                if (!cardElement.find('.new-vital-indicator').length) {
                                    cardElement.append('<div class="new-vital-indicator">NEW</div>');
                                }
                                
                                // Trigger shake animation
                                cardElement.addClass('shake-animation');
                                
                                // Play notification sound (optional)
                                if (soundEnabled) {
                                    try {
                                        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LKeSUFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhCSB9/9SUP');
                                        audio.volume = 0.3;
                                        audio.play().catch(e => console.log('Audio play failed:', e));
                                    } catch (e) {
                                        console.log('Audio notification not supported');
                                    }
                                }
                                
                                // Update the card content
                                updateCardContent(cardElement, patient);
                                
                                // Remove shake animation after 5 seconds
                                setTimeout(function() {
                                    cardElement.removeClass('shake-animation');
                                    
                                    // Remove new indicator after 10 seconds
                                    setTimeout(function() {
                                        cardElement.find('.new-vital-indicator').fadeOut(500, function() {
                                            $(this).remove();
                                        });
                                    }, 5000);
                                }, 5000);
                            } else if (!oldTimestamp && newTimestamp) {
                                // Store timestamp for new cards
                                vitalSignTimestamps[patientId] = newTimestamp;
                            }
                        }
                    });
                    
                    // Update status based on findings
                    if (updatesFound) {
                        $('#update-status').html('<i class="fas fa-exclamation-triangle"></i> New data found!')
                                           .removeClass('text-info text-success text-danger')
                                           .addClass('text-warning');
                        // Start countdown after showing new data message for 3 seconds
                        setTimeout(startCountdown, 3000);
                    } else {
                        // Start countdown immediately if no updates found
                        startCountdown();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error checking for updates:', error);
                $('#update-status').html('<i class="fas fa-times"></i> Update failed')
                                   .removeClass('text-info text-success text-warning')
                                   .addClass('text-danger');
                // Start countdown even after error, after 3 seconds
                setTimeout(startCountdown, 3000);
            }
        });
    }
    
    // Function to update card content
    function updateCardContent(cardElement, patient) {
        if (!patient.latest_vital_signs) return;
        
        const vital = patient.latest_vital_signs;
        let ewsClass = 'success';
        if (vital.total_ews >= 7) ewsClass = 'danger';
        else if (vital.total_ews >= 5) ewsClass = 'warning';
        else if (vital.total_ews >= 3) ewsClass = 'info';
        
        // Update EWS badge
        cardElement.find('.badge').removeClass('badge-success badge-info badge-warning badge-danger')
                   .addClass(`badge-${ewsClass}`)
                   .text(`EWS: ${vital.total_ews}`);
        
        // Update vital signs values
        const vitalGrid = cardElement.find('.vital-signs-grid');
        vitalGrid.find('.d-flex').each(function() {
            const label = $(this).find('.text-muted').text();
            const valueElement = $(this).find('strong');
            
            switch(label) {
                case 'Temp:':
                    valueElement.text(vital.temperature ? parseFloat(vital.temperature).toFixed(1) + '°C' : '-');
                    break;
                case 'HR:':
                    valueElement.text(vital.heart_rate ? vital.heart_rate + ' bpm' : '-');
                    break;
                case 'RR:':
                    valueElement.text(vital.respiratory_rate ? vital.respiratory_rate + ' bpm' : '-');
                    break;
                case 'BP:':
                    valueElement.text(vital.systolic_bp && vital.diastolic_bp ? 
                                    vital.systolic_bp + '/' + vital.diastolic_bp : '-');
                    break;
                case 'SpO2:':
                    valueElement.text(vital.oxygen_saturation ? vital.oxygen_saturation + '%' : '-');
                    break;
                case 'AVPU:':
                    valueElement.text(vital.consciousness || '-');
                    break;
            }
        });
        
        // Update timestamp
        const timeElement = cardElement.find('.text-center .text-muted').first();
        const recordedAt = new Date(vital.recorded_at);
        const timeAgo = getTimeAgo(recordedAt);
        timeElement.html('<i class="fas fa-clock mr-1"></i>' + timeAgo);
        
        // Update recorder info if available
        if (vital.recorder) {
            const recorderElement = timeElement.next('br').next('.text-muted');
            if (recorderElement.length) {
                recorderElement.text('By: ' + (vital.recorder.name.length > 15 ? 
                                             vital.recorder.name.substring(0, 15) + '...' : 
                                             vital.recorder.name));
            }
        }
        
        // Update the data attribute
        cardElement.attr('data-vital-timestamp', vital.recorded_at);
    }
    
    // Function to calculate time ago
    function getTimeAgo(date) {
        const now = new Date();
        const diffInMs = now - date;
        const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
        const diffInHours = Math.floor(diffInMinutes / 60);
        const diffInDays = Math.floor(diffInHours / 24);
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes} minutes ago`;
        if (diffInHours < 24) return `${diffInHours} hours ago`;
        return `${diffInDays} days ago`;
    }
    
    // Start initial countdown
    setTimeout(startCountdown, 2000); // Start countdown after 2 seconds
    
    // Full page refresh every 5 minutes as backup
    setTimeout(function() {
        window.location.reload();
    }, 300000); // 5 minutes
    
    // Add tooltip for buttons
    $('[title]').tooltip();
    
    // Handle manual refresh button
    $('#refresh-btn').on('click', function() {
        // Clear any existing countdown
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        checkForNewVitalSigns();
        $(this).find('i').addClass('fa-spin');
        setTimeout(() => {
            $(this).find('i').removeClass('fa-spin');
        }, 1000);
    });
    
    // Handle sound toggle button
    $('#sound-toggle').on('click', function() {
        soundEnabled = !soundEnabled;
        localStorage.setItem('vitalSignSoundEnabled', soundEnabled);
        updateSoundToggleButton();
    });
    
    // Function to update sound toggle button appearance
    function updateSoundToggleButton() {
        const btn = $('#sound-toggle');
        if (soundEnabled) {
            btn.removeClass('btn-outline-secondary').addClass('btn-success');
            btn.find('i').removeClass('fa-volume-mute').addClass('fa-volume-up');
            btn.attr('title', 'Sound notifications enabled - Click to disable');
        } else {
            btn.removeClass('btn-success').addClass('btn-outline-secondary');
            btn.find('i').removeClass('fa-volume-up').addClass('fa-volume-mute');
            btn.attr('title', 'Sound notifications disabled - Click to enable');
        }
    }
    
    // Initialize status to show system is ready
    setTimeout(function() {
        $('#update-status').html('<i class="fas fa-check"></i> System ready...')
                           .removeClass('text-info')
                           .addClass('text-success');
    }, 1000);
});
</script>
@stop 