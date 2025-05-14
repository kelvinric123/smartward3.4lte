@extends('adminlte::page')

@section('title', 'Edit Menu Item')

@section('content_header')
    <h1>Edit Menu Item</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Menu Item #{{ $menuItem->id }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.food-menu.update', $menuItem->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Item Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $menuItem->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $menuItem->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="meal_type">Meal Type <span class="text-danger">*</span></label>
                    <select class="form-control @error('meal_type') is-invalid @enderror" id="meal_type" name="meal_type" required>
                        <option value="">Select Meal Type</option>
                        <option value="Breakfast" {{ old('meal_type', $menuItem->meal_type) == 'Breakfast' ? 'selected' : '' }}>Breakfast</option>
                        <option value="Lunch" {{ old('meal_type', $menuItem->meal_type) == 'Lunch' ? 'selected' : '' }}>Lunch</option>
                        <option value="Dinner" {{ old('meal_type', $menuItem->meal_type) == 'Dinner' ? 'selected' : '' }}>Dinner</option>
                        <option value="Snack" {{ old('meal_type', $menuItem->meal_type) == 'Snack' ? 'selected' : '' }}>Snack</option>
                    </select>
                    @error('meal_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="dietary_tags">Dietary Tags</label>
                    <input type="text" class="form-control @error('dietary_tags') is-invalid @enderror" id="dietary_tags" name="dietary_tags" value="{{ old('dietary_tags', $menuItem->dietary_tags) }}" placeholder="e.g., Vegetarian, Gluten-Free, Halal">
                    <small class="form-text text-muted">Separate multiple tags with commas</small>
                    @error('dietary_tags')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="available" name="available" value="1" {{ old('available', $menuItem->available) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="available">Available for ordering</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Menu Item</button>
                    <a href="{{ route('admin.food-menu.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Edit menu item page loaded!');
    </script>
@stop 