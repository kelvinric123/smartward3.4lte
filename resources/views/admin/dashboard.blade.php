@extends('adminlte::page')

@section('title', 'Super Admin Dashboard')

@section('content_header')
    <h1>Super Admin Dashboard</h1>
@stop

@section('content')
    <!-- Dashboard Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-2"></i>Dashboard Filters
                    </h3>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm">
                    <div class="card-body">
                        <div class="row">
                            <!-- Ward Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ward_id">Ward</label>
                                    <select name="ward_id" id="ward_id" class="form-control">
                                        <option value="">All Wards</option>
                                        @foreach($wards as $ward)
                                            <option value="{{ $ward->id }}" 
                                                {{ $selectedWardId == $ward->id ? 'selected' : '' }}>
                                                {{ $ward->name }} - {{ $ward->hospital->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Date Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" 
                                           value="{{ $selectedDate }}">
                                </div>
                            </div>
                            
                            <!-- Duration Filter -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration">Duration</label>
                                    <select name="duration" id="duration" class="form-control">
                                        <option value="daily" {{ $selectedDuration == 'daily' ? 'selected' : '' }}>
                                            Daily
                                        </option>
                                        <option value="weekly" {{ $selectedDuration == 'weekly' ? 'selected' : '' }}>
                                            Weekly
                                        </option>
                                        <option value="monthly" {{ $selectedDuration == 'monthly' ? 'selected' : '' }}>
                                            Monthly
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filter Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i>Clear Filters
                                </a>
                                @if($selectedWard)
                                    <span class="badge badge-info ml-2">
                                        Viewing: {{ $selectedWard->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Users</h3>
                    <p>User Management</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Hospitals</h3>
                    <p>Hospital Management</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Doctors</h3>
                    <p>Doctor Management</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>Roles</h3>
                    <p>Role Management</p>
                </div>
                <div class="icon">
                    <i class="fas fa-lock"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Admin dashboard loaded');
        
        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const wardSelect = document.getElementById('ward_id');
            const dateInput = document.getElementById('date');
            const durationSelect = document.getElementById('duration');
            
            // Auto-submit when ward changes
            wardSelect.addEventListener('change', function() {
                filterForm.submit();
            });
            
            // Auto-submit when date changes
            dateInput.addEventListener('change', function() {
                filterForm.submit();
            });
            
            // Auto-submit when duration changes
            durationSelect.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    </script>
@stop 