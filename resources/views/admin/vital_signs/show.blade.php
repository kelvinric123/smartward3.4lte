@extends('adminlte::page')

@section('title', 'View Vital Signs')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Vital Signs Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $vitalSign->patient_id) }}">{{ $vitalSign->patient->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vital-signs.index', ['patient_id' => $vitalSign->patient_id]) }}">Vital Signs</a></li>
                    <li class="breadcrumb-item active">View Details</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vital Signs for {{ $vitalSign->patient->name }} ({{ $vitalSign->formatted_recorded_at }})</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vital-signs.edit', $vitalSign->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.vital-signs.index', ['patient_id' => $vitalSign->patient_id]) }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-{{ $vitalSign->status_color }}">
                                    <div class="card-header">
                                        <h3 class="card-title">Early Warning Score (EWS)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <div class="display-4 font-weight-bold">{{ $vitalSign->total_ews }}</div>
                                                <h4>{{ $vitalSign->clinical_status }}</h4>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th class="text-center">Value</th>
                                                            <th class="text-center">Score</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Temperature</td>
                                                            <td class="text-center">{{ $vitalSign->temperature ? $vitalSign->temperature . ' °C' : '-' }}</td>
                                                            <td class="text-center">{{ $vitalSign->temperature_score }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Heart Rate</td>
                                                            <td class="text-center">{{ $vitalSign->heart_rate ? $vitalSign->heart_rate . ' bpm' : '-' }}</td>
                                                            <td class="text-center">{{ $vitalSign->heart_rate_score }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Respiratory Rate</td>
                                                            <td class="text-center">{{ $vitalSign->respiratory_rate ? $vitalSign->respiratory_rate . ' breaths/min' : '-' }}</td>
                                                            <td class="text-center">{{ $vitalSign->respiratory_rate_score }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Blood Pressure</td>
                                                            <td class="text-center">{{ $vitalSign->systolic_bp && $vitalSign->diastolic_bp ? $vitalSign->systolic_bp . '/' . $vitalSign->diastolic_bp . ' mmHg' : '-' }}</td>
                                                            <td class="text-center">{{ $vitalSign->blood_pressure_score }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Oxygen Saturation</td>
                                                            <td class="text-center">{{ $vitalSign->oxygen_saturation ? $vitalSign->oxygen_saturation . '%' : '-' }}</td>
                                                            <td class="text-center">{{ $vitalSign->oxygen_saturation_score }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Consciousness</td>
                                                            <td class="text-center">{{ $vitalSign->consciousness ?: '-' }}</td>
                                                            <td class="text-center">{{ $vitalSign->consciousness_score }}</td>
                                                        </tr>
                                                        <tr class="font-weight-bold">
                                                            <td colspan="2" class="text-right">Total EWS Score:</td>
                                                            <td class="text-center">{{ $vitalSign->total_ews }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Patient & Recording Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 40%">Patient</th>
                                                <td>
                                                    <a href="{{ route('admin.patients.show', $vitalSign->patient_id) }}">
                                                        {{ $vitalSign->patient->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Patient ID/MRN</th>
                                                <td>{{ $vitalSign->patient->mrn ?: 'Not available' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Patient ID Type</th>
                                                <td>{{ strtoupper($vitalSign->patient->identity_type) }}: {{ $vitalSign->patient->identity_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Recorded By</th>
                                                <td>{{ $vitalSign->recorder ? $vitalSign->recorder->name : 'Unknown' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Recorded Date & Time</th>
                                                <td>{{ $vitalSign->formatted_recorded_at }}</td>
                                            </tr>
                                            <tr>
                                                <th>Record Created</th>
                                                <td>{{ $vitalSign->created_at->format('d M Y, h:ia') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Updated</th>
                                                <td>{{ $vitalSign->updated_at->format('d M Y, h:ia') }}</td>
                                            </tr>
                                        </table>
                                        
                                        @if($vitalSign->notes)
                                            <div class="mt-4">
                                                <h5>Notes</h5>
                                                <div class="p-3 bg-light">
                                                    {{ $vitalSign->notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Actions</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.vital-signs.edit', $vitalSign->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('admin.vital-signs.index', ['patient_id' => $vitalSign->patient_id]) }}" class="btn btn-info">
                                                <i class="fas fa-list"></i> View All Records
                                            </a>
                                            <a href="{{ route('admin.vital-signs.trend', $vitalSign->patient_id) }}" class="btn btn-primary">
                                                <i class="fas fa-chart-line"></i> View Trend
                                            </a>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-modal">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="delete-modal">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirm Delete</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this vital signs record?</p>
                                                        <p>This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.vital-signs.destroy', $vitalSign->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Additional styling for EWS colors */
        .card-success .card-header { background-color: #28a745; color: white; }
        .card-info .card-header { background-color: #17a2b8; color: white; }
        .card-warning .card-header { background-color: #ffc107; color: #212529; }
        .card-danger .card-header { background-color: #dc3545; color: white; }
    </style>
@stop 