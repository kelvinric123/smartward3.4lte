@extends('adminlte::page')

@section('title', 'Food Menu Management')

@section('content_header')
    <h1>Food Menu Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Menu Items</h3>
            <div class="card-tools">
                <a href="{{ route('admin.food-menu.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Item
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Meal Type</th>
                            <th>Dietary Tags</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ Str::limit($item->description, 50) }}</td>
                                <td>{{ $item->meal_type }}</td>
                                <td>{{ $item->dietary_tags ?? 'None' }}</td>
                                <td>
                                    <span class="badge {{ $item->available ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->available ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.food-menu.edit', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.food-menu.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No menu items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $menuItems->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Food menu page loaded!');
    </script>
@stop 