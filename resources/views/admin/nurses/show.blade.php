@extends('adminlte::page')

@section('title', 'Nurse Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Nurse Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.nurses.index') }}">Nurses</a></li>
                <li class="breadcrumb-item active">{{ $nurse->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $nurse->name }}
                        @if ($nurse->active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.nurses.edit', $nurse) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.nurses.destroy', $nurse) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this nurse?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Personal Information</h3>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Name:</dt>
                                        <dd class="col-sm-8">{{ $nurse->name }}</dd>

                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">{{ $nurse->email }}</dd>

                                        <dt class="col-sm-4">Phone:</dt>
                                        <dd class="col-sm-8">{{ $nurse->phone ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            @if ($nurse->active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </dd>

                                        <dt class="col-sm-4">Hospital:</dt>
                                        <dd class="col-sm-8">
                                            @if ($hospital)
                                                <a href="{{ route('admin.hospitals.show', $hospital) }}">{{ $hospital->name }}</a>
                                            @else
                                                Not Assigned
                                            @endif
                                        </dd>

                                        <dt class="col-sm-4">Last Login:</dt>
                                        <dd class="col-sm-8">
                                            @if ($nurse->last_login_at)
                                                {{ $nurse->last_login_at->format('M d, Y h:i A') }}
                                            @else
                                                Never
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Current Bed Assignments</h3>
                                </div>
                                <div class="card-body">
                                    @php
                                        $assignedBeds = $nurse->beds()->with(['ward.hospital', 'patient'])->get();
                                    @endphp

                                    @if ($assignedBeds->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Bed</th>
                                                        <th>Ward</th>
                                                        <th>Patient</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($assignedBeds as $bed)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('admin.beds.beds.show', $bed) }}">
                                                                    {{ $bed->bed_number }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.beds.wards.show', $bed->ward) }}">
                                                                    {{ $bed->ward->name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if ($bed->patient)
                                                                    <a href="{{ route('admin.patients.show', $bed->patient) }}">
                                                                        {{ $bed->patient->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No patient</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $bed->status == 'occupied' ? 'danger' : ($bed->status == 'available' ? 'success' : 'warning') }}">
                                                                    {{ ucfirst($bed->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No beds currently assigned to this nurse.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.nurses.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to Nurses List
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script></script>
@stop 