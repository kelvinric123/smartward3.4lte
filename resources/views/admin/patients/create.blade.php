@extends('adminlte::page')

@section('title', 'Add New Patient')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Add New Patient</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item active">Add New Patient</li>
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
                        <h3 class="card-title">Patient Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="{{ route('admin.patients.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="mrn">Medical Record Number (MRN)</label>
                                <input type="text" name="mrn" id="mrn" class="form-control @error('mrn') is-invalid @enderror" value="{{ old('mrn') }}">
                                <small class="text-muted">Leave blank to auto-generate</small>
                                @error('mrn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="identity_type">Identification Type</label>
                                <select name="identity_type" id="identity_type" class="form-control @error('identity_type') is-invalid @enderror" required>
                                    <option value="ic" {{ old('identity_type') == 'ic' ? 'selected' : '' }}>MyKad (IC)</option>
                                    <option value="passport" {{ old('identity_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                                </select>
                                @error('identity_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="identity_number">IC / Passport Number</label>
                                <input type="text" name="identity_number" id="identity_number" class="form-control @error('identity_number') is-invalid @enderror" value="{{ old('identity_number') }}" required>
                                <small class="text-muted" id="id-format-help">For MyKad, use format: YYMMDD-PB-###G (e.g., 950505-14-5566)</small>
                                @error('identity_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="age">Age</label>
                                        <input type="number" name="age" id="age" class="form-control @error('age') is-invalid @enderror" value="{{ old('age') }}">
                                        <small class="text-muted ic-info">Will be auto-calculated from IC number if left blank</small>
                                        <small id="age-autofill-msg" class="text-success" style="display: none;"><i class="fas fa-check"></i> Age automatically calculated from IC</small>
                                        @error('age')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <small class="text-muted ic-info">Will be auto-determined from IC number if left blank</small>
                                        <small id="gender-autofill-msg" class="text-success" style="display: none;"><i class="fas fa-check"></i> Gender automatically determined from IC</small>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email (Optional)</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone (Optional)</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address (Optional)</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save Patient</button>
                                <a href="{{ route('admin.patients.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .ic-info {
            display: block;
            margin-top: 5px;
        }
        .auto-filled {
            background-color: #e8f4fe !important;
            border-color: #3498db !important;
            transition: background-color 0.5s;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Show/hide auto-calculation info based on identity type
            $('#identity_type').change(function() {
                if ($(this).val() === 'ic') {
                    $('.ic-info').show();
                    $('#id-format-help').show();
                } else {
                    $('.ic-info').hide();
                    $('#id-format-help').hide();
                    // Remove auto-filled styling when changing to passport
                    $('#age, #gender').removeClass('auto-filled');
                }
            });
            
            // Auto-calculate age and gender from IC number
            $('#identity_number').on('blur', function() {
                if ($('#identity_type').val() === 'ic') {
                    const icNumber = $(this).val().replace(/-/g, '');
                    
                    // Verify IC format (basic check)
                    if (icNumber.length >= 12) {
                        // Extract birthdate (YYMMDD)
                        const birthYear = icNumber.substr(0, 2);
                        const birthMonth = icNumber.substr(2, 2);
                        const birthDay = icNumber.substr(4, 2);
                        
                        // Calculate full year (assuming 19YY for years > current year's last 2 digits, else 20YY)
                        const currentYear = new Date().getFullYear().toString().substr(2, 2);
                        const fullYear = parseInt(birthYear) > parseInt(currentYear) ? 
                            '19' + birthYear : '20' + birthYear;
                        
                        // Calculate age
                        const birthDate = new Date(`${fullYear}-${birthMonth}-${birthDay}`);
                        const today = new Date();
                        let age = today.getFullYear() - birthDate.getFullYear();
                        const m = today.getMonth() - birthDate.getMonth();
                        
                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        
                        // Set age if valid
                        if (!isNaN(age) && age > 0 && age < 120) {
                            $('#age').val(age).addClass('auto-filled');
                            
                            // Show tooltip or message
                            $('#age-autofill-msg').show().fadeOut(3000);
                        }
                        
                        // Determine gender (last digit: odd = male, even = female)
                        const lastDigit = parseInt(icNumber.slice(-1));
                        if (!isNaN(lastDigit)) {
                            const gender = lastDigit % 2 === 0 ? 'female' : 'male';
                            $('#gender').val(gender).addClass('auto-filled');
                            
                            // Show tooltip or message
                            $('#gender-autofill-msg').show().fadeOut(3000);
                        }
                    }
                }
            });
            
            // Remove auto-filled styling when user manually changes values
            $('#age, #gender').on('change', function() {
                $(this).removeClass('auto-filled');
            });
            
            // Trigger on page load
            $('#identity_type').trigger('change');
        });
    </script>
@stop 