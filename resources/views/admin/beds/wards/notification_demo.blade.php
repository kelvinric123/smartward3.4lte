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
                            <button type="button" class="btn btn-outline-success respond-btn" data-alert-id="{{ $alert->id }}">
                                <i class="fas fa-reply"></i> Respond
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
        <button type="button" class="btn btn-sm btn-success mr-2" id="create-test-alert-btn">
            <i class="fas fa-plus"></i> Create Test Alert
        </button>
        <button type="button" class="btn btn-sm btn-primary" id="refresh-btn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>

<style>
    /* Better button states */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Loading animation for buttons */
    .fa-spin {
        animation: fa-spin 1s infinite linear;
    }
    
    /* Toast notification styles */
    .toast-notification {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 6px;
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Enhanced card hover effects */
    .alert-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .alert-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    /* Better button styling */
    .btn-group .btn {
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-1px);
    }
    
    /* Fade out animation for responded cards */
    .alert-card.responding {
        opacity: 0.6;
        transform: scale(0.95);
        transition: all 0.5s ease;
    }
</style>

@push('js')
<script>
    // Comprehensive AdminLTE iframe error prevention
    (function() {
        'use strict';
        
        // Completely disable AdminLTE iframe functionality
        // This prevents the "Cannot read properties of null (reading 'autoIframeMode')" error
        
        // Create AdminLTE object if it doesn't exist
        if (typeof window.AdminLTE === 'undefined') {
            window.AdminLTE = {};
        }
        
        // Override IFrame object with safe dummy methods
        window.AdminLTE.IFrame = {
            _config: { autoIframeMode: false },
            _element: null,
            _init: function() { console.log('AdminLTE IFrame._init intercepted'); return this; },
            _initFrameElement: function() { console.log('AdminLTE IFrame._initFrameElement intercepted'); return this; },
            _jQueryInterface: function() { console.log('AdminLTE IFrame._jQueryInterface intercepted'); return this; },
            autoIframeMode: false
        };
        
        // Prevent jQuery plugin registration
        $(function() {
            // Override jQuery IFrame plugin completely
            $.fn.IFrame = function(config) { 
                console.log('jQuery IFrame plugin call intercepted and ignored');
                return this; 
            };
            
            // Remove any data-widget="iframe" elements to prevent auto-initialization
            $('[data-widget="iframe"]').removeAttr('data-widget');
            
            // Disable AdminLTE auto-initialization for iframes
            if (window.AdminLTE && window.AdminLTE.PluginManager) {
                window.AdminLTE.PluginManager.autoLoad = false;
            }
        });
        
        // Intercept and prevent iframe-related errors at the window level
        window.addEventListener('error', function(e) {
            if (e.message && (
                e.message.includes('autoIframeMode') || 
                e.message.includes('_initFrameElement') ||
                e.message.includes('IFrame')
            )) {
                console.log('Intercepted AdminLTE iframe error:', e.message);
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);
        
    })();

    $(document).ready(function() {
        console.log('Document ready - initializing notification demo...');
        console.log('jQuery version:', $.fn.jquery);
        console.log('Initial alert cards found:', $('.alert-card').length);
        console.log('Running inside iframe:', window.parent !== window);
        console.log('AdminLTE object:', typeof window.AdminLTE);
        console.log('Current URL:', window.location.href);
        
        // Additional runtime protection against AdminLTE iframe errors
        try {
            console.log('Applying additional AdminLTE iframe protection...');
            
            // Force disable any remaining iframe functionality
            if (window.AdminLTE && window.AdminLTE.IFrame) {
                window.AdminLTE.IFrame = null;
            }
            
            // Remove any iframe-related elements that might cause issues
            $('[data-widget="iframe"]').remove();
            $('.main-sidebar .nav-link[data-widget="iframe"]').remove();
            
            // Disable any AdminLTE auto-initialization
            if (window.AdminLTE && window.AdminLTE.PluginManager) {
                window.AdminLTE.PluginManager = {
                    autoLoad: false,
                    register: function() { return this; }
                };
            }
            
            console.log('Additional AdminLTE iframe protection applied successfully');
        } catch (e) {
            console.log('Error applying additional iframe protection:', e.message);
        }
        
        // Function to get the CSRF token
        function getCsrfToken() {
            const token = '{{ csrf_token() }}';
            console.log('CSRF Token:', token);
            return token;
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
                    if (response.success) {
                        updateAlertsContainer(response.alerts);
                        
                        // Update parent window notification count with current count
                        if (window.parent && window.parent.newAlertReceived) {
                            window.parent.newAlertReceived(response.new_alerts_count);
                        }
                        
                        // Show refresh success feedback briefly
                        const refreshBtn = $('#refresh-btn');
                        const originalText = refreshBtn.html();
                        refreshBtn.html('<i class="fas fa-check"></i> Updated').addClass('btn-success').removeClass('btn-primary');
                        
                        setTimeout(() => {
                            refreshBtn.html(originalText).removeClass('btn-success').addClass('btn-primary');
                        }, 1500);

                        // Show browser notification if permission is granted
                        if (Notification.permission === 'granted') {
                            new Notification('🚨 New Patient Alert', {
                                body: `${response.new_alerts_count} new patient alert(s) received - Click to view`,
                                requireInteraction: true,
                                tag: 'patient-alert',
                                vibrate: [200, 100, 200] // Vibration pattern for mobile devices
                            });
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Failed to refresh alerts:', xhr.responseText);
                    showToast('Failed to refresh alerts', 'error');
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
                                    <button type="button" class="btn btn-outline-success respond-btn" data-alert-id="${alert.id}">
                                        <i class="fas fa-reply"></i> Respond
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
        
        // Attach event handlers to buttons using event delegation
        function attachEventHandlers() {
            console.log('Attaching event handlers to buttons...');
            
            // Remove any existing handlers to prevent duplicates
            $(document).off('click', '.respond-btn');
            
            // Respond button using event delegation
            $(document).on('click', '.respond-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const alertId = $(this).data('alert-id');
                const card = $(this).closest('.alert-card');
                const btn = $(this);
                
                console.log('Respond clicked for alert ID:', alertId);
                
                // Prevent double-clicks
                if (btn.prop('disabled')) {
                    return false;
                }
                
                // Disable button and show loading state
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Responding...');
                
                // Add responding animation class
                card.addClass('responding');
                
                const respondUrl = '{{ route("admin.beds.wards.alerts.respond", ":alertId") }}'.replace(':alertId', alertId);
                console.log('Respond URL:', respondUrl);
                
                $.ajax({
                    url: respondUrl,
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        console.log('Respond response:', response);
                        if (response.success) {
                            // Immediate fade out and remove
                            card.fadeOut(400, function() {
                                $(this).remove();
                                
                                // If no alerts left, show message
                                if ($('.alert-card').length === 0) {
                                    $('#alerts-container').html(`
                                        <div class="alert alert-info" id="no-alerts-message">
                                            <i class="fas fa-info-circle"></i> No active alerts at this time.
                                        </div>
                                    `);
                                }
                                
                                // Update parent window notification count
                                updateParentNotificationCount();
                            });
                            
                            // Show success feedback
                            showToast('Alert responded to successfully. Patient has been notified.', 'success');
                        } else {
                            // Remove responding class and re-enable button on error
                            card.removeClass('responding');
                            btn.prop('disabled', false).html('<i class="fas fa-reply"></i> Respond');
                            showToast('Failed to respond to alert', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to respond to alert:', {xhr, status, error});
                        console.error('Response text:', xhr.responseText);
                        // Remove responding class and re-enable button on error
                        card.removeClass('responding');
                        btn.prop('disabled', false).html('<i class="fas fa-reply"></i> Respond');
                        showToast('Failed to respond to alert. Please try again.', 'error');
                    }
                });
                
                return false;
            });
            
            console.log('Event handlers attached using delegation. Found buttons:', {
                respondButtons: $('.respond-btn').length
            });
        }
        
        // Function to update parent window notification count
        function updateParentNotificationCount() {
            // Count remaining alerts (all alerts are now considered active)
            const remainingAlertsCount = $('.alert-card').length;
            
            // Notify parent window
            if (window.parent && window.parent.newAlertReceived) {
                window.parent.newAlertReceived(remainingAlertsCount);
            }
        }
        
        // Function to show toast notifications
        function showToast(message, type = 'info') {
            // Create toast notification
            const toastClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle');
            
            const toast = $(`
                <div class="toast-notification alert ${toastClass} alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="fas ${icon} mr-2"></i>
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            
            // Add to body and auto-remove after 3 seconds
            $('body').append(toast);
            setTimeout(() => {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
        // Attach event handlers on page load
        attachEventHandlers();
        
        // Test if buttons exist after page load
        setTimeout(function() {
            console.log('After timeout - buttons check:', {
                respondButtons: $('.respond-btn').length,
                alertCards: $('.alert-card').length
            });
        }, 1000);
        
        // Refresh button click handler
        $('#refresh-btn').on('click', function() {
            refreshAlerts();
        });
        
        // Create test alert button click handler
        $('#create-test-alert-btn').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            // Show loading state
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            
            $.ajax({
                url: '{{ route("admin.beds.wards.create-test-alert", $ward->id) }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Test alert created successfully!', 'success');
                        // Refresh alerts to show the new one
                        refreshAlerts();
                    } else {
                        showToast('Failed to create test alert', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Failed to create test alert:', xhr.responseText);
                    showToast('Failed to create test alert', 'error');
                },
                complete: function() {
                    // Restore button state
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Poll for updates every 15 seconds
        setInterval(refreshAlerts, 15000);
        
        // Refresh alerts when window becomes visible (when modal is opened)
        $(window).on('focus', function() {
            refreshAlerts();
        });
        
        // Also refresh when the document becomes visible (for tab switching)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshAlerts();
            }
        });
    });
</script>
@endpush
@endsection 