@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Create incident</h1>
    <p class="text-muted small">A unique incident ID will be assigned when the record is saved. Initial status: <strong>Pending</strong>.</p>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('incidents.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="incident_type" class="form-label">Incident type</label>
                    <input type="text" name="incident_type" id="incident_type" class="form-control" value="{{ old('incident_type') }}" required placeholder="e.g. Fire, Medical, Hazmat">
                </div>
                <div class="col-md-6">
                    <label for="severity_level" class="form-label">Severity level</label>
                    <select name="severity_level" id="severity_level" class="form-select" required>
                        <option value="">Select…</option>
                        <option value="low" @selected(old('severity_level') === 'low')>Low</option>
                        <option value="medium" @selected(old('severity_level') === 'medium')>Medium</option>
                        <option value="high" @selected(old('severity_level') === 'high')>High</option>
                        <option value="critical" @selected(old('severity_level') === 'critical')>Critical</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-danger">Save incident</button>
                <a href="{{ route('incidents.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
