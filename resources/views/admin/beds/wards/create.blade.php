@extends('adminlte::page')

@section('title', 'Add New Ward')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Add New Ward</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">Add New</li>
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

                        <form action="{{ route('admin.beds.wards.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Ward Code</label>
                                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g., ICU, CCU, WARD01">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Unique code for HL7 integration (optional)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Ward Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
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
                                        <input type="number" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 20) }}" min="1" required>
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
                                                <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>
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
                                        <label for="specialties">Specialties <span class="text-danger">*</span></label>
                                        <select name="specialties[]" id="specialties" class="form-control @error('specialties') is-invalid @enderror" multiple required>
                                            @foreach ($specialties as $specialty)
                                                <option value="{{ $specialty->id }}" {{ old('specialties') && in_array($specialty->id, old('specialties')) ? 'selected' : '' }}>
                                                    {{ $specialty->name }} ({{ $specialty->hospital->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select multiple specialties</small>
                                        @error('specialties')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('specialties.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        
                                        <!-- Keep legacy specialty_id field for backward compatibility - set to first selected specialty -->
                                        <input type="hidden" name="specialty_id" id="legacy_specialty_id" value="{{ old('specialty_id') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
                let currentSpecialties = [];
                
                // Get currently selected specialties
                $('#specialties option:selected').each(function() {
                    currentSpecialties.push($(this).val());
                });
                
                // Clear and disable specialty dropdown if no hospital selected
                if (!hospitalId) {
                    $('#specialties').empty().prop('disabled', true);
                    return;
                }
                
                // Filter specialties based on selected hospital
                $('#specialties').empty();
                @foreach ($specialties as $specialty)
                    if ('{{ $specialty->hospital_id }}' == hospitalId) {
                        let isSelected = currentSpecialties.includes('{{ $specialty->id }}') ? 'selected' : '';
                        $('#specialties').append(`<option value="{{ $specialty->id }}" ${isSelected}>{{ $specialty->name }}</option>`);
                    }
                @endforeach
                
                $('#specialties').prop('disabled', false);
            });
            
            // Update legacy specialty_id field when specialties selection changes
            $('#specialties').change(function() {
                let selectedSpecialties = $(this).val();
                if (selectedSpecialties && selectedSpecialties.length > 0) {
                    // Set the first selected specialty as the legacy specialty_id
                    $('#legacy_specialty_id').val(selectedSpecialties[0]);
                } else {
                    $('#legacy_specialty_id').val('');
                }
            });
            
            // Trigger change event on load if a hospital is already selected
            if ($('#hospital_id').val()) {
                $('#hospital_id').trigger('change');
            }
        });
    </script>
@stop 