@extends('adminlte::page')

@section('title', 'Admission History')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Admission & Discharge History</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admission History</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i> Patient Admission & Discharge Records
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> 
                            Admission history records are permanent and cannot be deleted to maintain a complete patient history.
                        </div>
                        
                        <!-- Search and Filters -->
                        <form action="{{ route('admin.admission.history') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search Patient</label>
                                        <input type="text" name="search" class="form-control" placeholder="Name, MRN, ID" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        @if($discharges->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Discharge Date</th>
                                            <th>Admission Date</th>
                                            <th>Stay Duration</th>
                                            <th>Ward</th>
                                            <th>Bed</th>
                                            <th>Discharge Type</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($discharges as $discharge)
                                            <tr>
                                                <td>
                                                    @if($discharge->patient)
                                                        <a href="{{ route('admin.patients.show', $discharge->patient_id) }}">
                                                            {{ $discharge->patient->name }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $discharge->patient->mrn ? 'MRN: ' . $discharge->patient->mrn : '' }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>{{ $discharge->formatted_discharge_date }}</td>
                                                <td>
                                                    @if($discharge->admission)
                                                        {{ $discharge->admission->formatted_admission_date }}
                                                    @else
                                                        @php
                                                            // Fall back to estimate if no admission record found
                                                            $admissionDate = \Carbon\Carbon::parse($discharge->discharge_date)
                                                                ->setTimezone('Asia/Kuala_Lumpur')
                                                                ->subDays(5);
                                                            echo $admissionDate->format('d M Y, h:i A');
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($discharge->admission)
                                                        @php
                                                            $admissionDate = $discharge->admission->admission_date;
                                                            $dischargeDate = $discharge->discharge_date;
                                                            $diff = $admissionDate->diff($dischargeDate);
                                                            
                                                            $days = $diff->days;
                                                            $hours = $diff->h;
                                                            
                                                            echo $days . ' days, ' . $hours . ' hours';
                                                        @endphp
                                                    @else
                                                        @php
                                                            // Fallback to estimate 
                                                            echo "5 days";
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($discharge->ward)
                                                        {{ $discharge->ward->name }}
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>{{ $discharge->bed_number ?: 'Unknown' }}</td>
                                                <td>
                                                    @if($discharge->discharge_type == 'routine' || $discharge->discharge_type == 'regular')
                                                        <span class="badge badge-success">Routine</span>
                                                    @elseif($discharge->discharge_type == 'against_medical_advice')
                                                        <span class="badge badge-warning">Against Medical Advice</span>
                                                    @elseif($discharge->discharge_type == 'transfer')
                                                        <span class="badge badge-info">Transfer</span>
                                                    @elseif($discharge->discharge_type == 'deceased')
                                                        <span class="badge badge-danger">Deceased</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($discharge->discharge_type) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($discharge->discharge_notes))
                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#notesModal-{{ $discharge->id }}">
                                                            <i class="fas fa-sticky-note"></i> Notes
                                                        </button>
                                                        
                                                        <!-- Notes Modal -->
                                                        <div class="modal fade" id="notesModal-{{ $discharge->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Discharge Notes</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        {{ $discharge->discharge_notes }}
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No notes</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                {{ $discharges->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                <h4>No records found</h4>
                                <p class="text-muted">
                                    @if(request()->has('search') || request()->has('date_from') || request()->has('date_to'))
                                        No admission records match your search criteria.
                                    @else
                                        There are no patient admission records in the system.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        /* Additional styling for badges */
        .badge-success { background-color: #28a745; }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
    </style>
@stop 