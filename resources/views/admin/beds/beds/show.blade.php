@extends('adminlte::page')

@section('title', 'Bed Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Bed Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.beds.index') }}">Beds</a></li>
                    <li class="breadcrumb-item active">View Bed</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{ $bed->bed_number }}</h3>
                            <div>
                                <a href="{{ route('admin.beds.beds.edit', $bed) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if ($bed->status == 'occupied' && $bed->patient)
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#dischargeModal">
                                    <i class="fas fa-procedures"></i> Discharge
                                </button>
                                @endif
                                <a href="{{ route('admin.beds.beds.index') }}" class="btn btn-default">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title">Bed Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Bed ID</th>
                                                <td>{{ $bed->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Bed Number</th>
                                                <td>{{ $bed->bed_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge {{ $bed->status_badge_class }}">
                                                        {{ $bed->formatted_status }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Ward</th>
                                                <td>
                                                    <a href="{{ route('admin.beds.wards.show', $bed->ward) }}">
                                                        {{ $bed->ward->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Hospital</th>
                                                <td>{{ $bed->ward->hospital->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Specialty</th>
                                                <td>{{ $bed->ward->specialty->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Notes</th>
                                                <td>{{ $bed->notes ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @if ($bed->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title">Assigned Personnel & Patient</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Consultant</th>
                                                <td>
                                                    @if ($bed->consultant)
                                                        <a href="{{ route('admin.consultants.show', $bed->consultant) }}">
                                                            {{ $bed->consultant->name }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">{{ $bed->consultant->specialty->name }}</small>
                                                    @else
                                                        <span class="text-muted">Not Assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Nurse</th>
                                                <td>
                                                    @if ($bed->nurse)
                                                        {{ $bed->nurse->name }}
                                                    @else
                                                        <span class="text-muted">Not Assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Patient</th>
                                                <td>
                                                    @if ($bed->patient)
                                                        <a href="{{ route('admin.patients.show', $bed->patient) }}">
                                                            {{ $bed->patient->name }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">
                                                            MRN: {{ $bed->patient->mrn ?: 'Not available' }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ strtoupper($bed->patient->identity_type) }}: 
                                                            {{ $bed->patient->identity_number }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">Not Assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if ($bed->status == 'occupied' && $bed->patient)
                                <div class="card mt-3">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title">Admission Details</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Admitted Since</th>
                                                <td>{{ $bed->updated_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Length of Stay</th>
                                                <td>
                                                    @php
                                                        $days = $bed->updated_at->diffInDays(now());
                                                        $hours = $bed->updated_at->diffInHours(now()) % 24;
                                                    @endphp
                                                    {{ $days }} days, {{ $hours }} hours
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Admission Notes</th>
                                                <td>{{ $bed->notes ?? 'No notes available' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

<!-- Discharge Confirmation Modal -->
<div class="modal fade" id="dischargeModal" tabindex="-1" role="dialog" aria-labelledby="dischargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dischargeModalLabel">Confirm Patient Discharge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.beds.beds.discharge', $bed) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($bed->patient)
                    <p>Are you sure you want to discharge <strong>{{ $bed->patient->name }}</strong> from bed <strong>{{ $bed->bed_number }}</strong>?</p>
                    <p>This will:</p>
                    <ul>
                        <li>Update the bed status to Available</li>
                        <li>Remove the patient, consultant, and nurse assignments</li>
                        <li>Create a discharge record in the database</li>
                    </ul>
                    
                    <div class="form-group">
                        <label for="notes">Discharge Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any notes about this discharge"></textarea>
                    </div>
                    @else
                    <p class="text-danger">No patient is currently assigned to this bed.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    @if ($bed->patient)
                    <button type="submit" class="btn btn-danger">Discharge Patient</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop 