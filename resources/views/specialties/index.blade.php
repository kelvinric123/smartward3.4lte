@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Specialties</h5>
                    <a href="{{ route('specialties.create') }}" class="btn btn-primary">Add New Specialty</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Hospital</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($specialties as $specialty)
                                    <tr>
                                        <td>{{ $specialty->name }}</td>
                                        <td>{{ $specialty->hospital->name }}</td>
                                        <td>{{ Str::limit($specialty->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ $specialty->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $specialty->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('specialties.edit', $specialty) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('specialties.destroy', $specialty) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $specialties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 