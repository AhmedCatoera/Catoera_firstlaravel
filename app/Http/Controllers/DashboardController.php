<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $totalIncidents = Incident::count();
        $activeIncidents = Incident::whereNotIn('status', [Incident::STATUS_CLOSED])->count();
        $availableTeams = Team::where('availability_status', 'available')->count();
        $totalUsers = User::where('status', 'active')->count();

        $recentIncidents = Incident::with('creator')
            ->latest()
            ->take(5)
            ->get();

        $statusBreakdown = [
            'pending' => Incident::where('status', Incident::STATUS_PENDING)->count(),
            'assigned' => Incident::where('status', Incident::STATUS_ASSIGNED)->count(),
            'en_route' => Incident::where('status', Incident::STATUS_EN_ROUTE)->count(),
            'on_scene' => Incident::where('status', Incident::STATUS_ON_SCENE)->count(),
            'resolved' => Incident::where('status', Incident::STATUS_RESOLVED)->count(),
            'closed' => Incident::where('status', Incident::STATUS_CLOSED)->count(),
        ];

        $incidentTypeStats = Incident::query()
            ->selectRaw('incident_type, COUNT(*) as aggregate')
            ->groupBy('incident_type')
            ->orderByDesc('aggregate')
            ->limit(6)
            ->get();

        return view('dashboard.index', [
            'title' => 'Dashboard — ERTMS',
            'user' => $user,
            'totalIncidents' => $totalIncidents,
            'activeIncidents' => $activeIncidents,
            'availableTeams' => $availableTeams,
            'totalUsers' => $totalUsers,
            'recentIncidents' => $recentIncidents,
            'statusBreakdown' => $statusBreakdown,
            'incidentTypeStats' => $incidentTypeStats,
        ]);
    }
}
