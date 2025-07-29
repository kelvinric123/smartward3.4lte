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
        
        /* Nurse selection form styling */
        .form-label {
            font-weight: 600;
            color: var(--bs-body-color, #495057);
            margin-bottom: 8px;
        }
        
        [data-bs-theme="dark"] .form-label,
        body.dark-mode .form-label {
            color: #ecf0f1 !important;
        }
        
        .form-control {
            border: 2px solid var(--bs-border-color, #e3e6f0);
            border-radius: 0.375rem;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: 0;
        }
        
        [data-bs-theme="dark"] .form-control,
        body.dark-mode .form-control {
            background-color: #34495e !important;
            border-color: #2c3e50 !important;
            color: #ecf0f1 !important;
        }
        
        [data-bs-theme="dark"] .form-control:focus,
        body.dark-mode .form-control:focus {
            border-color: #3498db !important;
            background-color: #34495e !important;
        }
        
        .selected-nurse-info .nurse-item {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
            border: 2px solid #28a745;
            margin-top: 8px;
        }
        
        [data-bs-theme="dark"] .selected-nurse-info .nurse-item,
        body.dark-mode .selected-nurse-info .nurse-item {
            background: linear-gradient(135deg, #1e3a1e 0%, #2d4a2d 100%) !important;
            border-color: #28a745 !important;
        }
        
        .btn {
            border-radius: 0.375rem;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        
        .btn:disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        /* Small text styling */
        .text-muted {
            font-size: 0.85rem;
        }
        
        [data-bs-theme="dark"] .text-muted,
        body.dark-mode .text-muted {
            color: #95a5a6 !important;
        }

        /* Procedure selection styling */
        .procedure-item {
            background: var(--bs-gray-50, #f8f9fa);
            border: 1px solid var(--bs-border-color, #e3e6f0);
            border-radius: 0.375rem;
            padding: 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        [data-bs-theme="dark"] .procedure-item,
        body.dark-mode .procedure-item {
            background: #34495e !important;
            border: 1px solid #2c3e50 !important;
            color: #ecf0f1 !important;
        }
        
        .procedure-icon {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #17a2b8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            flex-shrink: 0;
        }
        
        .procedure-details {
            flex: 1;
        }
        
        .procedure-name {
            font-weight: 500;
            color: var(--bs-body-color, #495057);
            margin-bottom: 2px;
            font-size: 0.9rem;
        }
        
        [data-bs-theme="dark"] .procedure-name,
        body.dark-mode .procedure-name {
            color: #ecf0f1 !important;
        }
        
        .procedure-description {
            font-size: 0.8rem;
            color: var(--bs-text-muted, #6c757d);
        }
        
        [data-bs-theme="dark"] .procedure-description,
        body.dark-mode .procedure-description {
            color: #95a5a6 !important;
        }
        
        .procedure-time {
            font-size: 0.75rem;
            color: #6f42c1;
            font-weight: 500;
        }
        
        .add-procedure-form {
            background: var(--bs-gray-50, #f8f9fa);
            border: 2px dashed var(--bs-border-color, #dee2e6);
            border-radius: 0.375rem;
            padding: 15px;
            margin-top: 10px;
        }
        
        [data-bs-theme="dark"] .add-procedure-form,
        body.dark-mode .add-procedure-form {
            background: #2c3e50 !important;
            border-color: #34495e !important;
        }
        
        .selected-procedures-list {
            margin-top: 10px;
        }

        /* Procedure filters styling */
        .procedure-filters .btn {
            border-radius: 0.375rem;
            padding: 6px 10px;
            font-size: 0.8rem;
            margin-right: 5px;
        }

        .procedure-filters .btn.active {
            background-color: var(--bs-primary, #007bff);
            color: white;
            border-color: var(--bs-primary, #007bff);
        }

        .procedure-filters .btn.active:hover {
            background-color: var(--bs-primary, #0056b3);
            border-color: var(--bs-primary, #0056b3);
        }

        .procedure-filters .badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            margin-left: 5px;
        }
        
        /* Procedure status styling */
        .procedure-status {
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 500;
            margin-left: 5px;
        }
        
        .procedure-status.pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .procedure-status.completed {
            background-color: #28a745;
            color: white;
        }
        
        .procedure-status.cancelled {
            background-color: #dc3545;
            color: white;
        }
        
        /* Procedure actions */
        .procedure-actions {
            display: flex;
            gap: 5px;
            align-items: center;
            margin-left: auto;
        }
        
        .procedure-item {
            align-items: flex-start;
        }
        
        .procedure-item.completed {
            opacity: 0.8;
        }
        
        .procedure-item.cancelled {
            opacity: 0.6;
        }
        
        .procedure-item.hidden {
            display: none !important;
        }
        
        /* Status reason styling */
        .procedure-reason {
            font-size: 0.75rem;
            color: var(--bs-text-muted, #6c757d);
            font-style: italic;
            margin-top: 3px;
        }
        
        [data-bs-theme="dark"] .procedure-reason,
        body.dark-mode .procedure-reason {
            color: #95a5a6 !important;
        }
        
        /* Action button styling */
        .action-btn {
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 0.25rem;
            min-width: auto;
        }
        
        /* Existing procedure styling */
        .existing-procedure {
            position: relative;
        }
        
        .existing-procedure.completed {
            opacity: 0.85;
        }
        
        .existing-procedure.cancelled {
            opacity: 0.6;
            background-color: var(--bs-gray-100, #f8f9fa) !important;
        }
        
        [data-bs-theme="dark"] .existing-procedure.cancelled,
        body.dark-mode .existing-procedure.cancelled {
            background-color: #1a252f !important;
        }
        
        .existing-procedure.hidden {
            display: none !important;
        }
        
        .completion-reason {
            margin-top: 8px;
        }
        
        .completion-reason small {
            color: var(--bs-text-muted, #6c757d);
            font-style: italic;
        }
        
        [data-bs-theme="dark"] .completion-reason small,
        body.dark-mode .completion-reason small {
            color: #95a5a6 !important;
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
            <!-- 1. Nurse Passover Form Section -->
            <div class="passover-section">
                <div class="section-header">
                    <div>
                        <i class="fas fa-exchange-alt mr-2"></i>Nurse Passover Selection
                    </div>
                    <span class="shift-time">{{ now()->format('H:i') }} - Today</span>
                </div>
                <div class="section-content">
                    <form id="nursePassoverForm" class="mb-4">
                        <div class="row">
                            <!-- Nurse On Duty Selection -->
                            <div class="col-md-6">
                                <label for="nurseOnDuty" class="form-label">
                                    <i class="fas fa-user-nurse mr-1"></i>Nurse On Duty
                                    @if($currentShift)
                                        <small class="text-muted">({{ $currentShift['name'] }} - {{ $currentShift['formatted_time_range'] }})</small>
                                    @endif
                                </label>
                                <select id="nurseOnDuty" name="nurse_on_duty" class="form-control" required>
                                    <option value="">Select Nurse On Duty...</option>
                                    @if($currentShiftNurses && $currentShiftNurses->count() > 0)
                                        @foreach($currentShiftNurses as $assignment)
                                            @if(isset($assignment['member']))
                                                <option value="{{ $assignment['member']['employee_id'] }}" 
                                                        data-name="{{ $assignment['member']['name'] }}"
                                                        data-position="{{ $assignment['member']['position'] ?? 'Nurse' }}">
                                                    {{ $assignment['member']['name'] }} - {{ $assignment['member']['position'] ?? 'Nurse' }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                    
                                    @if($assignedNurse)
                                        <option value="bed_assigned_{{ $assignedNurse->id }}" 
                                                data-name="{{ $assignedNurse->name }}"
                                                data-position="Bed Assigned Nurse"
                                                {{ !$currentShiftNurses || $currentShiftNurses->count() == 0 ? 'selected' : '' }}>
                                            {{ $assignedNurse->name }} - Bed Assigned Nurse
                                        </option>
                                    @endif
                                </select>
                                <div class="selected-nurse-info mt-2 d-none">
                                    <div class="nurse-item">
                                        <div class="nurse-avatar">
                                            <span id="selectedOnDutyInitials"></span>
                                        </div>
                                        <div class="nurse-info">
                                            <div class="nurse-name" id="selectedOnDutyName"></div>
                                            <div class="nurse-details" id="selectedOnDutyDetails"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nurse Receive Passover Selection -->
                            <div class="col-md-6">
                                <label for="nurseReceivePassover" class="form-label">
                                    <i class="fas fa-user-check mr-1"></i>Nurse Receive Passover
                                    @if($nextShift)
                                        <small class="text-muted">({{ $nextShift['name'] }} - {{ $nextShift['formatted_time_range'] }})</small>
                                    @endif
                                </label>
                                <select id="nurseReceivePassover" name="nurse_receive_passover" class="form-control" required>
                                    <option value="">Select Nurse to Receive Passover...</option>
                                    @if($nextShiftNurses && $nextShiftNurses->count() > 0)
                                        @foreach($nextShiftNurses as $assignment)
                                            @if(isset($assignment['member']))
                                                <option value="{{ $assignment['member']['employee_id'] }}" 
                                                        data-name="{{ $assignment['member']['name'] }}"
                                                        data-position="{{ $assignment['member']['position'] ?? 'Nurse' }}">
                                                    {{ $assignment['member']['name'] }} - {{ $assignment['member']['position'] ?? 'Nurse' }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="" disabled>No next shift nurses scheduled</option>
                                    @endif
                                </select>
                                <div class="selected-nurse-info mt-2 d-none">
                                    <div class="nurse-item">
                                        <div class="nurse-avatar">
                                            <span id="selectedReceiveInitials"></span>
                                        </div>
                                        <div class="nurse-info">
                                            <div class="nurse-name" id="selectedReceiveName"></div>
                                            <div class="nurse-details" id="selectedReceiveDetails"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Minor Procedures Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-tasks mr-1"></i>Minor Procedures
                                </label>
                                
                                <!-- Filter buttons for minor procedures -->
                                <div class="procedure-filters mb-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary active" data-filter="all" data-type="minor">
                                            All <span class="badge badge-light" id="minorAllCount">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" data-filter="pending" data-type="minor">
                                            Pending <span class="badge badge-light" id="minorPendingCount">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" data-filter="completed" data-type="minor">
                                            Completed <span class="badge badge-light" id="minorCompletedCount">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-filter="cancelled" data-type="minor">
                                            Cancelled <span class="badge badge-light" id="minorCancelledCount">0</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="selected-procedures-list" id="selectedMinorProcedures"></div>
                                <div class="add-procedure-form">
                                    <div class="row">
                                        <div class="col-8">
                                            <select id="minorProcedureSelect" class="form-control">
                                                <option value="">Select a minor procedure...</option>
                                                <option value="iv_insertion">IV Line Insertion</option>
                                                <option value="iv_infusion_saline">IV Infusion - Normal Saline</option>
                                                <option value="iv_infusion_dextrose">IV Infusion - 5% Dextrose</option>
                                                <option value="blood_sample">Blood Sample Collection</option>
                                                <option value="wound_dressing">Wound Dressing Change</option>
                                                <option value="vital_signs">Vital Signs Monitoring</option>
                                                <option value="medication_admin">Medication Administration</option>
                                                <option value="catheter_care">Catheter Care</option>
                                                <option value="oxygen_therapy">Oxygen Therapy</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100" id="addMinorProcedureBtn">
                                                <i class="fas fa-plus mr-1"></i>Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <textarea id="minorProcedureNotes" class="form-control" rows="2" placeholder="Add notes for this procedure..."></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Major Procedures Section -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-procedures mr-1"></i>Major Procedures
                                </label>
                                
                                <!-- Filter buttons for major procedures -->
                                <div class="procedure-filters mb-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary active" data-filter="all" data-type="major">
                                            All <span class="badge badge-light" id="majorAllCount">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" data-filter="pending" data-type="major">
                                            Pending <span class="badge badge-light" id="majorPendingCount">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" data-filter="completed" data-type="major">
                                            Completed <span class="badge badge-light" id="majorCompletedCount">0</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-filter="cancelled" data-type="major">
                                            Cancelled <span class="badge badge-light" id="majorCancelledCount">0</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="selected-procedures-list" id="selectedMajorProcedures"></div>
                                <div class="add-procedure-form">
                                    <div class="row">
                                        <div class="col-8">
                                            <select id="majorProcedureSelect" class="form-control">
                                                <option value="">Select a major procedure...</option>
                                                <option value="chest_xray">Chest X-Ray</option>
                                                <option value="ecg_monitoring">ECG Monitoring</option>
                                                <option value="ct_scan">CT Scan</option>
                                                <option value="mri_scan">MRI Scan</option>
                                                <option value="ultrasound">Ultrasound</option>
                                                <option value="endoscopy">Endoscopy</option>
                                                <option value="bronchoscopy">Bronchoscopy</option>
                                                <option value="chest_tube">Chest Tube Insertion</option>
                                                <option value="surgery_prep">Surgery Preparation</option>
                                                <option value="dialysis">Dialysis</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100" id="addMajorProcedureBtn">
                                                <i class="fas fa-plus mr-1"></i>Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <textarea id="majorProcedureNotes" class="form-control" rows="2" placeholder="Add notes for this procedure..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary" id="confirmPassoverBtn" disabled>
                                <i class="fas fa-check mr-1"></i>Confirm Nurse Passover
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" id="resetFormBtn">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </button>
                        </div>
                    </form>
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
                    <!-- Filter buttons for existing minor procedures -->
                    <div class="procedure-filters mb-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-filter="all" data-section="existing-minor">
                                All <span class="badge badge-light" id="existingMinorAllCount">5</span>
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-filter="pending" data-section="existing-minor">
                                Pending <span class="badge badge-light" id="existingMinorPendingCount">2</span>
                            </button>
                            <button type="button" class="btn btn-outline-success" data-filter="completed" data-section="existing-minor">
                                Completed <span class="badge badge-light" id="existingMinorCompletedCount">2</span>
                            </button>
                            <button type="button" class="btn btn-outline-info" data-filter="ongoing" data-section="existing-minor">
                                Ongoing <span class="badge badge-light" id="existingMinorOngoingCount">1</span>
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-filter="cancelled" data-section="existing-minor">
                                Cancelled <span class="badge badge-light" id="existingMinorCancelledCount">0</span>
                            </button>
                        </div>
                    </div>

                    <!-- IV Line Procedures -->
                    <div class="content-item existing-procedure" data-status="completed" data-procedure-id="minor-1">
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
                                <div class="completion-reason mt-2">
                                    <small class="text-muted"><strong>Completion Notes:</strong> Procedure completed successfully. No complications.</small>
                                </div>
                            </div>
                        </div>
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="minor-1" data-section="existing-minor" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- IV Infusion - Normal Saline -->
                    <div class="content-item existing-procedure" data-status="ongoing" data-procedure-id="minor-2">
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
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-success btn-sm action-btn complete-existing-procedure-btn" 
                                    data-procedure-id="minor-2" data-section="existing-minor" title="Mark as Complete">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm action-btn cancel-existing-procedure-btn" 
                                    data-procedure-id="minor-2" data-section="existing-minor" title="Cancel Procedure">
                                <i class="fas fa-ban"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="minor-2" data-section="existing-minor" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- IV Infusion - Dextrose -->
                    <div class="content-item existing-procedure" data-status="pending" data-procedure-id="minor-3">
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
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-success btn-sm action-btn complete-existing-procedure-btn" 
                                    data-procedure-id="minor-3" data-section="existing-minor" title="Mark as Complete">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm action-btn cancel-existing-procedure-btn" 
                                    data-procedure-id="minor-3" data-section="existing-minor" title="Cancel Procedure">
                                <i class="fas fa-ban"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="minor-3" data-section="existing-minor" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Blood Sample Collection -->
                    <div class="content-item existing-procedure" data-status="completed" data-procedure-id="minor-4">
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
                                <div class="completion-reason mt-2">
                                    <small class="text-muted"><strong>Completion Notes:</strong> All required samples collected. Sent to lab for analysis.</small>
                                </div>
                            </div>
                        </div>
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="minor-4" data-section="existing-minor" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="content-item existing-procedure" data-status="pending" data-procedure-id="minor-5">
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
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-success btn-sm action-btn complete-existing-procedure-btn" 
                                    data-procedure-id="minor-5" data-section="existing-minor" title="Mark as Complete">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm action-btn cancel-existing-procedure-btn" 
                                    data-procedure-id="minor-5" data-section="existing-minor" title="Cancel Procedure">
                                <i class="fas fa-ban"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="minor-5" data-section="existing-minor" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
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
                    <!-- Filter buttons for existing major procedures -->
                    <div class="procedure-filters mb-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-filter="all" data-section="existing-major">
                                All <span class="badge badge-light" id="existingMajorAllCount">2</span>
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-filter="pending" data-section="existing-major">
                                Pending <span class="badge badge-light" id="existingMajorPendingCount">0</span>
                            </button>
                            <button type="button" class="btn btn-outline-success" data-filter="completed" data-section="existing-major">
                                Completed <span class="badge badge-light" id="existingMajorCompletedCount">1</span>
                            </button>
                            <button type="button" class="btn btn-outline-info" data-filter="ongoing" data-section="existing-major">
                                Ongoing <span class="badge badge-light" id="existingMajorOngoingCount">1</span>
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-filter="cancelled" data-section="existing-major">
                                Cancelled <span class="badge badge-light" id="existingMajorCancelledCount">0</span>
                            </button>
                        </div>
                    </div>

                    <!-- Sample procedures - replace with actual data when available -->
                    <div class="content-item existing-procedure" data-status="completed" data-procedure-id="major-1">
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
                                <div class="completion-reason mt-2">
                                    <small class="text-muted"><strong>Completion Notes:</strong> X-ray completed successfully. Images clear, results sent to radiologist for review.</small>
                                </div>
                            </div>
                        </div>
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="major-1" data-section="existing-major" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="content-item existing-procedure" data-status="ongoing" data-procedure-id="major-2">
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
                        <div class="procedure-actions">
                            <button type="button" class="btn btn-outline-success btn-sm action-btn complete-existing-procedure-btn" 
                                    data-procedure-id="major-2" data-section="existing-major" title="Mark as Complete">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm action-btn cancel-existing-procedure-btn" 
                                    data-procedure-id="major-2" data-section="existing-major" title="Cancel Procedure">
                                <i class="fas fa-ban"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                                    data-procedure-id="major-2" data-section="existing-major" title="Remove Procedure">
                                <i class="fas fa-times"></i>
                            </button>
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
    
    // Procedure definitions
    const procedureDefinitions = {
        // Minor procedures
        'iv_insertion': {
            name: 'IV Line Insertion',
            icon: 'fas fa-syringe',
            type: 'minor'
        },
        'iv_infusion_saline': {
            name: 'IV Infusion - Normal Saline',
            icon: 'fas fa-tint',
            type: 'minor'
        },
        'iv_infusion_dextrose': {
            name: 'IV Infusion - 5% Dextrose',
            icon: 'fas fa-prescription-bottle',
            type: 'minor'
        },
        'blood_sample': {
            name: 'Blood Sample Collection',
            icon: 'fas fa-vial',
            type: 'minor'
        },
        'wound_dressing': {
            name: 'Wound Dressing Change',
            icon: 'fas fa-band-aid',
            type: 'minor'
        },
        'vital_signs': {
            name: 'Vital Signs Monitoring',
            icon: 'fas fa-heartbeat',
            type: 'minor'
        },
        'medication_admin': {
            name: 'Medication Administration',
            icon: 'fas fa-pills',
            type: 'minor'
        },
        'catheter_care': {
            name: 'Catheter Care',
            icon: 'fas fa-stethoscope',
            type: 'minor'
        },
        'oxygen_therapy': {
            name: 'Oxygen Therapy',
            icon: 'fas fa-lungs',
            type: 'minor'
        },
        // Major procedures
        'chest_xray': {
            name: 'Chest X-Ray',
            icon: 'fas fa-x-ray',
            type: 'major'
        },
        'ecg_monitoring': {
            name: 'ECG Monitoring',
            icon: 'fas fa-heartbeat',
            type: 'major'
        },
        'ct_scan': {
            name: 'CT Scan',
            icon: 'fas fa-search',
            type: 'major'
        },
        'mri_scan': {
            name: 'MRI Scan',
            icon: 'fas fa-brain',
            type: 'major'
        },
        'ultrasound': {
            name: 'Ultrasound',
            icon: 'fas fa-sound',
            type: 'major'
        },
        'endoscopy': {
            name: 'Endoscopy',
            icon: 'fas fa-microscope',
            type: 'major'
        },
        'bronchoscopy': {
            name: 'Bronchoscopy',
            icon: 'fas fa-lungs',
            type: 'major'
        },
        'chest_tube': {
            name: 'Chest Tube Insertion',
            icon: 'fas fa-lungs',
            type: 'major'
        },
        'surgery_prep': {
            name: 'Surgery Preparation',
            icon: 'fas fa-user-md',
            type: 'major'
        },
        'dialysis': {
            name: 'Dialysis',
            icon: 'fas fa-filter',
            type: 'major'
        }
    };
    
    // Selected procedures storage
    let selectedMinorProcedures = [];
    let selectedMajorProcedures = [];
    
    // Filter states
    let currentMinorFilter = 'all';
    let currentMajorFilter = 'all';
    
    // Nurse selection functionality
    function updateSelectedNurseDisplay(selectElement, initialsId, nameId, detailsId, containerSelector) {
        const selectedOption = selectElement.find('option:selected');
        const container = $(containerSelector);
        
        if (selectedOption.val() && selectedOption.val() !== '') {
            const name = selectedOption.data('name');
            const position = selectedOption.data('position');
            const initials = getInitials(name);
            
            $(initialsId).text(initials);
            $(nameId).text(name);
            $(detailsId).html(`<i class="fas fa-user-tag mr-1"></i>${position}`);
            
            container.removeClass('d-none');
        } else {
            container.addClass('d-none');
        }
        
        checkFormCompletion();
    }
    
    function getInitials(name) {
        if (!name) return '';
        const words = name.split(' ');
        if (words.length >= 2) {
            return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
        }
        return name.charAt(0).toUpperCase();
    }
    
    function checkFormCompletion() {
        const nurseOnDuty = $('#nurseOnDuty').val();
        const nurseReceivePassover = $('#nurseReceivePassover').val();
        
        if (nurseOnDuty && nurseReceivePassover && nurseOnDuty !== '' && nurseReceivePassover !== '') {
            $('#confirmPassoverBtn').prop('disabled', false);
        } else {
            $('#confirmPassoverBtn').prop('disabled', true);
        }
    }
    
    // Handle nurse on duty selection
    $('#nurseOnDuty').on('change', function() {
        updateSelectedNurseDisplay(
            $(this), 
            '#selectedOnDutyInitials', 
            '#selectedOnDutyName', 
            '#selectedOnDutyDetails', 
            '#nurseOnDuty + .selected-nurse-info'
        );
    });
    
    // Handle nurse receive passover selection
    $('#nurseReceivePassover').on('change', function() {
        updateSelectedNurseDisplay(
            $(this), 
            '#selectedReceiveInitials', 
            '#selectedReceiveName', 
            '#selectedReceiveDetails', 
            '#nurseReceivePassover + .selected-nurse-info'
        );
    });
    
    // Procedure management functions
    function addProcedureToList(procedureKey, notes, type) {
        const procedure = procedureDefinitions[procedureKey];
        if (!procedure) return;
        
        const procedureItem = {
            key: procedureKey,
            name: procedure.name,
            icon: procedure.icon,
            notes: notes || '',
            time: new Date().toLocaleTimeString(),
            status: 'pending',
            reason: '',
            id: Date.now() + Math.random() // Simple unique ID
        };
        
        if (type === 'minor') {
            selectedMinorProcedures.push(procedureItem);
            updateProcedureDisplay('minor');
            updateFilterCounts('minor');
        } else if (type === 'major') {
            selectedMajorProcedures.push(procedureItem);
            updateProcedureDisplay('major');
            updateFilterCounts('major');
        }
    }
    
    function removeProcedureFromList(procedureId, type) {
        // Find the procedure name for confirmation
        const procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const procedure = procedures.find(p => p.id === procedureId);
        
        if (!procedure) return;
        
        // Show confirmation dialog
        if (confirm(`Are you sure you want to remove "${procedure.name}"?\n\nThis action cannot be undone.`)) {
            if (type === 'minor') {
                selectedMinorProcedures = selectedMinorProcedures.filter(p => p.id !== procedureId);
                updateProcedureDisplay('minor');
                updateFilterCounts('minor');
            } else if (type === 'major') {
                selectedMajorProcedures = selectedMajorProcedures.filter(p => p.id !== procedureId);
                updateProcedureDisplay('major');
                updateFilterCounts('major');
            }
        }
    }
    
    function updateProcedureStatus(procedureId, type, status, reason = '') {
        let procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const procedure = procedures.find(p => p.id === procedureId);
        
        if (procedure) {
            procedure.status = status;
            procedure.reason = reason;
            updateProcedureDisplay(type);
            updateFilterCounts(type);
        }
    }
    
    function updateFilterCounts(type) {
        const procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const prefix = type === 'minor' ? 'minor' : 'major';
        
        const counts = {
            all: procedures.length,
            pending: procedures.filter(p => p.status === 'pending').length,
            completed: procedures.filter(p => p.status === 'completed').length,
            cancelled: procedures.filter(p => p.status === 'cancelled').length
        };
        
        $(`#${prefix}AllCount`).text(counts.all);
        $(`#${prefix}PendingCount`).text(counts.pending);
        $(`#${prefix}CompletedCount`).text(counts.completed);
        $(`#${prefix}CancelledCount`).text(counts.cancelled);
    }
    
    function applyFilter(type, filter) {
        const procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const containerId = type === 'minor' ? '#selectedMinorProcedures' : '#selectedMajorProcedures';
        
        // Update filter state
        if (type === 'minor') {
            currentMinorFilter = filter;
        } else {
            currentMajorFilter = filter;
        }
        
        // Update active button
        $(`.procedure-filters button[data-type="${type}"]`).removeClass('active');
        $(`.procedure-filters button[data-type="${type}"][data-filter="${filter}"]`).addClass('active');
        
        // Show/hide procedures based on filter
        $(`${containerId} .procedure-item`).each(function() {
            const procedureId = parseInt($(this).data('procedure-id'));
            const procedure = procedures.find(p => p.id === procedureId);
            
            if (!procedure) return;
            
            if (filter === 'all' || procedure.status === filter) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    }
    
    function updateProcedureDisplay(type) {
        const procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const containerId = type === 'minor' ? '#selectedMinorProcedures' : '#selectedMajorProcedures';
        const container = $(containerId);
        const currentFilter = type === 'minor' ? currentMinorFilter : currentMajorFilter;
        
        container.empty();
        
        procedures.forEach(procedure => {
            // Determine if procedure should be visible based on current filter
            const isVisible = currentFilter === 'all' || procedure.status === currentFilter;
            const hiddenClass = isVisible ? '' : 'hidden';
            
            // Build action buttons based on status
            let actionButtons = '';
            if (procedure.status === 'pending') {
                actionButtons = `
                    <button type="button" class="btn btn-outline-success btn-sm action-btn complete-procedure-btn" 
                            data-procedure-id="${procedure.id}" data-type="${type}" title="Mark as Complete">
                        <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm action-btn cancel-procedure-btn" 
                            data-procedure-id="${procedure.id}" data-type="${type}" title="Cancel Procedure">
                        <i class="fas fa-ban"></i>
                    </button>
                `;
            }
            
            const procedureHtml = `
                <div class="procedure-item ${procedure.status} ${hiddenClass}" data-procedure-id="${procedure.id}">
                    <div class="procedure-icon">
                        <i class="${procedure.icon}"></i>
                    </div>
                    <div class="procedure-details">
                        <div class="procedure-name">
                            ${procedure.name}
                            <span class="procedure-status ${procedure.status}">${procedure.status.charAt(0).toUpperCase() + procedure.status.slice(1)}</span>
                        </div>
                        ${procedure.notes ? `<div class="procedure-description">${procedure.notes}</div>` : ''}
                        ${procedure.reason ? `<div class="procedure-reason">Reason: ${procedure.reason}</div>` : ''}
                    </div>
                    <div class="procedure-time">${procedure.time}</div>
                    <div class="procedure-actions">
                        ${actionButtons}
                        <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-procedure-btn" 
                                data-procedure-id="${procedure.id}" data-type="${type}" title="Remove Procedure">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.append(procedureHtml);
        });
    }
    
    // Add minor procedure
    $('#addMinorProcedureBtn').on('click', function() {
        const procedureKey = $('#minorProcedureSelect').val();
        const notes = $('#minorProcedureNotes').val().trim();
        
        if (procedureKey) {
            addProcedureToList(procedureKey, notes, 'minor');
            $('#minorProcedureSelect').val('');
            $('#minorProcedureNotes').val('');
        }
    });
    
    // Add major procedure
    $('#addMajorProcedureBtn').on('click', function() {
        const procedureKey = $('#majorProcedureSelect').val();
        const notes = $('#majorProcedureNotes').val().trim();
        
        if (procedureKey) {
            addProcedureToList(procedureKey, notes, 'major');
            $('#majorProcedureSelect').val('');
            $('#majorProcedureNotes').val('');
        }
    });
    
    // Remove procedure (delegated event handler)
    $(document).on('click', '.remove-procedure-btn', function() {
        const procedureId = parseInt($(this).data('procedure-id'));
        const type = $(this).data('type');
        removeProcedureFromList(procedureId, type);
    });
    
    // Complete procedure (delegated event handler)
    $(document).on('click', '.complete-procedure-btn', function() {
        const procedureId = parseInt($(this).data('procedure-id'));
        const type = $(this).data('type');
        const procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const procedure = procedures.find(p => p.id === procedureId);
        
        if (procedure) {
            const reason = prompt(`Mark "${procedure.name}" as completed.\n\nPlease provide completion notes (optional):`);
            if (reason !== null) { // User didn't cancel
                updateProcedureStatus(procedureId, type, 'completed', reason);
            }
        }
    });
    
    // Cancel procedure (delegated event handler)
    $(document).on('click', '.cancel-procedure-btn', function() {
        const procedureId = parseInt($(this).data('procedure-id'));
        const type = $(this).data('type');
        const procedures = type === 'minor' ? selectedMinorProcedures : selectedMajorProcedures;
        const procedure = procedures.find(p => p.id === procedureId);
        
        if (procedure) {
            const reason = prompt(`Cancel "${procedure.name}".\n\nPlease provide cancellation reason:`);
            if (reason !== null && reason.trim() !== '') { // User provided a reason
                updateProcedureStatus(procedureId, type, 'cancelled', reason.trim());
            } else if (reason !== null) {
                alert('Cancellation reason is required.');
            }
        }
    });
    
    // Filter button handlers
    $(document).on('click', '.procedure-filters button', function() {
        const filter = $(this).data('filter');
        const type = $(this).data('type');
        const section = $(this).data('section');
        
        if (section) {
            // Handle existing procedure filters
            applyExistingProcedureFilter(section, filter);
        } else {
            // Handle new procedure filters
            applyFilter(type, filter);
        }
    });
    
    // Existing procedure management functions
    function applyExistingProcedureFilter(section, filter) {
        // Update active button
        $(`.procedure-filters button[data-section="${section}"]`).removeClass('active');
        $(`.procedure-filters button[data-section="${section}"][data-filter="${filter}"]`).addClass('active');
        
        // Show/hide procedures based on filter
        $('.existing-procedure').each(function() {
            const procedureStatus = $(this).data('status');
            const parentSection = $(this).closest('.passover-section');
            
            // Check if this procedure belongs to the current section
            const isMinorSection = parentSection.find(`button[data-section="${section}"]`).length > 0;
            if (!isMinorSection) return;
            
            if (filter === 'all' || procedureStatus === filter) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    }
    
    function updateExistingProcedureStatus(procedureId, section, newStatus, reason = '') {
        const procedure = $(`.existing-procedure[data-procedure-id="${procedureId}"]`);
        if (!procedure.length) return;
        
        // Update data attribute
        procedure.attr('data-status', newStatus);
        
        // Update status badge
        const statusBadge = procedure.find('.status-badge');
        statusBadge.removeClass('status-completed status-ongoing status-pending status-cancelled');
        statusBadge.addClass(`status-${newStatus}`);
        statusBadge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
        
        // Update visual styling
        procedure.removeClass('completed cancelled ongoing pending');
        procedure.addClass(newStatus);
        
        // Handle completion/cancellation reason
        const existingReason = procedure.find('.completion-reason, .cancellation-reason');
        existingReason.remove();
        
        if (reason && (newStatus === 'completed' || newStatus === 'cancelled')) {
            const reasonType = newStatus === 'completed' ? 'Completion Notes' : 'Cancellation Reason';
            const reasonHtml = `
                <div class="${newStatus === 'completed' ? 'completion' : 'cancellation'}-reason mt-2">
                    <small class="text-muted"><strong>${reasonType}:</strong> ${reason}</small>
                </div>
            `;
            procedure.find('.content-description').append(reasonHtml);
        }
        
        // Update action buttons
        updateExistingProcedureActions(procedureId, section, newStatus);
        
        // Update filter counts
        updateExistingProcedureCounts(section);
    }
    
    function updateExistingProcedureActions(procedureId, section, status) {
        const procedure = $(`.existing-procedure[data-procedure-id="${procedureId}"]`);
        const actionsContainer = procedure.find('.procedure-actions');
        
        let actionButtons = '';
        if (status === 'pending' || status === 'ongoing') {
            actionButtons = `
                <button type="button" class="btn btn-outline-success btn-sm action-btn complete-existing-procedure-btn" 
                        data-procedure-id="${procedureId}" data-section="${section}" title="Mark as Complete">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm action-btn cancel-existing-procedure-btn" 
                        data-procedure-id="${procedureId}" data-section="${section}" title="Cancel Procedure">
                    <i class="fas fa-ban"></i>
                </button>
            `;
        }
        
        actionButtons += `
            <button type="button" class="btn btn-outline-danger btn-sm action-btn remove-existing-procedure-btn" 
                    data-procedure-id="${procedureId}" data-section="${section}" title="Remove Procedure">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        actionsContainer.html(actionButtons);
    }
    
    function updateExistingProcedureCounts(section) {
        const prefix = section.replace('-', '');
        let counts = {
            all: 0,
            pending: 0,
            completed: 0,
            ongoing: 0,
            cancelled: 0
        };
        
        // Count procedures in the specific section
        const sectionElement = $(`.procedure-filters button[data-section="${section}"]`).closest('.passover-section');
        sectionElement.find('.existing-procedure').each(function() {
            const status = $(this).data('status');
            counts.all++;
            if (counts[status] !== undefined) {
                counts[status]++;
            }
        });
        
        // Update count badges
        Object.keys(counts).forEach(status => {
            $(`#${prefix}${status.charAt(0).toUpperCase() + status.slice(1)}Count`).text(counts[status]);
        });
    }
    
    // Event handlers for existing procedures
    $(document).on('click', '.complete-existing-procedure-btn', function() {
        const procedureId = $(this).data('procedure-id');
        const section = $(this).data('section');
        const procedure = $(`.existing-procedure[data-procedure-id="${procedureId}"]`);
        const procedureName = procedure.find('.content-title').text().replace(/\s+(Pending|Ongoing|Completed|Cancelled)/, '').trim();
        
        const reason = prompt(`Mark "${procedureName}" as completed.\n\nPlease provide completion notes (optional):`);
        if (reason !== null) { // User didn't cancel
            updateExistingProcedureStatus(procedureId, section, 'completed', reason);
        }
    });
    
    $(document).on('click', '.cancel-existing-procedure-btn', function() {
        const procedureId = $(this).data('procedure-id');
        const section = $(this).data('section');
        const procedure = $(`.existing-procedure[data-procedure-id="${procedureId}"]`);
        const procedureName = procedure.find('.content-title').text().replace(/\s+(Pending|Ongoing|Completed|Cancelled)/, '').trim();
        
        const reason = prompt(`Cancel "${procedureName}".\n\nPlease provide cancellation reason:`);
        if (reason !== null && reason.trim() !== '') { // User provided a reason
            updateExistingProcedureStatus(procedureId, section, 'cancelled', reason.trim());
        } else if (reason !== null) {
            alert('Cancellation reason is required.');
        }
    });
    
    $(document).on('click', '.remove-existing-procedure-btn', function() {
        const procedureId = $(this).data('procedure-id');
        const section = $(this).data('section');
        const procedure = $(`.existing-procedure[data-procedure-id="${procedureId}"]`);
        const procedureName = procedure.find('.content-title').text().replace(/\s+(Pending|Ongoing|Completed|Cancelled)/, '').trim();
        
        if (confirm(`Are you sure you want to remove "${procedureName}"?\n\nThis action cannot be undone.`)) {
            procedure.fadeOut(300, function() {
                $(this).remove();
                updateExistingProcedureCounts(section);
            });
        }
    });
    
    // Handle confirm button
    $('#confirmPassoverBtn').on('click', function() {
        const nurseOnDuty = $('#nurseOnDuty option:selected');
        const nurseReceivePassover = $('#nurseReceivePassover option:selected');
        
        if (nurseOnDuty.val() && nurseReceivePassover.val()) {
            // Show confirmation
            const onDutyName = nurseOnDuty.data('name');
            const receiveName = nurseReceivePassover.data('name');
            
            let confirmMessage = `Confirm Nurse Passover:\n\nFrom: ${onDutyName}\nTo: ${receiveName}`;
            
            if (selectedMinorProcedures.length > 0) {
                confirmMessage += `\n\nMinor Procedures (${selectedMinorProcedures.length}):`;
                selectedMinorProcedures.forEach(p => {
                    confirmMessage += `\n- ${p.name}`;
                });
            }
            
            if (selectedMajorProcedures.length > 0) {
                confirmMessage += `\n\nMajor Procedures (${selectedMajorProcedures.length}):`;
                selectedMajorProcedures.forEach(p => {
                    confirmMessage += `\n- ${p.name}`;
                });
            }
            
            confirmMessage += '\n\nProceed with this selection?';
            
            if (confirm(confirmMessage)) {
                // Here you can add functionality to save the passover selection and procedures
                // For now, we'll show a success message
                alert(`Nurse passover confirmed!\n\nHandover from ${onDutyName} to ${receiveName} has been recorded with ${selectedMinorProcedures.length} minor and ${selectedMajorProcedures.length} major procedures.`);
                
                // Optionally scroll to the vital signs section to continue with passover details
                $('html, body').animate({
                    scrollTop: $('.passover-section:nth-child(2)').offset().top
                }, 500);
            }
        }
    });
    
    // Handle reset button
    $('#resetFormBtn').on('click', function() {
        if (confirm('Are you sure you want to reset the form? This will clear all selected nurses and procedures.')) {
            $('#nursePassoverForm')[0].reset();
            $('.selected-nurse-info').addClass('d-none');
            $('#confirmPassoverBtn').prop('disabled', true);
            
            // Clear procedures
            selectedMinorProcedures = [];
            selectedMajorProcedures = [];
            updateProcedureDisplay('minor');
            updateProcedureDisplay('major');
            updateFilterCounts('minor');
            updateFilterCounts('major');
            
            // Reset filters
            currentMinorFilter = 'all';
            currentMajorFilter = 'all';
            $('.procedure-filters button').removeClass('active');
            $('.procedure-filters button[data-filter="all"]').addClass('active');
        }
    });
    
    // Initialize form state
    checkFormCompletion();
    
    // Initialize filter counts
    updateFilterCounts('minor');
    updateFilterCounts('major');
    updateExistingProcedureCounts('existing-minor');
    updateExistingProcedureCounts('existing-major');
    
    // Check if there are pre-selected values and update displays
    if ($('#nurseOnDuty').val()) {
        $('#nurseOnDuty').trigger('change');
    }
    if ($('#nurseReceivePassover').val()) {
        $('#nurseReceivePassover').trigger('change');
    }
});
</script>
@endsection 