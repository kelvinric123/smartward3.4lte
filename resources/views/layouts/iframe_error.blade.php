@extends('layouts.iframe')

@section('title', 'Error')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-header bg-danger text-white">
                    <h5 class="m-0"><i class="fas fa-exclamation-triangle mr-2"></i> Error</h5>
                </div>
                <div class="card-body">
                    <p class="text-center mb-0">{{ $message ?? 'An unknown error occurred.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 