@extends('adminlte::page')

@section('title', 'Admit Patient')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Admit Patient</h1>
                <span class="badge badge-secondary">{{ $ward->name }} - {{ $ward->specialty->name }}</span>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.dashboard.direct', ['ward' => $ward->id]) }}">Ward Dashboard</a></li>
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
                        <h3 class="card-title">Admit Patient to Bed {{ $bed->bed_number }}</h3>
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

                        <form action="{{ route('admin.beds.wards.admit.store.direct', ['ward' => $ward, 'bedId' => $bed->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                            <input type="hidden" name="bed_id" value="{{ $bed->id }}">
                            <input type="hidden" name="status" value="occupied">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                        <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                                            <option value="">Select Patient</option>
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                    {{ $patient->name }} ({{ $patient->identity_number }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-bed"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bed Information</span>
                                            <span class="info-box-number">{{ $bed->bed_number }}</span>
                                            <span class="info-box-text">{{ $ward->name }}</span>
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
                                                <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                                    {{ $consultant->name }} ({{ $consultant->specialty->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('consultant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                <label for="notes">Admission Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Admit Patient</button>
                                <a href="{{ route('admin.beds.wards.dashboard.direct', ['ward' => $ward->id]) }}" class="btn btn-default">Cancel</a>
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
            // You could add JavaScript for enhancing the form here if needed
        });
    </script>
@stop 