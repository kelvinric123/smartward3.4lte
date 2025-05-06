@extends('adminlte::page')

@section('title', $patient->name . ' - Vital Signs Trend')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Vital Signs for {{ $patient->name }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.beds.wards.show', $patient->bed->ward_id ?? 0) }}" class="btn btn-outline-secondary">Back to Ward</a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="startDate" value="{{ now()->subWeek()->format('Y-m-d') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Date:</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="endDate" value="{{ now()->format('Y-m-d') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-primary" id="updateBtn">Update</button>
                </div>
                
                <!-- Blood Pressure Chart -->
                <div class="card card-navy chart-card" id="bpChartCard">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-heartbeat mr-1"></i> Blood Pressure
                                <small class="text-muted ml-2">(← →)</small>
                                <small class="text-muted ml-2">Swipe or use arrows to view more charts</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-3">
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
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="bpChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Heart Rate Chart -->
                <div class="card card-navy chart-card" id="hrChartCard" style="display: none;">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-heartbeat mr-1"></i> Heart Rate
                                <small class="text-muted ml-2">(← →)</small>
                                <small class="text-muted ml-2">Swipe or use arrows to view more charts</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-3">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-primary"></i> Heart Rate (bpm)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="hrChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Temperature Chart -->
                <div class="card card-navy chart-card" id="tempChartCard" style="display: none;">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-thermometer-half mr-1"></i> Temperature
                                <small class="text-muted ml-2">(← →)</small>
                                <small class="text-muted ml-2">Swipe or use arrows to view more charts</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-3">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-danger"></i> Temperature (°C)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="tempChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SPO2 Chart -->
                <div class="card card-navy chart-card" id="spo2ChartCard" style="display: none;">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-lungs mr-1"></i> Oxygen Saturation
                                <small class="text-muted ml-2">(← →)</small>
                                <small class="text-muted ml-2">Swipe or use arrows to view more charts</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-3">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-warning"></i> SpO2 (%)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="spo2Chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Respiratory Rate Chart -->
                <div class="card card-navy chart-card" id="rrChartCard" style="display: none;">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-wind mr-1"></i> Respiratory Rate
                                <small class="text-muted ml-2">(← →)</small>
                                <small class="text-muted ml-2">Swipe or use arrows to view more charts</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-3">
                                <div>
                                    <span class="badge badge-pill badge-light">
                                        <i class="fas fa-circle text-purple"></i> Respiratory Rate (breaths/min)
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="rrChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EWS Chart -->
                <div class="card card-navy chart-card" id="ewsChartCard" style="display: none;">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i> Early Warning Score
                                <small class="text-muted ml-2">(← →)</small>
                                <small class="text-muted ml-2">Swipe or use arrows to view more charts</small>
                            </h3>
                            <div>
                                <button class="btn btn-sm btn-outline-light mr-1 prev-chart">Previous</button>
                                <button class="btn btn-sm btn-outline-light next-chart">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="chart">
                            <div class="d-flex justify-content-center mb-3">
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
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="ewsChart"></canvas>
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
        .card-navy {
            border-top: 3px solid #001f3f;
        }
        
        .chart {
            min-height: 300px;
        }
        
        .badge-pill {
            padding: 0.5em 1em;
        }

        .text-purple {
            color: #6f42c1;
        }
        
        /* Transition effect for chart changes */
        .chart-transition {
            animation: chartFadeIn 0.3s ease-in-out;
        }
        
        @keyframes chartFadeIn {
            0% { opacity: 0.7; transform: scale(0.98); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        /* Add visual cues for scrolling */
        .chart-card {
            position: relative;
            will-change: transform;
            touch-action: pan-y;
        }
        
        .chart-card::after {
            content: "Drag to view more";
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 12px;
            color: #6c757d;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 5px 10px;
            border-radius: 4px;
            opacity: 0.8;
        }
        
        /* Cursor styling to indicate scrollable content */
        .container-fluid {
            cursor: grab;
            overflow-x: hidden;
            touch-action: pan-y;
        }
        
        .container-fluid.dragging {
            cursor: grabbing;
            user-select: none;
        }
        
        /* Drag indicators */
        .drag-indicator {
            position: absolute;
            top: 50%;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .drag-indicator.left {
            left: 10px;
        }
        
        .drag-indicator.right {
            right: 10px;
        }
        
        .container-fluid.dragging .drag-indicator {
            opacity: 1;
        }
    </style>
@stop

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
            new Chart(document.getElementById('bpChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Systolic BP',
                            data: systolicBPs,
                            borderColor: 'rgba(220, 53, 69, 1)',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: false,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Diastolic BP',
                            data: diastolicBPs,
                            borderColor: 'rgba(23, 162, 184, 1)',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: false,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 40,
                            max: 200,
                            ticks: {
                                stepSize: 20
                            },
                            title: {
                                display: true,
                                text: 'mmHg'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.raw || 'N/A';
                                    return `${datasetLabel}: ${value} mmHg`;
                                }
                            }
                        }
                    }
                }
            });

            // Heart Rate Chart
            new Chart(document.getElementById('hrChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Heart Rate',
                        data: heartRates,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 40,
                            max: 180,
                            ticks: {
                                stepSize: 20
                            },
                            title: {
                                display: true,
                                text: 'bpm'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
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

            // Temperature Chart
            new Chart(document.getElementById('tempChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Temperature',
                        data: temperatures,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 35,
                            max: 40,
                            ticks: {
                                stepSize: 0.5
                            },
                            title: {
                                display: true,
                                text: '°C'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
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

            // SPO2 Chart
            new Chart(document.getElementById('spo2Chart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Oxygen Saturation',
                        data: oxygenSaturations,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 90,
                            max: 100,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: '%'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `SpO2: ${context.raw}%`;
                                }
                            }
                        }
                    }
                }
            });

            // Respiratory Rate Chart
            new Chart(document.getElementById('rrChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Respiratory Rate',
                        data: respiratoryRates,
                        borderColor: 'rgba(111, 66, 193, 1)',
                        backgroundColor: 'rgba(111, 66, 193, 0.1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 8,
                            max: 40,
                            ticks: {
                                stepSize: 4
                            },
                            title: {
                                display: true,
                                text: 'breaths/min'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Respiratory Rate: ${context.raw} breaths/min`;
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
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 12,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Score'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
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

            // Chart navigation
            const chartCards = document.querySelectorAll('.chart-card');
            const totalCharts = chartCards.length;
            let currentChartIndex = 0;
            let scrollTimeout = null;
            let isSwitchingChart = false;
            
            // Variables for drag scrolling
            let isDragging = false;
            let startDragX = 0;
            let dragThreshold = 100; // Minimum drag distance to trigger chart change
            let dragStartTime = 0;
            let dragEndTime = 0;
            let lastDragVelocity = 0;

            function showChart(index, transition = true) {
                // Prevent rapid chart switching
                if (isSwitchingChart) return;
                
                isSwitchingChart = true;
                
                // Hide all charts
                chartCards.forEach(card => card.style.display = 'none');
                
                // Show the current chart
                chartCards[index].style.display = 'block';
                currentChartIndex = index;
                
                // Add a visual indicator for the chart change
                if (transition) {
                    const activeCard = chartCards[index];
                    activeCard.classList.add('chart-transition');
                    
                    setTimeout(() => {
                        activeCard.classList.remove('chart-transition');
                        isSwitchingChart = false;
                    }, 300);
                } else {
                    isSwitchingChart = false;
                }
            }

            function nextChart() {
                let nextIndex = (currentChartIndex + 1) % totalCharts;
                showChart(nextIndex);
            }

            function prevChart() {
                let prevIndex = (currentChartIndex - 1 + totalCharts) % totalCharts;
                showChart(prevIndex);
            }

            // Add event listeners to next/prev buttons
            document.querySelectorAll('.next-chart').forEach(btn => {
                btn.addEventListener('click', nextChart);
            });

            document.querySelectorAll('.prev-chart').forEach(btn => {
                btn.addEventListener('click', prevChart);
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight') {
                    nextChart();
                } else if (e.key === 'ArrowLeft') {
                    prevChart();
                }
            });

            // Mouse wheel scrolling for chart navigation
            document.addEventListener('wheel', function(e) {
                // Clear any existing timeout
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }
                
                // Set a timeout to prevent too many chart changes
                scrollTimeout = setTimeout(() => {
                    // deltaY > 0 means scrolling down/right
                    if (e.deltaY > 50) {
                        nextChart();
                    } 
                    // deltaY < 0 means scrolling up/left
                    else if (e.deltaY < -50) {
                        prevChart();
                    }
                }, 100);
            });
            
            // Mouse drag implementation
            const container = document.querySelector('.container-fluid');
            
            // Mouse events for desktop drag
            container.addEventListener('mousedown', function(e) {
                startDragX = e.clientX;
                isDragging = true;
                dragStartTime = performance.now();
                
                // Change cursor to indicate dragging
                container.classList.add('dragging');
                
                // Start displaying drag indicator
                updateDragIndicator(0);
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                // Calculate drag distance
                const dragDistance = e.clientX - startDragX;
                
                // Update drag indicator
                updateDragIndicator(dragDistance);
                
                // Prevent default behavior to avoid text selection
                e.preventDefault();
            });
            
            document.addEventListener('mouseup', function(e) {
                if (!isDragging) return;
                
                // Calculate drag distance and velocity
                const dragDistance = e.clientX - startDragX;
                dragEndTime = performance.now();
                const dragDuration = dragEndTime - dragStartTime;
                lastDragVelocity = Math.abs(dragDistance) / dragDuration;
                
                // Check if drag was significant enough
                if (Math.abs(dragDistance) > dragThreshold || lastDragVelocity > 0.5) {
                    if (dragDistance > 0) {
                        prevChart();
                    } else {
                        nextChart();
                    }
                } else {
                    // If the drag wasn't enough, just reset the indicator
                    updateDragIndicator(0, true);
                }
                
                // Reset drag state
                isDragging = false;
                container.classList.remove('dragging');
            });
            
            // Cancel drag on mouse leave
            document.addEventListener('mouseleave', function() {
                if (isDragging) {
                    isDragging = false;
                    container.classList.remove('dragging');
                    updateDragIndicator(0, true);
                }
            });
            
            // Touch events for mobile drag (better than Hammer.js for this use case)
            container.addEventListener('touchstart', function(e) {
                startDragX = e.touches[0].clientX;
                isDragging = true;
                dragStartTime = performance.now();
                
                // Start displaying drag indicator
                updateDragIndicator(0);
            }, { passive: false });
            
            container.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                
                // Calculate drag distance
                const dragDistance = e.touches[0].clientX - startDragX;
                
                // Update drag indicator
                updateDragIndicator(dragDistance);
                
                // Prevent default scrolling behavior
                e.preventDefault();
            }, { passive: false });
            
            container.addEventListener('touchend', function(e) {
                if (!isDragging) return;
                
                // Calculate drag distance and velocity
                const dragDistance = e.changedTouches[0].clientX - startDragX;
                dragEndTime = performance.now();
                const dragDuration = dragEndTime - dragStartTime;
                lastDragVelocity = Math.abs(dragDistance) / dragDuration;
                
                // Check if drag was significant enough
                if (Math.abs(dragDistance) > dragThreshold || lastDragVelocity > 0.5) {
                    if (dragDistance > 0) {
                        prevChart();
                    } else {
                        nextChart();
                    }
                } else {
                    // If the drag wasn't enough, just reset the indicator
                    updateDragIndicator(0, true);
                }
                
                // Reset drag state
                isDragging = false;
            }, { passive: false });
            
            // Function to update the drag indicator
            function updateDragIndicator(dragDistance, reset = false) {
                // Get current visible chart
                const currentCard = chartCards[currentChartIndex];
                
                // If reset, return to normal position without animation
                if (reset) {
                    currentCard.style.transform = '';
                    currentCard.style.transition = 'transform 0.3s ease-out';
                    setTimeout(() => {
                        currentCard.style.transition = '';
                    }, 300);
                    return;
                }
                
                // Limit the drag effect (diminishing returns for long drags)
                const scaledDistance = Math.sign(dragDistance) * Math.min(Math.abs(dragDistance), 150);
                
                // Apply transform to indicate dragging direction
                currentCard.style.transform = `translateX(${scaledDistance}px)`;
                
                // Remove any transition during active dragging
                currentCard.style.transition = 'none';
                
                // Show preview of next/prev chart at edges
                if (Math.abs(dragDistance) > 50) {
                    // Determine which chart to preview
                    let previewIndex;
                    if (dragDistance > 0) {
                        // Dragging right - show previous chart
                        previewIndex = (currentChartIndex - 1 + totalCharts) % totalCharts;
                    } else {
                        // Dragging left - show next chart
                        previewIndex = (currentChartIndex + 1) % totalCharts;
                    }
                    
                    // Position the preview chart
                    const previewCard = chartCards[previewIndex];
                    previewCard.style.display = 'block';
                    previewCard.style.opacity = '0.5';
                    previewCard.style.position = 'absolute';
                    previewCard.style.top = currentCard.offsetTop + 'px';
                    previewCard.style.width = currentCard.offsetWidth + 'px';
                    previewCard.style.zIndex = '-1';
                    
                    if (dragDistance > 0) {
                        // Previous chart to the left
                        previewCard.style.transform = `translateX(${-currentCard.offsetWidth + scaledDistance}px)`;
                    } else {
                        // Next chart to the right
                        previewCard.style.transform = `translateX(${currentCard.offsetWidth + scaledDistance}px)`;
                    }
                }
            }

            // Keep simplified Hammer touch support for compatibility
            const hammer = new Hammer(container);
            hammer.get('swipe').set({ direction: Hammer.DIRECTION_HORIZONTAL });
            
            hammer.on('swipeleft', function(e) {
                if (e.distance > 50 && !isDragging) {
                    nextChart();
                }
            });
            
            hammer.on('swiperight', function(e) {
                if (e.distance > 50 && !isDragging) {
                    prevChart();
                }
            });

            // Initialize with the first chart
            showChart(0, false);
        @else
            document.getElementById('bpChart').innerHTML = '<div class="text-center p-5 text-muted">No data available</div>';
        @endif
    });
</script>
@stop 