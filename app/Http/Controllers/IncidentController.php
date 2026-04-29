<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Incident::with(['creator', 'assignment.team']);

        if ($user->isStaff()) {
            $teamIds = $user->associatedTeamIds();
            $query->where(function ($q) use ($teamIds, $user): void {
                if ($teamIds->isEmpty()) {
                    $q->whereRaw('1 = 0');
                    return;
                }

                $q->whereHas('assignment', fn ($sq) => $sq->whereIn('team_id', $teamIds));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('q')) {
            $term = trim((string) $request->string('q'));
            $query->where(function ($q) use ($term): void {
                $q->where('incident_code', 'like', "%{$term}%")
                    ->orWhere('incident_type', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }
        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->string('incident_type'));
        }
        if ($request->filled('from')) {
            $query->whereDate('date_reported', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date_reported', '<=', $request->date('to'));
        }

        $incidents = $query->latest()->paginate(12)->withQueryString();

        return view('incidents.index', [
            'title' => 'Incidents — ERTMS',
            'incidents' => $incidents,
            'statusLabels' => Incident::statusLabels(),
            'incidentTypes' => Incident::incidentTypeLabels(),
        ]);
    }

    public function create(): View
    {
        return view('incidents.create', [
            'title' => 'Create Incident — ERTMS',
            'incidentTypes' => Incident::incidentTypeLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'incident_type' => ['required', 'in:'.implode(',', array_keys(Incident::incidentTypeLabels()))],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string'],
            'caller_name' => ['nullable', 'string', 'max:120'],
            'caller_phone' => ['nullable', 'string', 'max:40'],
            'caller_relation' => ['nullable', 'in:'.implode(',', array_keys(Incident::callerRelationLabels()))],
            'verification_status' => ['nullable', 'in:'.implode(',', array_keys(Incident::verificationStatusLabels()))],
            'verification_sources' => ['nullable', 'array'],
            'verification_sources.*' => ['in:'.implode(',', array_keys(Incident::verificationSourceLabels()))],
            'verification_notes' => ['nullable', 'string', 'max:5000'],
            'confidence_score' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $incident = Incident::create([
            ...$data,
            'verification_status' => $data['verification_status'] ?? 'unverified',
            'status' => Incident::STATUS_PENDING,
            'date_reported' => now(),
            'created_by' => $request->user()->id,
        ]);
        $incident->logActivity(
            event: 'incident_created',
            details: 'Incident reported and queued for assignment.',
            userId: $request->user()->id,
            meta: [
                'incident_type' => $incident->incident_type,
                'location' => $incident->location,
                'verification_status' => $incident->verification_status,
            ],
        );

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incident '.$incident->incident_code.' created.');
    }

    public function show(Incident $incident): View|RedirectResponse
    {
        $incident->load(['creator', 'assignment.team.leader', 'reports.submitter', 'reports.attachments', 'activities.actor', 'attachments.uploader']);
        $user = auth()->user();

        if ($user->isStaff() && ! $user->isAdmin() && ! $user->isDispatcher()) {
            $teamIds = $user->associatedTeamIds();
            $assigned = $teamIds->isNotEmpty() && $incident->assignment && in_array($incident->assignment->team_id, $teamIds->all(), true);
            if (! $assigned) {
                return redirect()->route('incidents.index')->with('error', 'You can only open incidents assigned to your team.');
            }
        }

        $teamsForAssign = collect();
        $canAssign = $user->isAdmin() || $user->isDispatcher();
        if ($canAssign && $incident->status === Incident::STATUS_PENDING) {
            $teamsForAssign = Team::with('leader')->where('availability_status', 'available')->orderBy('team_name')->get();
        }

        return view('incidents.show', [
            'title' => 'Incident '.$incident->incident_code.' — ERTMS',
            'incident' => $incident,
            'statusLabels' => Incident::statusLabels(),
            'teamsForAssign' => $teamsForAssign,
        ]);
    }

    public function board(): View
    {
        $queue = Incident::query()
            ->with(['assignment.team'])
            ->prioritizedQueue()
            ->take(25)
            ->get();

        return view('operations.board', [
            'title' => 'Operations Board — ERTMS',
            'queue' => $queue,
            'statusLabels' => Incident::statusLabels(),
        ]);
    }

    public function boardData(): JsonResponse
    {
        $queue = Incident::query()
            ->with(['assignment.team'])
            ->prioritizedQueue()
            ->take(25)
            ->get()
            ->map(function (Incident $incident): array {
                return [
                    'id' => $incident->id,
                    'incident_code' => $incident->incident_code,
                    'incident_type' => $incident->incident_type,
                    'status' => $incident->status,
                    'status_label' => Incident::statusLabels()[$incident->status] ?? $incident->status,
                    'reported_at' => $incident->date_reported?->toDateTimeString(),
                    'minutes_open' => $incident->minutesOpen(),
                    'sla_breached' => $incident->isSlaBreached(),
                    'priority_score' => $incident->priorityScore(),
                    'team' => $incident->assignment?->team?->team_name,
                    'url' => route('incidents.show', $incident),
                ];
            });

        return response()->json(['queue' => $queue]);
    }

    public function edit(Incident $incident): View
    {
        return view('incidents.edit', [
            'title' => 'Edit Incident — ERTMS',
            'incident' => $incident,
            'incidentTypes' => Incident::incidentTypeLabels(),
        ]);
    }

    public function update(Request $request, Incident $incident): RedirectResponse
    {
        $data = $request->validate([
            'incident_type' => ['required', 'in:'.implode(',', array_keys(Incident::incidentTypeLabels()))],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string'],
            'caller_name' => ['nullable', 'string', 'max:120'],
            'caller_phone' => ['nullable', 'string', 'max:40'],
            'caller_relation' => ['nullable', 'in:'.implode(',', array_keys(Incident::callerRelationLabels()))],
            'verification_status' => ['nullable', 'in:'.implode(',', array_keys(Incident::verificationStatusLabels()))],
            'verification_sources' => ['nullable', 'array'],
            'verification_sources.*' => ['in:'.implode(',', array_keys(Incident::verificationSourceLabels()))],
            'verification_notes' => ['nullable', 'string', 'max:5000'],
            'confidence_score' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $incident->update($data);
        $incident->logActivity(
            event: 'incident_updated',
            details: 'Incident details were updated by administrator.',
            userId: $request->user()->id,
            meta: $data,
        );

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

        if ($user->isStaff() && ! $user->isAdmin()) {
            if (! $user->isLeaderOfAssignedTeam($incident)) {
                return redirect()->route('incidents.show', $incident)
                    ->with('error', 'Only the assigned team leader can update incident status.');
            }
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
        $incident->logActivity(
            event: 'status_updated',
            details: 'Response status changed to '.(Incident::statusLabels()[$validated['status']] ?? $validated['status']).'.',
            userId: $request->user()->id,
            meta: [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ],
        );

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
        $incident->logActivity(
            event: 'incident_closed',
            details: 'Incident closed and archived by administrator.',
            userId: $request->user()->id,
        );

        return back()->with('success', 'Incident closed and archived.');
    }
}
