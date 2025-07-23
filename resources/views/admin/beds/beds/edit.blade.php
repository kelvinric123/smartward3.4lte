@extends('adminlte::page')

@section('title', 'Edit Bed')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Bed</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.beds.index') }}">Beds</a></li>
                    <li class="breadcrumb-item active">Edit Bed</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bed Details</h3>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.beds.beds.update', $bed) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Bed Code</label>
                                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $bed->code) }}" placeholder="e.g., B1, B2, BED001">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Unique code for HL7 integration (optional)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bed_number">Bed Number <span class="text-danger">*</span></label>
                                        <input type="text" name="bed_number" id="bed_number" class="form-control @error('bed_number') is-invalid @enderror" value="{{ old('bed_number', $bed->bed_number) }}" required>
                                        @error('bed_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                            @foreach ($statuses as $key => $value)
                                                <option value="{{ $key }}" {{ old('status', $bed->status) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ward_id">Ward <span class="text-danger">*</span></label>
                                        <select name="ward_id" id="ward_id" class="form-control @error('ward_id') is-invalid @enderror" required>
                                            <option value="">Select Ward</option>
                                            @foreach ($wards as $ward)
                                                <option value="{{ $ward->id }}" {{ old('ward_id', $bed->ward_id) == $ward->id ? 'selected' : '' }}>
                                                    {{ $ward->name }} ({{ $ward->hospital->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ward_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group assign-fields" id="consultant-field">
                                        <label for="consultant_id">Consultant</label>
                                        <select name="consultant_id" id="consultant_id" class="form-control @error('consultant_id') is-invalid @enderror">
                                            <option value="">Select Consultant</option>
                                            @foreach ($consultants as $consultant)
                                                <option value="{{ $consultant->id }}" {{ old('consultant_id', $bed->consultant_id) == $consultant->id ? 'selected' : '' }}>
                                                    {{ $consultant->name }} ({{ $consultant->specialty->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('consultant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row assign-fields" id="nurse-patient-fields">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nurse_id">Nurse</label>
                                        <select name="nurse_id" id="nurse_id" class="form-control @error('nurse_id') is-invalid @enderror">
                                            <option value="">Select Nurse</option>
                                            @foreach ($nurses as $nurse)
                                                <option value="{{ $nurse->id }}" {{ old('nurse_id', $bed->nurse_id) == $nurse->id ? 'selected' : '' }}>
                                                    {{ $nurse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nurse_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="patient-field">
                                        <label for="patient_id">Patient</label>
                                        <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror">
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}" {{ old('patient_id', $bed->patient_id) == $patient->id ? 'selected' : '' }}>
                                                    {{ $patient->name }} ({{ $patient->identity_number }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group admission-field" id="admission-date-field" style="{{ isset($isAdmitting) && $isAdmitting ? '' : 'display: none;' }}">
                                <label for="admission_date">Admission Date & Time</label>
                                <input type="datetime-local" name="admission_date" id="admission_date" 
                                    class="form-control @error('admission_date') is-invalid @enderror" 
                                    value="{{ old('admission_date', isset($admissionDate) ? $admissionDate : now()->format('Y-m-d\TH:i')) }}">
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave as is for current date/time or adjust if needed.</small>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $bed->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $bed->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.beds.beds.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Handle changing the status to conditionally show/hide fields
            function updateFieldsVisibility() {
                var status = $('#status').val();
                var currentPatientId = "{{ $bed->patient_id }}";
                var newPatientId = $('#patient_id').val();
                
                if (status === 'available') {
                    $('.assign-fields select').val('');
                    $('.assign-fields').hide();
                    $('.admission-field').hide();
                } else if (status === 'maintenance') {
                    $('.assign-fields select').val('');
                    $('.assign-fields').hide();
                    $('.admission-field').hide();
                } else {
                    $('.assign-fields').show();
                    
                    // If occupied, patient field is required
                    if (status === 'occupied') {
                        $('#patient_id').prop('required', true);
                        $('#patient-field label').html('Patient <span class="text-danger">*</span>');
                        
                        // Show admission date field only when admitting a new patient
                        if (!currentPatientId || currentPatientId !== newPatientId) {
                            $('.admission-field').show();
                        } else {
                            $('.admission-field').hide();
                        }
                    } else {
                        $('#patient_id').prop('required', false);
                        $('#patient-field label').text('Patient');
                        $('.admission-field').hide();
                    }
                }
            }
            
            // Initial visibility
            updateFieldsVisibility();
            
            // Update visibility when status changes
            $('#status').change(updateFieldsVisibility);
            
            // Update visibility when patient changes
            $('#patient_id').change(updateFieldsVisibility);
        });
    </script>
@stop 