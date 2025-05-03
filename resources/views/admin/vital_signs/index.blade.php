@extends('adminlte::page')

@section('title', isset($patient) ? $patient->name . ' - Vital Signs' : 'Vital Signs')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ isset($patient) ? $patient->name . ' - Vital Signs' : 'Vital Signs' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @if(isset($patient))
                        <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient->id) }}">{{ $patient->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active">Vital Signs</li>
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
                        <h3 class="card-title">Vital Signs Records</h3>
                        <div class="card-tools">
                            @if(isset($patient))
                                <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Record Vital Signs
                                </a>
                                <a href="{{ route('admin.vital-signs.trend', $patient->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-chart-line"></i> View Trend
                                </a>
                            @else
                                <a href="{{ route('admin.vital-signs.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Record Vital Signs
                                </a>
                            @endif
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    @if(!isset($patient))
                                        <th>Patient</th>
                                    @endif
                                    <th>Date & Time</th>
                                    <th>Temp (°C)</th>
                                    <th>HR (bpm)</th>
                                    <th>RR (bpm)</th>
                                    <th>BP (mmHg)</th>
                                    <th>SpO2 (%)</th>
                                    <th>Consciousness</th>
                                    <th>EWS</th>
                                    <th>Recorded By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vitalSigns as $vitalSign)
                                    <tr class="@if($vitalSign->total_ews >= 7) table-danger @elseif($vitalSign->total_ews >= 5) table-warning @elseif($vitalSign->total_ews >= 3) table-info @else @endif">
                                        <td>{{ $vitalSign->id }}</td>
                                        @if(!isset($patient))
                                            <td>
                                                <a href="{{ route('admin.patients.show', $vitalSign->patient_id) }}">
                                                    {{ $vitalSign->patient->name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $vitalSign->patient->mrn ?: 'No MRN' }}
                                                </small>
                                            </td>
                                        @endif
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
                                        <td colspan="{{ isset($patient) ? '11' : '12' }}" class="text-center">No vital signs records found.</td>
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