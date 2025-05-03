@extends('adminlte::page')

@section('title', 'Beds')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Beds</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Bed Management</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bed List</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.beds.beds.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Bed
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form id="searchForm" method="GET" action="{{ route('admin.beds.beds.index') }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by bed number or ward..." value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <select name="ward_id" id="ward_id" class="form-control">
                                                <option value="">All Wards</option>
                                                @foreach ($wards as $ward)
                                                    <option value="{{ $ward->id }}" {{ request('ward_id') == $ward->id ? 'selected' : '' }}>
                                                        {{ $ward->name }} ({{ $ward->hospital->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <select name="status" id="status" class="form-control">
                                                <option value="">All Statuses</option>
                                                @foreach ($statuses as $key => $value)
                                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3 text-right">
                                            <div class="btn-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter"></i> Filter
                                                </button>
                                                <a href="{{ route('admin.beds.beds.index') }}" class="btn btn-default">
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
                                        <th>Bed Number</th>
                                        <th>Ward</th>
                                        <th>Status</th>
                                        <th>Consultant</th>
                                        <th>Nurse</th>
                                        <th>Patient</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($beds as $bed)
                                        <tr>
                                            <td>{{ $bed->id }}</td>
                                            <td>{{ $bed->bed_number }}</td>
                                            <td>{{ $bed->ward->name }} ({{ $bed->ward->hospital->name }})</td>
                                            <td>
                                                <span class="badge {{ $bed->status_badge_class }}">
                                                    {{ $bed->formatted_status }}
                                                </span>
                                            </td>
                                            <td>{{ $bed->consultant->name ?? 'Not Assigned' }}</td>
                                            <td>{{ $bed->nurse->name ?? 'Not Assigned' }}</td>
                                            <td>{{ $bed->patient->name ?? 'Not Assigned' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.beds.beds.show', $bed) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.beds.edit', $bed) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deleteConfirmation('{{ $bed->id }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $bed->id }}" 
                                                          action="{{ route('admin.beds.beds.destroy', $bed) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No beds found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $beds->links('vendor.pagination.bootstrap-4') }}
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
            $('#ward_id, #status').change(function() {
                // Only auto-submit if we're not on mobile (to avoid accidental filtering)
                if (window.innerWidth > 768) {
                    $('#searchForm').submit();
                }
            });
        });
        
        function deleteConfirmation(id) {
            if (confirm('Are you sure you want to delete this bed?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@stop 