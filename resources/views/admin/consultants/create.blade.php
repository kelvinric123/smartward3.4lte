@extends('adminlte::page')

@section('title', 'Add New Consultant')

@section('content_header')
    <h1>Add New Consultant</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Consultant Information</h3>
                </div>
                <form method="POST" action="{{ route('admin.consultants.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="e.g., DOCTOR0, DOCTOR1">
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Unique code for HL7 integration (optional)</small>
                                </div>

                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="specialty_id">Specialty</label>
                                    <select class="form-control @error('specialty_id') is-invalid @enderror" id="specialty_id" name="specialty_id" required>
                                        <option value="">Select Specialty</option>
                                        @foreach($specialties as $specialty)
                                            <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                                {{ $specialty->name }} ({{ $specialty->hospital->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qualification">Qualification</label>
                                    <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" value="{{ old('qualification') }}" required>
                                    @error('qualification')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="experience_years">Years of Experience</label>
                                    <input type="number" class="form-control @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years" value="{{ old('experience_years', 0) }}" min="0" required>
                                    @error('experience_years')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="bio">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="photo">Photo</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                            <label class="custom-file-label" for="photo">Choose file</label>
                                        </div>
                                    </div>
                                    @error('photo')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                                @error('is_active')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.consultants.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary float-right">Create Consultant</button>
                    </div>
                </form>
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
            // Show filename in custom file input
            $(document).on('change', '.custom-file-input', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@stop 