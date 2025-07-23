@extends('adminlte::page')

@section('title', 'HL7 Admission Integration')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-exchange-alt"></i> HL7 Admission Integration</h1>
                <p class="text-muted">Process HL7 ADT (Admission/Discharge/Transfer) messages for patient admissions</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Integration</a></li>
                    <li class="breadcrumb-item active">Admission</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        
        <!-- Instructions Card -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Instructions</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-file-medical"></i> HL7 ADT Message Format</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> <strong>Message Type:</strong> ADT (Admission/Discharge/Transfer)</li>
                                    <li><i class="fas fa-check text-success"></i> <strong>Trigger Events:</strong> A01, A02, A03, A04, A05 (Admission events)</li>
                                    <li><i class="fas fa-check text-success"></i> <strong>Required Segments:</strong> MSH, PID, PV1</li>
                                    <li><i class="fas fa-check text-success"></i> <strong>Optional Segments:</strong> PV2, AL1, ZAT, ZIT, ZFR (Custom segments)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-cogs"></i> Processing Features</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-user-plus text-primary"></i> Automatic patient creation/update</li>
                                    <li><i class="fas fa-bed text-info"></i> Bed assignment validation</li>
                                    <li><i class="fas fa-user-md text-warning"></i> Consultant assignment</li>
                                    <li><i class="fas fa-exclamation-triangle text-danger"></i> Allergy and alert processing</li>
                                    <li><i class="fas fa-shield-alt text-success"></i> Fall risk and isolation precautions</li>
                                </ul>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Paste your HL7 JSON message in the text area below. The system will validate the message structure and process the admission automatically.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HL7 Message Input Card -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-upload"></i> HL7 Message Input</h3>
                    </div>
                    <div class="card-body">
                        <form id="hl7-admission-form">
                            @csrf
                            <div class="form-group">
                                <label for="hl7_message">HL7 JSON Message</label>
                                <textarea 
                                    class="form-control" 
                                    id="hl7_message" 
                                    name="hl7_message" 
                                    rows="20" 
                                    placeholder="Paste your HL7 JSON message here...&#10;&#10;Example:&#10;{&#10;  &quot;message_type&quot;: &quot;ADT&quot;,&#10;  &quot;trigger_event&quot;: &quot;A01&quot;,&#10;  &quot;segments&quot;: {&#10;    &quot;MSH&quot;: {...},&#10;    &quot;PID&quot;: {...},&#10;    &quot;PV1&quot;: {...}&#10;  }&#10;}"
                                    required></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-lightbulb"></i> 
                                    Tip: You can format your JSON using online tools before pasting to ensure proper structure.
                                </small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-secondary btn-block" id="validate-btn">
                                        <i class="fas fa-check-circle"></i> Validate Message
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-sign-in-alt"></i> Process Admission
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Loading overlay -->
                    <div class="overlay" id="form-overlay" style="display: none;">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Card -->
        <div class="row mt-3" id="response-card" style="display: none;">
            <div class="col-12">
                <div class="card" id="response-card-container">
                    <div class="card-header" id="response-header">
                        <h3 class="card-title" id="response-title"></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="close-response" data-toggle="tooltip" title="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="response-body">
                        <!-- Response content will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Message Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-secondary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-code"></i> Example HL7 JSON Message</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Expand">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-sm btn-info float-right mb-2" id="copy-example">
                                    <i class="fas fa-copy"></i> Copy Example
                                </button>
                                <pre><code id="example-json">{
  "message_type": "ADT",
  "trigger_event": "A01",
  "message_control_id": "efe4c5bd-1db8-463f-b6b5-7495a8edd562",
  "processing_id": "P",
  "version_id": "2.8",
  "segments": {
    "MSH": {
      "field_separator": "|",
      "encoding_characters": "^~\\&",
      "sending_application": "HIS",
      "sending_facility": "PHKL",
      "receiving_application": "ADT",
      "receiving_facility": "QMED",
      "date_time_of_message": "20250227145411",
      "security": "xxx",
      "message_type": "ADT",
      "trigger_event": "A01",
      "message_structure": "ADT_A01",
      "message_control_id": "efe4c5bd-1db8-463f-b6b5-7495a8edd562",
      "processing_id": "P",
      "version_id": "2.8"
    },
    "EVN": {
      "event_type_code": "",
      "recorded_date_time": "20250227145411",
      "date_time_planned_event": null,
      "event_reason_code": null,
      "operator_id": null,
      "event_occurred": null
    },
    "PID": {
      "set_id": "",
      "external_patient_id": "123456789^^^MYS^MR",
      "internal_patient_id": "88888",
      "alternate_patient_id": "Doe^John^^^Mr.",
      "patient_name": "Doe^John^^^Mr.",
      "mothers_maiden_name": "",
      "date_of_birth": "19850615",
      "gender": "M",
      "patient_alias": "",
      "race": "^Chinese",
      "patient_address": "123 Main St.^Kuala Lumpur^Wilayah Persekutuan^51000^MYS^P",
      "county_code": "",
      "phone_number_home": "",
      "phone_number_business": "",
      "primary_language": "",
      "marital_status": "",
      "religion": "BUD",
      "account_number": "",
      "ssn": "",
      "drivers_license": "",
      "phone_number_ext": "+601822400114",
      "patient_name_components": {
        "family_name": "Doe",
        "given_name": "John",
        "middle_name": "",
        "suffix": "",
        "prefix": "Mr."
      }
    },
    "PV1": {
      "patient_class": "R",
      "assigned_location_bed_code": "B2",
      "admission_type": "E",
      "attending_doctor_code": "DOCTOR0",
      "referring_doctor_code": "DOCTOR1",
      "consulting_doctor_code": "DOCTOR2",
      "ambulatory_status": "VISITNO",
      "vip_indicator": "1",
      "admitting_doctor_code": "DOCTOR3",
      "visit_number": "VISITNO",
      "diet_type": "VEGETARIAN",
      "admit_date_time": "20250227145411",
      "attending_doctor": "DOCTOR0",
      "referring_doctor": "DOCTOR1",
      "consulting_doctor": "DOCTOR2",
      "admitting_doctor": "DOCTOR3"
    },
    "PV2": {
      "prior_pending_location": "",
      "accommodation_code": "",
      "admit_reason": "",
      "transfer_reason": "",
      "patient_valuables": "",
      "patient_valuables_location": "",
      "visit_user_code": "",
      "expected_admit_date": "",
      "expected_discharge_date": "20250227205411",
      "estimated_length_of_inpatient_stay": "6"
    },
    "AL1": [
      {
        "set_id": "1",
        "allergen_type_code": "DA",
        "allergen_code": "PENICILLIN",
        "allergy_severity_code": "MI",
        "allergy_reaction_code": null,
        "identification_date": null
      },
      {
        "set_id": "2",
        "allergen_type_code": "DA",
        "allergen_code": "PARACETAMOL",
        "allergy_severity_code": "MO",
        "allergy_reaction_code": null,
        "identification_date": null
      }
    ],
    "ZAT": {
      "segment_name": "ZAT",
      "fields": [
        {
          "field_number": 1,
          "field_value": "FR"
        },
        {
          "field_number": 2,
          "field_value": "FALL RISK"
        }
      ]
    },
    "ZIT": {
      "segment_name": "ZIT",
      "fields": [
        {
          "field_number": 1,
          "field_value": "DAC"
        },
        {
          "field_number": 2,
          "field_value": "Droplet, Airborne and Contact"
        }
      ]
    },
    "ZFR": {
      "segment_name": "ZFR",
      "fields": [
        {
          "field_number": 1,
          "field_value": "1"
        }
      ]
    }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')
    <style>
        #example-json {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            line-height: 1.4;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .overlay {
            background: rgba(255,255,255,0.7);
        }
        
        .response-success {
            border-left: 5px solid #28a745;
        }
        
        .response-error {
            border-left: 5px solid #dc3545;
        }
        
        .response-warning {
            border-left: 5px solid #ffc107;
        }
        
        .patient-info-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .admission-info-card {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .alert-info-card {
            background: linear-gradient(135deg, #fff3e0 0%, #ffcc80 100%);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Form submission handler
            $('#hl7-admission-form').on('submit', function(e) {
                e.preventDefault();
                processAdmission();
            });
            
            // Validate button handler
            $('#validate-btn').on('click', function() {
                validateMessage();
            });
            
            // Copy example button handler
            $('#copy-example').on('click', function() {
                const exampleText = $('#example-json').text();
                navigator.clipboard.writeText(exampleText).then(function() {
                    $(this).html('<i class="fas fa-check"></i> Copied!');
                    setTimeout(() => {
                        $('#copy-example').html('<i class="fas fa-copy"></i> Copy Example');
                    }, 2000);
                }).catch(function() {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = exampleText;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    $('#copy-example').html('<i class="fas fa-check"></i> Copied!');
                    setTimeout(() => {
                        $('#copy-example').html('<i class="fas fa-copy"></i> Copy Example');
                    }, 2000);
                });
            });
            
            // Close response card handler
            $('#close-response').on('click', function() {
                $('#response-card').fadeOut();
            });
        });
        
        function validateMessage() {
            const messageText = $('#hl7_message').val().trim();
            
            if (!messageText) {
                showResponse('error', 'Validation Error', 'Please enter an HL7 message to validate.');
                return;
            }
            
            try {
                const parsedMessage = JSON.parse(messageText);
                
                // Basic structure validation
                const requiredFields = ['message_type', 'trigger_event', 'segments'];
                const missingFields = requiredFields.filter(field => !parsedMessage[field]);
                
                if (missingFields.length > 0) {
                    showResponse('error', 'Validation Failed', `Missing required fields: ${missingFields.join(', ')}`);
                    return;
                }
                
                // Check message type
                if (parsedMessage.message_type !== 'ADT') {
                    showResponse('error', 'Invalid Message Type', 'Expected ADT (Admission/Discharge/Transfer) message type.');
                    return;
                }
                
                // Check required segments
                const requiredSegments = ['MSH', 'PID', 'PV1'];
                const missingSegments = requiredSegments.filter(segment => !parsedMessage.segments[segment]);
                
                if (missingSegments.length > 0) {
                    showResponse('error', 'Missing Segments', `Missing required segments: ${missingSegments.join(', ')}`);
                    return;
                }
                
                showResponse('success', 'Validation Successful', 'The HL7 message structure is valid and ready for processing.');
                
            } catch (error) {
                showResponse('error', 'JSON Parse Error', 'Invalid JSON format. Please check your message syntax.');
            }
        }
        
        function processAdmission() {
            const messageText = $('#hl7_message').val().trim();
            
            if (!messageText) {
                showResponse('error', 'Input Required', 'Please enter an HL7 message to process.');
                return;
            }
            
            // Show loading overlay
            $('#form-overlay').show();
            
            // Disable form elements
            $('#hl7_message, #validate-btn, button[type="submit"]').prop('disabled', true);
            
            $.ajax({
                url: '{{ route("admin.integration.admission.process") }}',
                method: 'POST',
                data: {
                    hl7_message: messageText,
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        showAdmissionSuccess(response);
                    } else {
                        showResponse('error', 'Processing Failed', response.message || 'Unknown error occurred.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while processing the admission.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Validation error. Please check your input.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please check the logs and try again.';
                    }
                    
                    showResponse('error', 'Processing Error', errorMessage);
                },
                complete: function() {
                    // Hide loading overlay
                    $('#form-overlay').hide();
                    
                    // Re-enable form elements
                    $('#hl7_message, #validate-btn, button[type="submit"]').prop('disabled', false);
                }
            });
        }
        
        function showAdmissionSuccess(response) {
            let content = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> ${response.message}</h5>
                </div>
            `;
            
            if (response.patient_info) {
                content += `
                    <div class="patient-info-card">
                        <h6><i class="fas fa-user"></i> Patient Information</h6>
                        <div class="row">
                            <div class="col-md-4"><strong>Name:</strong> ${response.patient_info.name}</div>
                            <div class="col-md-4"><strong>MRN:</strong> ${response.patient_info.mrn}</div>
                            <div class="col-md-4"><strong>ID:</strong> ${response.patient_info.id}</div>
                        </div>
                    </div>
                `;
            }
            
            if (response.admission_info) {
                content += `
                    <div class="admission-info-card">
                        <h6><i class="fas fa-bed"></i> Admission Information</h6>
                        <div class="row">
                            <div class="col-md-3"><strong>Ward:</strong> ${response.admission_info.ward}</div>
                            <div class="col-md-3"><strong>Bed:</strong> ${response.admission_info.bed_number}</div>
                            <div class="col-md-6"><strong>Admission Date:</strong> ${response.admission_info.admission_date}</div>
                        </div>
                    </div>
                `;
                
                if (response.admission_info.alerts && response.admission_info.alerts.length > 0) {
                    content += `
                        <div class="alert-info-card">
                            <h6><i class="fas fa-exclamation-triangle"></i> Clinical Alerts</h6>
                            <ul class="mb-0">
                    `;
                    
                    response.admission_info.alerts.forEach(alert => {
                        content += `<li><strong>${alert.type}:</strong> `;
                        if (alert.description) {
                            content += `${alert.description}`;
                        } else if (alert.code) {
                            content += `${alert.code}`;
                        } else if (alert.level) {
                            content += `Level ${alert.level}`;
                        }
                        content += `</li>`;
                    });
                    
                    content += `
                            </ul>
                        </div>
                    `;
                }
            }
            
            showResponse('success', 'Admission Processed Successfully', content);
        }
        
        function showResponse(type, title, content) {
            // Set card class based on type
            const cardClass = type === 'success' ? 'card-success response-success' : 
                             type === 'error' ? 'card-danger response-error' : 
                             'card-warning response-warning';
            
            $('#response-card-container').attr('class', `card ${cardClass}`);
            
            // Set icon based on type
            const icon = type === 'success' ? 'fas fa-check-circle' : 
                        type === 'error' ? 'fas fa-exclamation-circle' : 
                        'fas fa-exclamation-triangle';
            
            $('#response-title').html(`<i class="${icon}"></i> ${title}`);
            $('#response-body').html(content);
            
            // Show the response card
            $('#response-card').fadeIn();
            
            // Scroll to response
            $('html, body').animate({
                scrollTop: $('#response-card').offset().top - 100
            }, 500);
        }
    </script>
@stop 