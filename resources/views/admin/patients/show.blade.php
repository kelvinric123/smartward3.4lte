@extends('adminlte::page')

@section('title', 'Patient Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Patient Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item active">View Patient</li>
                </ol>
            </div>
        </div>
    </div>
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
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $patient->name }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.patients.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name:</label>
                                    <p>{{ $patient->name }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Medical Record Number:</label>
                                    <p>{{ $patient->mrn ?? 'Not assigned' }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>{{ $patient->identity_type == 'ic' ? 'IC Number' : 'Passport Number' }}:</label>
                                    <p>
                                        <span class="badge {{ $patient->identity_type == 'ic' ? 'badge-primary' : 'badge-secondary' }}">
                                            {{ strtoupper($patient->identity_type) }}
                                        </span>
                                        {{ $patient->identity_number }}
                                    </p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Age:</label>
                                    <p>{{ $patient->age ?? 'Not specified' }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Gender:</label>
                                    <p>{{ ucfirst($patient->gender ?? 'Not specified') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email:</label>
                                    <p>{{ $patient->email ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Phone:</label>
                                    <p>{{ $patient->phone ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Address:</label>
                                    <p>{{ $patient->address ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Registered On:</label>
                                    <p>{{ $patient->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this patient?')">
                                <i class="fas fa-trash"></i> Delete Patient
                            </button>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
                
                <!-- Vital Signs Section -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-heartbeat mr-1"></i> Vital Signs
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Record Vital Signs
                            </a>
                            <a href="{{ route('admin.vital-signs.trend', $patient->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-line"></i> View Trend
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($patient->vitalSigns()->count() > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Temp (°C)</th>
                                                    <th>HR (bpm)</th>
                                                    <th>RR (bpm)</th>
                                                    <th>BP (mmHg)</th>
                                                    <th>SpO2 (%)</th>
                                                    <th>Consciousness</th>
                                                    <th>EWS</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($patient->vitalSigns()->latest('recorded_at')->take(5)->get() as $vitalSign)
                                                    <tr class="@if($vitalSign->total_ews >= 7) table-danger @elseif($vitalSign->total_ews >= 5) table-warning @elseif($vitalSign->total_ews >= 3) table-info @else @endif">
                                                        <td>{{ $vitalSign->formatted_recorded_at }}</td>
                                                        <td>{{ $vitalSign->temperature ?: '-' }}</td>
                                                        <td>{{ $vitalSign->heart_rate ?: '-' }}</td>
                                                        <td>{{ $vitalSign->respiratory_rate ?: '-' }}</td>
                                                        <td>{{ $vitalSign->systolic_bp ?: '-' }}/{{ $vitalSign->diastolic_bp ?: '-' }}</td>
                                                        <td>{{ $vitalSign->oxygen_saturation ?: '-' }}</td>
                                                        <td>{{ $vitalSign->consciousness ?: '-' }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $vitalSign->status_color }}">
                                                                {{ $vitalSign->total_ews }} - {{ $vitalSign->clinical_status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.vital-signs.show', $vitalSign->id) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    @if($patient->vitalSigns()->count() > 5)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.vital-signs.index', ['patient_id' => $patient->id]) }}" class="btn btn-default">
                                                <i class="fas fa-list"></i> View All Vital Signs Records
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-heartbeat fa-4x text-muted mb-3"></i>
                                <h4>No vital signs recorded yet</h4>
                                <p class="text-muted">Record vital signs to monitor this patient's condition and Early Warning Score (EWS)</p>
                                <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus"></i> Record Vital Signs
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        /* Additional styling for EWS colors */
        .badge-success { background-color: #28a745; }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
    </style>
@stop 