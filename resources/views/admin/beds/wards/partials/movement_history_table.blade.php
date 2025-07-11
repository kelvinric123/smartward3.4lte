@if($patientMovements->count() > 0)
    <table class="table table-striped mb-0">
        <thead class="thead-light">
            <tr>
                <th>Date & Time</th>
                <th>Destination</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patientMovements as $movement)
                <tr>
                    <td>{{ $movement->formatted_scheduled_time }}</td>
                    <td>{{ $movement->to_service_location }}</td>
                    <td>
                        @if($movement->status == 'scheduled')
                            <span class="badge badge-info">Scheduled</span>
                        @elseif($movement->status == 'sent')
                            <span class="badge badge-warning">Out of Ward</span>
                        @elseif($movement->status == 'returned')
                            <span class="badge badge-success">Returned</span>
                        @elseif($movement->status == 'cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                        @endif
                    </td>
                    <td>
                        @if($movement->status == 'scheduled')
                            <form method="POST" action="{{ route('admin.movements.send', $movement->id) }}" class="movement-form" data-action="send">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-xs btn-warning">
                                    <i class="fas fa-sign-out-alt"></i> Send
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.movements.cancel', $movement->id) }}" class="movement-form" data-action="cancel">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        @elseif($movement->status == 'sent')
                            <form method="POST" action="{{ route('admin.movements.return', $movement->id) }}" class="movement-form" data-action="return">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-xs btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Return
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="text-center p-3">
        <p class="text-muted mb-0">No movement history found.</p>
    </div>
@endif 