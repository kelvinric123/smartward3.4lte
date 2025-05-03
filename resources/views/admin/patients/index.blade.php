@extends('adminlte::page')

@section('title', 'Patient List')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Patient List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Patient Management</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Patients</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.patients.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Patient
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form id="searchFilterForm" method="GET" action="{{ route('admin.patients.index') }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name, IC/passport, phone..." value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <select name="identity_type" id="identity_type" class="form-control">
                                                <option value="">All ID Types</option>
                                                <option value="ic" {{ request('identity_type') == 'ic' ? 'selected' : '' }}>MyKad (IC)</option>
                                                <option value="passport" {{ request('identity_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="">All Genders</option>
                                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <select name="age_range" id="age_range" class="form-control">
                                                <option value="">All Ages</option>
                                                <option value="0-18" {{ request('age_range') == '0-18' ? 'selected' : '' }}>Under 18</option>
                                                <option value="18-30" {{ request('age_range') == '18-30' ? 'selected' : '' }}>18-30</option>
                                                <option value="31-50" {{ request('age_range') == '31-50' ? 'selected' : '' }}>31-50</option>
                                                <option value="51-65" {{ request('age_range') == '51-65' ? 'selected' : '' }}>51-65</option>
                                                <option value="65+" {{ request('age_range') == '65+' ? 'selected' : '' }}>Above 65</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="btn-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter"></i> Filter
                                                </button>
                                                <a href="{{ route('admin.patients.index') }}" class="btn btn-default">
                                                    <i class="fas fa-sync"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="table-responsive p-0">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>MRN</th>
                                        <th>IC/Passport</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $patient)
                                        <tr>
                                            <td>{{ $patient->id }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ $patient->mrn ?? 'Not available' }}</td>
                                            <td>
                                                <span class="badge {{ $patient->identity_type == 'ic' ? 'badge-primary' : 'badge-secondary' }}">
                                                    {{ strtoupper($patient->identity_type) }}
                                                </span>
                                                {{ $patient->identity_number }}
                                            </td>
                                            <td>{{ $patient->age }}</td>
                                            <td>{{ ucfirst($patient->gender ?? 'Not specified') }}</td>
                                            <td>{{ $patient->phone ?? 'Not provided' }}</td>
                                            <td>
                                                <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this patient?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No patients found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $patients->appends(request()->except('page'))->links('vendor.pagination.bootstrap-4') }}
                        </div>
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
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 5000);
            
            // Auto-submit form when filter dropdowns change
            $('#identity_type, #gender, #age_range').change(function() {
                // Only auto-submit if we're not on mobile (to avoid accidental filtering)
                if (window.innerWidth > 768) {
                    $('#searchFilterForm').submit();
                }
            });
        });
    </script>
@stop 