@extends('adminlte::page')

@section('title', 'Menu Item Details')

@section('content_header')
    <h1>Menu Item Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $menuItem->name }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.food-menu.edit', $menuItem->id) }}" class="btn btn-info">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.food-menu.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $menuItem->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $menuItem->name }}</td>
                        </tr>
                        <tr>
                            <th>Meal Type</th>
                            <td>{{ $menuItem->meal_type }}</td>
                        </tr>
                        <tr>
                            <th>Dietary Tags</th>
                            <td>{{ $menuItem->dietary_tags ?? 'None' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge {{ $menuItem->available ? 'bg-success' : 'bg-danger' }}">
                                    {{ $menuItem->available ? 'Available' : 'Not Available' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $menuItem->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $menuItem->updated_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Description</h4>
                        </div>
                        <div class="card-body">
                            {{ $menuItem->description ?? 'No description available.' }}
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
        console.log('Menu item details page loaded!');
    </script>
@stop 