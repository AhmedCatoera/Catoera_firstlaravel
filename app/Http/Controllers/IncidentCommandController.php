<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\View\View;

class IncidentCommandController extends Controller
{
    public function printableSummary(Incident $incident): View
    {
        $incident->load(['creator', 'assignment.team.leader', 'reports.submitter', 'attachments']);

        return view('incidents.print-summary', [
            'incident' => $incident,
            'statusLabels' => Incident::statusLabels(),
        ]);
    }
}
