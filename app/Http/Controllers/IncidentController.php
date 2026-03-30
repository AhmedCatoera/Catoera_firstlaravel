<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Incident;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Incident::with(['creator', 'assignment.team']);

        if ($user->isTeamLeader()) {
            $teamIds = Team::where('team_leader_id', $user->id)->pluck('id');
            $query->whereHas('assignment', fn ($q) => $q->whereIn('team_id', $teamIds));
        } elseif ($user->isResponder()) {
            $teamIds = $user->teams()->pluck('teams.id');
            $query->whereHas('assignment', fn ($q) => $q->whereIn('team_id', $teamIds));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $incidents = $query->latest()->paginate(12)->withQueryString();

        return view('incidents.index', [
            'title' => 'Incidents — ERTMS',
            'incidents' => $incidents,
            'statusLabels' => Incident::statusLabels(),
        ]);
    }

    public function create(): View
    {
        return view('incidents.create', [
            'title' => 'Create Incident — ERTMS',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'incident_type' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'severity_level' => ['required', 'in:low,medium,high,critical'],
        ]);

        $incident = Incident::create([
            ...$data,
            'status' => Incident::STATUS_PENDING,
            'date_reported' => now(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident '.$incident->incident_code.' created.');
    }

    public function show(Incident $incident): View
    {
        $incident->load(['creator', 'assignment.team.leader', 'reports.submitter']);
        $user = auth()->user();

        if ($user->isTeamLeader() && ! $user->isAdmin()) {
            $teamIds = Team::where('team_leader_id', $user->id)->pluck('id');
            $assigned = $incident->assignment && in_array($incident->assignment->team_id, $teamIds->all(), true);
            abort_unless($assigned, 403);
        }
        if ($user->isResponder()) {
            $teamIds = $user->teams()->pluck('teams.id');
            $assigned = $incident->assignment && in_array($incident->assignment->team_id, $teamIds->all(), true);
            abort_unless($assigned, 403);
        }

        $teamsForAssign = collect();
        if (($user->isAdmin() || $user->isDispatcher()) && $incident->status === Incident::STATUS_PENDING) {
            $teamsForAssign = Team::with('leader')->where('availability_status', 'available')->orderBy('team_name')->get();
        }

        return view('incidents.show', [
            'title' => 'Incident '.$incident->incident_code.' — ERTMS',
            'incident' => $incident,
            'statusLabels' => Incident::statusLabels(),
            'teamsForAssign' => $teamsForAssign,
        ]);
    }

    public function edit(Incident $incident): View
    {
        return view('incidents.edit', [
            'title' => 'Edit Incident — ERTMS',
            'incident' => $incident,
        ]);
    }

    public function update(Request $request, Incident $incident): RedirectResponse
    {
        $data = $request->validate([
            'incident_type' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'severity_level' => ['required', 'in:low,medium,high,critical'],
        ]);

        $incident->update($data);

        return redirect()->route('incidents.show', $incident)->with('success', 'Incident updated.');
    }

    public function destroy(Incident $incident): RedirectResponse
    {
        $incident->delete();

        return redirect()->route('incidents.index')->with('success', 'Incident deleted.');
    }

    public function updateStatus(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();

        if ($user->isTeamLeader() && ! $user->isAdmin()) {
            $teamIds = Team::where('team_leader_id', $user->id)->pluck('id');
            $ok = $incident->assignment && in_array($incident->assignment->team_id, $teamIds->all(), true);
            abort_unless($ok, 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', [
                Incident::STATUS_EN_ROUTE,
                Incident::STATUS_ON_SCENE,
            ])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $updates = [
            'status' => $validated['status'],
            'notes' => array_key_exists('notes', $validated) ? $validated['notes'] : $incident->notes,
        ];

        if ($validated['status'] === Incident::STATUS_EN_ROUTE && ! $incident->en_route_at) {
            $updates['en_route_at'] = now();
        }
        if ($validated['status'] === Incident::STATUS_ON_SCENE && ! $incident->on_scene_at) {
            $updates['on_scene_at'] = now();
        }

        if ($validated['status'] === Incident::STATUS_ON_SCENE && $incident->assignment && ! $incident->assignment->arrival_time) {
            $incident->assignment->update(['arrival_time' => now()]);
        }

        $incident->update($updates);

        return back()->with('success', 'Status updated.');
    }

    public function close(Request $request, Incident $incident): RedirectResponse
    {
        if ($incident->status !== Incident::STATUS_RESOLVED) {
            return back()->withErrors(['close' => 'Close is only allowed after the incident is resolved.']);
        }

        $request->validate([
            'confirm' => ['accepted'],
        ]);

        $incident->update([
            'status' => Incident::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        if ($incident->assignment) {
            $incident->assignment->update(['completion_time' => now()]);
            $incident->assignment->team->update(['availability_status' => 'available']);
        }

        return back()->with('success', 'Incident closed and archived.');
    }
}
