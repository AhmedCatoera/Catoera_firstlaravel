@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Dashboard</h1>
    <p class="text-muted mb-0">Welcome, {{ $user->name }}.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-ertms p-3">
            <div class="text-muted small">Total incidents</div>
            <div class="fs-3 fw-semibold">{{ $totalIncidents }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-ertms p-3">
            <div class="text-muted small">Active incidents</div>
            <div class="fs-3 fw-semibold">{{ $activeIncidents }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-ertms p-3">
            <div class="text-muted small">Available teams</div>
            <div class="fs-3 fw-semibold">{{ $availableTeams }}</div>
        </div>
    </div>
    @if($user->isAdmin())
        <div class="col-md-3 col-6">
            <div class="card card-ertms p-3">
                <div class="text-muted small">Active users</div>
                <div class="fs-3 fw-semibold">{{ $totalUsers }}</div>
            </div>
        </div>
    @endif
</div>

<div class="card card-ertms">
    <div class="card-body">
        <h2 class="h5 mb-3">Recent incidents</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Reported</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentIncidents as $inc)
                        <tr>
                            <td><code>{{ $inc->incident_code }}</code></td>
                            <td>{{ $inc->incident_type }}</td>
                            <td><span class="badge badge-severity-{{ $inc->severity_level }}">{{ $inc->severity_level }}</span></td>
                            <td>{{ \App\Models\Incident::statusLabels()[$inc->status] ?? $inc->status }}</td>
                            <td>{{ $inc->date_reported?->format('M j, Y H:i') }}</td>
                            <td><a href="{{ route('incidents.show', $inc) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">No incidents yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($user->isDispatcher() || $user->isAdmin())
    <div class="mt-3">
        <a href="{{ route('incidents.create') }}" class="btn btn-danger">Create incident</a>
    </div>
@endif
@endsection
