@extends('adminlte::page')

@section('title', 'Nurse Schedule Management')

@section('content_header')
    <h1>Nurse Schedule Management</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload New Nurse Schedule</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.integration.nurse-schedule.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Schedule Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ward_id">Ward (Optional)</label>
                                    <select class="form-control @error('ward_id') is-invalid @enderror" id="ward_id" name="ward_id">
                                        <option value="">Select Ward</option>
                                        @foreach($wards as $ward)
                                            <option value="{{ $ward->id }}" {{ old('ward_id') == $ward->id ? 'selected' : '' }}>
                                                {{ $ward->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ward_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="schedule_file">Schedule JSON File</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('schedule_file') is-invalid @enderror" 
                                           id="schedule_file" name="schedule_file" accept=".json" required>
                                    <label class="custom-file-label" for="schedule_file">Choose file</label>
                                </div>
                            </div>
                            @error('schedule_file')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Upload a JSON file containing nurse schedule data. Maximum file size: 10MB.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Uploaded Schedules</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Ward</th>
                                    <th>Original Filename</th>
                                    <th>Schedule Period</th>
                                    <th>Status</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->name }}</td>
                                        <td>
                                            @if($schedule->ward)
                                                {{ $schedule->ward->name }}
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $schedule->original_filename }}</td>
                                        <td>
                                            @if($schedule->schedule_start_date && $schedule->schedule_end_date)
                                                {{ $schedule->schedule_start_date->format('M j, Y') }} - 
                                                {{ $schedule->schedule_end_date->format('M j, Y') }}
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($schedule->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $schedule->created_at->format('M j, Y g:i A') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.integration.nurse-schedule.show', $schedule->id) }}" 
                                                   class="btn btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.integration.nurse-schedule.iframe', $schedule->id) }}" 
                                                   class="btn btn-primary" title="View Schedule" target="_blank">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.integration.nurse-schedule.toggle', $schedule->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-{{ $schedule->is_active ? 'warning' : 'success' }}" 
                                                            title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $schedule->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.integration.nurse-schedule.destroy', $schedule->id) }}" 
                                                      method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                            <br>
                                            No schedules uploaded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Update file input label when file is selected
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });
});
</script>
@stop 