<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Incident Command Summary - {{ $incident->incident_code }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
        h1 { margin: 0 0 8px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; width: 22%; }
        .section { margin-top: 18px; }
        .list-item { margin: 4px 0; }
    </style>
</head>
<body>
    <h1>Incident Command Summary</h1>
    <div class="muted">{{ $incident->incident_code }} · {{ $statusLabels[$incident->status] ?? $incident->status }}</div>

    <div class="section">
        <table>
            <tr><th>Incident Type</th><td>{{ $incident->incident_type }}</td></tr>
            <tr><th>Location</th><td>{{ $incident->location }}</td></tr>
            <tr><th>Coordinates</th><td>{{ $incident->latitude ? number_format($incident->latitude, 7).', '.number_format($incident->longitude, 7) : 'Not captured' }}</td></tr>
            <tr><th>Reported</th><td>{{ $incident->date_reported?->toDateTimeString() ?? '—' }}</td></tr>
            <tr><th>Created By</th><td>{{ $incident->creator->name ?? 'System' }}</td></tr>
            <tr><th>Description</th><td>{{ $incident->description }}</td></tr>
            <tr><th>Operational Notes</th><td>{{ $incident->notes ?? 'None' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Team Assignment</h3>
        @if($incident->assignment)
            <div class="list-item"><strong>Team:</strong> {{ $incident->assignment->team->team_name ?? '—' }}</div>
            <div class="list-item"><strong>Leader:</strong> {{ $incident->assignment->team->leader->name ?? '—' }}</div>
            <div class="list-item"><strong>Assigned At:</strong> {{ $incident->assignment->assigned_date?->toDateTimeString() ?? '—' }}</div>
            <div class="list-item"><strong>Arrival:</strong> {{ $incident->assignment->arrival_time?->toDateTimeString() ?? '—' }}</div>
        @else
            <div class="muted">No team assigned.</div>
        @endif
    </div>

    <div class="section">
        <h3>Resolution Reports</h3>
        @forelse($incident->reports as $report)
            <div class="list-item"><strong>Submitted By:</strong> {{ $report->submitter->name ?? '—' }}</div>
            <div class="list-item"><strong>Submitted At:</strong> {{ $report->date_submitted?->toDateTimeString() ?? '—' }}</div>
            <div class="list-item"><strong>Details:</strong> {{ $report->resolution_details }}</div>
            <div class="list-item"><strong>Casualties:</strong> {{ $report->casualties ?? 'None reported' }}</div>
            <div class="list-item"><strong>Damage:</strong> {{ $report->damage_assessment ?? '—' }}</div>
            <hr>
        @empty
            <div class="muted">No resolution reports yet.</div>
        @endforelse
    </div>

    <div class="section">
        <h3>Attachments</h3>
        @forelse($incident->attachments as $attachment)
            <div class="list-item">{{ $attachment->original_name }} ({{ number_format(($attachment->file_size ?? 0) / 1024, 1) }} KB)</div>
        @empty
            <div class="muted">No attachments.</div>
        @endforelse
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
