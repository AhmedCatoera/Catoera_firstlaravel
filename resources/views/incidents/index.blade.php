@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-0">Incidents</h1>
        <p class="text-muted small mb-0">View and manage emergency incidents.</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isDispatcher())
        <a href="{{ route('incidents.create') }}" class="btn btn-danger">Create incident</a>
    @endif
</div>

<div class="card card-ertms mb-3">
    <div class="card-body">
        <form method="get" action="{{ route('incidents.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-0" for="q">Search</label>
                <input type="text" name="q" id="q" class="form-control form-control-sm" placeholder="ID, type, location..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0" for="status">Filter by status</label>
                <select name="status" id="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach($statusLabels as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-0" for="incident_type">Category</label>
                <select name="incident_type" id="incident_type" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($incidentTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('incident_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0" for="from">From</label>
                <input type="date" name="from" id="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0" for="to">To</label>
                <input type="date" name="to" id="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-danger">Apply filters</button>
                <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-ertms">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Incident ID</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Reported</th>
                    <th>Team</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $inc)
                    <tr>
                        <td><code>{{ $inc->incident_code }}</code></td>
                        <td>{{ $inc->incident_type }}</td>
                        <td>{{ str($inc->location)->limit(40) }}</td>
                        <td>{{ $statusLabels[$inc->status] ?? $inc->status }}</td>
                        <td>{{ $inc->date_reported?->format('M j, Y H:i') }}</td>
                        <td>{{ $inc->assignment?->team?->team_name ?? 'Unassigned' }}</td>
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
