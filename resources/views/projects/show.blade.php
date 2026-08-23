@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Back Link -->
    <div class="mb-3">
        <a href="{{ route('projects.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Project Team Finder
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

    <!-- Page Header Title & Actions Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="badge bg-light text-primary border rounded-pill px-3 py-1 fw-semibold fs-7">
                    <i class="bi bi-tag-fill me-1"></i> {{ $project->project_type }}
                </span>
                <h1 class="h2 fw-bold text-dark mb-0">{{ $project->title }}</h1>
                @if($project->isOpen() && !$project->hasReachedMaxMembers())
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold fs-7">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Recruiting
                    </span>
                @elseif($project->hasReachedMaxMembers())
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 fw-semibold fs-7">
                        <i class="bi bi-people-fill me-1"></i> Team Full
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-1 fw-semibold fs-7">
                        <i class="bi bi-lock-fill me-1"></i> Recruitment Closed
                    </span>
                @endif
            </div>
            <p class="text-secondary mb-0 fs-6">Course: <strong class="text-dark">{{ $project->course }}</strong> • Posted by <strong class="text-dark">{{ $project->creator->name ?? 'Student' }}</strong> ({{ $project->created_at->diffForHumans() }})</p>
        </div>

        <!-- Header Action Controls -->
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if($isCreator)
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-hub-outline btn-sm px-3 py-2 fw-medium">
                    <i class="bi bi-pencil me-1"></i> Edit Project
                </a>
                <form action="{{ route('projects.destroy_recruitment', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this recruitment post?');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3 py-2 fw-medium rounded-2">
                        <i class="bi bi-trash me-1"></i> Delete Post
                    </button>
                </form>
            @else
                {{-- Student Actions (Request to Join / Pending / Already Joined / Closed / Full) --}}
                @if($userMembership && $userMembership->isActive())
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6 rounded-pill d-inline-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i> Already Joined
                    </span>
                @elseif($userMembership && $userMembership->isPending())
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 fs-6 rounded-pill d-inline-flex align-items-center gap-1">
                            <i class="bi bi-clock-history"></i> Pending Approval
                        </span>
                        <form action="{{ route('projects.cancelRequest', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Withdraw your join request?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-2">
                                <i class="bi bi-x-circle me-1"></i> Cancel Request
                            </button>
                        </form>
                    </div>
                @elseif(!$project->isOpen())
                    <button class="btn btn-secondary btn-sm px-4 py-2" disabled>
                        <i class="bi bi-lock-fill me-1"></i> Recruitment Closed
                    </button>
                @elseif($project->hasReachedMaxMembers())
                    <button class="btn btn-secondary btn-sm px-4 py-2" disabled>
                        <i class="bi bi-slash-circle me-1"></i> Team Full
                    </button>
                @else
                    <form action="{{ route('projects.request', $project) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-hub-primary btn-sm px-4 py-2 shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Request to Join
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- 4 Info Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Team Capacity -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Team Capacity</div>
                    <div class="stat-value">{{ $project->current_members }} / {{ $project->required_members }} Members</div>
                </div>
            </div>
        </div>

        <!-- Meeting Date -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Sync Date</div>
                    <div class="stat-value">{{ $project->meeting_date ? $project->meeting_date->format('M d, Y') : 'TBD' }}</div>
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
                    <div class="stat-value">{{ $project->meeting_time ?? 'Flexible' }}</div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Recruitment Status</div>
                    <div class="stat-value text-capitalize">{{ $project->recruitment_status }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Project Overview, Required Skills, Active Team, and Creator Request Section -->
        <div class="col-12 col-lg-8">
            <!-- Overview Card -->
            <div class="hub-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Project Description & Objectives</h5>
                <p class="text-secondary" style="line-height: 1.75; font-size: 0.95rem; white-space: pre-line;">
                    {{ $project->description }}
                </p>
            </div>

            <!-- Required Skills Card -->
            <div class="hub-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Required Technical Skills & Technologies</h5>
                @php $skills = $project->skills_array; @endphp
                @if(!empty($skills))
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-medium rounded-3">
                                <i class="bi bi-code-slash text-primary me-1"></i> {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-secondary small mb-0">Open to all students interested in learning and collaborating.</p>
                @endif
            </div>

            <!-- Active Team Members Card -->
            <div class="hub-card p-4 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-people-fill text-primary me-2"></i> Project Team Members
                    </h5>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                        {{ $project->activeMembers->count() }} / {{ $project->required_members }} Members
                    </span>
                </div>

                <div class="row g-3">
                    @forelse($project->activeMembers as $activeMember)
                        @php $mUser = $activeMember->user; @endphp
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light border d-flex align-items-center gap-3">
                                @if($mUser && $mUser->profile && $mUser->profile->profile_photo)
                                    <img src="{{ asset('storage/' . $mUser->profile->profile_photo) }}" alt="{{ $mUser->name }}" class="rounded-circle border" width="44" height="44" style="object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 44px; height: 44px;">
                                        {{ strtoupper(substr($mUser->name ?? 'S', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $mUser->name ?? 'Student' }}</h6>
                                        @if($activeMember->isCreator())
                                            <span class="badge bg-primary text-white rounded-pill" style="font-size: 0.68rem;">Creator</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">Member</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small text-truncate">{{ $mUser->email ?? '' }}</div>
                                    @if($mUser && $mUser->profile && $mUser->profile->department)
                                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $mUser->profile->department }} • {{ $mUser->profile->semester ?? '' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-secondary small mb-0">No active members yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Creator-Only Team Requests Management Section -->
            @if($isCreator)
                <div class="hub-card p-4 shadow-sm border-primary mb-4" id="teamRequestsSection">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-inbox-fill text-primary me-1"></i> Team Requests
                            </h5>
                            @if($project->pendingRequests->count() > 0)
                                <span class="badge bg-danger rounded-pill">{{ $project->pendingRequests->count() }} Pending</span>
                            @endif
                        </div>
                        <span class="text-secondary small">Only visible to project creator</span>
                    </div>

                    @if($project->pendingRequests->isEmpty())
                        <div class="text-center py-4 text-secondary">
                            <i class="bi bi-check-all fs-2 text-success d-block mb-1"></i>
                            <p class="mb-0 small fw-medium">No pending join requests at this time.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Department & Semester</th>
                                        <th>Requested</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->pendingRequests as $pendingReq)
                                        @php $applicant = $pendingReq->user; @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($applicant && $applicant->profile && $applicant->profile->profile_photo)
                                                        <img src="{{ asset('storage/' . $applicant->profile->profile_photo) }}" alt="{{ $applicant->name }}" class="rounded-circle border" width="36" height="36" style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                            {{ strtoupper(substr($applicant->name ?? 'A', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $applicant->name ?? 'Applicant' }}</div>
                                                        <div class="text-muted small">{{ $applicant->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark small fw-medium">
                                                    {{ $applicant->profile->department ?? 'General' }}
                                                </span>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $applicant->profile->semester ?? 'Student' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-secondary small">{{ $pendingReq->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if($project->hasReachedMaxMembers())
                                                    <span class="badge bg-warning-subtle text-warning border me-2">Team Full</span>
                                                @else
                                                    <form action="{{ route('projects.requests.approve', [$project, $pendingReq]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success px-3 py-1 fw-medium shadow-sm">
                                                            <i class="bi bi-check-lg me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('projects.requests.reject', [$project, $pendingReq]) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Decline this applicant request?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1 fw-medium">
                                                        <i class="bi bi-x-lg me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Column: Creator Info, Location & Map Integration -->
        <div class="col-12 col-lg-4">
            <!-- Creator Information Card -->
            <div class="hub-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Project Creator</h5>
                @php $creator = $project->creator; @endphp
                <div class="d-flex align-items-center gap-3">
                    @if($creator && $creator->profile && $creator->profile->profile_photo)
                        <img src="{{ asset('storage/' . $creator->profile->profile_photo) }}" alt="{{ $creator->name }}" class="rounded-circle border" width="56" height="56" style="object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 56px; height: 56px;">
                            {{ strtoupper(substr($creator->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $creator->name ?? 'Student' }}</h6>
                        <div class="text-secondary small">{{ $creator->email ?? '' }}</div>
                        @if($creator && $creator->profile && $creator->profile->department)
                            <div class="text-muted small">{{ $creator->profile->department }} • {{ $creator->profile->semester ?? 'Student' }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Location & Map Card -->
            <div class="hub-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Project Meeting Location</h5>

                @if($project->location_name)
                    @php
                        $locationQuery = urlencode($project->location_name . ' ' . ($project->location_address ?? ''));
                        $mapsUrl = "https://www.google.com/maps/search/?api=1&query=" . ($project->latitude && $project->longitude ? "{$project->latitude},{$project->longitude}" : $locationQuery);
                    @endphp

                    <!-- Leaflet / OpenFreeMap Interactive Map Card -->
                    @if($project->latitude && $project->longitude)
                        <div id="projectLeafletMap" class="rounded-3 border overflow-hidden mb-3" style="height: 200px; width: 100%;"></div>
                        @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                HubMap.initDisplayMap(
                                    'projectLeafletMap',
                                    {{ (float)$project->latitude }},
                                    {{ (float)$project->longitude }},
                                    "{{ addslashes($project->location_name ?? 'Project Meeting Location') }}"
                                );
                            });
                        </script>
                        @endpush
                    @else
                        <!-- Graceful Location Fallback (Works 100% without coordinates) -->
                        <div class="rounded-3 border overflow-hidden position-relative mb-3 bg-light d-flex align-items-center justify-content-center p-3 text-center" style="min-height: 140px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                            <div>
                                <i class="bi bi-geo-alt-fill text-danger fs-1 d-block mb-1"></i>
                                <span class="fw-bold text-dark small d-block">{{ $project->location_name }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">Map location unavailable</div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-light rounded-3 p-3 border mb-3">
                        <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $project->location_name }}</div>
                        <div class="text-secondary small mb-3">{{ $project->location_address ?? 'Campus / Lab Room' }}</div>

                        <!-- Open in OpenStreetMap / External Navigation Link -->
                        @if($project->latitude && $project->longitude)
                            <a href="https://www.openstreetmap.org/?mlat={{ $project->latitude }}&mlon={{ $project->longitude }}#map=17/{{ $project->latitude }}/{{ $project->longitude }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-box-arrow-up-right"></i> Open in OpenStreetMap
                            </a>
                        @else
                            <a href="https://www.openstreetmap.org/search?query={{ urlencode($project->location_name . ' ' . ($project->location_address ?? '')) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-box-arrow-up-right"></i> Search in OpenStreetMap
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-secondary small">
                        <i class="bi bi-geo text-muted fs-3 d-block mb-2"></i>
                        No physical meeting location specified.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
