@extends('layouts.iframe')

@section('title', 'Nurses Passover - ' . $ward->name . ' - Bed ' . $bed->bed_number)

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            min-height: fit-content;
            background: var(--bs-body-bg, #f4f6f9);
            overflow-x: hidden;
        }
        .container-fluid {
            padding: 0;
            min-height: fit-content;
            display: flex;
            flex-direction: column;
        }
        .passover-card {
            border: none;
            margin: 0;
            border-radius: 0;
            flex: 1;
            background: var(--bs-body-bg, #ffffff);
        }
        .card-body {
            padding: 15px;
            background: var(--bs-body-bg, #ffffff);
        }
        
        /* Section styling following dashboard design */
        .passover-section {
            background: var(--bs-body-bg, #ffffff);
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: 0.5rem;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .section-header {
            background: var(--bs-gray-100, #f8f9fa);
            color: var(--bs-body-color, #495057);
            padding: 12px 15px;
            margin: 0;
            border-bottom: 1px solid var(--bs-border-color, #dee2e6);
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .section-content {
            padding: 15px;
            background: var(--bs-body-bg, #ffffff);
        }
        
        /* Dark mode support */
        [data-bs-theme="dark"] body,
        body.dark-mode {
            background: #2c3e50 !important;
        }
        
        [data-bs-theme="dark"] .passover-card,
        [data-bs-theme="dark"] .card-body,
        [data-bs-theme="dark"] .section-content,
        body.dark-mode .passover-card,
        body.dark-mode .card-body,
        body.dark-mode .section-content {
            background: #2c3e50 !important;
            color: #ecf0f1 !important;
        }
        
        [data-bs-theme="dark"] .passover-section,
        body.dark-mode .passover-section {
            background: #2c3e50 !important;
            border: 1px solid #34495e !important;
        }
        
        [data-bs-theme="dark"] .section-header,
        body.dark-mode .section-header {
            background: #34495e !important;
            color: #ecf0f1 !important;
            border-bottom: 1px solid #2c3e50 !important;
        }
        
        /* Nurse item styling */
        .nurse-item {
            background: var(--bs-body-bg, #ffffff);
            border: 1px solid var(--bs-border-color, #e3e6f0);
            border-radius: 0.375rem;
            padding: 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.2s ease;
        }
        
        .nurse-item:hover {
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        
        [data-bs-theme="dark"] .nurse-item,
        body.dark-mode .nurse-item {
            background: #34495e !important;
            border: 1px solid #2c3e50 !important;
            color: #ecf0f1 !important;
        }
        
        .nurse-item:last-child {
            margin-bottom: 0;
        }
        .nurse-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
            font-size: 14px;
        }
        .nurse-info {
            flex: 1;
        }
        .nurse-name {
            font-weight: 600;
            color: var(--bs-body-color, #2c3e50);
            margin-bottom: 2px;
        }
        
        [data-bs-theme="dark"] .nurse-name,
        body.dark-mode .nurse-name {
            color: #ecf0f1 !important;
        }
        
        .nurse-details {
            font-size: 0.85rem;
            color: var(--bs-text-muted, #6c757d);
        }
        
        [data-bs-theme="dark"] .nurse-details,
        body.dark-mode .nurse-details {
            color: #95a5a6 !important;
        }
        
        .shift-time {
            font-size: 0.8rem;
            color: #3498db;
            font-weight: 500;
        }
        .no-content {
            text-align: center;
            color: var(--bs-text-muted, #6c757d);
            font-style: italic;
            padding: 20px;
        }
        
        [data-bs-theme="dark"] .no-content,
        body.dark-mode .no-content {
            color: #95a5a6 !important;
        }
        
        .current-nurse-badge {
            background-color: #28a745;
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        .bed-info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 15px;
            margin-bottom: 0;
        }
        .bed-info h4 {
            margin: 0 0 5px 0;
            font-size: 1.3rem;
        }
        .bed-info p {
            margin: 0;
            opacity: 0.9;
        }
        
        /* Content item styling for procedures and vital signs */
        .content-item {
            background: var(--bs-gray-50, #f8f9fa);
            border: 1px solid var(--bs-border-color, #e3e6f0);
            border-radius: 0.375rem;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        [data-bs-theme="dark"] .content-item,
        body.dark-mode .content-item {
            background: #2c3e50 !important;
            border: 1px solid #34495e !important;
            color: #ecf0f1 !important;
        }
        
        .content-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            flex-shrink: 0;
        }
        
        .content-details {
            flex: 1;
        }
        
        .content-title {
            font-weight: 600;
            color: var(--bs-body-color, #495057);
            margin-bottom: 3px;
            font-size: 0.9rem;
        }
        
        [data-bs-theme="dark"] .content-title,
        body.dark-mode .content-title {
            color: #ecf0f1 !important;
        }
        
        .content-meta {
            font-size: 0.8rem;
            color: var(--bs-text-muted, #6c757d);
            margin-bottom: 5px;
        }
        
        [data-bs-theme="dark"] .content-meta,
        body.dark-mode .content-meta {
            color: #95a5a6 !important;
        }
        
        .content-description {
            font-size: 0.85rem;
            color: var(--bs-body-color, #495057);
            line-height: 1.4;
        }
        
        [data-bs-theme="dark"] .content-description,
        body.dark-mode .content-description {
            color: #bdc3c7 !important;
        }
        
        /* Status indicators */
        .status-badge {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        
        .status-critical {
            background-color: #dc3545;
            color: white;
        }
        
        .status-normal {
            background-color: #17a2b8;
            color: white;
        }
        
        .status-ongoing {
            background-color: #6f42c1;
            color: white;
        }
        
        /* Alert styling */
        .alert {
            border-radius: 0.375rem;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid transparent;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        [data-bs-theme="dark"] .alert-info,
        body.dark-mode .alert-info {
            background-color: #1e3a40 !important;
            border-color: #2c5763 !important;
            color: #7dd3fc !important;
        }
        
        /* Chart table styling */
        .chart-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        
        .chart-table th,
        .chart-table td {
            padding: 8px 10px;
            text-align: left;
            border: 1px solid var(--bs-border-color, #dee2e6);
        }
        
        .chart-table th {
            background: var(--bs-gray-100, #f8f9fa);
            font-weight: 600;
            color: var(--bs-body-color, #495057);
        }
        
        [data-bs-theme="dark"] .chart-table th,
        body.dark-mode .chart-table th {
            background: #34495e !important;
            color: #ecf0f1 !important;
            border-color: #2c3e50 !important;
        }
        
        [data-bs-theme="dark"] .chart-table td,
        body.dark-mode .chart-table td {
            border-color: #34495e !important;
            color: #ecf0f1 !important;
        }
        
        .chart-table tbody tr:nth-child(even) {
            background: var(--bs-gray-50, #f8f9fa);
        }
        
        [data-bs-theme="dark"] .chart-table tbody tr:nth-child(even),
        body.dark-mode .chart-table tbody tr:nth-child(even) {
            background: #2c3e50 !important;
        }
        
        /* GCS scoring styling */
        .gcs-score {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .gcs-component {
            background: var(--bs-gray-100, #f8f9fa);
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: 0.375rem;
            padding: 10px;
            text-align: center;
            min-width: 80px;
        }
        
        [data-bs-theme="dark"] .gcs-component,
        body.dark-mode .gcs-component {
            background: #34495e !important;
            border-color: #2c3e50 !important;
            color: #ecf0f1 !important;
        }
        
        .gcs-label {
            font-size: 0.75rem;
            color: var(--bs-text-muted, #6c757d);
            margin-bottom: 3px;
            font-weight: 500;
        }
        
        [data-bs-theme="dark"] .gcs-label,
        body.dark-mode .gcs-label {
            color: #95a5a6 !important;
        }
        
        .gcs-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--bs-body-color, #495057);
        }
        
        [data-bs-theme="dark"] .gcs-value,
        body.dark-mode .gcs-value {
            color: #ecf0f1 !important;
        }
        
        .gcs-total {
            background: #3498db !important;
            color: white !important;
            border-color: #2980b9 !important;
        }
        
        /* IV Flow rate styling */
        .iv-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 8px;
        }
        
        .iv-detail-item {
            background: var(--bs-gray-100, #f8f9fa);
            padding: 6px 8px;
            border-radius: 0.25rem;
            font-size: 0.8rem;
        }
        
        [data-bs-theme="dark"] .iv-detail-item,
        body.dark-mode .iv-detail-item {
            background: #34495e !important;
            color: #ecf0f1 !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .section-header {
                font-size: 1rem;
                padding: 10px 12px;
            }
            
            .section-content {
                padding: 12px;
            }
            
            .content-item {
                padding: 10px;
                gap: 10px;
            }
            
            .gcs-score {
                justify-content: center;
            }
            
            .iv-details {
                grid-template-columns: 1fr;
                gap: 5px;
            }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card passover-card">
        <div class="bed-info">
            <h4><i class="fas fa-bed mr-2"></i>{{ $ward->name }} - Bed {{ $bed->bed_number }}</h4>
            @if($bed->patient)
                <p><i class="fas fa-user mr-2"></i>Patient: {{ $bed->patient->name }}</p>
            @else
                <p><i class="fas fa-bed mr-2"></i>No Patient Assigned</p>
            @endif
        </div>
        
        <div class="card-body">
            <!-- 1. Nurses on Duty Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-user-nurse mr-2"></i>Nurses on Duty
                    </div>
                    <span class="shift-time">{{ now()->format('H:i') }} - Today</span>
                </div>
                <div class="section-content">
                    @if($assignedNurse)
                        <div class="nurse-item">
                            <div class="nurse-avatar">
                                {{ strtoupper(substr($assignedNurse->name, 0, 2)) }}
                            </div>
                            <div class="nurse-info">
                                <div class="nurse-name">
                                    {{ $assignedNurse->name }}
                                    <span class="current-nurse-badge">Assigned to Bed</span>
                                </div>
                                <div class="nurse-details">
                                    <i class="fas fa-envelope mr-1"></i>{{ $assignedNurse->email }}
                                    @if($assignedNurse->phone)
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-phone mr-1"></i>{{ $assignedNurse->phone }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($currentShiftNurses && $currentShiftNurses->count() > 0)
                        @foreach($currentShiftNurses as $shiftName => $nurses)
                            <div class="mb-2">
                                <small class="text-muted font-weight-bold">{{ ucfirst($shiftName) }} Shift:</small>
                                @foreach($nurses as $nurse)
                                    @if(!$assignedNurse || $nurse['nurse']['name'] !== $assignedNurse->name)
                                        <div class="nurse-item mt-1">
                                            <div class="nurse-avatar">
                                                {{ strtoupper(substr($nurse['nurse']['name'], 0, 2)) }}
                                            </div>
                                            <div class="nurse-info">
                                                <div class="nurse-name">{{ $nurse['nurse']['name'] }}</div>
                                                <div class="nurse-details">
                                                    <i class="fas fa-id-badge mr-1"></i>{{ $nurse['nurse']['registration_number'] ?? 'N/A' }}
                                                    @if(isset($nurse['shift_slot']['start_time']) && isset($nurse['shift_slot']['end_time']))
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-clock mr-1"></i>{{ $nurse['shift_slot']['start_time'] }} - {{ $nurse['shift_slot']['end_time'] }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    @elseif($wardNurses && $wardNurses->count() > 0 && !$assignedNurse)
                        @foreach($wardNurses as $nurse)
                            <div class="nurse-item">
                                <div class="nurse-avatar">
                                    {{ strtoupper(substr($nurse->name, 0, 2)) }}
                                </div>
                                <div class="nurse-info">
                                    <div class="nurse-name">{{ $nurse->name }}</div>
                                    <div class="nurse-details">
                                        <i class="fas fa-envelope mr-1"></i>{{ $nurse->email }}
                                        @if($nurse->phone)
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-phone mr-1"></i>{{ $nurse->phone }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif(!$assignedNurse)
                        <div class="no-content">
                            <i class="fas fa-info-circle mb-2" style="font-size: 2rem;"></i>
                            <p>No current shift nurses assigned to this ward.</p>
                        </div>
                    @endif

                    <!-- Next Shift Nurses -->
                    @if($nextShiftNurses && $nextShiftNurses->count() > 0)
                        <div class="mt-3">
                            <small class="text-muted font-weight-bold d-block mb-2">
                                <i class="fas fa-forward mr-1"></i>Next Shift (Tomorrow):
                            </small>
                            @foreach($nextShiftNurses as $shiftName => $nurses)
                                <div class="mb-2">
                                    <small class="text-muted">{{ ucfirst($shiftName) }} Shift:</small>
                                    @foreach($nurses as $nurse)
                                        <div class="nurse-item mt-1">
                                            <div class="nurse-avatar">
                                                {{ strtoupper(substr($nurse['nurse']['name'], 0, 2)) }}
                                            </div>
                                            <div class="nurse-info">
                                                <div class="nurse-name">{{ $nurse['nurse']['name'] }}</div>
                                                <div class="nurse-details">
                                                    <i class="fas fa-id-badge mr-1"></i>{{ $nurse['nurse']['registration_number'] ?? 'N/A' }}
                                                    @if(isset($nurse['shift_slot']['start_time']) && isset($nurse['shift_slot']['end_time']))
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-clock mr-1"></i>{{ $nurse['shift_slot']['start_time'] }} - {{ $nurse['shift_slot']['end_time'] }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Vital Signs Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-heartbeat mr-2"></i>Vital Signs
                    </div>
                    <span class="shift-time">Latest Readings</span>
                </div>
                <div class="section-content">
                    @if($bed->patient && $bed->patient->latestVitalSigns)
                        @php $vitals = $bed->patient->latestVitalSigns; @endphp
                        <div class="content-item">
                            <div class="content-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="content-details">
                                <div class="content-title">
                                    Latest Vital Signs
                                    <span class="status-badge {{ $vitals->total_ews >= 5 ? 'status-critical' : ($vitals->total_ews >= 3 ? 'status-pending' : 'status-normal') }}">
                                        EWS: {{ $vitals->total_ews }}
                                    </span>
                                </div>
                                <div class="content-meta">
                                    <i class="fas fa-clock mr-1"></i>{{ $vitals->created_at->format('d M Y, H:i') }}
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-user mr-1"></i>{{ $vitals->recordedBy->name ?? 'System' }}
                                </div>
                                <div class="content-description">
                                    <div class="row">
                                        <div class="col-md-3"><strong>BP:</strong> {{ $vitals->systolic_bp }}/{{ $vitals->diastolic_bp }} mmHg</div>
                                        <div class="col-md-3"><strong>HR:</strong> {{ $vitals->heart_rate }} bpm</div>
                                        <div class="col-md-3"><strong>Temp:</strong> {{ $vitals->temperature }}°C</div>
                                        <div class="col-md-3"><strong>SpO2:</strong> {{ $vitals->oxygen_saturation }}%</div>
                                    </div>
                                    @if($vitals->total_ews >= 3)
                                        <div class="mt-2">
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $vitals->total_ews >= 5 ? 'Critical EWS - Immediate attention required' : 'Elevated EWS - Monitor closely' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="no-content">
                            <i class="fas fa-chart-line mb-2" style="font-size: 2rem;"></i>
                            <p>No vital signs recorded yet for this patient.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Minor Procedures Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-tasks mr-2"></i>Minor Procedures
                    </div>
                    <span class="shift-time">Today's Activities</span>
                </div>
                <div class="section-content">
                    <!-- IV Line Procedures -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-syringe"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                IV Line Insertion
                                <span class="status-badge status-completed">Completed</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>{{ now()->format('d M Y, 06:30') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Nurse {{ $assignedNurse->name ?? 'Sarah' }}
                            </div>
                            <div class="content-description">
                                18G IV cannula inserted in left forearm. Patent and secure. No signs of infiltration or phlebitis.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Size:</strong> 18G</div>
                                    <div class="iv-detail-item"><strong>Site:</strong> Left forearm</div>
                                    <div class="iv-detail-item"><strong>Condition:</strong> Patent</div>
                                    <div class="iv-detail-item"><strong>Next Check:</strong> {{ now()->addHours(4)->format('H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IV Infusion - Normal Saline -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                IV Infusion - Normal Saline
                                <span class="status-badge status-ongoing">Ongoing</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Started: {{ now()->subHours(2)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Continuous infusion
                            </div>
                            <div class="content-description">
                                0.9% Normal Saline 1000ml infusing via IV pump. Patient tolerating well.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Type:</strong> 0.9% NaCl</div>
                                    <div class="iv-detail-item"><strong>Volume:</strong> 1000ml</div>
                                    <div class="iv-detail-item"><strong>Rate:</strong> 125ml/hr</div>
                                    <div class="iv-detail-item"><strong>Remaining:</strong> ~750ml</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IV Infusion - Dextrose -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-prescription-bottle"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                IV Infusion - 5% Dextrose
                                <span class="status-badge status-pending">Scheduled</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Scheduled: {{ now()->addHours(1)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Next shift nurse
                            </div>
                            <div class="content-description">
                                5% Dextrose in water 500ml to be started after current saline completes.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Type:</strong> 5% Dextrose</div>
                                    <div class="iv-detail-item"><strong>Volume:</strong> 500ml</div>
                                    <div class="iv-detail-item"><strong>Rate:</strong> 100ml/hr</div>
                                    <div class="iv-detail-item"><strong>Duration:</strong> 5 hours</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Blood Sample Collection -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-vial"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Blood Sample Collection
                                <span class="status-badge status-completed">Completed</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>{{ now()->format('d M Y, 08:30') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Nurse Sarah
                            </div>
                            <div class="content-description">
                                Routine blood collection for lab work. Sample sent to laboratory for analysis.
                            </div>
                        </div>
                    </div>

                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-band-aid"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Wound Dressing Change
                                <span class="status-badge status-pending">Scheduled</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>{{ now()->addHours(2)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Next shift nurse
                            </div>
                            <div class="content-description">
                                Scheduled wound dressing change. Check for signs of infection and document healing progress.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Major Procedures Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-procedures mr-2"></i>Major Procedures
                    </div>
                    <span class="shift-time">Scheduled & Completed</span>
                </div>
                <div class="section-content">
                    <!-- Sample procedures - replace with actual data when available -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-x-ray"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Chest X-Ray
                                <span class="status-badge status-completed">Completed</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>{{ now()->subHours(3)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user-md mr-1"></i>Dr. {{ $bed->consultant->name ?? 'Johnson' }}
                            </div>
                            <div class="content-description">
                                Routine chest X-ray completed. Results pending review by radiologist. Patient tolerated procedure well.
                            </div>
                        </div>
                    </div>

                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                ECG Monitoring
                                <span class="status-badge status-ongoing">Ongoing</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Started: {{ now()->subHours(6)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Continuous monitoring
                            </div>
                            <div class="content-description">
                                Continuous cardiac monitoring in progress. No arrhythmias detected. Monitor for changes and alert physician if irregularities occur.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Medical Chart Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-chart-area mr-2"></i>Medical Chart
                    </div>
                    <span class="shift-time">Current Status</span>
                </div>
                <div class="section-content">
                    <!-- Intake Output Chart -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Intake & Output Chart (24hrs)
                                <span class="status-badge status-normal">Updated</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Last updated: {{ now()->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>{{ $assignedNurse->name ?? 'Nursing Staff' }}
                            </div>
                            <div class="content-description">
                                <table class="chart-table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Intake (ml)</th>
                                            <th>Output (ml)</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Oral Fluids</strong></td>
                                            <td>1200</td>
                                            <td>-</td>
                                            <td>+1200</td>
                                        </tr>
                                        <tr>
                                            <td><strong>IV Fluids</strong></td>
                                            <td>2000</td>
                                            <td>-</td>
                                            <td>+2000</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Urine Output</strong></td>
                                            <td>-</td>
                                            <td>2800</td>
                                            <td>-2800</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Drainage</strong></td>
                                            <td>-</td>
                                            <td>150</td>
                                            <td>-150</td>
                                        </tr>
                                        <tr style="background-color: var(--bs-info, #e3f2fd);">
                                            <td><strong>Total</strong></td>
                                            <td><strong>3200</strong></td>
                                            <td><strong>2950</strong></td>
                                            <td><strong>+250</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Glasgow Coma Scale -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Glasgow Coma Scale (GCS)
                                <span class="status-badge status-normal">Normal</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Last assessed: {{ now()->subMinutes(30)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>{{ $assignedNurse->name ?? 'Nursing Staff' }}
                            </div>
                            <div class="content-description">
                                <div class="gcs-score">
                                    <div class="gcs-component">
                                        <div class="gcs-label">Eyes</div>
                                        <div class="gcs-value">4</div>
                                        <small>Spontaneous</small>
                                    </div>
                                    <div class="gcs-component">
                                        <div class="gcs-label">Verbal</div>
                                        <div class="gcs-value">5</div>
                                        <small>Oriented</small>
                                    </div>
                                    <div class="gcs-component">
                                        <div class="gcs-label">Motor</div>
                                        <div class="gcs-value">6</div>
                                        <small>Obeys commands</small>
                                    </div>
                                    <div class="gcs-component gcs-total">
                                        <div class="gcs-label">Total GCS</div>
                                        <div class="gcs-value">15</div>
                                        <small>Normal</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Assessment:</strong> Patient alert and oriented x3. Responds appropriately to verbal commands. No neurological deficits noted.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Dressing and Drains Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-first-aid mr-2"></i>Dressing and Drains
                    </div>
                    <span class="shift-time">Current Status</span>
                </div>
                <div class="section-content">
                    <!-- Surgical Wound Dressing -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-bandage"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Surgical Wound Dressing
                                <span class="status-badge status-normal">Clean & Dry</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Last changed: {{ now()->subHours(8)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>{{ $assignedNurse->name ?? 'Day shift nurse' }}
                            </div>
                            <div class="content-description">
                                Post-operative abdominal wound dressing intact. No signs of bleeding or discharge. 
                                Incision healing well with no redness or swelling noted.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Location:</strong> Lower abdomen</div>
                                    <div class="iv-detail-item"><strong>Size:</strong> 15cm incision</div>
                                    <div class="iv-detail-item"><strong>Type:</strong> Sterile gauze pad</div>
                                    <div class="iv-detail-item"><strong>Next change:</strong> {{ now()->addHours(16)->format('d M H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chest Drain -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-lungs"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Chest Drain (Right Side)
                                <span class="status-badge status-ongoing">Active</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Inserted: {{ now()->subDays(2)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user-md mr-1"></i>Dr. {{ $bed->consultant->name ?? 'Thompson' }}
                            </div>
                            <div class="content-description">
                                28Fr chest tube draining serosanguineous fluid. Underwater seal drainage system functioning properly.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Size:</strong> 28Fr chest tube</div>
                                    <div class="iv-detail-item"><strong>Output 24hr:</strong> 180ml</div>
                                    <div class="iv-detail-item"><strong>Type:</strong> Serosanguineous</div>
                                    <div class="iv-detail-item"><strong>Suction:</strong> -20cmH2O</div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Note:</strong> Monitor for air leak. Drainage decreasing appropriately. Consider removal in 24-48hrs if output remains low.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wound Drain -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Abdominal Drain (Jackson-Pratt)
                                <span class="status-badge status-normal">Functioning</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Emptied: {{ now()->subHours(4)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>{{ $assignedNurse->name ?? 'Nursing staff' }}
                            </div>
                            <div class="content-description">
                                JP drain secured at surgical site. Draining minimal serosanguineous fluid. Bulb compressed and patent.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Type:</strong> Jackson-Pratt</div>
                                    <div class="iv-detail-item"><strong>Output shift:</strong> 45ml</div>
                                    <div class="iv-detail-item"><strong>Character:</strong> Serosanguineous</div>
                                    <div class="iv-detail-item"><strong>Next empty:</strong> {{ now()->addHours(4)->format('H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pressure Ulcer Dressing -->
                    <div class="content-item">
                        <div class="content-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="content-details">
                            <div class="content-title">
                                Pressure Ulcer Dressing (Sacrum)
                                <span class="status-badge status-pending">Due Change</span>
                            </div>
                            <div class="content-meta">
                                <i class="fas fa-clock mr-1"></i>Last changed: {{ now()->subHours(20)->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-user mr-1"></i>Wound care team
                            </div>
                            <div class="content-description">
                                Stage 2 pressure ulcer on sacrum. Hydrocolloid dressing applied. Wound showing signs of granulation.
                                <div class="iv-details">
                                    <div class="iv-detail-item"><strong>Stage:</strong> Stage 2</div>
                                    <div class="iv-detail-item"><strong>Size:</strong> 3cm x 2cm</div>
                                    <div class="iv-detail-item"><strong>Dressing:</strong> Hydrocolloid</div>
                                    <div class="iv-detail-item"><strong>Due change:</strong> {{ now()->addHours(4)->format('d M H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$nurseSchedule)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    No nurse schedule found for this ward. Showing available information only.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Auto-refresh every 5 minutes to get updated information
    setInterval(function() {
        location.reload();
    }, 300000); // 5 minutes
    
    // Add hover effects for better interaction
    $('.content-item').hover(
        function() { $(this).addClass('shadow-sm'); },
        function() { $(this).removeClass('shadow-sm'); }
    );
});
</script>
@endsection 