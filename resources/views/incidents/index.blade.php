@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-0">Incidents</h1>
        <p class="text-muted small mb-0">View and manage emergency incidents.</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <a href="{{ route('incidents.create') }}" class="btn btn-danger">Create incident</a>
    @endif
</div>

<form method="get" action="{{ route('incidents.index') }}" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-0" for="status">Filter by status</label>
        <select name="status" id="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach($statusLabels as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-outline-secondary">Apply</button>
    </div>
</form>

<div class="card card-ertms">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Incident ID</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Reported</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $inc)
                    <tr>
                        <td><code>{{ $inc->incident_code }}</code></td>
                        <td>{{ $inc->incident_type }}</td>
                        <td>{{ str($inc->location)->limit(40) }}</td>
                        <td><span class="badge badge-severity-{{ $inc->severity_level }}">{{ $inc->severity_level }}</span></td>
                        <td>{{ $statusLabels[$inc->status] ?? $inc->status }}</td>
                        <td>{{ $inc->date_reported?->format('M j, Y H:i') }}</td>
                        <td><a href="{{ route('incidents.show', $inc) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No incidents found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $incidents->links() }}</div>
@endsection
