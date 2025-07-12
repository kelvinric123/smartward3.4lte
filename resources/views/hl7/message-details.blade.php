@extends('adminlte::page')

@section('title', 'HL7 Message Details')

@section('content_header')
    <h1>
        <i class="fas fa-file-medical"></i> HL7 Message Details
        <small class="text-muted">{{ $message->message_id }}</small>
    </h1>
@stop

@section('content')
    <!-- Message Overview -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Message Overview
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $message->status_badge_color }} badge-lg">
                            {{ $message->status_display }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Message ID:</dt>
                                <dd class="col-sm-7"><code>{{ $message->message_id }}</code></dd>
                                
                                <dt class="col-sm-5">Control ID:</dt>
                                <dd class="col-sm-7">{{ $message->message_control_id ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-5">Message Type:</dt>
                                <dd class="col-sm-7">{{ $message->message_type }}</dd>
                                
                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-{{ $message->status_badge_color }}">
                                        {{ $message->status_display }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Received:</dt>
                                <dd class="col-sm-7">{{ $message->received_at->format('Y-m-d H:i:s') }}</dd>
                                
                                <dt class="col-sm-5">Processed:</dt>
                                <dd class="col-sm-7">{{ $message->processed_at ? $message->processed_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>
                                
                                <dt class="col-sm-5">Completed:</dt>
                                <dd class="col-sm-7">{{ $message->completed_at ? $message->completed_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>
                                
                                <dt class="col-sm-5">Processing Time:</dt>
                                <dd class="col-sm-7">{{ $message->processing_time_display }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if($message->error_message)
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Error Message</h5>
                            <p>{{ $message->error_message }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($message->status === 'failed')
                            <button type="button" class="btn btn-warning btn-block" onclick="retryMessage({{ $message->id }})">
                                <i class="fas fa-redo"></i> Retry Processing
                            </button>
                        @endif
                        
                        @if($message->admission_id)
                            <a href="{{ route('admin.patients.show', $message->admission->patient_id) }}" class="btn btn-success btn-block">
                                <i class="fas fa-user"></i> View Patient
                            </a>
                        @endif

                        <a href="{{ route('hl7.message-history') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Back to History
                        </a>
                    </div>
                </div>
            </div>

            @if($message->admission_id)
                <!-- Admission Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bed"></i> Admission Info
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-6">Patient:</dt>
                            <dd class="col-sm-6">{{ $message->admission->patient->full_name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-6">MRN:</dt>
                            <dd class="col-sm-6">{{ $message->admission->patient->mrn ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-6">Ward:</dt>
                            <dd class="col-sm-6">{{ $message->admission->ward->name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-6">Bed:</dt>
                            <dd class="col-sm-6">{{ $message->admission->bed->bed_number ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-6">Admission Date:</dt>
                            <dd class="col-sm-6">{{ $message->admission->admission_date->format('Y-m-d H:i') ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Patient Demographics -->
    @if($message->parsed_data && isset($message->parsed_data['patient']))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-md"></i> Patient Demographics
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <dl class="row">
                            <dt class="col-sm-5">Full Name:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['full_name'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">First Name:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['first_name'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Middle Name:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['middle_name'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Last Name:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['last_name'] ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl class="row">
                            <dt class="col-sm-5">MRN:</dt>
                            <dd class="col-sm-7"><code>{{ $message->parsed_data['patient']['mrn'] ?? 'N/A' }}</code></dd>
                            
                            <dt class="col-sm-5">Date of Birth:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['date_of_birth'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Gender:</dt>
                            <dd class="col-sm-7">
                                @if(isset($message->parsed_data['patient']['gender']))
                                    <span class="badge badge-{{ $message->parsed_data['patient']['gender'] === 'M' ? 'primary' : 'pink' }}">
                                        {{ $message->parsed_data['patient']['gender'] === 'M' ? 'Male' : 'Female' }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </dd>
                            
                            <dt class="col-sm-5">Patient ID:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['patient_id'] ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl class="row">
                            <dt class="col-sm-5">Phone:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['phone'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Address:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['address'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">City:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['city'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">State:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['state'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Postal Code:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['patient']['postal_code'] ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Admission Details -->
    @if($message->parsed_data && isset($message->parsed_data['admission']))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hospital"></i> Admission Details
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Ward:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['ward'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Room:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['room'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Bed:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['bed'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Facility:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['facility'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Patient Class:</dt>
                            <dd class="col-sm-7">
                                @if(isset($message->parsed_data['admission']['patient_class']))
                                    <span class="badge badge-info">
                                        {{ $message->parsed_data['admission']['patient_class'] === 'I' ? 'Inpatient' : $message->parsed_data['admission']['patient_class'] }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Admission Date:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['recorded_date'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Attending Doctor:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['attending_doctor_name'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Attending Doctor ID:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['attending_doctor_id'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Admitting Doctor:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['admitting_doctor_name'] ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-5">Operator:</dt>
                            <dd class="col-sm-7">{{ $message->parsed_data['admission']['operator_name'] ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Allergies -->
    @if($message->parsed_data && isset($message->parsed_data['patient']['allergies']) && !empty($message->parsed_data['patient']['allergies']))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Allergies
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($message->parsed_data['patient']['allergies'] as $index => $allergy)
                        <div class="col-md-6">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">Allergy #{{ $index + 1 }}</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Type:</dt>
                                        <dd class="col-sm-8">{{ $allergy['type'] ?? 'N/A' }}</dd>
                                        
                                        <dt class="col-sm-4">Allergen:</dt>
                                        <dd class="col-sm-8">{{ $allergy['allergen'] ?? 'N/A' }}</dd>
                                        
                                        <dt class="col-sm-4">Severity:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge badge-warning">{{ $allergy['severity'] ?? 'N/A' }}</span>
                                        </dd>
                                        
                                        @if(!empty($allergy['description']))
                                            <dt class="col-sm-4">Description:</dt>
                                            <dd class="col-sm-8">{{ $allergy['description'] }}</dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- HL7 Segments Breakdown -->
    @if(!empty($processedSegments))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list-alt"></i> HL7 Segments Breakdown
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="accordion" id="segmentsAccordion">
                    @foreach($processedSegments as $index => $segment)
                        <div class="card">
                            <div class="card-header" id="heading{{ $index }}">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                        <i class="fas fa-{{ $segment['icon'] }}"></i>
                                        {{ $segment['type'] }} - {{ $segment['description'] }}
                                        <span class="badge badge-secondary">{{ $segment['field_count'] }} fields</span>
                                    </button>
                                </h5>
                            </div>
                            <div id="collapse{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-parent="#segmentsAccordion">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Field #</th>
                                                    <th>Description</th>
                                                    <th>Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($segment['processed_fields'] as $field)
                                                    <tr>
                                                        <td>{{ $field['label'] }}</td>
                                                        <td>{{ $field['description'] }}</td>
                                                        <td>
                                                            @if($field['is_empty'])
                                                                <span class="text-muted">Empty</span>
                                                            @else
                                                                <code>{{ $field['value'] }}</code>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Raw Message -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-code"></i> Raw HL7 Message
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <pre class="bg-light p-3" style="font-size: 12px; max-height: 400px; overflow-y: auto;"><code>{{ $message->raw_message }}</code></pre>
        </div>
    </div>

    <!-- Parsed Data JSON -->
    @if($message->parsed_data)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Parsed Data (JSON)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3" style="font-size: 12px; max-height: 400px; overflow-y: auto;"><code>{{ json_encode($message->parsed_data, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>
    @endif

    <!-- Mapped Data -->
    @if($message->mapped_data)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map"></i> Mapped Data
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3" style="font-size: 12px; max-height: 400px; overflow-y: auto;"><code>{{ json_encode($message->mapped_data, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>
    @endif

    <!-- Headers -->
    @if($message->headers)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-server"></i> HTTP Headers
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Header</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($message->headers as $header => $value)
                                <tr>
                                    <td><code>{{ $header }}</code></td>
                                    <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }
        .badge-pink {
            background-color: #e83e8c;
            color: white;
        }
        .card-body pre {
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
        }
        .d-grid {
            display: grid;
        }
        .gap-2 {
            gap: 0.5rem;
        }
        .btn-block {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        dl.row {
            margin-bottom: 1rem;
        }
        dt {
            font-weight: 600;
            color: #495057;
        }
        dd {
            margin-bottom: 0.5rem;
        }
        .alert {
            margin-top: 1rem;
        }
        .accordion .card {
            margin-bottom: 0;
        }
        .accordion .card-header {
            padding: 0.5rem 1rem;
        }
        .accordion .btn-link {
            text-decoration: none;
            color: #495057;
            font-weight: 500;
        }
        .accordion .btn-link:hover {
            text-decoration: none;
        }
        .table-responsive {
            border-radius: 0.25rem;
        }
        .card-outline {
            border: 1px solid;
        }
        .card-outline.card-warning {
            border-color: #ffc107;
        }
    </style>
@stop

@section('js')
    <script>
        function retryMessage(messageId) {
            if (confirm('Are you sure you want to retry processing this message?')) {
                fetch(`{{ route('hl7.retry-message', '') }}/${messageId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Message retried successfully!');
                        window.location.reload();
                    } else {
                        alert('Retry failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while retrying the message.');
                });
            }
        }

        // Auto-refresh status every 10 seconds for pending messages
        @if(in_array($message->status, ['received', 'parsed', 'mapped']))
            setInterval(function() {
                window.location.reload();
            }, 10000);
        @endif

        // Initialize tooltips
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop 