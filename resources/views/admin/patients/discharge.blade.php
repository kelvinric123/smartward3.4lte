@extends('adminlte::page')

@section('title', 'Discharge Patient')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Discharge Patient</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient->id) }}">{{ $patient->name }}</a></li>
                    <li class="breadcrumb-item active">Discharge</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Discharge {{ $patient->name }}</h3>
                    </div>
                    <form action="{{ route('admin.patients.discharge.store', $patient->id) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong>Current Admission Information:</strong><br>
                                Ward: {{ $bed->ward->name }} - Bed: {{ $bed->bed_number }}
                            </div>
                            
                            <div class="form-group">
                                <label for="discharge_date">Discharge Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="discharge_date" id="discharge_date" class="form-control @error('discharge_date') is-invalid @enderror" value="{{ old('discharge_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('discharge_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="discharge_type">Discharge Type <span class="text-danger">*</span></label>
                                <select name="discharge_type" id="discharge_type" class="form-control @error('discharge_type') is-invalid @enderror" required>
                                    <option value="routine" {{ old('discharge_type') == 'routine' ? 'selected' : '' }}>Routine Discharge</option>
                                    <option value="against_medical_advice" {{ old('discharge_type') == 'against_medical_advice' ? 'selected' : '' }}>Against Medical Advice</option>
                                    <option value="transfer" {{ old('discharge_type') == 'transfer' ? 'selected' : '' }}>Transfer to Another Facility</option>
                                    <option value="deceased" {{ old('discharge_type') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                </select>
                                @error('discharge_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="discharge_notes">Discharge Notes</label>
                                <textarea name="discharge_notes" id="discharge_notes" class="form-control @error('discharge_notes') is-invalid @enderror" rows="3">{{ old('discharge_notes') }}</textarea>
                                @error('discharge_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to discharge this patient?')">
                                <i class="fas fa-door-open mr-1"></i> Discharge Patient
                            </button>
                            <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-default float-right">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop 