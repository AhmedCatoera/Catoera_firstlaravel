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

        return view('dashboard.index', [
            'title' => 'Dashboard — ERTMS',
            'user' => $user,
            'totalIncidents' => $totalIncidents,
            'activeIncidents' => $activeIncidents,
            'availableTeams' => $availableTeams,
            'totalUsers' => $totalUsers,
            'recentIncidents' => $recentIncidents,
        ]);
    }
}
