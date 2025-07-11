@extends('adminlte::page')

@section('title', 'Wards')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Wards</h1>
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
                        <h3 class="card-title">Ward List</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.beds.wards.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Ward
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form id="searchForm" method="GET" action="{{ route('admin.beds.wards.index') }}">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by ward name..." value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{ route('admin.beds.wards.index') }}" class="btn btn-default">
                                                <i class="fas fa-sync"></i> Reset
                                            </a>
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
                                        <th>Hospital</th>
                                        <th>Specialty</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($wards as $ward)
                                        <tr>
                                            <td>{{ $ward->id }}</td>
                                            <td>{{ $ward->name }}</td>
                                            <td>{{ $ward->hospital->name }}</td>
                                            <td>{{ $ward->specialty->name }}</td>
                                            <td>{{ $ward->capacity }}</td>
                                            <td>
                                                @if ($ward->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.beds.wards.dashboard', $ward) }}" class="btn btn-primary btn-sm" title="View Dashboard">
                                                        <i class="fas fa-th"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.wards.show', $ward) }}" class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.beds.wards.edit', $ward) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="deleteConfirmation('{{ $ward->id }}')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $ward->id }}" 
                                                          action="{{ route('admin.beds.wards.destroy', $ward) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No wards found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $wards->links('vendor.pagination.bootstrap-4') }}
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
        });
        
        function deleteConfirmation(id) {
            if (confirm('Are you sure you want to delete this ward?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@stop 