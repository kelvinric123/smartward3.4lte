@extends('adminlte::page')

@section('title', $patient->name . ' - Vital Signs Trend')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Vital Signs Trend - {{ $patient->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient->id) }}">{{ $patient->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vital-signs.index', ['patient_id' => $patient->id]) }}">Vital Signs</a></li>
                    <li class="breadcrumb-item active">Trend</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(count($vitalSigns) < 2)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Not enough data to display trend charts. At least two vital sign records are required.
                    </div>
                @endif
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vital Signs Trend</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Record
                            </a>
                            <a href="{{ route('admin.vital-signs.index', ['patient_id' => $patient->id]) }}" class="btn btn-default btn-sm">
                                <i class="fas fa-list"></i> View All Records
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if(count($vitalSigns) >= 2)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Temperature (°C)</h3>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="temperatureChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Heart Rate (bpm)</h3>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="heartRateChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Blood Pressure (mmHg)</h3>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="bpChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Respiratory Rate & Oxygen Saturation</h3>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="respO2Chart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Early Warning Score (EWS)</h3>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="ewsChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                                <h4>Not enough data to display trend charts</h4>
                                <p class="text-muted">Record at least one more set of vital signs to see trends over time.</p>
                                <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus"></i> Record Vital Signs
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Table</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Temp (°C)</th>
                                    <th>HR (bpm)</th>
                                    <th>RR (bpm)</th>
                                    <th>BP (mmHg)</th>
                                    <th>SpO2 (%)</th>
                                    <th>Consciousness</th>
                                    <th>EWS</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vitalSigns->sortByDesc('recorded_at') as $vitalSign)
                                    <tr class="@if($vitalSign->total_ews >= 7) table-danger @elseif($vitalSign->total_ews >= 5) table-warning @elseif($vitalSign->total_ews >= 3) table-info @else @endif">
                                        <td>{{ $vitalSign->formatted_recorded_at }}</td>
                                        <td>{{ $vitalSign->temperature ?: '-' }}</td>
                                        <td>{{ $vitalSign->heart_rate ?: '-' }}</td>
                                        <td>{{ $vitalSign->respiratory_rate ?: '-' }}</td>
                                        <td>{{ $vitalSign->systolic_bp ?: '-' }}/{{ $vitalSign->diastolic_bp ?: '-' }}</td>
                                        <td>{{ $vitalSign->oxygen_saturation ?: '-' }}</td>
                                        <td>{{ $vitalSign->consciousness ?: '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $vitalSign->status_color }}">
                                                {{ $vitalSign->total_ews }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.vital-signs.show', $vitalSign->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(count($vitalSigns) >= 2)
            // Prepare data for charts
            const dates = @json($vitalSigns->pluck('formatted_recorded_at'));
            const temperatures = @json($vitalSigns->pluck('temperature'));
            const heartRates = @json($vitalSigns->pluck('heart_rate'));
            const respiratoryRates = @json($vitalSigns->pluck('respiratory_rate'));
            const systolicBPs = @json($vitalSigns->pluck('systolic_bp'));
            const diastolicBPs = @json($vitalSigns->pluck('diastolic_bp'));
            const oxygenSaturations = @json($vitalSigns->pluck('oxygen_saturation'));
            const ewsScores = @json($vitalSigns->pluck('total_ews'));
            
            // Temperature Chart
            new Chart(document.getElementById('temperatureChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: temperatures,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            min: 35,
                            max: 40
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Temperature: ${context.raw}°C`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Heart Rate Chart
            new Chart(document.getElementById('heartRateChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Heart Rate (bpm)',
                        data: heartRates,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Heart Rate: ${context.raw} bpm`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Blood Pressure Chart
            new Chart(document.getElementById('bpChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Systolic BP (mmHg)',
                            data: systolicBPs,
                            borderColor: 'rgba(255, 159, 64, 1)',
                            backgroundColor: 'rgba(255, 159, 64, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: false
                        },
                        {
                            label: 'Diastolic BP (mmHg)',
                            data: diastolicBPs,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: false
                        }
                    ]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    return `${datasetLabel}: ${context.raw} mmHg`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Respiratory Rate & O2 Saturation Chart
            new Chart(document.getElementById('respO2Chart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Respiratory Rate (breaths/min)',
                            data: respiratoryRates,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: false,
                            yAxisID: 'respRate'
                        },
                        {
                            label: 'Oxygen Saturation (%)',
                            data: oxygenSaturations,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: false,
                            yAxisID: 'o2sat'
                        }
                    ]
                },
                options: {
                    scales: {
                        respRate: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Respiratory Rate (breaths/min)'
                            }
                        },
                        o2sat: {
                            type: 'linear',
                            position: 'right',
                            min: 90,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Oxygen Saturation (%)'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.raw;
                                    if (datasetLabel.includes('Oxygen')) {
                                        return `${datasetLabel}: ${value}%`;
                                    } else {
                                        return `${datasetLabel}: ${value} breaths/min`;
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            // EWS Chart
            new Chart(document.getElementById('ewsChart'), {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'EWS Score',
                        data: ewsScores,
                        backgroundColor: ewsScores.map(score => {
                            if (score >= 7) return 'rgba(220, 53, 69, 0.7)';
                            if (score >= 5) return 'rgba(255, 193, 7, 0.7)';
                            if (score >= 3) return 'rgba(23, 162, 184, 0.7)';
                            return 'rgba(40, 167, 69, 0.7)';
                        }),
                        borderColor: ewsScores.map(score => {
                            if (score >= 7) return 'rgba(220, 53, 69, 1)';
                            if (score >= 5) return 'rgba(255, 193, 7, 1)';
                            if (score >= 3) return 'rgba(23, 162, 184, 1)';
                            return 'rgba(40, 167, 69, 1)';
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 12
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const score = context.raw;
                                    let status = 'Low Risk';
                                    if (score >= 7) status = 'Critical';
                                    else if (score >= 5) status = 'High Risk';
                                    else if (score >= 3) status = 'Medium Risk';
                                    
                                    return `EWS Score: ${score} (${status})`;
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@stop 