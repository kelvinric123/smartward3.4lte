@extends('adminlte::page')

@section('title', 'Ward Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ward Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">View Ward</li>
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
                            <h3 class="card-title">{{ $ward->name }}</h3>
                            <div>
                                <a href="{{ route('admin.beds.wards.dashboard', $ward) }}" class="btn btn-primary">
                                    <i class="fas fa-th"></i> View Dashboard
                                </a>
                                <a href="{{ route('admin.beds.wards.edit', $ward) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.beds.wards.index') }}" class="btn btn-default">
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
                                        <h3 class="card-title">Basic Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Ward ID</th>
                                                <td>{{ $ward->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $ward->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Capacity</th>
                                                <td>{{ $ward->capacity }} beds</td>
                                            </tr>
                                            <tr>
                                                <th>Description</th>
                                                <td>{{ $ward->description ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @if ($ward->is_active)
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
                                        <h3 class="card-title">Related Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Hospital</th>
                                                <td>
                                                    <a href="{{ route('admin.hospitals.show', $ward->hospital) }}">
                                                        {{ $ward->hospital->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Specialty</th>
                                                <td>{{ $ward->specialty->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $ward->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Updated</th>
                                                <td>{{ $ward->updated_at->format('d M Y, h:i A') }}</td>
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