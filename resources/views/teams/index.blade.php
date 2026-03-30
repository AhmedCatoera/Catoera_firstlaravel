@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h3 mb-0">Response teams</h1>
        <p class="text-muted small mb-0">Create teams, assign leaders, and track availability.</p>
    </div>
    <a href="{{ route('teams.create') }}" class="btn btn-danger">Create team</a>
</div>

<div class="card card-ertms">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Leader</th>
                    <th>Availability</th>
                    <th>Members</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $team)
                    <tr>
                        <td>{{ $team->team_name }}</td>
                        <td>{{ $team->leader->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $team->availability_status) }}</span></td>
                        <td>{{ $team->members->count() }}</td>
                        <td>
                            <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No teams yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $teams->links() }}</div>
@endsection
