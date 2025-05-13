@extends('layouts.iframe')

@section('title', 'Patient to Nurse Notifications')

@section('content')
<div class="container-fluid p-3">
    <h4 class="mb-3">Patient to Nurse Notifications (Demo)</h4>
    <div class="alert alert-info">
        <strong>Note:</strong> This is a demo page. You can design the detailed notification view here later.
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="media align-items-center mb-2">
                <span class="badge badge-primary mr-3" style="font-size:1.5em;"><i class="fas fa-user-injured"></i></span>
                <div class="media-body">
                    <strong>Ahmad bin Ali</strong> (Bed B01) <br>
                    <span class="text-muted">Needs assistance to bathroom</span>
                </div>
                <span class="badge badge-warning ml-3">New</span>
            </div>
            <div class="media align-items-center mb-2">
                <span class="badge badge-primary mr-3" style="font-size:1.5em;"><i class="fas fa-user-injured"></i></span>
                <div class="media-body">
                    <strong>Lee Chong Wei</strong> (Bed B02) <br>
                    <span class="text-muted">Request for pain medication</span>
                </div>
                <span class="badge badge-secondary ml-3">Seen</span>
            </div>
            <div class="media align-items-center mb-2">
                <span class="badge badge-primary mr-3" style="font-size:1.5em;"><i class="fas fa-user-injured"></i></span>
                <div class="media-body">
                    <strong>Siti Aishah</strong> (Bed B03) <br>
                    <span class="text-muted">Call for nurse</span>
                </div>
                <span class="badge badge-warning ml-3">New</span>
            </div>
        </div>
    </div>
    <div class="text-muted small">Last updated: {{ now()->format('d M Y, h:i A') }}</div>
</div>
@endsection 