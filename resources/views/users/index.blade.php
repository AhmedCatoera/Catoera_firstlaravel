@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-0">Users</h1>
        <p class="text-muted small mb-0">Role-based accounts (Admin, Dispatcher, Team Leader, Responder).</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-danger">Add user</a>
</div>

<div class="card card-ertms">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $roleLabels[$user->role] ?? $user->role }}</td>
                        <td><span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $user->status }}</span></td>
                        <td><a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
