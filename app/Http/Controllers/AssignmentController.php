<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Incident;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request, Incident $incident): RedirectResponse
    {
        $canAssign = $request->user()->isAdmin() || $request->user()->isDispatcher();
        if (! $canAssign) {
            return redirect()->route('incidents.index')->with('error', 'Only admin or dispatcher can assign teams.');
        }

        if ($incident->status !== Incident::STATUS_PENDING) {
            return back()->withErrors(['team_id' => 'Only pending incidents can be assigned.']);
        }

        $data = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $team = Team::findOrFail($data['team_id']);

        if ($team->availability_status !== 'available') {
            return back()->withErrors(['team_id' => 'Selected team is not available.']);
        }

        Assignment::create([
            'incident_id' => $incident->id,
            'team_id' => $team->id,
            'assigned_date' => now(),
        ]);

        $incident->update(['status' => Incident::STATUS_ASSIGNED]);
        $team->update(['availability_status' => 'deployed']);
        $incident->logActivity(
            event: 'team_assigned',
            details: 'Team '.$team->team_name.' deployed to incident.',
            userId: $request->user()->id,
            meta: [
                'team_id' => $team->id,
                'team_name' => $team->team_name,
            ],
        );

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Team assigned. Incident status: Assigned.');
    }
}
