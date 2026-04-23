@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Operations Live Board</h1>
        <p class="text-muted small mb-0">Auto-refreshing dispatch queue with SLA breach flags and priority scoring.</p>
    </div>
    <span class="badge bg-dark board-refresh-badge" id="lastRefresh">Loading...</span>
</div>

<div class="card card-ertms">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="boardTable">
            <thead class="table-light">
                <tr>
                    <th>Incident</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Minutes Open</th>
                    <th>SLA</th>
                    <th>Priority</th>
                    <th>Team</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($queue as $incident)
                <tr>
                    <td><code>{{ $incident->incident_code }}</code></td>
                    <td>{{ $incident->incident_type }}</td>
                    <td>{{ $statusLabels[$incident->status] ?? $incident->status }}</td>
                    <td>{{ $incident->minutesOpen() ?? '—' }}</td>
                    <td>
                        @if($incident->isSlaBreached())
                            <span class="badge bg-danger">Breached</span>
                        @else
                            <span class="badge bg-success">On Time</span>
                        @endif
                    </td>
                    <td><span class="badge bg-dark">{{ $incident->priorityScore() }}</span></td>
                    <td>{{ $incident->assignment?->team?->team_name ?? 'Unassigned' }}</td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('incidents.show', $incident) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const tableBody = document.querySelector('#boardTable tbody');
    const refreshBadge = document.getElementById('lastRefresh');

    function renderRow(item) {
        const slaBadge = item.sla_breached
            ? '<span class="badge bg-danger">Breached</span>'
            : '<span class="badge bg-success">On Time</span>';

        return `<tr>
            <td><code>${item.incident_code ?? ''}</code></td>
            <td>${item.incident_type ?? ''}</td>
            <td>${item.status_label ?? item.status}</td>
            <td>${item.minutes_open ?? '—'}</td>
            <td>${slaBadge}</td>
            <td><span class="badge bg-dark">${item.priority_score ?? 0}</span></td>
            <td>${item.team ?? 'Unassigned'}</td>
            <td><a class="btn btn-sm btn-outline-primary" href="${item.url}">Open</a></td>
        </tr>`;
    }

    async function pollBoard() {
        try {
            const res = await fetch(@json(route('operations.board.data')));
            const data = await res.json();
            tableBody.innerHTML = (data.queue || []).map(renderRow).join('');
            refreshBadge.textContent = `Updated: ${new Date().toLocaleTimeString()}`;
        } catch (e) {
            refreshBadge.textContent = 'Update failed';
        }
    }

    pollBoard();
    setInterval(pollBoard, 10000);
})();
</script>
@endpush
