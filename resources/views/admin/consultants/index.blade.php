@extends('adminlte::page')

@section('title', 'Consultants')

@section('content_header')
    <h1>Consultants</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3 class="card-title">List of Consultants</h3>
                <a href="{{ route('admin.consultants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Consultant
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
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Specialty</th>
                            <th>Hospital</th>
                            <th>Qualification</th>
                            <th>Experience</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultants as $consultant)
                            <tr>
                                <td>
                                    @if($consultant->photo)
                                        <img src="{{ Storage::url($consultant->photo) }}" alt="{{ $consultant->name }}" class="img-circle elevation-2" width="50" height="50">
                                    @else
                                        <div class="img-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            {{ substr($consultant->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $consultant->name }}</td>
                                <td>{{ $consultant->specialty->name }}</td>
                                <td>{{ $consultant->specialty->hospital->name }}</td>
                                <td>{{ $consultant->qualification }}</td>
                                <td>{{ $consultant->experience_years }} years</td>
                                <td>
                                    @if ($consultant->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.consultants.edit', $consultant) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteConfirmation('{{ $consultant->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $consultant->id }}" 
                                              action="{{ route('admin.consultants.destroy', $consultant) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No consultants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $consultants->links('vendor.pagination.bootstrap-4') }}
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
            if (confirm('Are you sure you want to delete this consultant?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@stop 