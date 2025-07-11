@extends('adminlte::page')

@section('title', 'PAC Dashboard - Patient Admission Centre')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-plus-circle"></i> Patient Admission Centre (PAC)</h1>
                <p class="text-muted">Manage admissions and pre-admissions across all wards</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">PAC Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        
        <!-- Filters Row -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.pac.dashboard') }}" id="filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hospital_id">Hospital</label>
                                        <select name="hospital_id" id="hospital_id" class="form-control select2">
                                            <option value="">All Hospitals</option>
                                            @foreach($hospitals as $hospital)
                                                <option value="{{ $hospital->id }}" {{ $selectedHospitalId == $hospital->id ? 'selected' : '' }}>
                                                    {{ $hospital->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ward_id">Ward</label>
                                        <select name="ward_id" id="ward_id" class="form-control select2">
                                            <option value="">All Wards</option>
                                            @foreach($wards as $ward)
                                                <option value="{{ $ward->id }}" {{ $selectedWardId == $ward->id ? 'selected' : '' }}>
                                                    {{ $ward->name }} ({{ $ward->specialty->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status_filter">Bed Status</label>
                                        <select name="status_filter" id="status_filter" class="form-control">
                                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Statuses</option>
                                            <option value="available" {{ $statusFilter == 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="occupied" {{ $statusFilter == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                            <option value="cleaning_needed" {{ $statusFilter == 'cleaning_needed' ? 'selected' : '' }}>Cleaning Needed</option>
                                            <option value="maintenance" {{ $statusFilter == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group w-100">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics Row -->
        <div class="row mb-4">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $availableBeds }}</h3>
                        <p>Available Beds</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bed"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $occupiedBeds }}</h3>
                        <p>Occupied Beds</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-injured"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $cleaningNeededBeds }}</h3>
                        <p>Cleaning Needed</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-broom"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $maintenanceBeds }}</h3>
                        <p>Maintenance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $totalAlerts }}</h3>
                        <p>Active Alerts</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $overallOccupancyRate }}%</h3>
                        <p>Occupancy Rate</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Ward Status Overview -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-building"></i> Ward Status Overview</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-primary" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($wardStats->count() > 0)
                            <div class="row">
                                @foreach($wardStats as $stat)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-left-primary h-100">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="m-0">
                                                        {{ $stat['ward']->name }}
                                                        <small class="text-muted">({{ $stat['ward']->specialty->name }})</small>
                                                    </h5>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.beds.wards.dashboard', $stat['ward']->id) }}" 
                                                           class="btn btn-primary btn-sm" title="Ward Dashboard">
                                                            <i class="fas fa-tachometer-alt"></i>
                                                        </a>
                                                        @php
                                                            $availableBed = $stat['ward']->beds->where('status', 'available')->first();
                                                        @endphp
                                                        @if($stat['available'] > 0)
                                                            <a href="{{ route('admin.pac.admit', $stat['ward']->id) }}" 
                                                               class="btn btn-success btn-sm" 
                                                               title="Quick Admit">
                                                                <i class="fas fa-plus"></i>
                                                            </a>
                                                        @else
                                                            <button class="btn btn-success btn-sm" disabled title="No available beds">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row text-center">
                                                    <div class="col-3">
                                                        <div class="text-success">
                                                            <strong>{{ $stat['available'] }}</strong>
                                                            <br><small>Available</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-info">
                                                            <strong>{{ $stat['occupied'] }}</strong>
                                                            <br><small>Occupied</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-warning">
                                                            <strong>{{ $stat['cleaning_needed'] }}</strong>
                                                            <br><small>Cleaning</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="text-secondary">
                                                            <strong>{{ $stat['total'] }}</strong>
                                                            <br><small>Total</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-6 text-center">
                                                        <span class="badge badge-{{ $stat['occupancy_rate'] > 90 ? 'danger' : ($stat['occupancy_rate'] > 75 ? 'warning' : 'success') }}">
                                                            {{ $stat['occupancy_rate'] }}% Occupancy
                                                        </span>
                                                    </div>
                                                    <div class="col-6 text-center">
                                                        @if($stat['alerts'] > 0)
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-bell"></i> {{ $stat['alerts'] }} Alert{{ $stat['alerts'] > 1 ? 's' : '' }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> No Alerts
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-building fa-3x mb-3"></i>
                                <p>No wards found with the current filters.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Admissions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock"></i> Recent Admissions (24h)</h3>
                    </div>
                    <div class="card-body">
                        @if($recentAdmissions->count() > 0)
                            <div class="timeline timeline-inverse">
                                @foreach($recentAdmissions as $admission)
                                    <div class="time-label">
                                        <span class="bg-success">{{ $admission->admission_date->format('H:i') }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-user-plus bg-blue"></i>
                                        <div class="timeline-item">
                                            <h3 class="timeline-header">
                                                {{ $admission->patient->name }}
                                                <small class="text-muted">MRN: {{ $admission->patient->mrn }}</small>
                                            </h3>
                                            <div class="timeline-body">
                                                <strong>Ward:</strong> {{ $admission->bed->ward->name }}<br>
                                                <strong>Bed:</strong> {{ $admission->bed->bed_number }}<br>
                                                <strong>Consultant:</strong> {{ $admission->consultant->name ?? 'Not assigned' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <p>No recent admissions</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Waiting Patients -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-hourglass-half"></i> Waiting for Admission</h3>
                        <div class="card-tools">
                            <span class="badge badge-warning">{{ $waitingPatients->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($waitingPatients->count() > 0)
                            @foreach($waitingPatients as $patient)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong>{{ $patient->name }}</strong>
                                        <br><small class="text-muted">MRN: {{ $patient->mrn ?? 'Not assigned' }}</small>
                                        <br><small class="text-info">{{ $patient->gender ?? 'Not specified' }} • {{ $patient->age ?? 'Age unknown' }}</small>
                                    </div>
                                    <div>
                                        @php
                                            $availableWard = \App\Models\Ward::whereHas('beds', function($query) {
                                                $query->where('status', 'available');
                                            })->first();
                                        @endphp
                                        @if($availableWard)
                                            <a href="{{ route('admin.pac.admit', $availableWard->id) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Admit Patient">
                                                <i class="fas fa-plus"></i> Admit
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-success" disabled title="No available beds">
                                                <i class="fas fa-plus"></i> Admit
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                <p>No patients waiting for admission</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('css')
    <style>
        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }
        
        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #dee2e6;
            left: 31px;
            margin: 0;
            border-radius: 2px;
        }
        
        .timeline > div {
            margin-bottom: 15px;
            position: relative;
        }
        
        .timeline > div:before {
            content: " ";
            display: table;
        }
        
        .timeline > div:after {
            content: " ";
            display: table;
            clear: both;
        }
        
        .timeline > div > .timeline-item {
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            margin-top: 0;
            background: #fff;
            color: #444;
            margin-left: 60px;
            margin-right: 15px;
            padding: 0;
            position: relative;
        }
        
        .timeline > div > .timeline-item > .timeline-header {
            margin: 0;
            padding: 10px;
            font-weight: 600;
            background: #f4f4f4;
            border-bottom: 1px solid #ddd;
        }
        
        .timeline > div > .timeline-item > .timeline-body {
            padding: 10px;
        }
        
        .timeline > div > .fa,
        .timeline > div > .fas,
        .timeline > div > .far,
        .timeline > div > .fab,
        .timeline > div > .fal,
        .timeline > div > .fad,
        .timeline > div > .svg-inline--fa {
            background: #adb5bd;
            border-radius: 50%;
            border: 3px solid #dee2e6;
            color: #fff;
            float: left;
            font-size: 16px;
            height: 30px;
            line-height: 24px;
            margin: 0;
            position: absolute;
            text-align: center;
            top: 18px;
            left: 18px;
            width: 30px;
        }
        
        .timeline > .time-label > span {
            background-color: #fff;
            border-radius: 4px;
            color: #000;
            font-size: 16px;
            font-weight: 600;
            padding: 5px;
            position: absolute;
            top: 8px;
            left: 3px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });
            
            // Auto-refresh dashboard every 5 minutes
            setInterval(function() {
                refreshDashboard();
            }, 300000);
        });
        
        function refreshDashboard() {
            location.reload();
        }
    </script>
@stop 