@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Submit resolution report</h1>
    <p class="text-muted small">Incident <code>{{ $incident->incident_code }}</code></p>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('reports.store', $incident) }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="resolution_outcome" class="form-label">Resolution outcome</label>
                    <select name="resolution_outcome" id="resolution_outcome" class="form-select" required>
                        <option value="">Select outcome</option>
                        @foreach($outcomes as $key => $label)
                            <option value="{{ $key }}" @selected(old('resolution_outcome') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="operations_category" class="form-label">Operation category</label>
                    <select name="operations_category" id="operations_category" class="form-select" required>
                        <option value="">Select category</option>
                        @foreach($operationsCategories as $key => $label)
                            <option value="{{ $key }}" @selected(old('operations_category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="response_effectiveness" class="form-label">Response effectiveness</label>
                    <select name="response_effectiveness" id="response_effectiveness" class="form-select" required>
                        <option value="">Select effectiveness</option>
                        @foreach($effectivenessLabels as $key => $label)
                            <option value="{{ $key }}" @selected(old('response_effectiveness') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="casualty_level" class="form-label">Casualty level</label>
                    <select name="casualty_level" id="casualty_level" class="form-select" required>
                        <option value="">Select casualty level</option>
                        @foreach($casualtyLevels as $key => $label)
                            <option value="{{ $key }}" @selected(old('casualty_level') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="property_damage_level" class="form-label">Property damage level</label>
                    <select name="property_damage_level" id="property_damage_level" class="form-select" required>
                        <option value="">Select damage level</option>
                        @foreach($damageLevels as $key => $label)
                            <option value="{{ $key }}" @selected(old('property_damage_level') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="resolution_photos" class="form-label">Resolution photos</label>
                    <input type="file" name="resolution_photos[]" id="resolution_photos" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
                    <div class="form-text">You can upload multiple photos (JPG, PNG, WEBP, max 5MB each).</div>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label">Action checklist</label>
                <div class="row g-2">
                    @foreach($actionChecklist as $key => $label)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="actions_taken[]"
                                    value="{{ $key }}"
                                    id="action_{{ $key }}"
                                    @checked(in_array($key, old('actions_taken', []), true))
                                >
                                <label class="form-check-label" for="action_{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-3">
                <label for="resolution_notes" class="form-label">Leader notes (optional)</label>
                <textarea name="resolution_notes" id="resolution_notes" class="form-control" rows="3" placeholder="Optional concise notes">{{ old('resolution_notes') }}</textarea>
            </div>
            <div class="mt-3">
                <label for="casualties" class="form-label">Casualties</label>
                <input type="text" name="casualties" id="casualties" class="form-control" value="{{ old('casualties') }}" placeholder="None / number / brief note">
            </div>
            <div class="mt-3">
                <label for="damage_assessment" class="form-label">Damage assessment</label>
                <textarea name="damage_assessment" id="damage_assessment" class="form-control" rows="3">{{ old('damage_assessment') }}</textarea>
            </div>
            <div class="mt-3">
                <label for="follow_up_actions" class="form-label">Follow-up actions</label>
                <textarea name="follow_up_actions" id="follow_up_actions" class="form-control" rows="3">{{ old('follow_up_actions') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Submit report</button>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
