@extends('adminlte::page')

@section('title', 'Ward Details')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="d-flex align-items-center">
                    <h1 class="mr-2">Ward Details</h1>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="wardViewDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-exchange-alt"></i> Change Ward
                        </button>
                        <div class="dropdown-menu" aria-labelledby="wardViewDropdown">
                            @foreach($allWards as $availableWard)
                                <a class="dropdown-item {{ $ward->id == $availableWard->id ? 'active' : '' }}" 
                                   href="{{ route('admin.beds.wards.show', $availableWard) }}{{ request()->has('fullscreen') ? '?fullscreen=true' : '' }}">
                                    {{ $availableWard->name }} <small class="text-muted">({{ $availableWard->specialty->name }})</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">View Ward</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content_top_nav_right')
    <li class="nav-item d-none" id="current-datetime">
        <span class="nav-link">
            <i class="far fa-clock mr-1"></i> <span id="current-date-time-display"></span>
        </span>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" id="fullscreen-toggle" role="button">
            <i class="fas fa-expand"></i>
        </a>
    </li>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{ $ward->name }}</h3>
                            <div>
                                <a href="{{ route('admin.beds.wards.dashboard', $ward) }}{{ request()->has('fullscreen') ? '?fullscreen=true' : '' }}" class="btn btn-primary">
                                    <i class="fas fa-th"></i> View Dashboard
                                </a>
                                <a href="{{ route('admin.beds.wards.edit', $ward) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title">Basic Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Ward ID</th>
                                                <td>{{ $ward->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $ward->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Capacity</th>
                                                <td>{{ $ward->capacity }} beds</td>
                                            </tr>
                                            <tr>
                                                <th>Description</th>
                                                <td>{{ $ward->description ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @if ($ward->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title">Related Information</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Hospital</th>
                                                <td>
                                                    <a href="{{ route('admin.hospitals.show', $ward->hospital) }}">
                                                        {{ $ward->hospital->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Specialty</th>
                                                <td>{{ $ward->specialty->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $ward->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Updated</th>
                                                <td>{{ $ward->updated_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        /* Fullscreen mode styles */
        body.fullscreen-mode .main-sidebar {
            display: none !important;
        }
        
        body.fullscreen-mode .content-wrapper {
            margin-left: 0 !important;
        }
        
        body.fullscreen-mode #fullscreen-toggle i {
            transform: rotate(180deg);
        }
        
        body.fullscreen-mode #current-datetime {
            display: block !important;
        }
        
        /* Hide pushmenu toggle button in fullscreen mode */
        body.fullscreen-mode [data-widget="pushmenu"] {
            display: none !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Fullscreen toggle functionality
            const fullscreenToggle = $('#fullscreen-toggle');
            const body = $('body');
            const currentDateTime = $('#current-datetime');
            const dateTimeDisplay = $('#current-date-time-display');
            
            // Update current date and time
            function updateDateTime() {
                const now = new Date();
                const formattedDateTime = now.toLocaleString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                dateTimeDisplay.text(formattedDateTime);
            }
            
            // Initial update
            updateDateTime();
            
            // Update every second
            setInterval(updateDateTime, 1000);
            
            // Toggle fullscreen mode
            fullscreenToggle.on('click', function(e) {
                e.preventDefault();
                body.toggleClass('fullscreen-mode');
                
                // Toggle icon
                const icon = $(this).find('i');
                if (body.hasClass('fullscreen-mode')) {
                    icon.removeClass('fa-expand').addClass('fa-compress');
                } else {
                    icon.removeClass('fa-compress').addClass('fa-expand');
                }
            });
            
            // Check URL for fullscreen parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('fullscreen') === 'true') {
                body.addClass('fullscreen-mode');
                fullscreenToggle.find('i').removeClass('fa-expand').addClass('fa-compress');
            }
        });
    </script>
@stop 