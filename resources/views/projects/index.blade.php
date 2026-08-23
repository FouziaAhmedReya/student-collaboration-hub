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

    <!-- Page Header Title & Post Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-semibold fs-7">
                    <i class="bi bi-person-lines-fill me-1"></i> Feature 2
                </div>
                <h1 class="h2 fw-bold text-dark mb-0">Project Team Finder</h1>
            </div>
            <p class="text-secondary mb-0 fs-6">Find students looking for teammates, recruit talent, or discover project collaboration opportunities.</p>
        </div>
        <div>
            <a href="{{ route('projects.create') }}" class="btn btn-hub-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i> Post a Project
            </a>
        </div>
    </div>

    <!-- Filters and Search Toolbar -->
    <div class="hub-card p-3 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
            <!-- Status Filter Pills -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('projects.index', ['status' => 'all', 'search' => $search, 'course' => $course, 'type' => $projectType]) }}"
                   class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    All Opportunities <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('projects.index', ['status' => 'open', 'search' => $search, 'course' => $course, 'type' => $projectType]) }}"
                   class="btn btn-sm {{ $status === 'open' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    <i class="bi bi-door-open me-1"></i> Recruiting <span class="badge {{ $status === 'open' ? 'bg-white text-primary' : 'bg-success text-white' }} ms-1 rounded-pill">{{ $counts['open'] }}</span>
                </a>
                <a href="{{ route('projects.index', ['status' => 'closed', 'search' => $search, 'course' => $course, 'type' => $projectType]) }}"
                   class="btn btn-sm {{ $status === 'closed' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    <i class="bi bi-lock me-1"></i> Team Full / Closed <span class="badge {{ $status === 'closed' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1 rounded-pill">{{ $counts['closed'] }}</span>
                </a>
                <a href="{{ route('projects.index', ['status' => 'my', 'search' => $search, 'course' => $course, 'type' => $projectType]) }}"
                   class="btn btn-sm {{ $status === 'my' ? 'btn-primary' : 'btn-light border text-secondary' }} rounded-pill px-3 py-2 fw-medium">
                    <i class="bi bi-person me-1"></i> My Posts <span class="badge {{ $status === 'my' ? 'bg-white text-primary' : 'bg-info text-white' }} ms-1 rounded-pill">{{ $counts['my'] }}</span>
                </a>
            </div>

            <!-- Search and Dropdown Filter Form -->
            <form method="GET" action="{{ route('projects.index') }}" class="d-flex flex-wrap flex-md-nowrap gap-2" style="min-width: 320px;">
                <input type="hidden" name="status" value="{{ $status }}">
                
                <select name="type" class="form-select form-select-sm" style="max-width: 160px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($projectTypes as $t)
                        <option value="{{ $t }}" {{ $projectType === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>

                <div class="input-group input-group-sm flex-grow-1">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by title, skill, course..." value="{{ $search }}">
                    @if($search || $course || $projectType)
                        <a href="{{ route('projects.index', ['status' => $status]) }}" class="btn btn-outline-secondary border-start-0" title="Clear filters">
                            <i class="bi bi-x"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary px-3">Filter</button>
            </form>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="row g-4">
        @forelse($projects as $project)
            @php
                $isCreator = $project->isCreator(auth()->user());
                $isOpen = $project->isOpen();
                $skills = $project->skills_array;
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="hub-card p-4 shadow-sm h-100 d-flex flex-column border">
                    <!-- Top Category & Status Badge -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 fw-medium" style="font-size: 0.75rem;">
                            <i class="bi bi-tag-fill me-1"></i> {{ $project->project_type }}
                        </span>
                        @if($isOpen)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Recruiting
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                <i class="bi bi-lock-fill me-1"></i> Closed
                            </span>
                        @endif
                    </div>

                    <!-- Project Title -->
                    <h5 class="fw-bold text-dark mb-1 text-truncate">
                        <a href="{{ route('projects.show', $project) }}" class="text-dark text-decoration-none">
                            {{ $project->title }}
                        </a>
                    </h5>

                    <!-- Course Badge -->
                    <div class="mb-2">
                        <span class="text-muted small fw-medium">
                            <i class="bi bi-mortarboard me-1 text-secondary"></i> {{ $project->course }}
                        </span>
                    </div>

                    <!-- Description Snippet -->
                    <p class="text-secondary small mb-3 text-break flex-grow-0" style="min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                        {{ $project->description }}
                    </p>

                    <!-- Required Skills Badges -->
                    @if(!empty($skills))
                        <div class="mb-3">
                            <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;">REQUIRED SKILLS:</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(array_slice($skills, 0, 4) as $sk)
                                    <span class="skill-tag">{{ $sk }}</span>
                                @endforeach
                                @if(count($skills) > 4)
                                    <span class="skill-tag text-muted">+{{ count($skills) - 4 }} more</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Project Meta Info Box -->
                    <div class="bg-light rounded-3 p-3 mb-3 border">
                        <div class="row g-2 small">
                            <div class="col-6">
                                <div class="text-muted"><i class="bi bi-people-fill me-1 text-primary"></i> Team Capacity</div>
                                <div class="fw-semibold text-dark">
                                    {{ $project->current_members }} / {{ $project->required_members }} Members
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted"><i class="bi bi-calendar-event me-1 text-primary"></i> Meeting Date</div>
                                <div class="fw-semibold text-dark">{{ $project->meeting_date ? $project->meeting_date->format('M d, Y') : 'TBD' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted"><i class="bi bi-clock me-1 text-primary"></i> Meeting Time</div>
                                <div class="fw-semibold text-dark">{{ $project->meeting_time ?? 'Flexible' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted"><i class="bi bi-person-circle me-1 text-primary"></i> Creator</div>
                                <div class="fw-semibold text-dark text-truncate">{{ $project->creator->name ?? 'Student' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Meeting Venue -->
                    @if($project->location_name)
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-1 text-dark small fw-semibold mb-0 text-truncate">
                                <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                <span class="text-truncate">{{ $project->location_name }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-auto pt-2 border-top">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-hub-primary flex-grow-1">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                            @if($isCreator)
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary" title="Edit Post">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('projects.destroy_recruitment', $project) }}" method="POST" onsubmit="return confirm('Delete this project recruitment post?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Post">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="hub-card text-center py-5 shadow-sm">
                    <i class="bi bi-briefcase fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-dark">No project recruitment posts found</h5>
                    <p class="text-secondary small mb-4">Be the first to create a team recruitment post for your project!</p>
                    <a href="{{ route('projects.create') }}" class="btn btn-hub-primary">
                        <i class="bi bi-plus-lg me-1"></i> Post a Project
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $projects->links() }}
    </div>
</div>
@endsection
