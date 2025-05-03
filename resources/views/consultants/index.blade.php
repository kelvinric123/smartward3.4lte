@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Consultants</h5>
                    <a href="{{ route('consultants.create') }}" class="btn btn-primary">Add New Consultant</a>
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
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Specialty</th>
                                    <th>Hospital</th>
                                    <th>Qualification</th>
                                    <th>Experience</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultants as $consultant)
                                    <tr>
                                        <td>
                                            @if($consultant->photo)
                                                <img src="{{ Storage::url($consultant->photo) }}" alt="{{ $consultant->name }}" class="rounded-circle" width="50" height="50">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
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
                                            <span class="badge {{ $consultant->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $consultant->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('consultants.edit', $consultant) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('consultants.destroy', $consultant) }}" method="POST" class="d-inline">
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
                        {{ $consultants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 