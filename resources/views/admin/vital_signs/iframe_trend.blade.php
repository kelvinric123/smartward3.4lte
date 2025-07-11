@extends('layouts.iframe')

@section('title', $patient->name . ' - Vital Signs Trend')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="row mb-2">
                    <div class="col-md-5">
                        <div class="form-group mb-2">
                            <label>Start Date:</label>
                            <div class="input-group">
                                <input type="date" class="form-control form-control-sm" id="startDate" value="{{ now()->subWeek()->format('Y-m-d') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-2">
                            <label>End Date:</label>
                            <div class="input-group">
                                <input type="date" class="form-control form-control-sm" id="endDate" value="{{ now()->format('Y-m-d') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-sm w-100" id="updateBtn">Update</button>
                    </div>
                </div>
                
                <!-- Blood Pressure Chart -->
                <div class="card card-navy chart-card" id="bpChartCard">
                    <div class="card-header py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-heartbeat mr-1"></i> Blood Pressure
                                <small class="text-muted ml-2">(← →)</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-2">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-2">
                                <div class="mr-4">
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-danger"></i> Systolic BP
                                    </span>
                                </div>
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-info"></i> Diastolic BP
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="bpChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Heart Rate Chart -->
                <div class="card card-navy chart-card" id="hrChartCard" style="display: none;">
                    <div class="card-header py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-heartbeat mr-1"></i> Heart Rate
                                <small class="text-muted ml-2">(← →)</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-2">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-2">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-primary"></i> Heart Rate (bpm)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="hrChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Temperature Chart -->
                <div class="card card-navy chart-card" id="tempChartCard" style="display: none;">
                    <div class="card-header py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-thermometer-half mr-1"></i> Temperature
                                <small class="text-muted ml-2">(← →)</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-2">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-2">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-danger"></i> Temperature (°C)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="tempChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SPO2 Chart -->
                <div class="card card-navy chart-card" id="spo2ChartCard" style="display: none;">
                    <div class="card-header py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-lungs mr-1"></i> Oxygen Saturation
                                <small class="text-muted ml-2">(← →)</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-2">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-2">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-warning"></i> SpO2 (%)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="spo2Chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Respiratory Rate Chart -->
                <div class="card card-navy chart-card" id="rrChartCard" style="display: none;">
                    <div class="card-header py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-wind mr-1"></i> Respiratory Rate
                                <small class="text-muted ml-2">(← →)</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-2">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-2">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-purple"></i> Respiratory Rate (breaths/min)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="rrChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EWS Chart -->
                <div class="card card-navy chart-card" id="ewsChartCard" style="display: none;">
                    <div class="card-header py-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i> Early Warning Score
                                <small class="text-muted ml-2">(← →)</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-2">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-2">
                                <div class="mr-2">
                                    <span class="badge badge-pill badge-success">0-2: Low Risk</span>
                                </div>
                                <div class="mr-2">
                                    <span class="badge badge-pill badge-info">3-4: Medium Risk</span>
                                </div>
                                <div class="mr-2">
                                    <span class="badge badge-pill badge-warning">5-6: High Risk</span>
                                </div>
                                <div>
                                    <span class="badge badge-pill badge-danger">7+: Critical</span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="ewsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .chart-card {
        margin-bottom: 12px;
    }
    
    .chart-card .card-title {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .chart-card .card-header {
        padding: 0.5rem 1rem;
    }
    
    .chart-card .card-body {
        padding: 0.5rem;
    }
    
    .badge-pill {
        font-size: 0.75rem;
    }
    
    .form-control-sm {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .input-group-text {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    label {
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
    }
    
    /* Responsive charts */
    @media (max-width: 768px) {
        .chart-container {
            height: 200px !important;
        }
    }
    
    /* Custom colors */
    .text-purple {
        color: #6f42c1;
    }
    
    /* Swipe animations */
    .chart-card.animated {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Tooltip styles */
    .tooltip-inner {
        max-width: 200px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(count($vitalSigns) >= 1)
            // Prepare data for charts
            const dates = @json($vitalSigns->pluck('formatted_recorded_at'));
            const systolicBPs = @json($vitalSigns->pluck('systolic_bp'));
            const diastolicBPs = @json($vitalSigns->pluck('diastolic_bp'));
            const heartRates = @json($vitalSigns->pluck('heart_rate'));
            const temperatures = @json($vitalSigns->pluck('temperature'));
            const oxygenSaturations = @json($vitalSigns->pluck('oxygen_saturation'));
            const respiratoryRates = @json($vitalSigns->pluck('respiratory_rate'));
            const ewsScores = @json($vitalSigns->pluck('total_ews'));
            
            // Blood Pressure Chart
            const bpCtx = document.getElementById('bpChart').getContext('2d');
            const bpChart = new Chart(bpCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Systolic BP',
                            data: systolicBPs,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)'
                        },
                        {
                            label: 'Diastolic BP',
                            data: diastolicBPs,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'mmHg'
                            }
                        }
                    }
                }
            });
            
            // Heart Rate Chart
            const hrCtx = document.getElementById('hrChart').getContext('2d');
            const hrChart = new Chart(hrCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Heart Rate',
                            data: heartRates,
                            backgroundColor: 'rgba(93, 83, 201, 0.2)',
                            borderColor: 'rgba(93, 83, 201, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(93, 83, 201, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'bpm'
                            }
                        }
                    }
                }
            });
            
            // Temperature Chart
            const tempCtx = document.getElementById('tempChart').getContext('2d');
            const tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Temperature',
                            data: temperatures,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: '°C'
                            }
                        }
                    }
                }
            });
            
            // SpO2 Chart
            const spo2Ctx = document.getElementById('spo2Chart').getContext('2d');
            const spo2Chart = new Chart(spo2Ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Oxygen Saturation',
                            data: oxygenSaturations,
                            backgroundColor: 'rgba(255, 193, 7, 0.2)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(255, 193, 7, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 80,
                            max: 100,
                            title: {
                                display: true,
                                text: '%'
                            }
                        }
                    }
                }
            });
            
            // Respiratory Rate Chart
            const rrCtx = document.getElementById('rrChart').getContext('2d');
            const rrChart = new Chart(rrCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Respiratory Rate',
                            data: respiratoryRates,
                            backgroundColor: 'rgba(156, 39, 176, 0.2)',
                            borderColor: 'rgba(156, 39, 176, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(156, 39, 176, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'breaths/min'
                            }
                        }
                    }
                }
            });
            
            // EWS Chart
            const ewsCtx = document.getElementById('ewsChart').getContext('2d');
            const ewsChart = new Chart(ewsCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'EWS',
                            data: ewsScores,
                            backgroundColor: 'rgba(76, 175, 80, 0.2)',
                            borderColor: 'rgba(76, 175, 80, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: function(context) {
                                var index = context.dataIndex;
                                var value = context.dataset.data[index];
                                
                                if (value >= 7) {
                                    return 'rgba(220, 53, 69, 1)'; // Critical
                                } else if (value >= 5) {
                                    return 'rgba(255, 193, 7, 1)'; // High Risk
                                } else if (value >= 3) {
                                    return 'rgba(23, 162, 184, 1)'; // Medium Risk
                                } else {
                                    return 'rgba(40, 167, 69, 1)'; // Low Risk
                                }
                            }
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Score'
                            }
                        }
                    }
                }
            });
            
            // Chart card navigation
            const chartCards = [
                'bpChartCard',
                'hrChartCard',
                'tempChartCard',
                'spo2ChartCard',
                'rrChartCard',
                'ewsChartCard'
            ];
            let currentCardIndex = 0;
            
            function showCard(index) {
                // Hide all cards
                chartCards.forEach(cardId => {
                    document.getElementById(cardId).style.display = 'none';
                });
                
                // Show the selected card
                document.getElementById(chartCards[index]).style.display = 'block';
                currentCardIndex = index;
            }
            
            function nextCard() {
                let newIndex = (currentCardIndex + 1) % chartCards.length;
                showCard(newIndex);
            }
            
            function prevCard() {
                let newIndex = (currentCardIndex - 1 + chartCards.length) % chartCards.length;
                showCard(newIndex);
            }
            
            // Add event listeners to next/prev buttons
            document.querySelectorAll('.next-chart').forEach(button => {
                button.addEventListener('click', nextCard);
            });
            
            document.querySelectorAll('.prev-chart').forEach(button => {
                button.addEventListener('click', prevCard);
            });
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight') {
                    nextCard();
                } else if (e.key === 'ArrowLeft') {
                    prevCard();
                }
            });
            
            // Touch navigation with Hammer.js
            const container = document.querySelector('.container-fluid');
            const hammer = new Hammer(container);
            
            hammer.on('swipeleft', nextCard);
            hammer.on('swiperight', prevCard);
            
            // Visual indicator for swiping
            const leftIndicator = document.createElement('div');
            leftIndicator.className = 'drag-indicator left';
            leftIndicator.innerHTML = '<i class="fas fa-chevron-left"></i>';
            document.body.appendChild(leftIndicator);
            
            const rightIndicator = document.createElement('div');
            rightIndicator.className = 'drag-indicator right';
            rightIndicator.innerHTML = '<i class="fas fa-chevron-right"></i>';
            document.body.appendChild(rightIndicator);
            
            hammer.on('panstart', function() {
                container.classList.add('dragging');
            });
            
            hammer.on('panend', function() {
                container.classList.remove('dragging');
            });
            
            // Date filter functionality
            document.getElementById('updateBtn').addEventListener('click', function() {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                
                if (startDate && endDate) {
                    window.location.href = '{{ route("admin.vital-signs.iframe-trend", $patient->id) }}?start=' + startDate + '&end=' + endDate;
                }
            });
        @else
            // No vital signs data available
            const noDataContainer = document.createElement('div');
            noDataContainer.className = 'alert alert-info text-center mt-4';
            noDataContainer.innerHTML = '<i class="fas fa-info-circle mr-2"></i> No vital signs data available for this time period.';
            document.querySelector('.col-md-12').appendChild(noDataContainer);
        @endif
    });
</script>
@endsection 