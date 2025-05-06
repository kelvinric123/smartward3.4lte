@extends('adminlte::page')

@section('title', $patient->name . ' - Vital Signs Trend')

@section('content_header')
    <div class="container-fluid bg-dark text-white py-2">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.beds.wards.dashboard.direct', $ward) }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Ward
            </a>
            <h1 class="m-0">Vital Signs for {{ $patient->name }}</h1>
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
                                        <i class="fas fa-circle text-success"></i> Respiratory Rate (bpm)
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
                                <i class="fas fa-chart-line mr-1"></i> Early Warning Score (EWS)
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
                                        <i class="fas fa-circle text-pink"></i> EWS Score
                                    </span>
                                </div>
                            </div>
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="ewsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <div class="btn-group">
                            <a href="{{ route('admin.vital-signs.create.direct', ['patient_id' => $patient->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Record New Vital Signs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Text colors */
        .text-pink {
            color: #e83e8c !important;
        }
        
        /* Chart card styling */
        .chart-card {
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set fullscreen mode
            $('body').addClass('fullscreen-mode');
            
            // Initialize variables
            let currentChartIndex = 0;
            const charts = [
                'bpChartCard',
                'hrChartCard',
                'tempChartCard',
                'spo2ChartCard',
                'rrChartCard',
                'ewsChartCard'
            ];
            
            // Load vital signs data from JSON
            const vitalSignsData = @json($vitalSigns);
            
            // Format data for charts
            const formattedData = formatChartData(vitalSignsData);
            
            // Show the first chart
            showChart(currentChartIndex);
            
            // Initialize all charts
            initializeCharts(formattedData);
            
            // Handle navigation buttons
            $('.next-chart').click(function() {
                navigateChart(1);
            });
            
            $('.prev-chart').click(function() {
                navigateChart(-1);
            });
            
            // Add keyboard navigation
            $(document).keydown(function(e) {
                if (e.keyCode === 37) { // Left arrow
                    navigateChart(-1);
                } else if (e.keyCode === 39) { // Right arrow
                    navigateChart(1);
                }
            });
            
            // Add swipe gesture support
            const chartContainer = document.querySelector('.container-fluid');
            const hammer = new Hammer(chartContainer);
            hammer.on('swipeleft', function() {
                navigateChart(1);
            });
            hammer.on('swiperight', function() {
                navigateChart(-1);
            });
            
            // Handle date filter
            $('#updateBtn').click(function() {
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                if (!startDate || !endDate) {
                    alert('Please select both start and end dates');
                    return;
                }
                
                const filteredData = filterDataByDateRange(vitalSignsData, startDate, endDate);
                const formattedFilteredData = formatChartData(filteredData);
                
                // Reinitialize charts with filtered data
                initializeCharts(formattedFilteredData);
            });
            
            // Function to navigate charts
            function navigateChart(direction) {
                $('.chart-card').hide();
                currentChartIndex = (currentChartIndex + direction + charts.length) % charts.length;
                showChart(currentChartIndex);
            }
            
            // Function to show current chart
            function showChart(index) {
                $('#' + charts[index]).show();
            }
            
            // Function to filter data by date range
            function filterDataByDateRange(data, startDate, endDate) {
                const startTimestamp = new Date(startDate).getTime();
                const endTimestamp = new Date(endDate + 'T23:59:59').getTime();
                
                return data.filter(item => {
                    const recordTimestamp = new Date(item.recorded_at).getTime();
                    return recordTimestamp >= startTimestamp && recordTimestamp <= endTimestamp;
                });
            }
            
            // Function to format data for charts
            function formatChartData(data) {
                // Sort data by timestamp ascending
                data.sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));
                
                return {
                    labels: data.map(item => formatDateTime(item.recorded_at)),
                    systolicBP: data.map(item => item.systolic_bp),
                    diastolicBP: data.map(item => item.diastolic_bp),
                    heartRate: data.map(item => item.heart_rate),
                    temperature: data.map(item => item.temperature),
                    spo2: data.map(item => item.oxygen_saturation),
                    respiratoryRate: data.map(item => item.respiratory_rate),
                    ews: data.map(item => item.total_ews)
                };
            }
            
            // Function to format date and time
            function formatDateTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            
            // Function to initialize all charts
            function initializeCharts(data) {
                initializeBPChart(data);
                initializeHRChart(data);
                initializeTempChart(data);
                initializeSpo2Chart(data);
                initializeRRChart(data);
                initializeEwsChart(data);
            }
            
            // Initializers for individual charts
            function initializeBPChart(data) {
                const ctx = document.getElementById('bpChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Systolic BP',
                                data: data.systolicBP,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            },
                            {
                                label: 'Diastolic BP',
                                data: data.diastolicBP,
                                borderColor: '#17a2b8',
                                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
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
            }
            
            function initializeHRChart(data) {
                const ctx = document.getElementById('hrChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Heart Rate',
                                data: data.heartRate,
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
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
            }
            
            function initializeTempChart(data) {
                const ctx = document.getElementById('tempChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Temperature',
                                data: data.temperature,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 35,
                                max: 40,
                                title: {
                                    display: true,
                                    text: '°C'
                                }
                            }
                        }
                    }
                });
            }
            
            function initializeSpo2Chart(data) {
                const ctx = document.getElementById('spo2Chart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'SpO2',
                                data: data.spo2,
                                borderColor: '#ffc107',
                                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 85,
                                max: 100,
                                title: {
                                    display: true,
                                    text: '%'
                                }
                            }
                        }
                    }
                });
            }
            
            function initializeRRChart(data) {
                const ctx = document.getElementById('rrChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Respiratory Rate',
                                data: data.respiratoryRate,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
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
            }
            
            function initializeEwsChart(data) {
                const ctx = document.getElementById('ewsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'EWS',
                                data: data.ews,
                                borderColor: '#e83e8c',
                                backgroundColor: 'rgba(232, 62, 140, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 12,
                                title: {
                                    display: true,
                                    text: 'Score'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@stop 