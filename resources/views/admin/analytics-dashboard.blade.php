@extends('adminlte::page')

@section('title', 'Analytics Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-chart-line mr-2"></i>Analytics Dashboard</h1>
                <p class="text-muted">Comprehensive hospital metrics and insights</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        
        <!-- Quick Overview Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $bedOccupancyMetrics['occupancy_rate'] }}%</h3>
                        <p>Bed Occupancy Rate</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bed"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $patientFlowMetrics['admissions']['today'] }}</h3>
                        <p>Admissions Today</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $patientStatusMetrics['critical_patients'] }}</h3>
                        <p>Critical Patients</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $patientStatusMetrics['urgent_alerts'] }}</h3>
                        <p>Urgent Alerts</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bed Occupancy Metrics Section -->
        <div class="row" id="bed-occupancy">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bed mr-2"></i>Bed Occupancy Metrics</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Overall Bed Statistics -->
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-bed"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Beds</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['total_beds'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-user-injured"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Occupied Beds</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['occupied_beds'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Available</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['available_beds'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-broom"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Cleaning Needed</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['cleaning_needed_beds'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-secondary"><i class="fas fa-wrench"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Maintenance</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['maintenance_beds'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Avg Length of Stay</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['average_length_of_stay'] }} days</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-exchange-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Bed Turnover (30d)</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['bed_turnover'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-chart-pie"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Occupancy Rate</span>
                                        <span class="info-box-number">{{ $bedOccupancyMetrics['occupancy_rate'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bed Status Chart -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Bed Status Distribution</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="bedStatusChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Ward-wise breakdown -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Ward-wise Occupancy</h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($bedOccupancyMetrics['ward_breakdown'] as $ward)
                                            <div class="progress-group">
                                                <span class="progress-text">{{ $ward['ward_name'] }}</span>
                                                <span class="float-right"><b>{{ $ward['occupied_beds'] }}</b>/{{ $ward['total_beds'] }}</span>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-primary" style="width: {{ $ward['occupancy_rate'] }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Flow Indicators Section -->
        <div class="row" id="patient-flow">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Patient Flow Indicators</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Admissions -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h3 class="card-title">Admissions</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="text-success text-xl">
                                                <i class="fas fa-calendar-day"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-plus text-success"></i>
                                                    {{ $patientFlowMetrics['admissions']['today'] }}
                                                </span>
                                                <span class="text-muted">Today</span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="text-success text-xl">
                                                <i class="fas fa-calendar-week"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-plus text-success"></i>
                                                    {{ $patientFlowMetrics['admissions']['this_week'] }}
                                                </span>
                                                <span class="text-muted">This Week</span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-success text-xl">
                                                <i class="fas fa-calendar-alt"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-plus text-success"></i>
                                                    {{ $patientFlowMetrics['admissions']['this_month'] }}
                                                </span>
                                                <span class="text-muted">This Month</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Discharges -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-info">
                                        <h3 class="card-title">Discharges</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="text-info text-xl">
                                                <i class="fas fa-calendar-day"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-minus text-info"></i>
                                                    {{ $patientFlowMetrics['discharges']['today'] }}
                                                </span>
                                                <span class="text-muted">Today</span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="text-info text-xl">
                                                <i class="fas fa-calendar-week"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-minus text-info"></i>
                                                    {{ $patientFlowMetrics['discharges']['this_week'] }}
                                                </span>
                                                <span class="text-muted">This Week</span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-info text-xl">
                                                <i class="fas fa-calendar-alt"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-minus text-info"></i>
                                                    {{ $patientFlowMetrics['discharges']['this_month'] }}
                                                </span>
                                                <span class="text-muted">This Month</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfers -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-warning">
                                        <h3 class="card-title">Transfers</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="text-warning text-xl">
                                                <i class="fas fa-calendar-day"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-exchange-alt text-warning"></i>
                                                    {{ $patientFlowMetrics['transfers']['today'] }}
                                                </span>
                                                <span class="text-muted">Today</span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="text-warning text-xl">
                                                <i class="fas fa-calendar-week"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-exchange-alt text-warning"></i>
                                                    {{ $patientFlowMetrics['transfers']['this_week'] }}
                                                </span>
                                                <span class="text-muted">This Week</span>
                                            </p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="text-warning text-xl">
                                                <i class="fas fa-calendar-alt"></i>
                                            </p>
                                            <p class="d-flex flex-column text-right">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-exchange-alt text-warning"></i>
                                                    {{ $patientFlowMetrics['transfers']['this_month'] }}
                                                </span>
                                                <span class="text-muted">This Month</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Flow Chart -->
                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">7-Day Admission & Discharge Trend</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="patientFlowChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Active Movements</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <h3 class="text-warning">{{ $patientFlowMetrics['active_movements'] }}</h3>
                                            <p class="text-muted">Patients currently away from beds</p>
                                        </div>
                                        @if(!empty($patientFlowMetrics['peak_hours']))
                                            <hr>
                                            <p><strong>Peak Admission Hours:</strong></p>
                                            @foreach($patientFlowMetrics['peak_hours'] as $hour)
                                                <span class="badge badge-info mr-1">{{ $hour }}:00</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Status Monitoring Section -->
        <div class="row" id="patient-status">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-heartbeat mr-2"></i>Patient Status Monitoring</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Current Patients</span>
                                        <span class="info-box-number">{{ $patientStatusMetrics['total_current_patients'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-heartbeat"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Recent Vitals</span>
                                        <span class="info-box-number">{{ $patientStatusMetrics['patients_with_recent_vitals'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Critical (EWS ≥7)</span>
                                        <span class="info-box-number">{{ $patientStatusMetrics['critical_patients'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">High Risk (EWS 5-6)</span>
                                        <span class="info-box-number">{{ $patientStatusMetrics['high_risk_patients'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Patient Alerts</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-info">
                                                <i class="fas fa-bell mr-2"></i>Total Active Alerts
                                            </span>
                                            <span class="badge badge-info badge-lg">{{ $patientStatusMetrics['active_alerts'] }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-danger">
                                                <i class="fas fa-bell-slash mr-2"></i>Urgent Alerts
                                            </span>
                                            <span class="badge badge-danger badge-lg">{{ $patientStatusMetrics['urgent_alerts'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Risk Factor Breakdown</h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($patientStatusMetrics['risk_factor_breakdown'] as $factor => $count)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span>
                                                    @if($factor === 'fallrisk')
                                                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>Fall Risk
                                                    @elseif($factor === 'dnr')
                                                        <i class="fas fa-heart text-danger mr-2"></i>DNR
                                                    @elseif($factor === 'intubated')
                                                        <i class="fas fa-lungs text-primary mr-2"></i>Intubated
                                                    @elseif($factor === 'isolation')
                                                        <i class="fas fa-shield-virus text-info mr-2"></i>Isolation
                                                    @endif
                                                </span>
                                                <span class="badge badge-secondary">{{ $count }}</span>
                                            </div>
                                        @endforeach
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
    <style>
        .info-box .info-box-number {
            font-size: 1.8rem;
        }
        .progress-group {
            margin-bottom: 1rem;
        }
        .card-header {
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Bed Status Distribution Chart
            const bedStatusCtx = document.getElementById('bedStatusChart').getContext('2d');
            new Chart(bedStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Occupied', 'Available', 'Cleaning Needed', 'Maintenance'],
                    datasets: [{
                        data: [
                            {{ $chartData['bed_status_distribution']['occupied'] }},
                            {{ $chartData['bed_status_distribution']['available'] }},
                            {{ $chartData['bed_status_distribution']['cleaning_needed'] }},
                            {{ $chartData['bed_status_distribution']['maintenance'] }}
                        ],
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Patient Flow Chart
            const patientFlowCtx = document.getElementById('patientFlowChart').getContext('2d');
            new Chart(patientFlowCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($chartData['last_7_days'] as $day)
                            '{{ $day['date'] }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Admissions',
                        data: [
                            @foreach($chartData['last_7_days'] as $day)
                                {{ $day['admissions'] }},
                            @endforeach
                        ],
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Discharges',
                        data: [
                            @foreach($chartData['last_7_days'] as $day)
                                {{ $day['discharges'] }},
                            @endforeach
                        ],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@stop 
