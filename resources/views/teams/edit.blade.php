@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3">Edit team</h1>
    <p class="text-muted small mb-0">{{ $team->team_name }}</p>
</div>

<div class="card card-ertms">
    <div class="card-body">
        <form method="post" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="team_name" class="form-label">Team name</label>
                <input type="text" name="team_name" id="team_name" class="form-control" value="{{ old('team_name', $team->team_name) }}" required>
            </div>
            <div class="mb-3">
                <label for="team_leader_id" class="form-label">Team leader</label>
                <select name="team_leader_id" id="team_leader_id" class="form-select" required>
                    @foreach($leaders as $l)
                        <option value="{{ $l->id }}" @selected(old('team_leader_id', $team->team_leader_id) == $l->id)>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="availability_status" class="form-label">Availability</label>
                <select name="availability_status" id="availability_status" class="form-select" required>
                    @foreach(['available','deployed','unavailable'] as $av)
                        <option value="{{ $av }}" @selected(old('availability_status', $team->availability_status) === $av)>{{ ucfirst($av) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Members</label>
                <select name="member_ids[]" class="form-select" multiple size="6">
                    @foreach($responders as $r)
                        <option value="{{ $r->id }}" @selected(in_array($r->id, old('member_ids', $team->members->pluck('id')->all())))>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-danger">Update</button>
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
        <hr class="my-4">
        <form method="post" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('Delete this team?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">Delete team</button>
        </form>
    </div>
</div>
@endsection
