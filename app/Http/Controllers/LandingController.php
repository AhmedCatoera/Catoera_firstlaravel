<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Public landing page; authenticated users go straight to the dashboard.
     */
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('landing', [
            'title' => 'ERTMS — Emergency Response Team Management',
        ]);
    }
}
