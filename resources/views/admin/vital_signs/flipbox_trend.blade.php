@extends('adminlte::page')

@section('title', $patient->name . ' - Vital Signs Trend')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Vital Signs Monitor - {{ $patient->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">Vital Signs Monitor</li>
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
                    <div class="card-header bg-primary">
                        <h3 class="card-title">
                            <i class="fas fa-heartbeat mr-1"></i> Patient Vital Signs Monitor
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Add New Record
                            </a>
                            <a href="{{ route('admin.vital-signs.trend', ['patientId' => $patient->id]) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-chart-line"></i> Standard View
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        @if(count($vitalSigns) >= 2)
                            <div class="row no-gutters">
                                <!-- Patient Info Section -->
                                <div class="col-lg-3 bg-dark">
                                    <div class="p-3">
                                        <div class="text-center mb-3">
                                            <div class="user-block">
                                                <img class="img-circle" src="{{ asset('img/patient-avatar.png') }}" alt="Patient">
                                                <span class="username">
                                                    <a href="{{ route('admin.patients.show', $patient->id) }}" class="text-white">{{ $patient->name }}</a>
                                                </span>
                                                <span class="description text-light">
                                                    <i class="fas fa-id-card mr-1"></i> MRN: {{ $patient->mrn ?: 'N/A' }}
                                                </span>
                                                <span class="description text-light">
                                                    <i class="fas fa-birthday-cake mr-1"></i> Age: {{ $patient->age }} years
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="info-box bg-gradient-primary mb-3">
                                            <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Latest EWS Score</span>
                                                <span class="info-box-number">
                                                    {{ $vitalSigns->last()->total_ews ?? 'N/A' }}
                                                </span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: {{ ($vitalSigns->last()->total_ews ?? 0) * 10 }}%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    {{ $vitalSigns->last()->formatted_recorded_at ?? 'No data' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="info-box bg-gradient-success mb-3">
                                            <span class="info-box-icon"><i class="fas fa-user-md"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Last Assessment</span>
                                                <span class="info-box-number">
                                                    {{ $vitalSigns->last()->formatted_recorded_at ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('admin.vital-signs.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-block mb-4">
                                            <i class="fas fa-plus mr-1"></i> Record New Vital Signs
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Vital Signs Charts -->
                                <div class="col-lg-9">
                                    <div class="flipbox-container">
                                        <div class="flipbox-item active" id="vitals-charts">
                                            <div class="p-3">
                                                <h4>Vital Sign Trends</h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-header bg-light">
                                                                <h3 class="card-title">Temperature (°C)</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="temperatureChart" height="200"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-header bg-light">
                                                                <h3 class="card-title">Heart Rate (bpm)</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="heartRateChart" height="200"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flipbox-item" id="bp-resp-chart">
                                            <div class="p-3">
                                                <h4>Respiratory & Blood Pressure</h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-header bg-light">
                                                                <h3 class="card-title">Blood Pressure (mmHg)</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="bpChart" height="200"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-header bg-light">
                                                                <h3 class="card-title">Respiratory Rate & Oxygen Saturation</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="respO2Chart" height="200"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flipbox-item" id="ews-chart">
                                            <div class="p-3">
                                                <h4>Early Warning Score Trend</h4>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-header bg-light">
                                                                <h3 class="card-title">Early Warning Score (EWS)</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <canvas id="ewsChart" height="200"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flipbox-controls text-center p-2 bg-light">
                                        <div class="btn-group">
                                            <button class="btn btn-primary" onclick="showFlipboxItem('vitals-charts')">Vital Signs</button>
                                            <button class="btn btn-primary" onclick="showFlipboxItem('bp-resp-chart')">BP & Respiratory</button>
                                            <button class="btn btn-primary" onclick="showFlipboxItem('ews-chart')">EWS Chart</button>
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
        /* Flipbox styling */
        .flipbox-container {
            position: relative;
            min-height: 400px;
        }
        
        .flipbox-item {
            display: none;
            transition: all 0.4s ease;
        }
        
        .flipbox-item.active {
            display: block;
            animation: flipIn 0.5s ease;
        }
        
        @keyframes flipIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
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
                            fill: true
                        },
                        {
                            label: 'Diastolic BP (mmHg)',
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
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.raw;
                                    return `${datasetLabel}: ${value} mmHg`;
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
                            label: 'Respiratory Rate (bpm)',
                            data: respiratoryRates,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true,
                            yAxisID: 'respRate'
                        },
                        {
                            label: 'Oxygen Saturation (%)',
                            data: oxygenSaturations,
                            borderColor: 'rgba(201, 203, 207, 1)',
                            backgroundColor: 'rgba(201, 203, 207, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true,
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
                        label: 'Early Warning Score',
                        data: ewsScores,
                        backgroundColor: ewsScores.map(score => {
                            if (score >= 7) return 'rgba(220, 53, 69, 0.7)';  // Danger
                            if (score >= 5) return 'rgba(255, 193, 7, 0.7)';  // Warning
                            if (score >= 3) return 'rgba(23, 162, 184, 0.7)'; // Info
                            return 'rgba(40, 167, 69, 0.7)';                  // Success
                        }),
                        borderColor: ewsScores.map(score => {
                            if (score >= 7) return 'rgb(220, 53, 69)';  // Danger
                            if (score >= 5) return 'rgb(255, 193, 7)';  // Warning
                            if (score >= 3) return 'rgb(23, 162, 184)'; // Info
                            return 'rgb(40, 167, 69)';                  // Success
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 14
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const score = context.raw;
                                    let riskLevel = 'Low Risk';
                                    
                                    if (score >= 7) riskLevel = 'High Risk';
                                    else if (score >= 5) riskLevel = 'Medium-High Risk';
                                    else if (score >= 3) riskLevel = 'Medium Risk';
                                    
                                    return [`EWS Score: ${score}`, `Risk Level: ${riskLevel}`];
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
    
    // Flipbox control function
    function showFlipboxItem(itemId) {
        // Hide all items
        document.querySelectorAll('.flipbox-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Show the selected item
        document.getElementById(itemId).classList.add('active');
    }
</script>
@stop 