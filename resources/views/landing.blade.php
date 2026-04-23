@extends('layouts.landing')

@section('content')
<header class="landing2-nav">
    <div class="container py-3 d-flex align-items-center justify-content-between gap-3">
        <a href="{{ route('home') }}" class="landing2-brand text-decoration-none">
            <span class="landing2-brand-mark">E</span>
            <span>ERTMS</span>
        </a>
        <nav class="d-flex align-items-center gap-2">
            <a href="#features" class="landing2-link d-none d-md-inline">Features</a>
            <a href="#workflow" class="landing2-link d-none d-md-inline">Workflow</a>
            <a href="#roles" class="landing2-link d-none d-md-inline">Roles</a>
            <a href="{{ route('login') }}" class="btn btn-danger px-4">Sign in</a>
        </nav>
    </div>
</header>

<section class="landing2-hero">
    <div class="container py-5 py-lg-6 position-relative">
        <div class="landing2-glow landing2-glow-red"></div>
        <div class="landing2-glow landing2-glow-blue"></div>
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <span class="landing2-chip">Emergency Coordination Platform</span>
                <h1 class="landing2-title mt-3 mb-3">Command, dispatch, and close incidents in one mission-ready system.</h1>
                <p class="landing2-subtitle mb-4">
                    ERTMS helps control rooms and field teams move from incident intake to resolution using categorized workflows, map intelligence, team dispatching, and evidence-backed reporting.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">Open Console</a>
                    <a href="#workflow" class="btn btn-outline-light btn-lg px-4">View Workflow</a>
                </div>
                <div class="landing2-metrics row g-2 mt-4">
                    <div class="col-4"><div class="landing2-metric"><strong>Live</strong><span>Ops Board</span></div></div>
                    <div class="col-4"><div class="landing2-metric"><strong>SLA</strong><span>Queue Alerts</span></div></div>
                    <div class="col-4"><div class="landing2-metric"><strong>360°</strong><span>Incident Trace</span></div></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="landing2-panel">
                    <h2 class="h6 text-uppercase mb-3 text-danger">Mission Snapshot</h2>
                    <ul class="list-unstyled mb-0 small">
                        <li class="landing2-line"><span>Incident Intake</span><strong>Categorized + Geotagged</strong></li>
                        <li class="landing2-line"><span>Dispatch Routing</span><strong>Priority Driven</strong></li>
                        <li class="landing2-line"><span>Field Updates</span><strong>Timestamped</strong></li>
                        <li class="landing2-line"><span>Resolution Reports</span><strong>Structured + Photos</strong></li>
                        <li class="landing2-line"><span>Command Summary</span><strong>Printable / Exportable</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="landing2-section bg-white">
    <div class="container py-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Core Capabilities</h2>
            <p class="text-muted mb-0">Built for fast decision-making and operational clarity.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-lg-3"><div class="landing2-card h-100"><h3>Smart Intake</h3><p>Incident type categories, mapped locations, and automatic queueing.</p></div></div>
            <div class="col-md-6 col-lg-3"><div class="landing2-card h-100"><h3>Dispatch Control</h3><p>Assign available teams with role-based control for admin and dispatcher.</p></div></div>
            <div class="col-md-6 col-lg-3"><div class="landing2-card h-100"><h3>Field Execution</h3><p>Team leaders update status and progress from en route to on scene.</p></div></div>
            <div class="col-md-6 col-lg-3"><div class="landing2-card h-100"><h3>Resolution Intelligence</h3><p>Categorized reports, follow-up actions, and attached photo evidence.</p></div></div>
        </div>
    </div>
</section>

<section id="workflow" class="landing2-section landing2-surface">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Operational Workflow</h2>
            <p class="text-muted mb-0">One process, fully tracked from first alert to closure.</p>
        </div>
        <div class="landing2-timeline">
            <div class="landing2-step"><span>1</span><div><h3>Report</h3><p>Incident is created and placed in pending queue.</p></div></div>
            <div class="landing2-step"><span>2</span><div><h3>Assign</h3><p>Dispatcher selects team and deploys resources.</p></div></div>
            <div class="landing2-step"><span>3</span><div><h3>Respond</h3><p>Leader tracks movement, scene status, and notes.</p></div></div>
            <div class="landing2-step"><span>4</span><div><h3>Resolve</h3><p>Categorized resolution report with photos is submitted.</p></div></div>
            <div class="landing2-step"><span>5</span><div><h3>Close</h3><p>Admin validates completion and archives incident record.</p></div></div>
        </div>
    </div>
</section>

<section id="roles" class="landing2-section bg-white">
    <div class="container py-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Categorized by Role</h2>
            <p class="text-muted mb-0">Each account sees only what they need to act.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-4"><div class="landing2-role h-100"><h3>Admin</h3><p>Manage users, teams, incidents, closure, analytics, and exports.</p></div></div>
            <div class="col-md-4"><div class="landing2-role h-100"><h3>Dispatcher</h3><p>Create incidents, assign teams, and monitor live operations board.</p></div></div>
            <div class="col-md-4"><div class="landing2-role h-100"><h3>Staff / Team Leader</h3><p>View assigned incidents, update field status, and submit final reports.</p></div></div>
        </div>
    </div>
</section>

<section class="landing2-section landing2-cta">
    <div class="container py-5 text-center">
        <h2 class="fw-bold text-white mb-3">Ready for deployment?</h2>
        <p class="text-white-75 mb-4">Sign in with your authorized account and open the ERTMS command interface.</p>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5">Sign in to ERTMS</a>
    </div>
</section>

<footer class="landing-footer py-4 mt-auto small text-center text-white-50">
    <div class="container">
        <p class="mb-1">Emergency Response Team Management System (ERTMS)</p>
        <p class="mb-0">&copy; {{ date('Y') }} — For authorized operational use only.</p>
    </div>
</footer>
@endsection
