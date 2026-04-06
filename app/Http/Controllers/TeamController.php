<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::with(['leader', 'members'])->latest()->paginate(12);

        return view('teams.index', [
            'title' => 'Teams — ERTMS',
            'teams' => $teams,
        ]);
    }

    public function create(): View
    {
        $leaders = User::where('role', User::ROLE_STAFF)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $responders = User::where('role', User::ROLE_STAFF)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('teams.create', [
            'title' => 'Create Team — ERTMS',
            'leaders' => $leaders,
            'responders' => $responders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
            'team_leader_id' => ['required', 'exists:users,id'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['exists:users,id'],
        ]);

        $team = Team::create([
            'team_name' => $data['team_name'],
            'team_leader_id' => $data['team_leader_id'],
            'availability_status' => 'available',
        ]);

        if (! empty($data['member_ids'])) {
            $team->members()->sync($data['member_ids']);
        }

        return redirect()->route('teams.index')->with('success', 'Team created.');
    }

    public function edit(Team $team): View
    {
        $leaders = User::where('role', User::ROLE_STAFF)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $responders = User::where('role', User::ROLE_STAFF)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('teams.edit', [
            'title' => 'Edit Team — ERTMS',
            'team' => $team->load('members'),
            'leaders' => $leaders,
            'responders' => $responders,
        ]);
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'team_name' => ['required', 'string', 'max:255'],
            'team_leader_id' => ['required', 'exists:users,id'],
            'availability_status' => ['required', 'in:available,deployed,unavailable'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['exists:users,id'],
        ]);

        $team->update([
            'team_name' => $data['team_name'],
            'team_leader_id' => $data['team_leader_id'],
            'availability_status' => $data['availability_status'],
        ]);

        $team->members()->sync($data['member_ids'] ?? []);

        return redirect()->route('teams.index')->with('success', 'Team updated.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team deleted.');
    }
}
