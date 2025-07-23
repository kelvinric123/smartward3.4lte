@extends('layouts.iframe')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #007bff;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-search__field {
            width: 100% !important;
            padding: 8px !important;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        /* Patient result styles */
        .select2-result-patient {
            padding: 6px 0;
        }
        .select2-result-patient__name {
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }
        .select2-result-patient__id {
            color: #666;
            font-size: 0.9em;
        }
        .select2-result-patient__phone {
            color: #666;
            font-size: 0.85em;
            margin-top: 2px;
        }
        .loading-item {
            padding: 10px;
            text-align: center;
            color: #666;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid p-0">
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

                <form action="{{ route('admin.beds.wards.admit.store', ['ward' => $ward, 'bedId' => $bed->id]) }}" method="POST" id="admitPatientForm">
                    @csrf
                    <input type="hidden" name="ward_id" value="{{ $ward->id }}">
                    <input type="hidden" name="bed_id" value="{{ $bed->id }}">
                    <input type="hidden" name="status" value="occupied">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="patient_id">Search Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-control select2 @error('patient_id') is-invalid @enderror" required>
                                    <option value="">Type to search patient by name, IC/Passport, MRN, or phone...</option>
                                </select>
                                @error('patient_id')
                                    <div class="invalid-feedback d-block">
                                        <div class="alert alert-danger mt-2">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </div>
                                    </div>
                                @enderror
                                <small class="form-text text-muted">Start typing to search by patient name, IC/Passport number, MRN, or phone number</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="consultant_id">Consultant</label>
                                <select name="consultant_id" id="consultant_id" class="form-control @error('consultant_id') is-invalid @enderror">
                                    <option value="">Select Consultant</option>
                                    @foreach ($consultants as $consultant)
                                        <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                            {{ $consultant->name }}
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
                                <label for="nurse_id">Nurse</label>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="admission_date">Admission Date</label>
                                <input type="datetime-local" name="admission_date" id="admission_date" 
                                    class="form-control @error('admission_date') is-invalid @enderror"
                                    value="{{ old('admission_date', now()->format('Y-m-d\TH:i')) }}">
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expected_discharge_date">Expected Discharge Date & Time <small class="text-muted">(Optional)</small></label>
                                <input type="datetime-local" name="expected_discharge_date" id="expected_discharge_date" 
                                    class="form-control @error('expected_discharge_date') is-invalid @enderror"
                                    value="{{ old('expected_discharge_date') }}">
                                @error('expected_discharge_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expected_length_of_stay">Expected Length of Stay (days) <small class="text-muted">(Optional)</small></label>
                                <input type="number" name="expected_length_of_stay" id="expected_length_of_stay" 
                                    class="form-control @error('expected_length_of_stay') is-invalid @enderror"
                                    min="1" max="365" value="{{ old('expected_length_of_stay') }}">
                                @error('expected_length_of_stay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Enter number of days (1-365)</small>
                            </div>
                        </div>
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
                        <button type="button" class="btn btn-default" onclick="window.parent.closeAdmitPatientModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#patient_id').select2({
                placeholder: 'Type to search patient...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '{{ url("/admin/patients/search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            query: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                templateResult: formatPatient,
                templateSelection: formatPatientSelection
            }).on('select2:open', function() {
                // Clear any previous error messages when opening the dropdown
                $(this).next('.invalid-feedback').remove();
                $(this).removeClass('is-invalid');
            });

            // Format the patient option in the dropdown
            function formatPatient(patient) {
                if (patient.loading) {
                    return $('<div class="loading-item">Searching...</div>');
                }

                if (!patient.id) {
                    return patient.text;
                }

                var $container = $(
                    '<div class="select2-result-patient clearfix">' +
                        '<div class="select2-result-patient__name">' + patient.name + '</div>' +
                        '<div class="select2-result-patient__id">' + (patient.identity_number || patient.mrn || '') + '</div>' +
                        (patient.phone ? '<div class="select2-result-patient__phone">' + patient.phone + '</div>' : '') +
                    '</div>'
                );

                return $container;
            }

            // Format the selected patient
            function formatPatientSelection(patient) {
                return patient.text || (patient.name ? patient.name + ' (' + (patient.identity_number || patient.mrn) + ')' : 'Select a patient');
            }

            // Form submission
            $('#admitPatientForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        // Check if response indicates success
                        if (response.success) {
                            // Close the modal and refresh the parent page
                            window.parent.closeAdmitPatientModal();
                            window.parent.location.reload();
                        } else {
                            alert('Admission failed: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function(xhr, status, error) {
                        
                        // Handle validation errors
                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;
                            
                            // Check if this is a system alert for already admitted patient
                            if (response.systemAlert) {
                                // Show a system alert modal that user must acknowledge
                                alert(response.message);
                                
                                // Focus back on the patient selection after alert is dismissed
                                $('#patient_id').select2('open');
                                return;
                            }
                            
                            // For regular validation errors
                            let errors = response.errors || {};
                            
                            // Clear any existing error messages
                            $('.alert-danger').remove();
                            
                            // Handle patient_id specific error (already admitted)
                            if (errors.patient_id) {
                                let patientErrorMessage = errors.patient_id[0];
                                $('#patient_id').addClass('is-invalid');
                                // Add specific error message below the patient field
                                $('#patient_id').after(
                                    `<div class="invalid-feedback d-block">
                                        <div class="alert alert-danger mt-2">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            ${patientErrorMessage}
                                        </div>
                                    </div>`
                                );
                                // Scroll to the error
                                $('html, body').animate({
                                    scrollTop: $('#patient_id').offset().top - 100
                                }, 200);
                            } else {
                                // For other errors, show at the top of the form
                                let errorHtml = '<div class="alert alert-danger"><ul>';
                                for (let field in errors) {
                                    errorHtml += `<li>${errors[field][0]}</li>`;
                                }
                                errorHtml += '</ul></div>';
                                $('#admitPatientForm').prepend(errorHtml);
                            }
                        } else {
                            // Show more detailed error information
                            let errorMessage = 'An error occurred while admitting the patient.';
                            if (xhr.status === 404) {
                                errorMessage += ' (Route not found - Status 404)';
                            } else if (xhr.status === 500) {
                                errorMessage += ' (Server error - Status 500)';
                            } else if (xhr.status === 403) {
                                errorMessage += ' (Access forbidden - Status 403)';
                            } else {
                                errorMessage += ` (HTTP Status: ${xhr.status})`;
                            }
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += '\nDetails: ' + xhr.responseJSON.message;
                            }
                            
                            alert(errorMessage);
                        }
                    }
                });
            });
        });
    </script>
@stop 