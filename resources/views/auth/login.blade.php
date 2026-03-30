@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card card-ertms p-4">
            <h1 class="h4 mb-4 text-center">ERTMS Login</h1>
            <p class="text-muted small text-center mb-4">Emergency Response Team Management System</p>
            <form method="post" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-danger w-100">Sign in</button>
            </form>
            <p class="mt-3 text-center small mb-0">
                <a href="{{ route('home') }}">Back to home</a>
                <span class="text-muted px-1">·</span>
                <a href="{{ route('password.request') }}">Forgot password?</a>
            </p>
        </div>
    </div>
</div>
@endsection
