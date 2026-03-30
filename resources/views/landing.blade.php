@extends('layouts.landing')

@section('content')
<header class="landing-nav border-bottom border-light border-opacity-10">
    <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold fs-5 d-flex align-items-center gap-2">
            <span class="landing-logo-icon rounded-2 d-inline-flex align-items-center justify-content-center" aria-hidden="true">E</span>
            ERTMS
        </a>
        <nav class="d-flex align-items-center gap-2" aria-label="Primary">
            <a href="#how-it-works" class="btn btn-link text-white-50 text-decoration-none px-2 d-none d-sm-inline">How it works</a>
            <a href="#capabilities" class="btn btn-link text-white-50 text-decoration-none px-2 d-none d-sm-inline">Capabilities</a>
            <a href="{{ route('login') }}" class="btn btn-danger px-4">Sign in</a>
        </nav>
    </div>
</header>

<section class="landing-hero text-white py-5 py-lg-6">
    <div class="container py-lg-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-uppercase small fw-semibold landing-hero-kicker mb-2">Emergency operations</p>
                <h1 class="display-5 fw-bold mb-3">Coordinate response teams when every minute counts.</h1>
                <p class="lead text-white-75 mb-4 landing-hero-lead">
                    ERTMS helps dispatchers and administrators report incidents, deploy teams, track status from en route to on scene, and close cases with auditable reports—all in one secure, role-based system.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">Access the system</a>
                    <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4">See the workflow</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="landing-stat-grid p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-25">
                    <div class="row g-3 text-center small">
                        <div class="col-6">
                            <div class="landing-stat-value fw-bold fs-4">1</div>
                            <div class="text-white-50">Incident record</div>
                        </div>
                        <div class="col-6">
                            <div class="landing-stat-value fw-bold fs-4">4</div>
                            <div class="text-white-50">Role types</div>
                        </div>
                        <div class="col-6">
                            <div class="landing-stat-value fw-bold fs-4">∞</div>
                            <div class="text-white-50">Teams & assignments</div>
                        </div>
                        <div class="col-6">
                            <div class="landing-stat-value fw-bold fs-4">PDF</div>
                            <div class="text-white-50">Export reports</div>
                        </div>
                    </div>
                    <p class="mb-0 mt-3 text-white-50 small text-center">Built for training and operational readiness—not a public emergency hotline.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="how-it-works" class="py-5 bg-white">
    <div class="container">
        <h2 class="h3 fw-bold text-center mb-2">End-to-end response workflow</h2>
        <p class="text-muted text-center mb-5 mx-auto" style="max-width: 36rem;">From first report to archived closure, the system keeps roles, teams, and timestamps aligned.</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card card-ertms h-100 border-0 p-4">
                    <div class="landing-step-num mb-3">1</div>
                    <h3 class="h6 fw-bold">Incident reporting</h3>
                    <p class="text-muted small mb-0">Dispatchers log type, location, severity, and description. A unique incident ID is issued; status starts as pending.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-ertms h-100 border-0 p-4">
                    <div class="landing-step-num mb-3">2</div>
                    <h3 class="h6 fw-bold">Team assignment</h3>
                    <p class="text-muted small mb-0">An available team is assigned; status moves to assigned and the team is marked deployed.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-ertms h-100 border-0 p-4">
                    <div class="landing-step-num mb-3">3</div>
                    <h3 class="h6 fw-bold">Response tracking</h3>
                    <p class="text-muted small mb-0">Team leaders update en route and on scene, add notes, and timestamps are recorded automatically.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card card-ertms h-100 border-0 p-4">
                    <div class="landing-step-num mb-3">4</div>
                    <h3 class="h6 fw-bold">Resolution &amp; close</h3>
                    <p class="text-muted small mb-0">A final report is submitted; administrators review, close the incident, and export documentation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="capabilities" class="py-5 landing-surface">
    <div class="container">
        <h2 class="h3 fw-bold mb-4">What the platform supports</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <h3 class="h6 text-danger">Role-based access</h3>
                <p class="text-muted small mb-0">Separate experiences for administrators, dispatchers, team leaders, and responders—each sees what they need to act.</p>
            </div>
            <div class="col-md-6 col-lg-4">
                <h3 class="h6 text-danger">Teams &amp; availability</h3>
                <p class="text-muted small mb-0">Define teams, leaders, and members; availability updates when teams are deployed.</p>
            </div>
            <div class="col-md-6 col-lg-4">
                <h3 class="h6 text-danger">Reporting &amp; analytics</h3>
                <p class="text-muted small mb-0">Resolution records, filters, response-time insight, and PDF export for documentation.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white border-top">
    <div class="container text-center">
        <h2 class="h4 fw-bold mb-3">Ready to sign in?</h2>
        <p class="text-muted mb-4">Use credentials issued by your administrator. This interface is for authorized personnel only.</p>
        <a href="{{ route('login') }}" class="btn btn-danger btn-lg px-5">Sign in to ERTMS</a>
    </div>
</section>

<footer class="landing-footer py-4 mt-auto small text-center text-white-50">
    <div class="container">
        <p class="mb-1">Emergency Response Team Management System (ERTMS)</p>
        <p class="mb-0">&copy; {{ date('Y') }} — For authorized use only.</p>
    </div>
</footer>
@endsection
