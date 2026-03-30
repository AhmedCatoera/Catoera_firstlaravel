@extends('layouts.app')

@php
    $u = auth()->user();
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Incident {{ $incident->incident_code }}</h1>
        <p class="text-muted small mb-0">Reported {{ $incident->date_reported?->format('M j, Y H:i') }}
            @if($incident->creator) by {{ $incident->creator->name }} @endif
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if($u->isAdmin())
            <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        @endif
        <a href="{{ route('incidents.export-pdf', $incident) }}" class="btn btn-outline-secondary btn-sm">Export PDF</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card card-ertms mb-3">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted">Details</h2>
                <dl class="row mb-0 small">
                    <dt class="col-sm-3">Type</dt><dd class="col-sm-9">{{ $incident->incident_type }}</dd>
                    <dt class="col-sm-3">Severity</dt><dd class="col-sm-9"><span class="badge badge-severity-{{ $incident->severity_level }}">{{ $incident->severity_level }}</span></dd>
                    <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ $statusLabels[$incident->status] ?? $incident->status }}</dd>
                    <dt class="col-sm-3">Location</dt><dd class="col-sm-9">{{ $incident->location }}</dd>
                    <dt class="col-sm-3">Description</dt><dd class="col-sm-9">{{ $incident->description }}</dd>
                </dl>
            </div>
        </div>

        <div class="card card-ertms mb-3">
            <div class="card-body">
                <h2 class="h6 text-uppercase text-muted">Timeline</h2>
                <ul class="list-unstyled small mb-0">
                    <li><strong>Reported:</strong> {{ $incident->date_reported?->toDateTimeString() ?? '—' }}</li>
                    <li><strong>En route:</strong> {{ $incident->en_route_at?->toDateTimeString() ?? '—' }}</li>
                    <li><strong>On scene:</strong> {{ $incident->on_scene_at?->toDateTimeString() ?? '—' }}</li>
                    <li><strong>Resolved:</strong> {{ $incident->resolved_at?->toDateTimeString() ?? '—' }}</li>
                    <li><strong>Closed:</strong> {{ $incident->closed_at?->toDateTimeString() ?? '—' }}</li>
                </ul>
            </div>
        </div>

        @if($incident->notes)
            <div class="card card-ertms mb-3">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Notes</h2>
                    <p class="mb-0 small">{{ $incident->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-5">
        @if($incident->assignment)
            <div class="card card-ertms mb-3">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Assignment</h2>
                    <p class="small mb-1"><strong>Team:</strong> {{ $incident->assignment->team->team_name }}</p>
                    <p class="small mb-1"><strong>Leader:</strong> {{ $incident->assignment->team->leader->name ?? '—' }}</p>
                    <p class="small mb-1"><strong>Assigned:</strong> {{ $incident->assignment->assigned_date?->toDateTimeString() }}</p>
                    <p class="small mb-0"><strong>Arrival:</strong> {{ $incident->assignment->arrival_time?->toDateTimeString() ?? '—' }}</p>
                </div>
            </div>
        @endif

        @if(($u->isAdmin() || $u->isDispatcher()) && $incident->status === \App\Models\Incident::STATUS_PENDING && $teamsForAssign->isNotEmpty())
            <div class="card card-ertms mb-3 border-danger">
                <div class="card-body">
                    <h2 class="h6">Assign team</h2>
                    <form method="post" action="{{ route('assignments.store', $incident) }}">
                        @csrf
                        <label class="form-label small" for="team_id">Available team</label>
                        <select name="team_id" id="team_id" class="form-select form-select-sm mb-2" required>
                            <option value="">Select team…</option>
                            @foreach($teamsForAssign as $team)
                                <option value="{{ $team->id }}">{{ $team->team_name }} ({{ $team->leader->name ?? 'Leader' }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-danger btn-sm w-100">Assign & deploy</button>
                    </form>
                </div>
            </div>
        @endif

        @if(($u->isAdmin() || ($u->isTeamLeader() && $incident->assignment && $incident->assignment->team->team_leader_id === $u->id)) && in_array($incident->status, [\App\Models\Incident::STATUS_ASSIGNED, \App\Models\Incident::STATUS_EN_ROUTE, \App\Models\Incident::STATUS_ON_SCENE], true))
            <div class="card card-ertms mb-3">
                <div class="card-body">
                    <h2 class="h6">Update response status</h2>
                    <form method="post" action="{{ route('incidents.status', $incident) }}">
                        @csrf
                        @method('PATCH')
                        @php
                            $statusValue = old('status', $incident->status === \App\Models\Incident::STATUS_ASSIGNED ? \App\Models\Incident::STATUS_EN_ROUTE : $incident->status);
                        @endphp
                        <div class="mb-2">
                            <label class="form-label small" for="status">Status</label>
                            <select name="status" id="status" class="form-select form-select-sm" required>
                                <option value="{{ \App\Models\Incident::STATUS_EN_ROUTE }}" @selected($statusValue === \App\Models\Incident::STATUS_EN_ROUTE)>En Route</option>
                                <option value="{{ \App\Models\Incident::STATUS_ON_SCENE }}" @selected($statusValue === \App\Models\Incident::STATUS_ON_SCENE)>On Scene</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small" for="notes">Updates / notes</label>
                            <textarea name="notes" id="notes" class="form-control form-control-sm" rows="2" placeholder="Optional field notes">{{ old('notes', $incident->notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Save status</button>
                    </form>
                </div>
            </div>
        @endif

        @if(($u->isAdmin() || ($u->isTeamLeader() && $incident->assignment && $incident->assignment->team->team_leader_id === $u->id)) && ! $incident->reports->count() && in_array($incident->status, [\App\Models\Incident::STATUS_ASSIGNED, \App\Models\Incident::STATUS_EN_ROUTE, \App\Models\Incident::STATUS_ON_SCENE], true))
            <div class="card card-ertms mb-3">
                <div class="card-body">
                    <h2 class="h6">Resolution</h2>
                    <p class="small text-muted">Submit the final report when the response is complete.</p>
                    <a href="{{ route('reports.create', $incident) }}" class="btn btn-success btn-sm w-100">Submit resolution report</a>
                </div>
            </div>
        @endif

        @if($u->isAdmin() && $incident->status === \App\Models\Incident::STATUS_RESOLVED)
            <div class="card card-ertms mb-3 border-secondary">
                <div class="card-body">
                    <h2 class="h6">Close incident</h2>
                    <form method="post" action="{{ route('incidents.close', $incident) }}">
                        @csrf
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="confirm" id="confirm" value="1" required>
                            <label class="form-check-label small" for="confirm">Confirm archive and release team</label>
                        </div>
                        <button type="submit" class="btn btn-dark btn-sm w-100">Close incident</button>
                    </form>
                </div>
            </div>
        @endif

        @foreach($incident->reports as $rep)
            <div class="card card-ertms mb-3">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted">Resolution report</h2>
                    <p class="small mb-1">{{ $rep->resolution_details }}</p>
                    <p class="small mb-0"><strong>Casualties:</strong> {{ $rep->casualties ?? 'None reported' }}</p>
                    <p class="small"><strong>Damage:</strong> {{ $rep->damage_assessment ?? '—' }}</p>
                    <p class="small text-muted mb-0">Submitted {{ $rep->date_submitted?->toDateTimeString() }} by {{ $rep->submitter->name ?? '—' }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">Back to list</a>
</div>
@endsection
