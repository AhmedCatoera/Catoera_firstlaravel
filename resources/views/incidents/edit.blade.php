@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Edit incident</h1>
    <p class="text-muted small">Incident ID: <code>{{ $incident->incident_code }}</code></p>
    <p class="small mb-0"><span class="badge bg-danger">Administrator only</span> Full edit and delete are restricted to admins.</p>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('incidents.update', $incident) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="incident_type" class="form-label">Incident type</label>
                    <input type="text" name="incident_type" id="incident_type" class="form-control" value="{{ old('incident_type', $incident->incident_type) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="severity_level" class="form-label">Severity level</label>
                    <select name="severity_level" id="severity_level" class="form-select" required>
                        @foreach(['low','medium','high','critical'] as $sev)
                            <option value="{{ $sev }}" @selected(old('severity_level', $incident->severity_level) === $sev)>{{ ucfirst($sev) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $incident->location) }}" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $incident->description) }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-danger">Update</button>
                <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
        <hr class="my-4">
        <form method="post" action="{{ route('incidents.destroy', $incident) }}" onsubmit="return confirm('Delete this incident permanently?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">Delete incident</button>
        </form>
    </div>
</div>
@endsection
