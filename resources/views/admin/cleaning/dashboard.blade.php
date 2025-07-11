@extends('adminlte::page')

@section('title', 'Room Cleaning Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-broom"></i> Room Cleaning Dashboard</h1>
                <p class="text-muted">Manage and track room cleaning activities across all wards</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Room Cleaning</li>
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
                        <form method="GET" action="{{ route('admin.cleaning.dashboard') }}" id="filter-form">
                            <div class="row">
                                <div class="col-md-4">
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
                                <div class="col-md-4">
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
                                <div class="col-md-4 d-flex align-items-end">
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
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalBedsNeedingCleaning }}</h3>
                        <p>Beds Need Cleaning</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-broom"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalBedsInSystem - $totalBedsNeedingCleaning }}</h3>
                        <p>Beds Clean</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $totalBedsInSystem }}</h3>
                        <p>Total Beds</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bed"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Beds Needing Cleaning -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Beds Needing Cleaning</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" id="mark-all-cleaned" {{ $totalBedsNeedingCleaning == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-check-double"></i> Mark All Cleaned
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($totalBedsNeedingCleaning > 0)
                            @foreach($bedsByWard as $wardId => $beds)
                                @php
                                    $ward = $beds->first()->ward;
                                @endphp
                                <div class="card border-warning mb-3">
                                    <div class="card-header bg-warning">
                                        <h5 class="m-0">
                                            <i class="fas fa-building"></i> {{ $ward->name }}
                                            <span class="badge badge-light">{{ $beds->count() }} bed(s) need cleaning</span>
                                        </h5>
                                        <small class="text-muted">{{ $ward->specialty->name }} • {{ $ward->hospital->name }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($beds as $bed)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card border-warning h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="card-title mb-1">
                                                                        <i class="fas fa-bed text-warning"></i> Bed {{ $bed->bed_number }}
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        Last updated: {{ $bed->updated_at->diffForHumans() }}
                                                                    </small>
                                                                    @if($bed->notes)
                                                                        <br><small class="text-info">{{ Str::limit($bed->notes, 50) }}</small>
                                                                    @endif
                                                                </div>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-success mark-cleaned-btn" 
                                                                            data-bed-id="{{ $bed->id }}" 
                                                                            data-bed-number="{{ $bed->bed_number }}"
                                                                            title="Mark as Cleaned">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                    <button class="btn btn-warning send-whatsapp-btn" 
                                                                            data-bed-id="{{ $bed->id }}" 
                                                                            data-bed-number="{{ $bed->bed_number }}"
                                                                            title="Send WhatsApp Notification">
                                                                        <i class="fab fa-whatsapp"></i>
                                                                    </button>
                                                                    <a href="{{ route('admin.beds.wards.dashboard', $ward->id) }}" 
                                                                       class="btn btn-primary" 
                                                                       title="View Ward Dashboard">
                                                                        <i class="fas fa-external-link-alt"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h4>All Clean!</h4>
                                <p>No beds currently need cleaning. Great job!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- WhatsApp Notifications -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fab fa-whatsapp text-success"></i> Recent Notifications</h3>
                    </div>
                    <div class="card-body">
                        @if($recentNotifications->count() > 0)
                            <div class="timeline timeline-inverse">
                                @foreach($recentNotifications as $notification)
                                    <div class="time-label">
                                        <span class="bg-success">{{ $notification['created_at']->format('H:i') }}</span>
                                    </div>
                                    <div>
                                        <i class="fab fa-whatsapp bg-green"></i>
                                        <div class="timeline-item">
                                            <h3 class="timeline-header">
                                                WhatsApp Notification
                                                <small class="text-muted">{{ $notification['created_at']->diffForHumans() }}</small>
                                            </h3>
                                            <div class="timeline-body">
                                                <strong>Message:</strong> {{ $notification['message'] }}<br>
                                                <strong>Recipient:</strong> {{ $notification['recipient'] }}<br>
                                                <strong>Status:</strong> 
                                                <span class="badge badge-success">{{ $notification['status'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fab fa-whatsapp fa-2x text-success mb-2"></i>
                                <p>No recent notifications</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recently Cleaned Beds -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock"></i> Recently Cleaned (24h)</h3>
                    </div>
                    <div class="card-body">
                        @if($recentlyCleanedBeds->count() > 0)
                            <div class="timeline timeline-inverse">
                                @foreach($recentlyCleanedBeds as $bed)
                                    <div class="time-label">
                                        <span class="bg-success">{{ $bed->updated_at->format('H:i') }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-check bg-green"></i>
                                        <div class="timeline-item">
                                            <h3 class="timeline-header">
                                                Bed {{ $bed->bed_number }}
                                                <small class="text-muted">{{ $bed->ward->name }}</small>
                                            </h3>
                                            <div class="timeline-body">
                                                <strong>Ward:</strong> {{ $bed->ward->name }}<br>
                                                <strong>Specialty:</strong> {{ $bed->ward->specialty->name }}<br>
                                                <strong>Hospital:</strong> {{ $bed->ward->hospital->name }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <p>No recent cleaning activity</p>
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
        
        // Mark individual bed as cleaned
        $('.mark-cleaned-btn').on('click', function() {
            const bedId = $(this).data('bed-id');
            const bedNumber = $(this).data('bed-number');
            const button = $(this);
            
            if (confirm('Mark Bed ' + bedNumber + ' as cleaned?')) {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ route("admin.cleaning.mark-cleaned", ":bedId") }}'.replace(':bedId', bedId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Remove the bed card from the UI
                            button.closest('.col-md-6').fadeOut();
                            
                            // Update counters
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                            button.prop('disabled', false).html('<i class="fas fa-check"></i>');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response ? response.message : 'An error occurred');
                        button.prop('disabled', false).html('<i class="fas fa-check"></i>');
                    }
                });
            }
        });
        
        // Mark all beds as cleaned
        $('#mark-all-cleaned').on('click', function() {
            const button = $(this);
            const bedIds = [];
            
            // Collect all bed IDs that need cleaning
            $('.mark-cleaned-btn').each(function() {
                bedIds.push($(this).data('bed-id'));
            });
            
            if (bedIds.length === 0) {
                toastr.warning('No beds to mark as cleaned');
                return;
            }
            
            if (confirm('Mark all ' + bedIds.length + ' beds as cleaned?')) {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Marking All Cleaned...');
                
                $.ajax({
                    url: '{{ route("admin.cleaning.mark-multiple-cleaned") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        bed_ids: bedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                            button.prop('disabled', false).html('<i class="fas fa-check-double"></i> Mark All Cleaned');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response ? response.message : 'An error occurred');
                        button.prop('disabled', false).html('<i class="fas fa-check-double"></i> Mark All Cleaned');
                    }
                });
            }
        });
        
        // Send WhatsApp notification
        $('.send-whatsapp-btn').on('click', function() {
            const bedId = $(this).data('bed-id');
            const bedNumber = $(this).data('bed-number');
            const button = $(this);
            
            if (confirm('Send WhatsApp notification for Bed ' + bedNumber + '?')) {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '{{ route("admin.cleaning.send-whatsapp-notification") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        bed_id: bedId,
                        recipient: '+60123456789' // Demo phone number
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Add the notification to the timeline
                            addNotificationToTimeline(response.notification);
                        } else {
                            toastr.error(response.message);
                            button.prop('disabled', false).html('<i class="fab fa-whatsapp"></i>');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response ? response.message : 'An error occurred');
                        button.prop('disabled', false).html('<i class="fab fa-whatsapp"></i>');
                    }
                });
            }
        });
        
        // Function to add notification to timeline
        function addNotificationToTimeline(notification) {
            const timeline = $('.timeline');
            const timeLabel = $('<div class="time-label"><span class="bg-success">' + new Date(notification.created_at).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'}) + '</span></div>');
            const notificationItem = $('<div><i class="fab fa-whatsapp bg-green"></i><div class="timeline-item"><h3 class="timeline-header">WhatsApp Notification<small class="text-muted">Just now</small></h3><div class="timeline-body"><strong>Message:</strong> ' + notification.message + '<br><strong>Recipient:</strong> ' + notification.recipient + '<br><strong>Status:</strong> <span class="badge badge-success">' + notification.status + '</span></div></div></div>');
            
            timeline.prepend(notificationItem);
            timeline.prepend(timeLabel);
            
            // Remove "No recent notifications" message if it exists
            $('.text-center.text-muted').hide();
        }
    </script>
@stop 