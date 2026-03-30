@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Submit resolution report</h1>
    <p class="text-muted small">Incident <code>{{ $incident->incident_code }}</code></p>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('reports.store', $incident) }}">
            @csrf
            <div class="mb-3">
                <label for="resolution_details" class="form-label">Resolution details</label>
                <textarea name="resolution_details" id="resolution_details" class="form-control" rows="5" required placeholder="Final situation report, actions taken, handover…">{{ old('resolution_details') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="casualties" class="form-label">Casualties</label>
                <input type="text" name="casualties" id="casualties" class="form-control" value="{{ old('casualties') }}" placeholder="None / number / brief note">
            </div>
            <div class="mb-3">
                <label for="damage_assessment" class="form-label">Damage assessment</label>
                <textarea name="damage_assessment" id="damage_assessment" class="form-control" rows="3">{{ old('damage_assessment') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Submit report</button>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
