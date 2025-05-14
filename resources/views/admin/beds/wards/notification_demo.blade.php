@extends('layouts.iframe')

@section('title', 'Patient to Nurse Notifications')

@section('content')
<div class="container-fluid p-3">
    <h4 class="mb-3">Patient Alerts</h4>
    
    <div id="alerts-container">
        @if(isset($patientAlerts) && $patientAlerts->count() > 0)
            @foreach($patientAlerts as $alert)
                <div class="card mb-3 alert-card {{ $alert->is_urgent ? 'border-danger' : '' }}" data-alert-id="{{ $alert->id }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $alert->is_urgent ? 'badge-danger' : 'badge-primary' }} mr-3" style="font-size:1.5em;">
                                    @if($alert->alert_type == 'emergency')
                                        <i class="fas fa-exclamation-triangle"></i>
                                    @elseif($alert->alert_type == 'pain')
                                        <i class="fas fa-heartbeat"></i>
                                    @elseif($alert->alert_type == 'assistance')
                                        <i class="fas fa-hands-helping"></i>
                                    @elseif($alert->alert_type == 'water')
                                        <i class="fas fa-tint"></i>
                                    @elseif($alert->alert_type == 'bathroom')
                                        <i class="fas fa-toilet"></i>
                                    @elseif($alert->alert_type == 'food')
                                        <i class="fas fa-utensils"></i>
                                    @else
                                        <i class="fas fa-bell"></i>
                                    @endif
                                </span>
                                <div>
                                    <strong>{{ $alert->patient->name }}</strong> (Bed {{ $alert->bed->bed_number }}) <br>
                                    <span class="text-muted">{{ $alert->message }}</span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <span class="badge {{ $alert->status == 'new' ? 'badge-warning' : 'badge-secondary' }}">
                                    {{ ucfirst($alert->status) }}
                                </span>
                                <small class="d-block text-muted mt-1">
                                    {{ $alert->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm w-100">
                            @if($alert->status == 'new')
                                <button type="button" class="btn btn-outline-primary mark-seen-btn" data-alert-id="{{ $alert->id }}">
                                    <i class="fas fa-check"></i> Mark as Seen
                                </button>
                            @endif
                            <button type="button" class="btn btn-outline-success resolve-btn" data-alert-id="{{ $alert->id }}">
                                <i class="fas fa-check-double"></i> Resolve
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info" id="no-alerts-message">
                <i class="fas fa-info-circle"></i> No active alerts at this time.
            </div>
        @endif
    </div>
    
    <div class="text-right">
        <button type="button" class="btn btn-sm btn-primary" id="refresh-btn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        // Function to get the CSRF token
        function getCsrfToken() {
            return '{{ csrf_token() }}';
        }
        
        // Function to refresh alerts
        function refreshAlerts() {
            $.ajax({
                url: '{{ route("admin.beds.wards.alerts", $ward->id) }}',
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    updateAlertsContainer(response.alerts);
                    
                    // If there are new alerts, notify the parent window
                    if (response.newAlertsCount > 0) {
                        if (window.parent && window.parent.newAlertReceived) {
                            window.parent.newAlertReceived(response.newAlertsCount);
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Failed to refresh alerts:', xhr.responseText);
                }
            });
        }
        
        // Function to update the alerts container
        function updateAlertsContainer(alerts) {
            const alertsContainer = $('#alerts-container');
            
            // Clear existing content
            alertsContainer.empty();
            
            if (alerts && alerts.length > 0) {
                // Create and append alert cards
                alerts.forEach(function(alert) {
                    const badge = getBadgeForAlertType(alert.alert_type);
                    const statusBadge = alert.status === 'new' ? 'badge-warning' : 'badge-secondary';
                    const isUrgentClass = alert.is_urgent ? 'border-danger' : '';
                    
                    let card = `
                        <div class="card mb-3 alert-card ${isUrgentClass}" data-alert-id="${alert.id}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge ${alert.is_urgent ? 'badge-danger' : 'badge-primary'} mr-3" style="font-size:1.5em;">
                                            ${badge}
                                        </span>
                                        <div>
                                            <strong>${alert.patient.name}</strong> (Bed ${alert.bed.bed_number}) <br>
                                            <span class="text-muted">${alert.message}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="badge ${statusBadge}">
                                            ${alert.status.charAt(0).toUpperCase() + alert.status.slice(1)}
                                        </span>
                                        <small class="d-block text-muted mt-1">
                                            ${formatTimestamp(alert.created_at)}
                                        </small>
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm w-100">
                                    ${alert.status === 'new' ? 
                                        `<button type="button" class="btn btn-outline-primary mark-seen-btn" data-alert-id="${alert.id}">
                                            <i class="fas fa-check"></i> Mark as Seen
                                        </button>` : ''
                                    }
                                    <button type="button" class="btn btn-outline-success resolve-btn" data-alert-id="${alert.id}">
                                        <i class="fas fa-check-double"></i> Resolve
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    alertsContainer.append(card);
                });
            } else {
                // Show no alerts message
                alertsContainer.html(`
                    <div class="alert alert-info" id="no-alerts-message">
                        <i class="fas fa-info-circle"></i> No active alerts at this time.
                    </div>
                `);
            }
            
            // Reattach event handlers
            attachEventHandlers();
        }
        
        // Format timestamp for display
        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) {
                return 'Just now';
            } else if (diffMins < 60) {
                return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            } else if (diffMins < 1440) {
                const hours = Math.floor(diffMins / 60);
                return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            } else {
                const days = Math.floor(diffMins / 1440);
                return `${days} day${days > 1 ? 's' : ''} ago`;
            }
        }
        
        // Get appropriate badge icon for alert type
        function getBadgeForAlertType(type) {
            switch (type) {
                case 'emergency':
                    return '<i class="fas fa-exclamation-triangle"></i>';
                case 'pain':
                    return '<i class="fas fa-heartbeat"></i>';
                case 'assistance':
                    return '<i class="fas fa-hands-helping"></i>';
                case 'water':
                    return '<i class="fas fa-tint"></i>';
                case 'bathroom':
                    return '<i class="fas fa-toilet"></i>';
                case 'food':
                    return '<i class="fas fa-utensils"></i>';
                default:
                    return '<i class="fas fa-bell"></i>';
            }
        }
        
        // Attach event handlers to buttons
        function attachEventHandlers() {
            // Mark as seen button
            $('.mark-seen-btn').on('click', function() {
                const alertId = $(this).data('alert-id');
                const btn = $(this);
                
                $.ajax({
                    url: '{{ url("admin/beds/wards/alerts") }}/' + alertId + '/seen',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        // Change button appearance
                        btn.closest('.card').find('.badge-warning').removeClass('badge-warning').addClass('badge-secondary').text('Seen');
                        btn.remove();
                    },
                    error: function(xhr) {
                        console.error('Failed to mark alert as seen:', xhr.responseText);
                        alert('Failed to mark alert as seen. Please try again.');
                    }
                });
            });
            
            // Resolve button
            $('.resolve-btn').on('click', function() {
                const alertId = $(this).data('alert-id');
                const card = $(this).closest('.alert-card');
                
                $.ajax({
                    url: '{{ url("admin/beds/wards/alerts") }}/' + alertId + '/resolve',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        // Remove the card with animation
                        card.fadeOut(500, function() {
                            $(this).remove();
                            
                            // If no alerts left, show message
                            if ($('.alert-card').length === 0) {
                                $('#alerts-container').html(`
                                    <div class="alert alert-info" id="no-alerts-message">
                                        <i class="fas fa-info-circle"></i> No active alerts at this time.
                                    </div>
                                `);
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Failed to resolve alert:', xhr.responseText);
                        alert('Failed to resolve alert. Please try again.');
                    }
                });
            });
        }
        
        // Attach event handlers on page load
        attachEventHandlers();
        
        // Refresh button click handler
        $('#refresh-btn').on('click', function() {
            refreshAlerts();
        });
        
        // Poll for updates every 15 seconds
        setInterval(refreshAlerts, 15000);
    });
</script>
@endpush
@endsection 