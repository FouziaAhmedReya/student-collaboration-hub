@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css"/>
    <style>
        header, header * {
            text-decoration: none !important;
        }
        header a {
            color: inherit !important;
            font-family: inherit !important;
        }
        header a.text-red-600 {
            color: rgb(220 38 38) !important;
        }
        .hub-card { background: #fff; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; }
        .btn-hub-primary { background-color: #2563eb; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-weight: 600; }
        .btn-hub-primary:hover { background-color: #1d4ed8; color: #fff; }
        .btn-hub-outline { background-color: transparent; color: #2563eb; border: 1px solid #2563eb; border-radius: 0.5rem; padding: 0.5rem 1rem; font-weight: 600; }
        .btn-hub-outline:hover { background-color: #eff6ff; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <script src="https://unpkg.com/@maplibre/maplibre-gl-leaflet@0.0.22/leaflet-maplibre-gl.js"></script>
    <script src="{{ asset('js/hub-map.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Back Link -->
    <div class="mb-2">
        <a href="{{ route('project-recruitments.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Project Team Finder
        </a>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 py-2 shadow-sm small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3 py-2 shadow-sm small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-3 py-2 shadow-sm small" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header Title & Actions Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                    <i class="bi bi-tag-fill me-1"></i> {{ $project->project_type }}
                </span>
                <h1 class="h4 fw-bold text-dark mb-0">{{ $project->title }}</h1>
                @if($project->isOpen() && !$project->hasReachedMaxMembers())
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i> Recruiting
                    </span>
                @elseif($project->hasReachedMaxMembers())
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                        <i class="bi bi-people-fill me-1"></i> Team Full
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                        <i class="bi bi-lock-fill me-1"></i> Recruitment Closed
                    </span>
                @endif
            </div>
            <p class="text-secondary mb-0 small">Course: <strong class="text-dark">{{ $project->course }}</strong> • Posted by <strong class="text-dark">{{ $project->creator->name ?? 'Student' }}</strong> ({{ $project->created_at->diffForHumans() }})</p>
        </div>

        <!-- Header Action Controls -->
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if($isCreator)
                <a href="{{ route('project-recruitments.edit', $project) }}" class="btn btn-hub-outline btn-sm px-3 py-1.5 fw-medium">
                    <i class="bi bi-pencil me-1"></i> Edit Project
                </a>
                <form action="{{ route('project-recruitments.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this recruitment post?');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3 py-1.5 fw-medium rounded-2">
                        <i class="bi bi-trash me-1"></i> Delete Post
                    </button>
                </form>
            @else
                {{-- Student Actions (Request to Join / Pending / Already Joined / Closed / Full) --}}
                @if($userMembership && $userMembership->isActive())
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 small">
                        <i class="bi bi-check-circle-fill"></i> Already Joined
                    </span>
                @elseif($userMembership && $userMembership->isPending())
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill d-inline-flex align-items-center gap-1 small">
                            <i class="bi bi-clock-history"></i> Pending Approval
                        </span>
                        <form action="{{ route('project-recruitments.join.cancel', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Withdraw your join request?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1">
                                <i class="bi bi-x-circle me-1"></i> Cancel Request
                            </button>
                        </form>
                    </div>
                @elseif(!$project->isOpen())
                    <button class="btn btn-secondary btn-sm px-3 py-1.5" disabled>
                        <i class="bi bi-lock-fill me-1"></i> Recruitment Closed
                    </button>
                @elseif($project->hasReachedMaxMembers())
                    <button class="btn btn-secondary btn-sm px-3 py-1.5" disabled>
                        <i class="bi bi-slash-circle me-1"></i> Team Full
                    </button>
                @else
                    <form action="{{ route('project-recruitments.join.store', $project) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-hub-primary btn-sm px-3 py-1.5 shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Request to Join
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- 4 Info Cards Row (Compact) -->
    <div class="row g-2 mb-3">
        <!-- Team Capacity -->
        <div class="col-6 col-md-3">
            <div class="stat-card" style="padding: 0.65rem 0.85rem; gap: 0.75rem; border-radius: 10px;">
                <div class="stat-icon" style="width: 36px; height: 36px; font-size: 1.1rem; border-radius: 8px;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label" style="font-size: 0.72rem; margin-bottom: 0;">Team Capacity</div>
                    <div class="stat-value" style="font-size: 0.92rem;">{{ $project->current_members }} / {{ $project->required_members }} Members</div>
                </div>
            </div>
        </div>

        <!-- Meeting Date -->
        <div class="col-6 col-md-3">
            <div class="stat-card" style="padding: 0.65rem 0.85rem; gap: 0.75rem; border-radius: 10px;">
                <div class="stat-icon" style="width: 36px; height: 36px; font-size: 1.1rem; border-radius: 8px;">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="stat-label" style="font-size: 0.72rem; margin-bottom: 0;">Sync Date</div>
                    <div class="stat-value" style="font-size: 0.92rem;">{{ $project->meeting_date ? $project->meeting_date->format('M d, Y') : 'TBD' }}</div>
                </div>
            </div>
        </div>

        <!-- Meeting Time -->
        <div class="col-6 col-md-3">
            <div class="stat-card" style="padding: 0.65rem 0.85rem; gap: 0.75rem; border-radius: 10px;">
                <div class="stat-icon" style="width: 36px; height: 36px; font-size: 1.1rem; border-radius: 8px;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <div class="stat-label" style="font-size: 0.72rem; margin-bottom: 0;">Meeting Time</div>
                    <div class="stat-value" style="font-size: 0.92rem;">{{ $project->meeting_time ?? 'Flexible' }}</div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="col-6 col-md-3">
            <div class="stat-card" style="padding: 0.65rem 0.85rem; gap: 0.75rem; border-radius: 10px;">
                <div class="stat-icon" style="width: 36px; height: 36px; font-size: 1.1rem; border-radius: 8px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-label" style="font-size: 0.72rem; margin-bottom: 0;">Status</div>
                    <div class="stat-value text-capitalize" style="font-size: 0.92rem;">{{ $project->recruitment_status }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Left Column: Project Overview, Required Skills, Active Team, and Creator Request Section -->
        <div class="col-12 col-lg-8">
            <!-- Overview Card -->
            <div class="hub-card p-3 mb-3 shadow-sm">
                <h6 class="fw-bold text-dark mb-2 pb-2 border-bottom">
                    <i class="bi bi-file-text-fill text-primary me-1"></i> Project Description & Objectives
                </h6>
                <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 0.875rem; white-space: pre-line;">{{ $project->description }}</p>
            </div>

            <!-- Required Skills Card -->
            <div class="hub-card p-3 mb-3 shadow-sm">
                <h6 class="fw-bold text-dark mb-2 pb-2 border-bottom">
                    <i class="bi bi-gear-wide-connected text-primary me-1"></i> Required Technical Skills & Technologies
                </h6>
                @php $skills = $project->skills_array; @endphp
                @if(!empty($skills))
                    <div class="d-flex flex-wrap gap-1.5">
                        @foreach($skills as $skill)
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-medium rounded-2" style="font-size: 0.8rem;">
                                <i class="bi bi-code-slash text-primary me-1"></i> {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-secondary small mb-0">Open to all students interested in learning and collaborating.</p>
                @endif
            </div>

            <!-- Active Team Members Card -->
            <div class="hub-card p-3 mb-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-people-fill text-primary me-1"></i> Project Team Members
                    </h6>
                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem;">
                        {{ $project->activeMembers->count() }} / {{ $project->required_members }} Members
                    </span>
                </div>

                <div class="row g-2">
                    @forelse($project->activeMembers as $activeMember)
                        @php $mUser = $activeMember->user; @endphp
                        <div class="col-12 col-md-6">
                            <div class="p-2 rounded-2 bg-light border d-flex align-items-center gap-2">
                                @if($mUser && $mUser->profile && $mUser->profile->profile_photo)
                                    <img src="{{ asset('storage/' . $mUser->profile->profile_photo) }}" alt="{{ $mUser->name }}" class="rounded-circle border" width="36" height="36" style="object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($mUser->name ?? 'S', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.85rem;">{{ $mUser->name ?? 'Student' }}</h6>
                                        @if($activeMember->isCreator())
                                            <span class="badge bg-primary text-white rounded-pill" style="font-size: 0.65rem;">Creator</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.65rem;">Member</span>
                                        @endif
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $mUser->email ?? '' }}</div>
                                    @if($mUser && $mUser->profile && $mUser->profile->department)
                                        <div class="text-secondary" style="font-size: 0.72rem;">{{ $mUser->profile->department }} • {{ $mUser->profile->semester ?? '' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-secondary small mb-0 py-1">No active members yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Creator-Only Team Requests Management Section -->
            @if($isCreator)
                <div class="hub-card p-3 shadow-sm border-primary mb-3" id="teamRequestsSection">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-inbox-fill text-primary me-1"></i> Team Requests
                            </h6>
                            @if($project->pendingRequests->count() > 0)
                                <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">{{ $project->pendingRequests->count() }} Pending</span>
                            @endif
                        </div>
                        <span class="text-secondary" style="font-size: 0.75rem;">Only visible to creator</span>
                    </div>

                    @if($project->pendingRequests->isEmpty())
                        <div class="text-center py-3 text-secondary">
                            <i class="bi bi-check-all fs-3 text-success d-block mb-1"></i>
                            <p class="mb-0 small fw-medium">No pending join requests at this time.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small" style="font-size: 0.75rem;">
                                    <tr>
                                        <th class="py-2">Applicant</th>
                                        <th class="py-2">Department & Semester</th>
                                        <th class="py-2">Requested</th>
                                        <th class="text-end py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->pendingRequests as $pendingReq)
                                        @php $applicant = $pendingReq->user; @endphp
                                        <tr>
                                            <td class="py-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($applicant && $applicant->profile && $applicant->profile->profile_photo)
                                                        <img src="{{ asset('storage/' . $applicant->profile->profile_photo) }}" alt="{{ $applicant->name }}" class="rounded-circle border" width="32" height="32" style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                            {{ strtoupper(substr($applicant->name ?? 'A', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.825rem;">{{ $applicant->name ?? 'Applicant' }}</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $applicant->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <span class="text-dark fw-medium" style="font-size: 0.78rem;">
                                                    {{ $applicant->profile->department ?? 'General' }}
                                                </span>
                                                <div class="text-muted" style="font-size: 0.72rem;">
                                                    {{ $applicant->profile->semester ?? 'Student' }}
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <span class="text-secondary" style="font-size: 0.75rem;">{{ $pendingReq->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td class="text-end py-2">
                                                @if($project->hasReachedMaxMembers())
                                                    <span class="badge bg-warning-subtle text-warning border me-1 small">Team Full</span>
                                                @else
                                                    <form action="{{ route('project-recruitments.members.approve', [$project, $pendingReq]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success px-2.5 py-1 fw-medium shadow-sm" style="font-size: 0.75rem;">
                                                            <i class="bi bi-check-lg me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('project-recruitments.members.reject', [$project, $pendingReq]) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Decline this applicant request?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1 fw-medium" style="font-size: 0.75rem;">
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
            <div class="hub-card p-3 mb-3 shadow-sm">
                <h6 class="fw-bold text-dark mb-2 pb-2 border-bottom">
                    <i class="bi bi-person-badge-fill text-primary me-1"></i> Project Creator
                </h6>
                @php $creator = $project->creator; @endphp
                <div class="d-flex align-items-center gap-2.5">
                    @if($creator && $creator->profile && $creator->profile->profile_photo)
                        <img src="{{ asset('storage/' . $creator->profile->profile_photo) }}" alt="{{ $creator->name }}" class="rounded-circle border" width="44" height="44" style="object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 44px; height: 44px;">
                            {{ strtoupper(substr($creator->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">{{ $creator->name ?? 'Student' }}</h6>
                        <div class="text-secondary small" style="font-size: 0.78rem;">{{ $creator->email ?? '' }}</div>
                        @if($creator && $creator->profile && $creator->profile->department)
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $creator->profile->department }} • {{ $creator->profile->semester ?? 'Student' }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Location & Map Card -->
            <div class="hub-card p-3 shadow-sm">
                <h6 class="fw-bold text-dark mb-2 pb-2 border-bottom">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> Meeting Location
                </h6>

                @if($project->location_name)
                    @php
                        $locationQuery = urlencode($project->location_name . ' ' . ($project->location_address ?? ''));
                    @endphp

                    <!-- Leaflet / OpenFreeMap Interactive Map Card -->
                    @if($project->latitude && $project->longitude)
                        <div id="projectLeafletMap" class="rounded-3 border overflow-hidden mb-2" style="height: 165px; width: 100%;"></div>
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
                        <!-- Graceful Location Fallback -->
                        <div class="rounded-3 border overflow-hidden position-relative mb-2 bg-light d-flex align-items-center justify-content-center p-2.5 text-center" style="min-height: 100px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                            <div>
                                <i class="bi bi-geo-alt-fill text-danger fs-3 d-block mb-1"></i>
                                <span class="fw-bold text-dark small d-block">{{ $project->location_name }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">Map location unavailable</div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-light rounded-2 p-2.5 border mb-2">
                        <div class="fw-semibold text-dark mb-1" style="font-size: 0.875rem;">{{ $project->location_name }}</div>
                        <div class="text-secondary mb-2" style="font-size: 0.75rem;">{{ $project->location_address ?? 'Campus / Lab Room' }}</div>

                        <!-- Open in OpenStreetMap / External Navigation Link -->
                        @if($project->latitude && $project->longitude)
                            <a href="https://www.openstreetmap.org/?mlat={{ $project->latitude }}&mlon={{ $project->longitude }}#map=17/{{ $project->latitude }}/{{ $project->longitude }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-box-arrow-up-right"></i> Open in OpenStreetMap
                            </a>
                        @else
                            <a href="https://www.openstreetmap.org/search?query={{ urlencode($project->location_name . ' ' . ($project->location_address ?? '')) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-box-arrow-up-right"></i> Search in OpenStreetMap
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-3 text-secondary small">
                        <i class="bi bi-geo text-muted fs-4 d-block mb-1"></i>
                        No physical meeting location specified.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
