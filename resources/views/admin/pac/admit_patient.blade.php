@extends('adminlte::page')

@section('title', 'Admit Patient - PAC')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-plus-circle"></i> Admit Patient</h1>
                <span class="badge badge-secondary">{{ $ward->name }} - {{ $ward->specialty->name }}</span>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pac.dashboard') }}">PAC Dashboard</a></li>
                    <li class="breadcrumb-item active">Admit Patient</li>
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
                        <h3 class="card-title">Admit Patient to {{ $ward->name }}</h3>
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

                        <form action="{{ route('admin.pac.admit.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                        <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                    {{ $patient->name }} ({{ $patient->mrn ?? 'No MRN' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bed_id">Select Bed <span class="text-danger">*</span></label>
                                        <select name="bed_id" id="bed_id" class="form-control @error('bed_id') is-invalid @enderror" required>
                                            <option value="">Select Available Bed</option>
                                            @foreach ($availableBeds as $availableBed)
                                                <option value="{{ $availableBed->id }}" {{ old('bed_id', $defaultBed->id) == $availableBed->id ? 'selected' : '' }}>
                                                    Bed {{ $availableBed->bed_number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bed_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">{{ $availableBeds->count() }} available bed(s) in {{ $ward->name }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-building"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Ward Information</span>
                                            <span class="info-box-number">{{ $ward->name }}</span>
                                            <span class="info-box-text">{{ $ward->specialty->name }} • {{ $ward->hospital->name }}</span>
                                            <span class="info-box-text">Capacity: {{ $ward->beds->count() }} beds</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="consultant_id">Assigned Consultant</label>
                                        <select name="consultant_id" id="consultant_id" class="form-control @error('consultant_id') is-invalid @enderror">
                                            <option value="">Select Consultant</option>
                                            @foreach ($consultants as $consultant)
                                                <option value="{{ $consultant->id }}" 
                                                        data-specialty="{{ $consultant->specialty_id }}"
                                                        {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                                    {{ $consultant->name }} ({{ $consultant->specialty->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('consultant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Consultants will be filtered by ward specialty automatically.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nurse_id">Assigned Nurse</label>
                                        <select name="nurse_id" id="nurse_id" class="form-control @error('nurse_id') is-invalid @enderror">
                                            <option value="">Select Nurse</option>
                                            @foreach ($nurses as $nurse)
                                                <option value="{{ $nurse->id }}" {{ old('nurse_id') == $nurse->id ? 'selected' : '' }}>
                                                    {{ $nurse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nurse_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="admission_date">Admission Date & Time</label>
                                <input type="datetime-local" name="admission_date" id="admission_date" 
                                    class="form-control @error('admission_date') is-invalid @enderror" 
                                    value="{{ old('admission_date', now()->format('Y-m-d\TH:i')) }}">
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave as is for current date/time or adjust if needed.</small>
                            </div>

                            <div class="form-group">
                                <label for="admission_notes">Admission Notes</label>
                                <textarea name="admission_notes" id="admission_notes" class="form-control @error('admission_notes') is-invalid @enderror" rows="3">{{ old('admission_notes') }}</textarea>
                                @error('admission_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Admit Patient
                                </button>
                                <a href="{{ route('admin.pac.dashboard') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
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
            // Filter consultants by ward specialty
            const wardSpecialtyId = {{ $ward->specialty_id }};
            
            // Hide consultants that don't match the ward specialty
            $('#consultant_id option').each(function() {
                const specialtyId = $(this).data('specialty');
                if (specialtyId && specialtyId != wardSpecialtyId) {
                    $(this).hide();
                }
            });
            
            // Show only consultants for this specialty
            $('#consultant_id option').each(function() {
                const specialtyId = $(this).data('specialty');
                if (specialtyId == wardSpecialtyId) {
                    $(this).show();
                }
            });
            
            // Update consultant dropdown to show only relevant options
            $('#consultant_id').trigger('change');
            
            // Initialize Select2 for better UX
            $('#patient_id, #bed_id, #consultant_id, #nurse_id').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@stop 