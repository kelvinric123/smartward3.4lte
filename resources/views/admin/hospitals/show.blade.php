@extends('adminlte::page')

@section('title', 'Hospital Details')

@section('content_header')
    <h1>Hospital Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">{{ $hospital->name }}</h3>
                <div>
                    <a href="{{ route('admin.hospitals.edit', $hospital) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.hospitals.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Name</th>
                                    <td>{{ $hospital->name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($hospital->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $hospital->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $hospital->updated_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt"></i> Contact & Location
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Address</th>
                                    <td>{{ $hospital->address ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>City</th>
                                    <td>{{ $hospital->city ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>State/Province</th>
                                    <td>{{ $hospital->state ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>{{ $hospital->country ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Postal Code</th>
                                    <td>{{ $hospital->postal_code ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $hospital->phone ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $hospital->email ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Website</th>
                                    <td>
                                        @if ($hospital->website)
                                            <a href="{{ $hospital->website }}" target="_blank">{{ $hospital->website }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
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

@section('js')
    <script>
        // Any additional JavaScript can go here
    </script>
@stop 