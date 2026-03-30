<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incident {{ $incident->incident_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>ERTMS — Incident report</h1>
    <p><strong>Incident ID:</strong> {{ $incident->incident_code }}</p>
    <p><strong>Type:</strong> {{ $incident->incident_type }} &nbsp;|&nbsp; <strong>Severity:</strong> {{ $incident->severity_level }}</p>
    <p><strong>Status:</strong> {{ $incident->status }}</p>
    <p><strong>Location:</strong> {{ $incident->location }}</p>
    <p><strong>Description:</strong><br>{{ $incident->description }}</p>

    <table>
        <tr><th>Reported</th><td>{{ $incident->date_reported }}</td></tr>
        <tr><th>En route</th><td>{{ $incident->en_route_at ?? '—' }}</td></tr>
        <tr><th>On scene</th><td>{{ $incident->on_scene_at ?? '—' }}</td></tr>
        <tr><th>Resolved</th><td>{{ $incident->resolved_at ?? '—' }}</td></tr>
        <tr><th>Closed</th><td>{{ $incident->closed_at ?? '—' }}</td></tr>
    </table>

    @if($incident->assignment)
        <h2 style="margin-top:16px;font-size:14px;">Assignment</h2>
        <p>Team: {{ $incident->assignment->team->team_name }} — Leader: {{ $incident->assignment->team->leader->name ?? '' }}</p>
    @endif

    @foreach($incident->reports as $rep)
        <h2 style="margin-top:16px;font-size:14px;">Resolution report</h2>
        <p>{{ $rep->resolution_details }}</p>
        <p><strong>Casualties:</strong> {{ $rep->casualties ?? '—' }}</p>
        <p><strong>Damage:</strong> {{ $rep->damage_assessment ?? '—' }}</p>
    @endforeach
</body>
</html>
