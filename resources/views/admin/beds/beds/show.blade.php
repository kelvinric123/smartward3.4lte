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
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $bed->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Updated</th>
                                                <td>{{ $bed->updated_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        </table>
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
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop 