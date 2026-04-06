@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Reports & analytics</h1>
    <p class="text-muted small mb-0">Submitted resolution reports and incident statistics.</p>
</div>

@if($stats)
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-ertms p-3">
                <div class="text-muted small">Total incidents</div>
                <div class="fs-4 fw-semibold">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-ertms p-3">
                <div class="text-muted small">Closed incidents</div>
                <div class="fs-4 fw-semibold">{{ $stats['closed'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-ertms p-3">
                <div class="text-muted small">Avg. response time (reported → en route)</div>
                <div class="fs-4 fw-semibold">
                    {{ $stats['avg_response_minutes'] !== null ? $stats['avg_response_minutes'].' min' : '—' }}
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info small mb-4" role="status">
        Aggregate analytics on this page are visible to <strong>Administrators</strong> only. You can still browse submitted resolution reports below.
    </div>
@endif

<form method="get" action="{{ route('reports.index') }}" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label small mb-0" for="from">From</label>
        <input type="date" name="from" id="from" class="form-control form-control-sm" value="{{ request('from') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label small mb-0" for="to">To</label>
        <input type="date" name="to" id="to" class="form-control form-control-sm" value="{{ request('to') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label small mb-0" for="incident_type">Incident type</label>
        <select name="incident_type" id="incident_type" class="form-select form-select-sm">
            <option value="">All types</option>
            @foreach($incidentTypes as $t)
                <option value="{{ $t }}" @selected(request('incident_type') === $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-sm btn-danger">Filter</button>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card card-ertms">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Incident</th>
                    <th>Submitted</th>
                    <th>By</th>
                    <th>Summary</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $rep)
                    <tr>
                        <td>
                            <a href="{{ route('incidents.show', $rep->incident) }}">{{ $rep->incident->incident_code }}</a>
                            <div class="text-muted small">{{ $rep->incident->incident_type }}</div>
                        </td>
                        <td>{{ $rep->date_submitted?->format('M j, Y H:i') }}</td>
                        <td>{{ $rep->submitter->name ?? '—' }}</td>
                        <td>{{ str($rep->resolution_details)->limit(80) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No reports match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $reports->links() }}</div>
@endsection
