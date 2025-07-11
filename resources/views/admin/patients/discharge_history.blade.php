@extends('adminlte::page')

@section('title', 'Discharge History - ' . $patient->name)

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Discharge History</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient->id) }}">{{ $patient->name }}</a></li>
                    <li class="breadcrumb-item active">Discharge History</li>
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
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i> Discharge History for {{ $patient->name }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Patient
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($discharges->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Ward</th>
                                            <th>Bed</th>
                                            <th>Discharge Type</th>
                                            <th>Discharged By</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($discharges as $discharge)
                                            <tr>
                                                <td>{{ $discharge->formatted_discharge_date }}</td>
                                                <td>
                                                    @if($discharge->ward)
                                                        {{ $discharge->ward->name }}
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>{{ $discharge->bed_number ?: 'Unknown' }}</td>
                                                <td>
                                                    @if($discharge->discharge_type == 'routine')
                                                        <span class="badge badge-success">Routine</span>
                                                    @elseif($discharge->discharge_type == 'against_medical_advice')
                                                        <span class="badge badge-warning">Against Medical Advice</span>
                                                    @elseif($discharge->discharge_type == 'transfer')
                                                        <span class="badge badge-info">Transfer</span>
                                                    @elseif($discharge->discharge_type == 'deceased')
                                                        <span class="badge badge-danger">Deceased</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($discharge->discharge_type) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($discharge->dischargedBy)
                                                        {{ $discharge->dischargedBy->name }}
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($discharge->discharge_notes))
                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#notesModal-{{ $discharge->id }}">
                                                            <i class="fas fa-sticky-note"></i> View
                                                        </button>
                                                        
                                                        <!-- Notes Modal -->
                                                        <div class="modal fade" id="notesModal-{{ $discharge->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Discharge Notes</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        {{ $discharge->discharge_notes }}
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No notes</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                {{ $discharges->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                <h4>No discharge records found</h4>
                                <p class="text-muted">This patient has no discharge history records.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        /* Additional styling for badges */
        .badge-success { background-color: #28a745; }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
    </style>
@stop 