@extends('adminlte::page')

@section('title', 'Nurse Schedule Details')

@section('content_header')
    <h1>{{ $schedule->name }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Schedule Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Schedule Name:</strong><br>
                            {{ $schedule->name }}<br><br>

                            <strong>Ward:</strong><br>
                            @if($schedule->ward)
                                {{ $schedule->ward->name }}
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                            <br><br>

                            <strong>Original Filename:</strong><br>
                            {{ $schedule->original_filename }}<br><br>
                        </div>
                        <div class="col-md-6">
                            <strong>Schedule Period:</strong><br>
                            @if($schedule->schedule_start_date && $schedule->schedule_end_date)
                                {{ $schedule->schedule_start_date->format('M j, Y') }} - 
                                {{ $schedule->schedule_end_date->format('M j, Y') }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                            <br><br>

                            <strong>Status:</strong><br>
                            @if($schedule->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                            <br><br>

                            <strong>Upload Date:</strong><br>
                            {{ $schedule->created_at->format('M j, Y g:i A') }}<br><br>
                        </div>
                    </div>

                    @if(isset($schedule->schedule_data['roster_info']))
                        <h5>Roster Information</h5>
                        <ul>
                            @if(isset($schedule->schedule_data['roster_info']['hospital']))
                                <li><strong>Hospital:</strong> {{ $schedule->schedule_data['roster_info']['hospital']['name'] ?? 'N/A' }}</li>
                            @endif
                            @if(isset($schedule->schedule_data['roster_info']['department']))
                                <li><strong>Department:</strong> {{ $schedule->schedule_data['roster_info']['department']['name'] ?? 'N/A' }}</li>
                            @endif
                            @if(isset($schedule->schedule_data['roster_info']['status']))
                                <li><strong>Roster Status:</strong> {{ ucfirst($schedule->schedule_data['roster_info']['status']) }}</li>
                            @endif
                            @if(isset($schedule->schedule_data['export_info']['total_records']))
                                <li><strong>Total Assignments:</strong> {{ $schedule->schedule_data['export_info']['total_records'] }}</li>
                            @endif
                        </ul>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.integration.nurse-schedule') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Schedules
                    </a>
                    <a href="{{ route('admin.integration.nurse-schedule.iframe', $schedule->id) }}" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-calendar-alt"></i> View Schedule
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.integration.nurse-schedule.toggle', $schedule->id) }}" 
                          method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-{{ $schedule->is_active ? 'warning' : 'success' }} btn-block">
                            <i class="fas fa-{{ $schedule->is_active ? 'pause' : 'play' }}"></i>
                            {{ $schedule->is_active ? 'Deactivate' : 'Activate' }} Schedule
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.integration.nurse-schedule.destroy', $schedule->id) }}" 
                          method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete Schedule
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($schedule->schedule_data['roster_info']['schedules']))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Schedule Types</h3>
                    </div>
                    <div class="card-body">
                        @foreach($schedule->schedule_data['roster_info']['schedules'] as $scheduleType)
                            <div class="mb-3">
                                <strong>{{ $scheduleType['name'] ?? 'Unknown' }}</strong><br>
                                <small class="text-muted">
                                    Type: {{ ucfirst($scheduleType['type'] ?? 'N/A') }}<br>
                                    Required per day: {{ $scheduleType['people_required_per_day'] ?? 'N/A' }}
                                </small>
                                
                                @if(isset($scheduleType['shift_slots']))
                                    <br><strong>Shifts:</strong>
                                    <ul class="list-unstyled ml-3">
                                        @foreach($scheduleType['shift_slots'] as $shift)
                                            <li>
                                                <small>
                                                    {{ $shift['name'] ?? 'Unknown' }}: 
                                                    {{ $shift['formatted_time_range'] ?? 'N/A' }}
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@stop 