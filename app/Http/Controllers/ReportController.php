<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = IncidentReport::with(['incident.assignment', 'submitter', 'attachments']);
        $user = $request->user();

        if ($user->isStaff()) {
            $teamIds = $user->associatedTeamIds();
            if ($teamIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('incident.assignment', fn ($q) => $q->whereIn('team_id', $teamIds));
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('date_submitted', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date_submitted', '<=', $request->date('to'));
        }
        if ($request->filled('incident_type')) {
            $query->whereHas('incident', fn ($q) => $q->where('incident_type', $request->string('incident_type')));
        }

        $reports = $query->latest('date_submitted')->paginate(15)->withQueryString();

        $incidentTypes = collect(array_keys(Incident::incidentTypeLabels()));

        $stats = null;
        if ($user->isAdmin()) {
            $stats = [
                'total' => Incident::count(),
                'closed' => Incident::where('status', Incident::STATUS_CLOSED)->count(),
                'avg_response_minutes' => $this->averageResponseMinutes(),
            ];
        }

        return view('reports.index', [
            'title' => 'Reports — ERTMS',
            'reports' => $reports,
            'incidentTypes' => $incidentTypes,
            'stats' => $stats,
        ]);
    }

    public function create(Incident $incident): View|RedirectResponse
    {
        $user = auth()->user();
        if ($user->isStaff() && ! $user->isAdmin()) {
            if (! $user->isLeaderOfAssignedTeam($incident)) {
                return redirect()->route('incidents.show', $incident)
                    ->with('error', 'Only the assigned team leader can submit the resolution report.');
            }
        }

        if ($incident->reports()->exists()) {
            return redirect()->route('incidents.show', $incident)
                ->withErrors(['report' => 'A resolution report was already submitted for this incident.']);
        }

        if (! in_array($incident->status, [Incident::STATUS_ON_SCENE, Incident::STATUS_EN_ROUTE, Incident::STATUS_ASSIGNED], true)) {
            return redirect()->route('incidents.show', $incident)
                ->withErrors(['report' => 'Submit a report when the team is assigned or on scene.']);
        }

        return view('reports.create', [
            'title' => 'Submit Resolution Report — ERTMS',
            'incident' => $incident,
            'outcomes' => IncidentReport::outcomeLabels(),
            'operationsCategories' => IncidentReport::operationsCategories(),
            'effectivenessLabels' => IncidentReport::effectivenessLabels(),
            'casualtyLevels' => IncidentReport::casualtyLevels(),
            'damageLevels' => IncidentReport::damageLevels(),
            'actionChecklist' => IncidentReport::actionChecklist(),
        ]);
    }

    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if ($user->isStaff() && ! $user->isAdmin()) {
            if (! $user->isLeaderOfAssignedTeam($incident)) {
                return redirect()->route('incidents.show', $incident)
                    ->with('error', 'Only the assigned team leader can submit the resolution report.');
            }
        }

        if ($incident->reports()->exists()) {
            return redirect()->route('incidents.show', $incident)
                ->withErrors(['report' => 'A resolution report was already submitted.']);
        }

        $data = $request->validate([
            'resolution_outcome' => ['required', 'in:'.implode(',', array_keys(IncidentReport::outcomeLabels()))],
            'operations_category' => ['required', 'in:'.implode(',', array_keys(IncidentReport::operationsCategories()))],
            'response_effectiveness' => ['required', 'in:'.implode(',', array_keys(IncidentReport::effectivenessLabels()))],
            'casualty_level' => ['required', 'in:'.implode(',', array_keys(IncidentReport::casualtyLevels()))],
            'property_damage_level' => ['required', 'in:'.implode(',', array_keys(IncidentReport::damageLevels()))],
            'actions_taken' => ['nullable', 'array'],
            'actions_taken.*' => ['in:'.implode(',', array_keys(IncidentReport::actionChecklist()))],
            'resolution_notes' => ['nullable', 'string', 'max:5000'],
            'casualties' => ['nullable', 'string', 'max:255'],
            'damage_assessment' => ['nullable', 'string'],
            'follow_up_actions' => ['nullable', 'string'],
            'resolution_photos' => ['nullable', 'array'],
            'resolution_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $resolutionSummary = $this->buildResolutionSummary($data);

        $report = IncidentReport::create([
            'incident_id' => $incident->id,
            'submitted_by' => $user->id,
            'resolution_details' => $resolutionSummary,
            'resolution_outcome' => $data['resolution_outcome'],
            'operations_category' => $data['operations_category'],
            'response_effectiveness' => $data['response_effectiveness'],
            'casualty_level' => $data['casualty_level'],
            'property_damage_level' => $data['property_damage_level'],
            'actions_taken' => $data['actions_taken'] ?? [],
            'casualties' => $data['casualties'] ?? null,
            'damage_assessment' => $data['damage_assessment'] ?? null,
            'follow_up_actions' => $data['follow_up_actions'] ?? null,
            'date_submitted' => now(),
        ]);

        foreach ($request->file('resolution_photos', []) as $photo) {
            $path = $photo->store('report-attachments', 'public');
            $report->attachments()->create([
                'uploaded_by' => $user->id,
                'original_name' => $photo->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $photo->getClientMimeType(),
                'file_size' => $photo->getSize() ?: 0,
            ]);
        }

        $incident->update([
            'status' => Incident::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
        $incident->logActivity(
            event: 'resolution_report_submitted',
            details: 'Final report submitted. Incident status changed to Resolved.',
            userId: $user->id,
            meta: [
                'casualties' => $data['casualties'] ?? null,
            ],
        );

        return redirect()->route('incidents.show', $incident)->with('success', 'Resolution report submitted with categorized details. Status: Resolved.');
    }

    public function pdfIncident(Incident $incident)
    {
        $incident->load(['creator', 'assignment.team.leader', 'reports.submitter', 'reports.attachments']);

        $pdf = Pdf::loadView('reports.pdf-incident', ['incident' => $incident]);

        return $pdf->download('incident-'.$incident->incident_code.'.pdf');
    }

    protected function averageResponseMinutes(): ?float
    {
        $rows = Incident::query()
            ->whereNotNull('date_reported')
            ->whereNotNull('en_route_at')
            ->get(['date_reported', 'en_route_at']);

        if ($rows->isEmpty()) {
            return null;
        }

        $total = $rows->sum(fn ($i) => $i->date_reported->diffInMinutes($i->en_route_at));

        return round($total / $rows->count(), 1);
    }

    protected function buildResolutionSummary(array $data): string
    {
        $outcomes = IncidentReport::outcomeLabels();
        $operations = IncidentReport::operationsCategories();
        $effectiveness = IncidentReport::effectivenessLabels();
        $casualtyLevels = IncidentReport::casualtyLevels();
        $damageLevels = IncidentReport::damageLevels();
        $checklist = IncidentReport::actionChecklist();

        $actions = collect($data['actions_taken'] ?? [])
            ->map(fn (string $item) => $checklist[$item] ?? $item)
            ->values()
            ->all();

        return implode("\n", array_filter([
            'Outcome: '.($outcomes[$data['resolution_outcome']] ?? $data['resolution_outcome']),
            'Operation Category: '.($operations[$data['operations_category']] ?? $data['operations_category']),
            'Response Effectiveness: '.($effectiveness[$data['response_effectiveness']] ?? $data['response_effectiveness']),
            'Casualty Level: '.($casualtyLevels[$data['casualty_level']] ?? $data['casualty_level']),
            'Property Damage Level: '.($damageLevels[$data['property_damage_level']] ?? $data['property_damage_level']),
            ! empty($actions) ? 'Actions Taken: '.implode(', ', $actions) : null,
            ! empty($data['resolution_notes']) ? 'Leader Notes: '.$data['resolution_notes'] : null,
        ]));
    }
}
