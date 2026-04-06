@php
    $u = auth()->user();
@endphp
<nav class="navbar navbar-expand-lg navbar-dark ertms-navbar mb-0">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">ERTMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ertmsNav" aria-controls="ertmsNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="ertmsNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('incidents.index') || request()->routeIs('incidents.show') || request()->routeIs('incidents.edit') ? 'active' : '' }}" href="{{ route('incidents.index') }}">Incidents</a>
                </li>
                @if($u->isAdmin() || $u->isStaff())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('incidents.create') ? 'active' : '' }}" href="{{ route('incidents.create') }}">Create Incident</a>
                    </li>
                @endif
                @if($u->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}">Teams</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">Profile</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $u->name }}
                        <span class="badge bg-light text-dark ms-1">{{ \App\Models\User::roleLabels()[$u->role] ?? $u->role }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('logout') }}" method="post" class="px-3 py-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
