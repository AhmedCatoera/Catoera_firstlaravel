<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');

    Route::middleware('role:admin,dispatcher')->group(function () {
        Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
        Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    });

    Route::post('/incidents/{incident}/assign', [AssignmentController::class, 'store'])
        ->middleware('role:admin,dispatcher')
        ->name('assignments.store');

    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');

    Route::middleware('role:admin')->group(function () {
        Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
        Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
        Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
        Route::post('/incidents/{incident}/close', [IncidentController::class, 'close'])->name('incidents.close');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');

        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    });

    Route::middleware('role:admin,team_leader')->group(function () {
        Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.status');
    });

    Route::middleware('role:admin,team_leader')->group(function () {
        Route::get('/incidents/{incident}/report/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/incidents/{incident}/report', [ReportController::class, 'store'])->name('reports.store');
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/incidents/{incident}/export-pdf', [ReportController::class, 'pdfIncident'])->name('incidents.export-pdf');
});
