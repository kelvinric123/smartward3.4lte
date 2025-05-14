@extends('adminlte::page')

@section('title', 'Food Orders')

@section('content_header')
    <h1>Food Orders</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Food Orders</h3>
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
                            <th>Patient Name</th>
                            <th>Ward</th>
                            <th>Bed</th>
                            <th>Meal Type</th>
                            <th>Item Name</th>
                            <th>Dietary Restriction</th>
                            <th>Status</th>
                            <th>Order Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->patient->full_name ?? 'N/A' }}</td>
                                <td>{{ $order->ward->name ?? 'N/A' }}</td>
                                <td>{{ $order->bed->bed_number ?? 'N/A' }}</td>
                                <td>{{ $order->meal_type }}</td>
                                <td>{{ $order->item_name }}</td>
                                <td>{{ $order->dietary_restriction ?? 'None' }}</td>
                                <td>
                                    <span class="badge {{ $order->status_badge_class }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->order_time->format('M d, Y h:i A') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                                            Update Status
                                        </button>
                                        <div class="dropdown-menu">
                                            <form action="{{ route('admin.food-orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item">Pending</button>
                                            </form>
                                            <form action="{{ route('admin.food-orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="preparing">
                                                <button type="submit" class="dropdown-item">Preparing</button>
                                            </form>
                                            <form action="{{ route('admin.food-orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="ready">
                                                <button type="submit" class="dropdown-item">Ready</button>
                                            </form>
                                            <form action="{{ route('admin.food-orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="delivered">
                                                <button type="submit" class="dropdown-item">Delivered</button>
                                            </form>
                                            <form action="{{ route('admin.food-orders.update-status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item">Cancelled</button>
                                            </form>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.food-orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No food orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Food orders page loaded!');
    </script>
@stop 