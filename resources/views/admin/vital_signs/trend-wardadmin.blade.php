@extends('adminlte::page')

@section('title', $patient->name . ' - Vital Signs Trend')

@section('content_header')
    <div class="container-fluid bg-dark text-white py-2">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.beds.wards.dashboard.direct', $ward) }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Ward
            </a>
            <h1 class="m-0">Vital Signs Trend - {{ $patient->name }}</h1>
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
                            <a href="{{ route('admin.vital-signs.create.direct', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Record
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
                                <a href="{{ route('admin.vital-signs.create.direct', ['patient_id' => $patient->id]) }}" class="btn btn-primary mt-3">
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
                                            <a href="{{ route('admin.vital-signs.show.direct', $vitalSign->id) }}" class="btn btn-sm btn-info">
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
        
        /* Fullscreen mode styles */
        body.fullscreen-mode .main-sidebar {
            display: none !important;
        }
        
        body.fullscreen-mode .content-wrapper {
            margin-left: 0 !important;
        }
        
        /* Hide pushmenu toggle button in fullscreen mode */
        body.fullscreen-mode [data-widget="pushmenu"] {
            display: none !important;
        }
    </style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set fullscreen mode
        $('body').addClass('fullscreen-mode');
        
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
                            label: 'Systolic BP',
                            data: systolicBPs,
                            borderColor: 'rgba(255, 159, 64, 1)',
                            backgroundColor: 'rgba(255, 159, 64, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        },
                        {
                            label: 'Diastolic BP',
                            data: diastolicBPs,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        }
                    ]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Systolic BP') {
                                        return `Systolic BP: ${context.raw} mmHg`;
                                    } else {
                                        return `Diastolic BP: ${context.raw} mmHg`;
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            // Respiratory Rate & Oxygen Saturation Chart
            new Chart(document.getElementById('respO2Chart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Respiratory Rate',
                            data: respiratoryRates,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Oxygen Saturation',
                            data: oxygenSaturations,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Respiratory Rate (bpm)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            min: 85,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Oxygen Saturation (%)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Respiratory Rate') {
                                        return `Respiratory Rate: ${context.raw} bpm`;
                                    } else {
                                        return `Oxygen Saturation: ${context.raw}%`;
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            // EWS Chart
            new Chart(document.getElementById('ewsChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'EWS Score',
                        data: ewsScores,
                        borderColor: 'rgba(233, 30, 99, 1)',
                        backgroundColor: 'rgba(233, 30, 99, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            min: 0,
                            max: 12,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let score = context.raw;
                                    let status = '';
                                    
                                    if (score >= 7) {
                                        status = '(Urgent: High Risk)';
                                    } else if (score >= 5) {
                                        status = '(Action: Medium Risk)';
                                    } else if (score >= 3) {
                                        status = '(Monitor: Low Risk)';
                                    } else {
                                        status = '(Normal)';
                                    }
                                    
                                    return `EWS Score: ${score} ${status}`;
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