@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card card-ertms p-4">
            <h1 class="h5 mb-3">Forgot password</h1>
            <p class="text-muted small mb-4">Enter your email address and we will send you a reset link if an account exists.</p>

            @if (session('status'))
                <div class="alert alert-success small">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-danger w-100">Email reset link</button>
            </form>
            <p class="mt-3 mb-0 text-center small">
                <a href="{{ route('login') }}">Back to login</a>
                <span class="text-muted px-1">·</span>
                <a href="{{ route('home') }}">Home</a>
            </p>
        </div>
    </div>
</div>
@endsection
