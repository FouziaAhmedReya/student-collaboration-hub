@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Session Flash Alerts -->
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

    <!-- Page Header Title & Create Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Study Group Management</h1>
            <p class="text-secondary mb-0 fs-6">Create, organize, and manage your study groups with meeting schedules and locations.</p>
        </div>
        <div>
            <a href="{{ route('groups.create') }}" class="btn btn-hub-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i> Create New Group
            </a>
        </div>
    </div>

    <!-- Filters and Search Toolbar -->
    <div class="hub-card p-3 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <!-- Filter Pills -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('groups.index', array_merge(request()->except('filter', 'page'), ['filter' => 'all'])) }}"
                   class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    All Groups <span class="badge {{ $filter === 'all' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('groups.index', array_merge(request()->except('filter', 'page'), ['filter' => 'my'])) }}"
                   class="btn btn-sm {{ $filter === 'my' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    My Groups <span class="badge {{ $filter === 'my' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['my'] }}</span>
                </a>
                <a href="{{ route('groups.index', array_merge(request()->except('filter', 'page'), ['filter' => 'public'])) }}"
                   class="btn btn-sm {{ $filter === 'public' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    Public Groups <span class="badge {{ $filter === 'public' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['public'] }}</span>
                </a>
                <a href="{{ route('groups.index', array_merge(request()->except('filter', 'page'), ['filter' => 'private'])) }}"
                   class="btn btn-sm {{ $filter === 'private' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    Private Groups <span class="badge {{ $filter === 'private' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['private'] }}</span>
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('groups.index') }}" class="d-flex gap-2" style="min-width: 280px;">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search groups..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('groups.index', ['filter' => $filter]) }}" class="btn btn-outline-secondary border-start-0" title="Clear search">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>
    </div>

    <!-- Study Groups Cards Grid -->
    @if($groups->isEmpty())
        <div class="hub-card text-center py-5">
            <div class="my-4">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-people text-secondary fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark">No Study Groups Found</h4>
                <p class="text-secondary mb-4">
                    @if($search)
                        No study groups match your search "{{ $search }}".
                    @else
                        There are currently no study groups in this category.
                    @endif
                </p>
                <a href="{{ route('groups.create') }}" class="btn btn-hub-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create the First Group
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($groups as $group)
                @php
                    $isCreator = $group->isCreator(Auth::user());
                    $isAdmin = $group->isAdmin(Auth::user());
                    $isMember = $group->isMember(Auth::user());
                    $isPending = $group->isPending(Auth::user());
                    $activeCount = $group->activeMembersCount();
                    $isFull = $activeCount >= $group->max_members;
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="hub-card d-flex flex-column h-100 position-relative shadow-sm hover-shadow transition">
                        <!-- Top Header: Name and Visibility Badge -->
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <h3 class="h5 fw-bold text-dark mb-0 text-truncate" title="{{ $group->name }}">{{ $group->name }}</h3>
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

                        <!-- Course Tag -->
                        <div class="mb-2">
                            <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 fw-medium">
                                <i class="bi bi-book-half me-1"></i> {{ $group->course }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-secondary small mb-3 text-break flex-grow-0" style="min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $group->description }}
                        </p>

                        <!-- Group Meta Info Box -->
                        <div class="bg-light rounded-3 p-3 mb-3 border">
                            <div class="row g-2 small">
                                <div class="col-6">
                                    <div class="text-muted"><i class="bi bi-people-fill me-1 text-primary"></i> Members</div>
                                    <div class="fw-semibold text-dark">{{ $activeCount }} / {{ $group->max_members }} Max</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted"><i class="bi bi-calendar-event me-1 text-primary"></i> Meeting Date</div>
                                    <div class="fw-semibold text-dark">{{ $group->meeting_date ? $group->meeting_date->format('M d, Y') : 'N/A' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted"><i class="bi bi-clock me-1 text-primary"></i> Meeting Time</div>
                                    <div class="fw-semibold text-dark">{{ $group->meeting_time ? date('g:i A', strtotime($group->meeting_time)) : 'N/A' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted"><i class="bi bi-person-circle me-1 text-primary"></i> Created By</div>
                                    <div class="fw-semibold text-dark text-truncate">{{ $group->creator->name ?? 'Unknown' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Meeting Location & Map Preview -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-1 text-dark small fw-semibold mb-1">
                                <i class="bi bi-geo-alt-fill text-danger"></i>
                                <span class="text-truncate">{{ $group->location_name ?? 'Location Not Specified' }}</span>
                            </div>
                            @if($group->location_address)
                                <div class="text-secondary small text-truncate mb-2">{{ $group->location_address }}</div>
                            @endif

                            <!-- Mini Map Preview Box -->
                            <div class="rounded-3 overflow-hidden border" style="height: 120px; background-color: #f1f5f9; position: relative;">
                                @if(env('GOOGLE_MAPS_API_KEY') && $group->latitude && $group->longitude)
                                    <iframe
                                        width="100%"
                                        height="100%"
                                        style="border:0"
                                        loading="lazy"
                                        allowfullscreen
                                        src="https://www.google.com/maps/embed/v1/place?key={{ env('GOOGLE_MAPS_API_KEY') }}&q={{ $group->latitude }},{{ $group->longitude }}">
                                    </iframe>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-2" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                                        <i class="bi bi-geo-alt-fill text-danger fs-3 mb-1"></i>
                                        <span class="small fw-semibold text-dark text-truncate px-2">{{ $group->location_name ?? 'Campus / Library' }}</span>
                                        @if($group->latitude && $group->longitude)
                                            <span class="text-muted" style="font-size: 0.72rem;">📍 {{ number_format($group->latitude, 4) }}, {{ number_format($group->longitude, 4) }}</span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.72rem;">Meeting Location Preview</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Action Buttons (Pushed to bottom) -->
                        <div class="mt-auto pt-2 border-top">
                            @if($isAdmin)
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <a href="{{ route('groups.members.index', $group) }}" class="btn btn-sm btn-hub-primary flex-grow-1">
                                        <i class="bi bi-people me-1"></i> Manage Members
                                    </a>
                                    <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-outline-secondary" title="Edit Group">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Group" data-bs-toggle="modal" data-bs-target="#deleteGroupModal{{ $group->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @elseif($isMember)
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Member
                                    </span>
                                    <form method="POST" action="{{ route('groups.leave', $group) }}" onsubmit="return confirm('Are you sure you want to leave this study group?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-box-arrow-right me-1"></i> Leave
                                        </button>
                                    </form>
                                </div>
                            @elseif($isPending)
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending Approval
                                    </span>
                                    <form method="POST" action="{{ route('groups.leave', $group) }}" onsubmit="return confirm('Cancel your join request?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            Cancel Request
                                        </button>
                                    </form>
                                </div>
                            @else
                                @if($isFull)
                                    <button class="btn btn-sm btn-secondary w-100" disabled>
                                        <i class="bi bi-slash-circle me-1"></i> Group Full
                                    </button>
                                @elseif($group->visibility === 'public')
                                    <form method="POST" action="{{ route('groups.join', $group) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-hub-primary w-100">
                                            <i class="bi bi-person-plus-fill me-1"></i> Join Group
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('groups.join', $group) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-hub-outline w-100">
                                            <i class="bi bi-send me-1"></i> Request to Join
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal for this Group -->
                @if($isAdmin)
                    <div class="modal fade" id="deleteGroupModal{{ $group->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Study Group
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <p class="mb-2">Are you sure you want to permanently delete the study group <strong class="text-dark">"{{ $group->name }}"</strong>?</p>
                                    <p class="text-secondary small mb-0">This action will remove all associated group memberships and cannot be undone.</p>
                                </div>
                                <div class="modal-footer border-top-0 pt-0">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST" action="{{ route('groups.destroy', $group) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Yes, Delete Group</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
