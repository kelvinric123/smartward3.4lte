@extends('adminlte::page')

@section('title', $ward->name . ' Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $ward->name }}</h1>
                <span class="badge badge-secondary">{{ $ward->specialty->name }} Ward</span>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.beds.wards.index') }}">Wards</a></li>
                    <li class="breadcrumb-item active">Ward Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Bed Grid -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Beds Layout</h3>
                            <div class="btn-group">
                                <a href="{{ route('admin.beds.wards.show', $ward) }}" class="btn btn-default">
                                    <i class="fas fa-info-circle"></i> Ward Details
                                </a>
                                <a href="{{ route('admin.beds.beds.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Bed
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($ward->beds as $bed)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card border-{{ $bed->status === 'available' ? 'success' : ($bed->status === 'occupied' ? 'danger' : ($bed->status === 'reserved' ? 'warning' : 'secondary')) }} mb-0">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center p-2">
                                            <h5 class="m-0">Bed {{ $bed->bed_number }} 
                                                @if($bed->status === 'occupied')
                                                    <span class="badge badge-success">1</span>
                                                @endif
                                            </h5>
                                            <span>
                                                MRN: {{ $bed->patient ? ($bed->patient->mrn ?: 'Not available') : 'Not assigned' }}
                                            </span>
                                        </div>
                                        <div class="card-body p-3">
                                            @if($bed->consultant)
                                                <p class="mb-1">
                                                    <i class="fas fa-user-md"></i> {{ $bed->consultant->name }}
                                                </p>
                                            @else
                                                <p class="mb-1">
                                                    <i class="fas fa-user-md"></i> Not assigned
                                                </p>
                                            @endif
                                            
                                            @if($bed->status == 'occupied' && $bed->patient)
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-clock"></i> 
                                                    @php
                                                        // For demo, just show random days
                                                        $days = $bed->id * 11 % 120;
                                                        echo $days . ' days';
                                                    @endphp
                                                </p>
                                                
                                                <div class="btn-group btn-group-sm w-100 mt-2">
                                                    <a href="{{ route('admin.beds.beds.show', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-heart"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.beds.show', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-list"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.beds.show', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    @if($bed->id % 7 == 0)
                                                        <a href="#" class="btn btn-outline-secondary text-pink">
                                                            <i class="fas fa-heart"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-secondary text-info">
                                                            <i class="fas fa-info-circle"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-outline-secondary text-warning">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @elseif($bed->status == 'available')
                                                <p class="mb-1">
                                                    <i class="fas fa-bed"></i> No Patient
                                                </p>
                                                <div class="mt-2">
                                                    <a href="{{ route('admin.beds.wards.admit', ['ward' => $ward, 'bedId' => $bed->id]) }}" class="btn btn-success btn-block">
                                                        <i class="fas fa-user-plus"></i> Admit Patient
                                                    </a>
                                                </div>
                                            @else
                                                <p class="mb-1">
                                                    <i class="fas fa-bed"></i> No Patient
                                                </p>
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-clock"></i> 
                                                    @php
                                                        // For demo, just show random days
                                                        $days = $bed->id * 11 % 120;
                                                        echo $days . ' days';
                                                    @endphp
                                                </p>
                                                <div class="btn-group btn-group-sm w-100 mt-2">
                                                    <a href="{{ route('admin.beds.beds.show', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.beds.edit', $bed) }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">
                                    <p>No beds available in this ward. <a href="{{ route('admin.beds.beds.create') }}">Add beds</a>.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Footer with Stats -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">AVAILABLE BEDS</div>
                                <div class="h5 mb-0 text-success">
                                    <i class="fas fa-bed"></i> {{ $availableBeds }}
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">NURSES ON DUTY</div>
                                <div class="h5 mb-0 text-primary">
                                    <i class="fas fa-user-nurse"></i> {{ $nursesOnDuty }}
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">PATIENTS</div>
                                <div class="h5 mb-0 text-warning">
                                    <i class="fas fa-user-injured"></i> {{ $occupiedBeds }}
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 text-center border-right">
                                <div class="text-muted mb-1">NURSE-PATIENT RATIO</div>
                                <div class="h5 mb-0 text-info">
                                    <i class="fas fa-balance-scale"></i> {{ $nursePatientRatio }}:1
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-8 col-12 text-center">
                                <div class="text-muted mb-1">OCCUPANCY RATE</div>
                                <div class="h5 mb-0 text-danger">
                                    <i class="fas fa-chart-pie"></i> {{ $occupancyRate }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a href="{{ route('admin.beds.wards.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to Wards
                    </a>
                    <a href="{{ route('admin.beds.beds.index') }}" class="btn btn-info">
                        <i class="fas fa-list"></i> View All Beds
                    </a>
                    <button class="btn btn-success">
                        <i class="fas fa-print"></i> Print Ward Report
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .text-pink {
            color: #e83e8c !important;
        }
        
        .border-right {
            border-right: 1px solid #444 !important;
        }
        
        @media (max-width: 768px) {
            .border-right {
                border-right: none !important;
                border-bottom: 1px solid #444 !important;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // You can add JavaScript for dynamic functions here if needed
        });
    </script>
@stop 