@extends('adminlte::page')

@section('title', 'Edit Ward')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Ward</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">Edit Ward</li>
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
                        <h3 class="card-title">Ward Details</h3>
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

                        <form action="{{ route('admin.beds.wards.update', $ward) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Ward Code</label>
                                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $ward->code) }}" placeholder="e.g., ICU, CCU, WARD01">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Unique code for HL7 integration (optional)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Ward Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $ward->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="capacity">Capacity <span class="text-danger">*</span></label>
                                        <input type="number" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $ward->capacity) }}" min="1" required>
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hospital_id">Hospital <span class="text-danger">*</span></label>
                                        <select name="hospital_id" id="hospital_id" class="form-control @error('hospital_id') is-invalid @enderror" required>
                                            <option value="">Select Hospital</option>
                                            @foreach ($hospitals as $hospital)
                                                <option value="{{ $hospital->id }}" {{ old('hospital_id', $ward->hospital_id) == $hospital->id ? 'selected' : '' }}>
                                                    {{ $hospital->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('hospital_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialty_id">Specialty <span class="text-danger">*</span></label>
                                        <select name="specialty_id" id="specialty_id" class="form-control @error('specialty_id') is-invalid @enderror" required>
                                            <option value="">Select Specialty</option>
                                            @foreach ($specialties as $specialty)
                                                <option value="{{ $specialty->id }}" {{ old('specialty_id', $ward->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                    {{ $specialty->name }} ({{ $specialty->hospital->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('specialty_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $ward->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $ward->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.beds.wards.index') }}" class="btn btn-default">Cancel</a>
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
        // When hospital changes, filter specialties
        $(document).ready(function() {
            $('#hospital_id').change(function() {
                let hospitalId = $(this).val();
                let currentSpecialtyId = '{{ $ward->specialty_id }}';
                
                // Clear and disable specialty dropdown if no hospital selected
                if (!hospitalId) {
                    $('#specialty_id').empty().append('<option value="">Select Specialty</option>').prop('disabled', true);
                    return;
                }
                
                // Filter specialties based on selected hospital
                $('#specialty_id').empty().append('<option value="">Select Specialty</option>');
                @foreach ($specialties as $specialty)
                    if ('{{ $specialty->hospital_id }}' == hospitalId) {
                        let isSelected = '{{ $specialty->id }}' == currentSpecialtyId ? 'selected' : '';
                        $('#specialty_id').append(`<option value="{{ $specialty->id }}" ${isSelected}>{{ $specialty->name }}</option>`);
                    }
                @endforeach
                
                $('#specialty_id').prop('disabled', false);
            });
            
            // Trigger change event on load to initialize specialty dropdown
            $('#hospital_id').trigger('change');
        });
    </script>
@stop 