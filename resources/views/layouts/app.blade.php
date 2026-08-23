<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Student Collaboration Hub') }} - Module 1</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet.js CSS for OpenFreeMap & OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Custom Style matching Figma -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .navbar-hub {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .navbar-brand-hub {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            text-decoration: none;
            color: #1e293b;
        }

        .navbar-brand-hub .brand-icon {
            width: 38px;
            height: 38px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .navbar-brand-hub .brand-text {
            line-height: 1.1;
        }

        .navbar-brand-hub .brand-text .title-top {
            font-size: 1.05rem;
            color: #2563eb;
            font-weight: 700;
        }

        .navbar-brand-hub .brand-text .title-bottom {
            font-size: 0.95rem;
            color: #475569;
            font-weight: 600;
        }

        .nav-hub-links {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            margin-bottom: 0;
            list-style: none;
        }

        .nav-hub-link {
            position: relative;
            padding: 0.75rem 0;
            text-decoration: none;
            color: #475569;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .nav-hub-link:hover {
            color: #2563eb;
        }

        .nav-hub-link.active {
            color: #2563eb;
            font-weight: 700;
        }

        .nav-hub-link.active::after {
            content: '';
            position: absolute;
            bottom: -0.6rem;
            left: 0;
            right: 0;
            height: 3px;
            background-color: #2563eb;
            border-radius: 3px 3px 0 0;
        }

        .hub-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: 0.825rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0;
        }

        .badge-interest {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #c7d2fe;
            margin-right: 6px;
            margin-bottom: 8px;
        }

        .badge-interest .btn-remove-interest {
            background: none;
            border: none;
            color: #4338ca;
            padding: 0;
            font-size: 0.85rem;
            cursor: pointer;
        }

        /* Interest suggestion pills in the Add Interest modal */
        .interest-suggestion-pill {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            border: 1px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
            white-space: nowrap;
        }

        .interest-suggestion-pill:hover:not(:disabled) {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .interest-suggestion-pill.already-added {
            background-color: #dcfce7;
            border-color: #86efac;
            color: #15803d;
            cursor: default;
            opacity: 0.8;
        }

        .progress-hub {
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-hub-bar {
            background-color: #2563eb;
            border-radius: 6px;
            height: 100%;
        }

        .btn-hub-primary {
            background-color: #2563eb;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border: none;
            transition: all 0.2s;
        }

        .btn-hub-primary:hover {
            background-color: #1d4ed8;
            color: white;
        }

        .btn-hub-outline {
            background-color: #ffffff;
            color: #2563eb;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s;
        }

        .btn-hub-outline:hover {
            background-color: #f1f5f9;
            border-color: #94a3b8;
            color: #1d4ed8;
        }

        .btn-add-item {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .btn-add-item:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .project-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .project-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .project-icon {
            width: 36px;
            height: 36px;
            background: #f1f5f9;
            color: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .portfolio-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .portfolio-icon {
            width: 36px;
            height: 36px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            background-color: #f1f5f9;
        }

        .card-highlight-border {
            border: 2px solid #2563eb !important;
        }
    </style>

    @if(env('GOOGLE_MAPS_API_KEY'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    @endif
</head>
<body>
    <!-- Top Header / Navigation -->
    <nav class="navbar navbar-expand-lg navbar-hub sticky-top">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand-hub" href="{{ route('profile.index') }}">
                <div class="brand-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="brand-text">
                    <div class="title-top">Student</div>
                    <div class="title-bottom">Collaboration Hub</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="nav-hub-links mx-auto my-2 my-lg-0">
                    <li>
                        <a class="nav-hub-link" href="#">Dashboard</a>
                    </li>
                    <li>
                        <a class="nav-hub-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">My Profile & Skills</a>
                    </li>
                    <li>
                        <a class="nav-hub-link" href="#">Marketplace</a>
                    </li>
                    <li>
                        <a class="nav-hub-link {{ request()->routeIs('groups.*') ? 'active' : '' }}" href="{{ route('groups.index') }}">Groups</a>
                    </li>
                    <li>
                        <a class="nav-hub-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Projects</a>
                    </li>
                    <li>
                        <a class="nav-hub-link" href="#">Chat</a>
                    </li>
                    <li>
                        <a class="nav-hub-link" href="#">Events</a>
                    </li>
                </ul>

                @auth
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-secondary position-relative me-2 fs-5">
                        <i class="bi bi-bell"></i>
                    </a>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark fw-semibold" data-bs-toggle="dropdown">
                            @if(Auth::user()->profile && Auth::user()->profile->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile->profile_photo) }}" alt="Avatar" class="rounded-circle me-2" width="36" height="36">
                            @else
                                <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person-fill text-secondary fs-5"></i>
                                </div>
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-menu-item dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Body Container -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet.js JS for OpenFreeMap & OpenStreetMap -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Reusable HubMap library -->
    <script src="{{ asset('js/hub-map.js') }}"></script>
    @stack('scripts')
</body>
</html>
