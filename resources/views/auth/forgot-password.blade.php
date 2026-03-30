@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-ertms p-4">
            <h1 class="h4 mb-3">Forgot password</h1>
            <p class="text-muted">Password reset is not enabled in this demo. Please contact your system administrator to recover your account.</p>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Back to login</a>
            <a href="{{ route('home') }}" class="btn btn-link">Home</a>
        </div>
    </div>
</div>
@endsection
