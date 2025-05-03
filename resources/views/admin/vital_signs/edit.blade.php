@extends('adminlte::page')

@section('title', 'Edit Vital Signs')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Vital Signs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $vitalSign->patient_id) }}">{{ $vitalSign->patient->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vital-signs.index', ['patient_id' => $vitalSign->patient_id]) }}">Vital Signs</a></li>
                    <li class="breadcrumb-item active">Edit Vital Signs</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Edit Vital Signs Information</h3>
                    </div>
                    
                    <form action="{{ route('admin.vital-signs.update', $vitalSign->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                        <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                                            <option value="">Select Patient</option>
                                            @foreach($patients as $patient)
                                                <option value="{{ $patient->id }}" {{ (old('patient_id', $vitalSign->patient_id) == $patient->id) ? 'selected' : '' }}>
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
                                    <div class="form-group">
                                        <label for="recorded_at">Date and Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="recorded_at" id="recorded_at" class="form-control @error('recorded_at') is-invalid @enderror" value="{{ old('recorded_at', $vitalSign->recorded_at ? $vitalSign->recorded_at->format('Y-m-d\TH:i') : '') }}" required>
                                        @error('recorded_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="mt-4">Basic Vital Signs</h4>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="temperature">Temperature (°C)</label>
                                                <input type="number" step="0.1" name="temperature" id="temperature" class="form-control @error('temperature') is-invalid @enderror" value="{{ old('temperature', $vitalSign->temperature) }}" placeholder="36.5">
                                                @error('temperature')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="heart_rate">Heart Rate (bpm)</label>
                                                <input type="number" name="heart_rate" id="heart_rate" class="form-control @error('heart_rate') is-invalid @enderror" value="{{ old('heart_rate', $vitalSign->heart_rate) }}" placeholder="80">
                                                @error('heart_rate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="respiratory_rate">Respiratory Rate (breaths/min)</label>
                                                <input type="number" name="respiratory_rate" id="respiratory_rate" class="form-control @error('respiratory_rate') is-invalid @enderror" value="{{ old('respiratory_rate', $vitalSign->respiratory_rate) }}" placeholder="18">
                                                @error('respiratory_rate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="oxygen_saturation">Oxygen Saturation (%)</label>
                                                <input type="number" step="0.1" name="oxygen_saturation" id="oxygen_saturation" class="form-control @error('oxygen_saturation') is-invalid @enderror" value="{{ old('oxygen_saturation', $vitalSign->oxygen_saturation) }}" placeholder="98">
                                                @error('oxygen_saturation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="systolic_bp">Systolic BP (mmHg)</label>
                                                <input type="number" name="systolic_bp" id="systolic_bp" class="form-control @error('systolic_bp') is-invalid @enderror" value="{{ old('systolic_bp', $vitalSign->systolic_bp) }}" placeholder="120">
                                                @error('systolic_bp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="diastolic_bp">Diastolic BP (mmHg)</label>
                                                <input type="number" name="diastolic_bp" id="diastolic_bp" class="form-control @error('diastolic_bp') is-invalid @enderror" value="{{ old('diastolic_bp', $vitalSign->diastolic_bp) }}" placeholder="80">
                                                @error('diastolic_bp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4 class="mt-4">Consciousness Level and Notes</h4>
                                    <hr>
                                    
                                    <div class="form-group">
                                        <label for="consciousness">Consciousness Level (AVPU)</label>
                                        <select name="consciousness" id="consciousness" class="form-control @error('consciousness') is-invalid @enderror">
                                            <option value="">Select Level</option>
                                            <option value="A" {{ old('consciousness', $vitalSign->consciousness) == 'A' ? 'selected' : '' }}>A - Alert</option>
                                            <option value="V" {{ old('consciousness', $vitalSign->consciousness) == 'V' ? 'selected' : '' }}>V - Verbal (Responds to voice)</option>
                                            <option value="P" {{ old('consciousness', $vitalSign->consciousness) == 'P' ? 'selected' : '' }}>P - Pain (Responds to pain)</option>
                                            <option value="U" {{ old('consciousness', $vitalSign->consciousness) == 'U' ? 'selected' : '' }}>U - Unresponsive</option>
                                        </select>
                                        @error('consciousness')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="notes">Additional Notes</label>
                                        <textarea name="notes" id="notes" rows="5" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter any additional observations or notes here...">{{ old('notes', $vitalSign->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="alert alert-info">
                                            <h5><i class="icon fas fa-info"></i> Current EWS Score</h5>
                                            <p>Current Early Warning Score (EWS): <span class="badge badge-{{ $vitalSign->status_color }}">{{ $vitalSign->total_ews }}</span> - {{ $vitalSign->clinical_status }}</p>
                                            <p class="mb-0">The EWS will be recalculated after you save changes.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field to handle redirect logic -->
                            <input type="hidden" name="redirect" value="patient">
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">Update Vital Signs</button>
                            <a href="{{ route('admin.vital-signs.index', ['patient_id' => $vitalSign->patient_id]) }}" class="btn btn-default float-right">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Additional styling for EWS colors */
        .badge-success { background-color: #28a745; }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
    </style>
@stop 