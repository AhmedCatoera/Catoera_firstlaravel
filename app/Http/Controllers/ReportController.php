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
        $query = IncidentReport::with(['incident', 'submitter']);

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

        $incidentTypes = Incident::query()->distinct()->orderBy('incident_type')->pluck('incident_type');

        $stats = null;
        if ($request->user()->isAdmin()) {
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
            abort_unless($user->isLeaderOfAssignedTeam($incident), 403);
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
        ]);
    }

    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $user = $request->user();
        if ($user->isStaff() && ! $user->isAdmin()) {
            abort_unless($user->isLeaderOfAssignedTeam($incident), 403);
        }

        if ($incident->reports()->exists()) {
            return redirect()->route('incidents.show', $incident)
                ->withErrors(['report' => 'A resolution report was already submitted.']);
        }

        $data = $request->validate([
            'resolution_details' => ['required', 'string'],
            'casualties' => ['nullable', 'string', 'max:255'],
            'damage_assessment' => ['nullable', 'string'],
        ]);

        IncidentReport::create([
            'incident_id' => $incident->id,
            'submitted_by' => $user->id,
            'resolution_details' => $data['resolution_details'],
            'casualties' => $data['casualties'] ?? null,
            'damage_assessment' => $data['damage_assessment'] ?? null,
            'date_submitted' => now(),
        ]);

        $incident->update([
            'status' => Incident::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);

        return redirect()->route('incidents.show', $incident)->with('success', 'Resolution report submitted. Status: Resolved.');
    }

    public function pdfIncident(Incident $incident)
    {
        $incident->load(['creator', 'assignment.team.leader', 'reports.submitter']);

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
}
