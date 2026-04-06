@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Profile</h1>
    <p class="text-muted small mb-0">Update your name, email, and password. Role is assigned by an administrator.</p>
</div>

@if (session('status') === 'profile-updated')
    <div class="alert alert-success small">Saved.</div>
@endif

<div class="card card-ertms mb-4">
    <div class="card-body">
        <h2 class="h6 text-uppercase text-muted">Profile information</h2>
        <form method="post" action="{{ route('profile.update') }}" class="mt-3">
            @csrf
            @method('patch')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-danger">Save profile</button>
        </form>
    </div>
</div>

<div class="card card-ertms mb-4">
    <div class="card-body">
        <h2 class="h6 text-uppercase text-muted">Update password</h2>
        @if (session('status') === 'password-updated')
            <div class="alert alert-success small mt-2">Password updated.</div>
        @endif
        <form method="post" action="{{ route('password.update') }}" class="mt-3">
            @csrf
            @method('put')
            <div class="mb-3">
                <label for="current_password" class="form-label">Current password</label>
                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New password</label>
                <input type="password" name="password" id="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm new password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-outline-danger">Update password</button>
        </form>
    </div>
</div>

@if($user->isAdmin())
    <div class="card card-ertms border-danger">
        <div class="card-body">
            <h2 class="h6 text-danger">Delete account</h2>
            <p class="small text-muted">Permanently delete this administrator account. Only administrators see this section.</p>
            @error('password', 'userDeletion')
                <div class="alert alert-danger small">{{ $message }}</div>
            @enderror
            <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Delete this account permanently?');">
                @csrf
                @method('delete')
                <div class="mb-3">
                    <label for="del_password" class="form-label">Confirm with password</label>
                    <input type="password" name="password" id="del_password" class="form-control" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete account</button>
            </form>
        </div>
    </div>
@endif
@endsection
