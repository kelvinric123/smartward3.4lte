@extends('adminlte::page')

@section('title', 'Specialties')

@section('content_header')
    <h1>Specialties</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">List of Specialties</h3>
                <a href="{{ route('admin.specialties.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Specialty
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Hospital</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specialties as $specialty)
                            <tr>
                                <td>{{ $specialty->name }}</td>
                                <td>{{ $specialty->hospital->name }}</td>
                                <td>{{ Str::limit($specialty->description, 50) }}</td>
                                <td>
                                    @if ($specialty->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.specialties.show', $specialty) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteConfirmation('{{ $specialty->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $specialty->id }}" 
                                              action="{{ route('admin.specialties.destroy', $specialty) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No specialties found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $specialties->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function deleteConfirmation(id) {
            if (confirm('Are you sure you want to delete this specialty?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@stop 