<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schedule ? $schedule->name : 'No Schedule' }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: #f4f4f4;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-size: 14px;
            color: #495057;
        }
        
        .main-container {
            min-height: 100vh;
            background-color: #f4f4f4;
            padding: 15px;
        }
        
        .header-section {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border: 1px solid #dee2e6;
        }
        
        .date-nav-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .date-nav-btn {
            background: #007bff;
            border: 1px solid #007bff;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .date-nav-btn:hover {
            background: #0056b3;
            border-color: #0056b3;
            transform: none;
        }
        
        .date-nav-btn:disabled {
            background: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }
        
        .current-date {
            background: #fff;
            color: #495057;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border: 1px solid #dee2e6;
            min-width: 200px;
        }
        
        .current-date:hover {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .content-section {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border: 1px solid #dee2e6;
            transition: all 0.15s ease-in-out;
        }
        
        .stat-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 20px;
            color: white;
        }
        
        .stat-icon.nurses { background-color: #007bff; }
        .stat-icon.shifts { background-color: #28a745; }
        .stat-icon.ward { background-color: #17a2b8; }
        .stat-icon.date { background-color: #6c757d; }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .shifts-container {
            display: grid;
            gap: 20px;
        }
        
        .shift-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border: 1px solid #dee2e6;
        }
        
        .shift-header {
            background: #495057;
            padding: 15px 20px;
            color: white;
        }
        
        .shift-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .shift-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .shift-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .shift-time {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .shift-body {
            padding: 0;
        }
        
        .nurses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }
        
        .nurse-card {
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            transition: all 0.15s ease;
            background: white;
        }
        
        .nurse-card:last-child {
            border-bottom: none;
        }
        
        .nurse-card:hover {
            background: #f8f9fa;
        }
        
        .nurse-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .nurse-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .nurse-details {
            flex: 1;
        }
        
        .nurse-name {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 4px;
        }
        
        .nurse-position {
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6c757d;
        }
        
        .contact-icon {
            width: 14px;
            text-align: center;
            color: #007bff;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border: 1px solid #dee2e6;
        }
        
        .no-data-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 24px;
        }
        
        .no-data h4 {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .no-data p {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .loading {
            text-align: center;
            padding: 60px 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border: 1px solid #dee2e6;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #dee2e6;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading h4 {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        .fade-out {
            animation: fadeOut 0.2s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            
            .header-section {
                padding: 15px;
            }
            
            .date-nav-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .current-date {
                min-width: 180px;
            }
            
            .stats-overview {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 10px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .nurses-grid {
                grid-template-columns: 1fr;
            }
            
            .nurse-card {
                padding: 15px;
            }
            
            .nurse-info {
                gap: 12px;
            }
            
            .nurse-avatar {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
        }
        
        /* Flatpickr custom styling */
        .flatpickr-calendar {
            background: #fff;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .flatpickr-day.selected {
            background: #007bff;
            border-color: #007bff;
        }
        
        .flatpickr-day:hover {
            background: #e9ecef;
        }
        
        /* Match AdminLTE button styles */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="main-container">
        @if($schedule)
            <div class="header-section">
                <div class="date-nav-container">
                    <button class="date-nav-btn" id="prevDay">
                        <i class="fas fa-chevron-left"></i>
                        <span>Previous</span>
                    </button>
                    <div class="current-date" id="currentDate" title="Click to select date">
                        {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}
                    </div>
                    <button class="date-nav-btn" id="nextDay">
                        <span>Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="content-section">
            <div id="scheduleContent" class="schedule-content fade-in">
                @if($schedule)
                    @if($todayAssignments && $todayAssignments->count() > 0)
                        @php
                            // Calculate total unique nurses on duty
                            $uniqueNurses = collect();
                            foreach($todayAssignments as $shiftName => $nurses) {
                                foreach($nurses as $assignment) {
                                    if(isset($assignment['member']['employee_id'])) {
                                        $uniqueNurses->put($assignment['member']['employee_id'], $assignment['member']);
                                    }
                                }
                            }
                            $totalNursesOnDuty = $uniqueNurses->count();
                        @endphp

                        <!-- Statistics Overview -->
                        <div class="stats-overview">
                            <div class="stat-card">
                                <div class="stat-icon nurses">
                                    <i class="fas fa-user-nurse"></i>
                                </div>
                                <div class="stat-number">{{ $totalNursesOnDuty }}</div>
                                <div class="stat-label">Nurses on Duty</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon shifts">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-number">{{ $todayAssignments->count() }}</div>
                                <div class="stat-label">Active Shifts</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon ward">
                                    <i class="fas fa-hospital"></i>
                                </div>
                                <div class="stat-number">1</div>
                                <div class="stat-label">Ward</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon date">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-number">{{ \Carbon\Carbon::parse($today)->format('d') }}</div>
                                <div class="stat-label">{{ \Carbon\Carbon::parse($today)->format('M Y') }}</div>
                            </div>
                        </div>

                        @php
                            // Define shift order for better display
                            $shiftOrder = ['AM Shift', 'DA Shift', 'EV Shift'];
                            $orderedAssignments = collect();
                            
                            // First add shifts in preferred order
                            foreach ($shiftOrder as $shiftName) {
                                if ($todayAssignments->has($shiftName)) {
                                    $orderedAssignments->put($shiftName, $todayAssignments->get($shiftName));
                                }
                            }
                            
                            // Then add any remaining shifts not in our predefined order
                            foreach ($todayAssignments as $shiftName => $nurses) {
                                if (!in_array($shiftName, $shiftOrder)) {
                                    $orderedAssignments->put($shiftName, $nurses);
                                }
                            }
                        @endphp

                        <!-- Shifts Container -->
                        <div class="shifts-container">
                            @foreach($orderedAssignments as $shiftName => $nurses)
                                <div class="shift-card">
                                    <div class="shift-header">
                                        <div class="shift-title-row">
                                            <h3 class="shift-title">
                                                <i class="fas fa-clock"></i>
                                                {{ $shiftName }}
                                            </h3>
                                            <div class="shift-badge">
                                                {{ $nurses->count() }} {{ $nurses->count() == 1 ? 'Nurse' : 'Nurses' }}
                                            </div>
                                        </div>
                                        @if($nurses->first())
                                            <div class="shift-time">
                                                <i class="fas fa-stopwatch"></i>
                                                {{ $nurses->first()['shift_slot']['formatted_time_range'] ?? 'Time not specified' }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="shift-body">
                                        <div class="nurses-grid">
                                            @foreach($nurses as $assignment)
                                                @if(isset($assignment['member']))
                                                    <div class="nurse-card">
                                                        <div class="nurse-info">
                                                            <div class="nurse-avatar">
                                                                {{ strtoupper(substr($assignment['member']['name'], 0, 1)) }}{{ strtoupper(substr(explode(' ', $assignment['member']['name'])[1] ?? '', 0, 1)) }}
                                                            </div>
                                                            <div class="nurse-details">
                                                                <div class="nurse-name">
                                                                    {{ $assignment['member']['name'] }}
                                                                </div>
                                                                <div class="nurse-position">
                                                                    {{ $assignment['member']['position'] ?? 'Nurse' }}
                                                                </div>
                                                                <div class="contact-info">
                                                                    @if(isset($assignment['member']['employee_id']))
                                                                        <div class="contact-item">
                                                                            <div class="contact-icon">
                                                                                <i class="fas fa-id-badge"></i>
                                                                            </div>
                                                                            <span>ID: {{ $assignment['member']['employee_id'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($assignment['member']['phone']))
                                                                        <div class="contact-item">
                                                                            <div class="contact-icon">
                                                                                <i class="fas fa-phone"></i>
                                                                            </div>
                                                                            <span>{{ $assignment['member']['phone'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($assignment['member']['email']))
                                                                        <div class="contact-item">
                                                                            <div class="contact-icon">
                                                                                <i class="fas fa-envelope"></i>
                                                                            </div>
                                                                            <span>{{ $assignment['member']['email'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">
                            <div class="no-data-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h4>No Nurses on Duty</h4>
                            <p id="noNursesDate">
                                No nurses are scheduled for duty on {{ \Carbon\Carbon::parse($today)->format('F j, Y') }}.<br>
                                Please check the schedule or contact administration.
                            </p>
                        </div>
                    @endif
                @else
                    <div class="no-data">
                        <div class="no-data-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4>No Schedule Available</h4>
                        <p>
                            @if(isset($ward))
                                No active nurse schedule found for {{ $ward->name }}.<br>
                                Please upload a schedule for this ward.
                            @else
                                No nurse schedule available.<br>
                                Please contact the system administrator.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let currentDate = new Date('{{ $today }}');
        const scheduleId = {{ $schedule ? $schedule->id : 'null' }};
        const wardId = {{ isset($ward) ? $ward->id : ($schedule && $schedule->ward ? $schedule->ward->id : 'null') }};
        let datePicker = null;

        function formatDate(date) {
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            return date.toLocaleDateString('en-US', options);
        }

        function formatDateForAPI(date) {
            return date.toISOString().split('T')[0];
        }

        function updateDateDisplay() {
            const dateElement = document.getElementById('currentDate');
            if (dateElement) {
                dateElement.textContent = formatDate(currentDate);
            }
        }

        function loadScheduleForDate(date) {
            const formattedDate = formatDateForAPI(date);
            const content = document.getElementById('scheduleContent');
            
            if (!content) return;
            
            // Add fade out animation
            content.classList.add('fade-out');
            
            setTimeout(() => {
                // Show loading
                content.innerHTML = `
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        <h4>Loading schedule...</h4>
                    </div>
                `;
                content.classList.remove('fade-out');
                content.classList.add('fade-in');

                // Make AJAX request to get schedule for the specific date
                let url;
                if (scheduleId) {
                    url = `/admin/integration/nurse-schedule/${scheduleId}/iframe?date=${formattedDate}`;
                } else if (wardId) {
                    url = `/admin/beds/wards/${wardId}/nurses?date=${formattedDate}`;
                } else {
                    content.innerHTML = `
                        <div class="no-data">
                            <div class="no-data-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h4>No Schedule Available</h4>
                            <p>Unable to load schedule data.</p>
                        </div>
                    `;
                    return;
                }

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        // Extract only the schedule content from the returned HTML
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const scheduleContent = doc.getElementById('scheduleContent');
                        
                        if (scheduleContent) {
                            // Only update the content, not the navigation
                            content.innerHTML = scheduleContent.innerHTML;
                        } else {
                            content.innerHTML = `
                                <div class="no-data">
                                    <div class="no-data-icon">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <h4>No Nurses on Duty</h4>
                                    <p>No nurses are scheduled for duty on ${formatDate(date)}.</p>
                                </div>
                            `;
                        }
                        
                        // Update date display after loading
                        updateDateDisplay();
                        
                        // Add fade in animation to new content
                        content.classList.remove('fade-in');
                        content.classList.add('fade-in');
                    })
                    .catch(error => {
                        console.error('Error loading schedule:', error);
                        content.innerHTML = `
                            <div class="no-data">
                                <div class="no-data-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <h4>Error Loading Schedule</h4>
                                <p>Unable to load schedule data. Please try again.</p>
                            </div>
                        `;
                        content.classList.remove('fade-in');
                        content.classList.add('fade-in');
                    });
            }, 200);
        }

        // Initialize date picker
        function initializeDatePicker() {
            const dateElement = document.getElementById('currentDate');
            if (dateElement && !datePicker) {
                datePicker = flatpickr(dateElement, {
                    defaultDate: currentDate,
                    dateFormat: "l, F j, Y",
                    altInput: true,
                    altFormat: "l, F j, Y",
                    allowInput: false,
                    clickOpens: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            currentDate = selectedDates[0];
                            updateDateDisplay();
                            loadScheduleForDate(currentDate);
                        }
                    }
                });
            }
        }

        // Event listeners for navigation buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date picker
            initializeDatePicker();

            // Previous day button
            const prevBtn = document.getElementById('prevDay');
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Loading...</span>';
                    
                    currentDate.setDate(currentDate.getDate() - 1);
                    updateDateDisplay();
                    loadScheduleForDate(currentDate);
                    
                    // Update date picker
                    if (datePicker) {
                        datePicker.setDate(currentDate, false);
                    }
                    
                    // Re-enable button after a short delay
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-chevron-left"></i><span>Previous</span>';
                    }, 1000);
                });
            }

            // Next day button
            const nextBtn = document.getElementById('nextDay');
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Loading...</span>';
                    
                    currentDate.setDate(currentDate.getDate() + 1);
                    updateDateDisplay();
                    loadScheduleForDate(currentDate);
                    
                    // Update date picker
                    if (datePicker) {
                        datePicker.setDate(currentDate, false);
                    }
                    
                    // Re-enable button after a short delay
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<span>Next</span><i class="fas fa-chevron-right"></i>';
                    }, 1000);
                });
            }

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft' && prevBtn && !prevBtn.disabled) {
                    prevBtn.click();
                } else if (e.key === 'ArrowRight' && nextBtn && !nextBtn.disabled) {
                    nextBtn.click();
                }
            });
        });

        // Auto-refresh every 5 minutes (only for current date)
        setInterval(function() {
            const today = new Date();
            const todayFormatted = formatDateForAPI(today);
            const currentFormatted = formatDateForAPI(currentDate);
            
            if (todayFormatted === currentFormatted) {
                loadScheduleForDate(currentDate);
            }
        }, 300000); // 5 minutes
    </script>
</body>
</html> 