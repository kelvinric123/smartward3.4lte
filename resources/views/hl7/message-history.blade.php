@extends('adminlte::page')

@section('title', 'HL7 Message History')

@section('content_header')
    <h1>
        <i class="fas fa-exchange-alt"></i> HL7 Message History
        <small class="text-muted">View and manage HL7 message processing status</small>
    </h1>
@stop

@section('content')
    @if(isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> {{ $error }}
        </div>
    @endif

    <!-- Statistics Dashboard -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $statistics['total_messages'] ?? 0 }}</h3>
                    <p>Total Messages</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $statistics['successful_messages'] ?? 0 }}</h3>
                    <p>Successful</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $statistics['failed_messages'] ?? 0 }}</h3>
                    <p>Failed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $statistics['pending_messages'] ?? 0 }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Performance Metrics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-blue"><i class="fas fa-stopwatch"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avg Processing Time</span>
                                    <span class="info-box-number">
                                        {{ isset($performanceMetrics['average_processing_time']) ? number_format($performanceMetrics['average_processing_time'], 2) . ' ms' : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Success Rate</span>
                                    <span class="info-box-number">
                                        {{ isset($statistics['total_messages']) && $statistics['total_messages'] > 0 ? number_format(($statistics['successful_messages'] / $statistics['total_messages']) * 100, 1) . '%' : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fas fa-user-md"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Processed Messages</span>
                                    <span class="info-box-number">{{ $performanceMetrics['total_processed'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Recent Errors
                    </h3>
                </div>
                <div class="card-body">
                    @if($recentErrors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentErrors as $error)
                                        <tr>
                                            <td>{{ $error->received_at->format('H:i') }}</td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($error->error_message, 30) }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No recent errors</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Message History -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Message History
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" onclick="testMessage()">
                    <i class="fas fa-vial"></i> Test Message
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="showManualTestModal()">
                    <i class="fas fa-edit"></i> Manual Test
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="refreshPage()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('hl7.message-history') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="status" class="sr-only">Status</label>
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="parsed" {{ request('status') == 'parsed' ? 'selected' : '' }}>Parsed</option>
                                <option value="mapped" {{ request('status') == 'mapped' ? 'selected' : '' }}>Mapped</option>
                                <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <label for="patient_mrn" class="sr-only">Patient MRN</label>
                            <input type="text" name="patient_mrn" class="form-control form-control-sm" placeholder="Patient MRN" value="{{ request('patient_mrn') }}">
                        </div>
                        <div class="form-group mr-2">
                            <label for="date_from" class="sr-only">Date From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group mr-2">
                            <label for="date_to" class="sr-only">Date To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('hl7.message-history') }}" class="btn btn-sm btn-secondary ml-2">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- Message Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Message ID</th>
                            <th>Status</th>
                            <th>Patient</th>
                            <th>MRN</th>
                            <th>Received</th>
                            <th>Processing Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                            <tr>
                                <td>
                                    <code>{{ $message->message_id }}</code>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $message->status_badge_color }}">
                                        {{ $message->status_display }}
                                    </span>
                                </td>
                                <td>
                                    {{ $message->patient_name ?? 'Unknown' }}
                                </td>
                                <td>
                                    {{ $message->patient_mrn ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $message->received_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td>
                                    {{ $message->processing_time_display }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('hl7.message-details', $message->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($message->status === 'failed')
                                            <button type="button" class="btn btn-sm btn-warning" onclick="retryMessage({{ $message->id }})">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        @endif
                                        @if($message->admission_id)
                                            <a href="{{ route('admin.patients.show', $message->admission->patient_id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    No messages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($messages->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $messages->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Manual Test Modal -->
    <div class="modal fade" id="manualTestModal" tabindex="-1" role="dialog" aria-labelledby="manualTestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualTestModalLabel">
                        <i class="fas fa-edit"></i> Manual HL7 Message Test
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="testTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="raw-tab" data-toggle="tab" href="#raw" role="tab" aria-controls="raw" aria-selected="true">
                                <i class="fas fa-code"></i> Raw HL7 Message
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="form-tab" data-toggle="tab" href="#form" role="tab" aria-controls="form" aria-selected="false">
                                <i class="fas fa-form"></i> Form Builder
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="testTabsContent">
                        <!-- Raw Message Tab -->
                        <div class="tab-pane fade show active" id="raw" role="tabpanel" aria-labelledby="raw-tab">
                            <div class="mt-3">
                                <label for="rawMessage">Raw HL7 Message:</label>
                                <textarea class="form-control" id="rawMessage" rows="8" placeholder="Enter your raw HL7 message here...
Example:
MSH|^~\&|SENDING_APP|SENDING_FACILITY|RECEIVING_APP|RECEIVING_FACILITY|20250712172433||ADT^A01|CTRL1752312273|P|2.5
EVN|A01|20250712172433|||DOC1^SMITH^JOHN^||20250712172433
PID|1||MRN9118^^^MR^MR||DOE^JOHN^MIDDLE||19800101|M|||123 MAIN ST^^CITY^STATE^12345||555-1234||||
PV1|1|I|WARD1^BED1^WARD1^FACILITY|||DOC1^SMITH^JOHN^|MED||||||||||||||||||||||||||||||||||||20250712172433
AL1|1||DRUG^PENICILLIN|MO|RASH"></textarea>
                            </div>
                        </div>
                        
                        <!-- Form Builder Tab -->
                        <div class="tab-pane fade" id="form" role="tabpanel" aria-labelledby="form-tab">
                            <div class="mt-3">
                                <form id="hl7Form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6><i class="fas fa-user"></i> Patient Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="patientMRN">MRN *</label>
                                                        <input type="text" class="form-control" id="patientMRN" placeholder="MRN123456" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="firstName">First Name *</label>
                                                        <input type="text" class="form-control" id="firstName" placeholder="John" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="lastName">Last Name *</label>
                                                        <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="middleName">Middle Name</label>
                                                        <input type="text" class="form-control" id="middleName" placeholder="Middle">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="gender">Gender *</label>
                                                        <select class="form-control" id="gender" required>
                                                            <option value="">Select Gender</option>
                                                            <option value="M">Male</option>
                                                            <option value="F">Female</option>
                                                            <option value="O">Other</option>
                                                            <option value="U">Unknown</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dateOfBirth">Date of Birth *</label>
                                                        <input type="date" class="form-control" id="dateOfBirth" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="phoneNumber">Phone Number</label>
                                                        <input type="text" class="form-control" id="phoneNumber" placeholder="555-1234">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="address">Address</label>
                                                        <input type="text" class="form-control" id="address" placeholder="123 Main St">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6><i class="fas fa-bed"></i> Admission Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="wardName">Ward Name *</label>
                                                        <input type="text" class="form-control" id="wardName" placeholder="WARD1" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bedNumber">Bed Number *</label>
                                                        <input type="text" class="form-control" id="bedNumber" placeholder="BED1" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="facilityName">Facility Name</label>
                                                        <input type="text" class="form-control" id="facilityName" placeholder="FACILITY">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="doctorId">Doctor ID</label>
                                                        <input type="text" class="form-control" id="doctorId" placeholder="DOC1">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="doctorName">Doctor Name</label>
                                                        <input type="text" class="form-control" id="doctorName" placeholder="SMITH^JOHN">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="admissionType">Admission Type</label>
                                                        <select class="form-control" id="admissionType">
                                                            <option value="I">Inpatient</option>
                                                            <option value="O">Outpatient</option>
                                                            <option value="E">Emergency</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="admissionDate">Admission Date</label>
                                                        <input type="datetime-local" class="form-control" id="admissionDate">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6><i class="fas fa-exclamation-triangle"></i> Allergy Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="allergyType">Allergy Type</label>
                                                        <input type="text" class="form-control" id="allergyType" placeholder="DRUG^PENICILLIN">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="allergyReaction">Reaction</label>
                                                        <input type="text" class="form-control" id="allergyReaction" placeholder="RASH">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="allergySeverity">Severity</label>
                                                        <select class="form-control" id="allergySeverity">
                                                            <option value="">Select Severity</option>
                                                            <option value="MI">Mild</option>
                                                            <option value="MO">Moderate</option>
                                                            <option value="SV">Severe</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-info" onclick="generateHL7Message()">
                                            <i class="fas fa-cogs"></i> Generate HL7 Message
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="sendManualTest()">
                        <i class="fas fa-paper-plane"></i> Send Test Message
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box {
            margin-bottom: 20px;
        }
        .info-box {
            margin-bottom: 15px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .badge {
            font-size: 0.8em;
        }
        
        /* Manual Test Modal Styles */
        #manualTestModal .modal-dialog {
            max-width: 900px;
        }
        
        #manualTestModal .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        
        #manualTestModal .card-header h6 {
            margin: 0;
            color: #495057;
        }
        
        #manualTestModal textarea {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            resize: vertical;
        }
        
        #manualTestModal .form-group label {
            font-weight: 600;
            color: #495057;
        }
        
        #manualTestModal .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }
        
        #manualTestModal .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        
        #manualTestModal .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 1rem;
            background-color: #fff;
        }
        
        #manualTestModal .card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        #manualTestModal .card-body {
            padding: 1rem;
        }
        
        #manualTestModal .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@stop

@section('js')
    <script>
        function refreshPage() {
            window.location.reload();
        }

        function testMessage() {
            if (confirm('This will send a test HL7 message to the system. Continue?')) {
                fetch('{{ route('hl7.test-message') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Test message processed successfully!');
                        window.location.reload();
                    } else {
                        alert('Test message failed: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing the test message.');
                });
            }
        }

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

        // Auto-refresh every 30 seconds
        setInterval(function() {
            // Only refresh if user is not actively interacting
            if (document.hidden === false) {
                // Update statistics without full page reload
                fetch('{{ route('hl7.dashboard-data') }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update statistics counters
                            updateStatistics(data.data.statistics);
                        }
                    })
                    .catch(error => console.error('Auto-refresh error:', error));
            }
        }, 30000);

        function updateStatistics(stats) {
            // Update the statistics boxes
            document.querySelector('.bg-info .inner h3').textContent = stats.total_messages || 0;
            document.querySelector('.bg-success .inner h3').textContent = stats.successful_messages || 0;
            document.querySelector('.bg-danger .inner h3').textContent = stats.failed_messages || 0;
            document.querySelector('.bg-warning .inner h3').textContent = stats.pending_messages || 0;
        }

        function showManualTestModal() {
            // Set default admission date to current date/time
            const now = new Date();
            const localDateTime = now.toISOString().slice(0, 16);
            document.getElementById('admissionDate').value = localDateTime;
            
            // Show the modal
            $('#manualTestModal').modal('show');
        }

        function generateHL7Message() {
            // Get form data
            const mrn = document.getElementById('patientMRN').value;
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const middleName = document.getElementById('middleName').value;
            const gender = document.getElementById('gender').value;
            const dateOfBirth = document.getElementById('dateOfBirth').value;
            const phoneNumber = document.getElementById('phoneNumber').value;
            const address = document.getElementById('address').value;
            const wardName = document.getElementById('wardName').value;
            const bedNumber = document.getElementById('bedNumber').value;
            const facilityName = document.getElementById('facilityName').value;
            const doctorId = document.getElementById('doctorId').value;
            const doctorName = document.getElementById('doctorName').value;
            const admissionType = document.getElementById('admissionType').value;
            const admissionDate = document.getElementById('admissionDate').value;
            const allergyType = document.getElementById('allergyType').value;
            const allergyReaction = document.getElementById('allergyReaction').value;
            const allergySeverity = document.getElementById('allergySeverity').value;

            // Validate required fields
            if (!mrn || !firstName || !lastName || !gender || !dateOfBirth || !wardName || !bedNumber) {
                alert('Please fill in all required fields (marked with *)');
                return;
            }

            // Format date of birth (YYYYMMDD)
            const dobFormatted = dateOfBirth.replace(/-/g, '');
            
            // Format admission date (YYYYMMDDHHMMSS)
            let admissionDateTime = '';
            if (admissionDate) {
                const admissionDateObj = new Date(admissionDate);
                admissionDateTime = admissionDateObj.toISOString().replace(/[-T:]/g, '').slice(0, 14);
            } else {
                admissionDateTime = new Date().toISOString().replace(/[-T:]/g, '').slice(0, 14);
            }

            // Generate message control ID
            const messageControlId = 'CTRL' + Date.now();

            // Build HL7 message
            let hl7Message = '';
            
            // MSH segment
            hl7Message += `MSH|^~\\&|SENDING_APP|SENDING_FACILITY|RECEIVING_APP|RECEIVING_FACILITY|${admissionDateTime}||ADT^A01|${messageControlId}|P|2.5\n`;
            
            // EVN segment
            hl7Message += `EVN|A01|${admissionDateTime}|||${doctorId}^${doctorName}^||${admissionDateTime}\n`;
            
            // PID segment
            const patientName = `${lastName}^${firstName}^${middleName}`;
            hl7Message += `PID|1||${mrn}^^^MR^MR||${patientName}||${dobFormatted}|${gender}|||${address}||${phoneNumber}||||\n`;
            
            // PV1 segment
            const locationInfo = `${wardName}^${bedNumber}^${wardName}^${facilityName}`;
            hl7Message += `PV1|1|${admissionType}|${locationInfo}|||${doctorId}^${doctorName}^|MED||||||||||||||||||||||||||||||||||||${admissionDateTime}\n`;
            
            // AL1 segment (if allergy info provided)
            if (allergyType && allergyReaction && allergySeverity) {
                hl7Message += `AL1|1||${allergyType}|${allergySeverity}|${allergyReaction}`;
            }

            // Set the generated message in the raw message textarea
            document.getElementById('rawMessage').value = hl7Message;
            
            // Switch to raw message tab
            $('#raw-tab').tab('show');
            
            alert('HL7 message generated successfully! You can now send it or modify it as needed.');
        }

        function sendManualTest() {
            const rawMessage = document.getElementById('rawMessage').value.trim();
            
            if (!rawMessage) {
                alert('Please enter a raw HL7 message or use the form builder to generate one.');
                return;
            }

            // Validate HL7 message format (basic check)
            if (!rawMessage.startsWith('MSH|')) {
                alert('Invalid HL7 message format. Message must start with MSH segment.');
                return;
            }

            // Show loading state
            const sendButton = document.querySelector('#manualTestModal .btn-primary');
            const originalText = sendButton.innerHTML;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            sendButton.disabled = true;

            // Send the message
            fetch('{{ route('hl7.manual-test') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: rawMessage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Manual test message processed successfully!\nMessage ID: ' + data.message_id);
                    $('#manualTestModal').modal('hide');
                    window.location.reload();
                } else {
                    alert('Manual test failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the manual test message.');
            })
            .finally(() => {
                // Reset button state
                sendButton.innerHTML = originalText;
                sendButton.disabled = false;
            });
        }
    </script>
@stop 