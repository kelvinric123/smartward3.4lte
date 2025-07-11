@extends('adminlte::page')

@section('title', isset($patient) ? $patient->name . ' - Vital Signs' : 'Patient Vital Signs')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ isset($patient) ? $patient->name . ' - Vital Signs' : 'Patient Vital Signs' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @if(isset($patient))
                        <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.vital-signs.index') }}">Vital Signs</a></li>
                        <li class="breadcrumb-item active">{{ $patient->name }}</li>
                    @else
                        <li class="breadcrumb-item active">Vital Signs</li>
                    @endif
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
                
                @if(isset($patient))
                <!-- Patient Information Card -->
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-injured mr-2"></i>
                            Patient Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong><i class="fas fa-id-card mr-1"></i> Name:</strong> {{ $patient->name }}
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-hospital-user mr-1"></i> MRN:</strong> {{ $patient->mrn ?: 'Not Available' }}
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-calendar-alt mr-1"></i> DOB:</strong> {{ $patient->dob ? $patient->dob->format('d/m/Y') : 'Not Available' }}
                                @if($patient->dob)
                                    ({{ $patient->age }} years)
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-venus-mars mr-1"></i> Gender:</strong> {{ $patient->gender ?? 'Not Available' }}
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-phone mr-1"></i> Contact:</strong> {{ $patient->phone ?? 'Not Available' }}
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group float-right">
                                    <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-user mr-1"></i> Patient Profile
                                    </a>
                                    <a href="{{ route('admin.vital-signs.trend', $patient->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-chart-line mr-1"></i> View Trend
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vital Signs Records</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Record Vital Signs
                            </a>
                            <a href="{{ route('admin.vital-signs.trend', $patient->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-line"></i> View Trend
                            </a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date & Time</th>
                                    <th>Temp (°C)</th>
                                    <th>HR (bpm)</th>
                                    <th>RR (bpm)</th>
                                    <th>BP (mmHg)</th>
                                    <th>SpO2 (%)</th>
                                    <th>Consciousness</th>
                                    <th>GCS</th>
                                    <th>EWS</th>
                                    <th>Recorded By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vitalSigns as $vitalSign)
                                    <tr class="@if($vitalSign->total_ews >= 7) table-danger @elseif($vitalSign->total_ews >= 5) table-warning @elseif($vitalSign->total_ews >= 3) table-info @else @endif">
                                        <td>{{ $vitalSign->id }}</td>
                                        <td>{{ $vitalSign->formatted_recorded_at }}</td>
                                        <td>{{ $vitalSign->temperature ?: '-' }}</td>
                                        <td>{{ $vitalSign->heart_rate ?: '-' }}</td>
                                        <td>{{ $vitalSign->respiratory_rate ?: '-' }}</td>
                                        <td>{{ $vitalSign->systolic_bp ?: '-' }}/{{ $vitalSign->diastolic_bp ?: '-' }}</td>
                                        <td>{{ $vitalSign->oxygen_saturation ?: '-' }}</td>
                                        <td>{{ $vitalSign->consciousness ?: '-' }}</td>
                                        <td>
                                            @if($vitalSign->gcs_total)
                                                <span class="badge badge-{{ $vitalSign->gcs_status_color }}">
                                                    {{ $vitalSign->gcs_display }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $vitalSign->status_color }}">
                                                {{ $vitalSign->total_ews }} - {{ $vitalSign->clinical_status }}
                                            </span>
                                        </td>
                                        <td>{{ $vitalSign->recorder ? $vitalSign->recorder->name : 'Unknown' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.vital-signs.show', $vitalSign->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.vital-signs.edit', $vitalSign->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-modal-{{ $vitalSign->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="delete-modal-{{ $vitalSign->id }}">
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No vital signs records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        {{ $vitalSigns->appends(request()->query())->links() }}
                    </div>
                </div>
                @else
                <!-- Patients List Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Patients with Vital Sign Records</h3>
                        <div class="card-tools">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.vital-signs.index', ['view' => 'table']) }}" class="btn {{ request('view') === 'table' ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm">
                                    <i class="fas fa-list"></i> Table View
                                </a>
                                <a href="{{ route('admin.vital-signs.index', ['view' => 'card']) }}" class="btn {{ !request('view') || request('view') === 'card' ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm">
                                    <i class="fas fa-th-large"></i> Card View
                                </a>
                            </div>
                            <a href="{{ route('admin.vital-signs.create') }}" class="btn btn-primary btn-sm ml-2">
                                <i class="fas fa-plus"></i> Record New Vital Signs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.vital-signs.index') }}">
                                    @if(request('view'))
                                        <input type="hidden" name="view" value="{{ request('view') }}">
                                    @endif
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
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient Name</th>
                                        <th>MRN</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Records Count</th>
                                        <th>Last Record</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $patient)
                                        <tr>
                                            <td>{{ $patient->id }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ $patient->mrn ?: 'Not available' }}</td>
                                            <td>{{ ucfirst($patient->gender ?? 'Not specified') }}</td>
                                            <td>{{ $patient->age ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $patient->vital_signs_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($patient->latestVitalSigns)
                                                    {{ $patient->latestVitalSigns->formatted_recorded_at ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.vital-signs.index', ['patient_id' => $patient->id]) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View Records
                                                </a>
                                                <a href="{{ route('admin.vital-signs.trend', $patient->id) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-chart-line"></i> Trend
                                                </a>
                                                <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Record
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No patients with vital signs found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $patients->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
                @endif
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Additional styling for EWS colors */
        .badge-success { background-color: #28a745; }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
    </style>
@stop 