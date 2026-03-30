@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Create team</h1>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('teams.store') }}">
            @csrf
            <div class="mb-3">
                <label for="team_name" class="form-label">Team name</label>
                <input type="text" name="team_name" id="team_name" class="form-control" value="{{ old('team_name') }}" required>
            </div>
            <div class="mb-3">
                <label for="team_leader_id" class="form-label">Team leader</label>
                <select name="team_leader_id" id="team_leader_id" class="form-select" required>
                    <option value="">Select user…</option>
                    @foreach($leaders as $l)
                        <option value="{{ $l->id }}" @selected(old('team_leader_id') == $l->id)>{{ $l->name }} ({{ $l->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Members (responders)</label>
                <select name="member_ids[]" class="form-select" multiple size="6">
                    @foreach($responders as $r)
                        <option value="{{ $r->id }}" @selected(collect(old('member_ids'))->contains($r->id))>{{ $r->name }} — {{ \App\Models\User::roleLabels()[$r->role] }}</option>
                    @endforeach
                </select>
                <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</div>
            </div>
            <button type="submit" class="btn btn-danger">Save</button>
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
