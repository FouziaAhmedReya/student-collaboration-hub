@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Back Link -->
    <div class="mb-3">
        <a href="{{ route('groups.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Study Groups
        </a>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header Title & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h2 fw-bold text-dark mb-0">{{ $group->name }}</h1>
                @if($group->visibility === 'public')
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-7 fw-semibold">
                        <i class="bi bi-globe me-1"></i> Public
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fs-7 fw-semibold">
                        <i class="bi bi-lock-fill me-1"></i> Private
                    </span>
                @endif
            </div>
            <p class="text-secondary mb-0 fs-6">Course: <strong class="text-dark">{{ $group->course }}</strong> • Created by <strong class="text-dark">{{ $group->creator->name ?? 'Admin' }}</strong></p>
        </div>

        <div class="d-flex gap-2 align-items-center">
            @if($membershipState === 'creator' || $membershipState === 'admin')
                <a href="{{ route('groups.members', ['group' => $group, 'tab' => 'all']) }}" class="btn btn-hub-outline btn-sm px-3 py-2 fw-medium">
                    <i class="bi bi-people me-1"></i> Members ({{ $activeCount }})
                </a>
                <a href="{{ route('groups.members', ['group' => $group, 'tab' => 'pending']) }}" class="btn btn-hub-primary btn-sm px-3 py-2 fw-medium position-relative">
                    <i class="bi bi-person-check me-1"></i> Manage Requests
                    @if($pendingCount > 0)
                        <span class="badge rounded-pill bg-danger ms-1">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            @elseif($membershipState === 'active')
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> You are a Member
                </span>
                <a href="{{ route('groups.members', ['group' => $group, 'tab' => 'all']) }}" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-medium">
                    <i class="bi bi-people me-1"></i> View Members ({{ $activeCount }})
                </a>
            @elseif($membershipState === 'pending')
                <!-- Pending Join Request State for Student -->
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-clock-history me-1"></i> Join Request Pending
                    </span>
                    <form action="{{ route('groups.cancelRequest', $group) }}" method="POST" onsubmit="return confirm('Withdraw your request to join this group?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-medium">
                            Cancel Request
                        </button>
                    </form>
                </div>
            @else
                <!-- No Request / Not a Member -> Send Request to Join -->
                @if($group->hasReachedMaxMembers())
                    <button class="btn btn-secondary btn-sm px-4 py-2 fw-medium" disabled>
                        Group is Full ({{ $activeCount }}/{{ $group->max_members }})
                    </button>
                @else
                    <form action="{{ route('groups.request', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-hub-primary btn-sm px-4 py-2 fw-medium shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Request to Join Group
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- 4 Stat / Info Cards Row matching Module 1 & 2 layout -->
    <div class="row g-3 mb-4">
        <!-- Meeting Date -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Meeting Date</div>
                    <div class="stat-value">{{ $group->meeting_date ? $group->meeting_date->format('M d, Y') : 'TBD' }}</div>
                </div>
            </div>
        </div>

        <!-- Meeting Time -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Meeting Time</div>
                    <div class="stat-value">{{ $group->meeting_time ?? 'TBD' }}</div>
                </div>
            </div>
        </div>

        <!-- Capacity -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Capacity</div>
                    <div class="stat-value">{{ $activeCount }} / {{ $group->max_members }} Active</div>
                </div>
            </div>
        </div>

        <!-- Visibility / Access -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Access Mode</div>
                    <div class="stat-value text-capitalize">{{ $group->visibility }} Group</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: About & Active Members List -->
        <div class="col-12 col-lg-8">
            <!-- About Group Card -->
            <div class="hub-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">About the Study Group</h5>
                <p class="text-secondary" style="line-height: 1.7;">
                    {{ $group->description }}
                </p>
            </div>

            <!-- Active Members Card -->
            <div class="hub-card p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">Active Members ({{ $activeCount }})</h5>
                    @if($group->isAdmin(auth()->user()))
                        <a href="{{ route('groups.members', ['group' => $group, 'tab' => 'all']) }}" class="small text-primary text-decoration-none fw-medium">
                            Manage All Members →
                        </a>
                    @endif
                </div>

                <div class="row g-3">
                    @forelse($group->memberships->where('status', 'active') as $membership)
                        @php $u = $membership->user; @endphp
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light">
                                @if($u->profile && $u->profile->profile_photo)
                                    <img src="{{ asset('storage/' . $u->profile->profile_photo) }}" alt="{{ $u->name }}" class="rounded-circle border" width="44" height="44" style="object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $u->name ?? 'User' }}</div>
                                    <div class="text-muted small">
                                        <span class="badge {{ $membership->role === 'admin' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-secondary border' }} rounded-pill text-capitalize" style="font-size: 0.72rem;">
                                            {{ $membership->role }}
                                        </span>
                                        @if($u->profile && $u->profile->department)
                                            • {{ $u->profile->department }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-secondary small py-2">No active members yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Location / Venue Details -->
        <div class="col-12 col-lg-4">
            <div class="hub-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Meeting Location</h5>

                @if($group->location_name)
                    <div class="rounded-3 border overflow-hidden position-relative mb-3 bg-light d-flex align-items-center justify-content-center p-3 text-center" style="min-height: 120px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                        <div>
                            <i class="bi bi-geo-alt-fill text-danger fs-2 d-block mb-1"></i>
                            <span class="fw-bold text-dark small">{{ $group->location_name }}</span>
                            @if($group->latitude && $group->longitude)
                                <div class="text-muted" style="font-size: 0.72rem;">📍 {{ number_format($group->latitude, 4) }}, {{ number_format($group->longitude, 4) }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-3 border mb-3">
                        <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $group->location_name }}</div>
                        <div class="text-secondary small mb-3">{{ $group->location_address ?? 'Campus / Library Area' }}</div>

                        <div class="text-secondary small" style="font-size: 0.8rem; line-height: 1.7;">
                            <div><i class="bi bi-check2 text-success me-1"></i> Quiet Environment</div>
                            <div><i class="bi bi-wifi text-success me-1"></i> Wifi Available</div>
                            <div><i class="bi bi-clock text-success me-1"></i> Study Area Access</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 text-secondary small">
                        <i class="bi bi-geo text-muted fs-3 d-block mb-2"></i>
                        No specific meeting location specified.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
