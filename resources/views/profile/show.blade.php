@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header Title & Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">My Profile & Skills</h1>
            <p class="text-secondary mb-0 fs-6">Manage your profile, technical skills and portfolio.</p>
        </div>
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-hub-outline d-inline-flex align-items-center gap-2">
                <i class="bi bi-pencil"></i> Edit Profile
            </a>
        </div>
    </div>

    <!-- 4 Information Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Department -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-shield-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Department</div>
                    <div class="stat-value">{{ $profile->department ?? 'CSE' }}</div>
                </div>
            </div>
        </div>

        <!-- Semester -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Semester</div>
                    <div class="stat-value">{{ $profile->semester ?? '10th Semester' }}</div>
                </div>
            </div>
        </div>

        <!-- University -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-building-fill"></i>
                </div>
                <div>
                    <div class="stat-label">University</div>
                    <div class="stat-value">{{ $profile->university ?? 'BRAC University' }}</div>
                </div>
            </div>
        </div>

        <!-- Joined -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Joined</div>
                    <div class="stat-value">
                        {{ $profile->joined_date ? \Carbon\Carbon::parse($profile->joined_date)->format('M Y') : 'Jan 2022' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Profile Card & About Me -->
    <div class="row g-3 mb-4">
        <!-- Profile Card -->
        <div class="col-12 col-lg-4">
            <div class="hub-card text-center d-flex flex-column align-items-center justify-content-center">
                <div class="position-relative mb-3">
                    @if($profile->profile_photo)
                        <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="{{ $user->name }}" class="profile-avatar-large">
                    @else
                        <div class="profile-avatar-large d-flex align-items-center justify-content-center text-secondary">
                            <i class="bi bi-person-fill fs-1"></i>
                        </div>
                    @endif
                </div>

                <h3 class="h5 fw-bold text-dark mb-1">{{ $user->name }}</h3>
                <p class="text-secondary small mb-3">
                    {{ $profile->department ?? 'CSE' }}, {{ $profile->semester ?? '10th Semester' }}<br>
                    <span class="fw-medium text-dark">{{ $profile->university ?? 'BRAC University' }}</span>
                </p>

                <!-- Profile Completion -->
                <div class="w-100 mt-2 px-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-secondary">Profile Completion</span>
                        <span class="small fw-bold text-primary">{{ $completionPercentage }}%</span>
                    </div>
                    <div class="progress-hub">
                        <div class="progress-hub-bar" style="width: {{ $completionPercentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Me Card -->
        <div class="col-12 col-lg-8">
            <div class="hub-card d-flex flex-column justify-content-start">
                <h3 class="h6 fw-bold text-dark mb-3">About Me</h3>
                <p class="text-secondary lh-lg mb-0" style="font-size: 0.95rem;">
                    {{ $profile->about_me ?? 'Passionate about building scalable web applications and AI solutions. I love solving real-world problems and collaborating with like-minded people.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Row 3: 4 Column Cards (Technical Skills, Interests, Projects, Portfolio Links) -->
    <div class="row g-3 mb-4">
        <!-- Technical Skills -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="hub-card d-flex flex-column justify-content-between">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-3">Technical Skills</h3>
                    <div class="space-y-3">
                        @forelse($profile->skills as $skill)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-dark">{{ $skill->name }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small text-secondary">{{ $skill->proficiency }}%</span>
                                        <button class="btn btn-sm btn-link text-secondary p-0 border-0" onclick="openEditSkillModal({{ $skill->id }}, '{{ addslashes($skill->name) }}', {{ $skill->proficiency }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('skills.destroy', $skill) }}" class="d-inline" onsubmit="return confirm('Delete this skill?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="progress-hub">
                                    <div class="progress-hub-bar" style="width: {{ $skill->proficiency }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">No technical skills added yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 mt-auto">
                    <button type="button" class="btn-add-item" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                        + Add Skills
                    </button>
                </div>
            </div>
        </div>

        <!-- Interests -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="hub-card d-flex flex-column justify-content-between">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-3">Interests</h3>
                    <div class="d-flex flex-wrap align-items-center">
                        @forelse($profile->interests as $interest)
                            <span class="badge-interest">
                                {{ $interest->name }}
                                <form method="POST" action="{{ route('interests.destroy', $interest) }}" class="d-inline" onsubmit="return confirm('Remove interest?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove-interest" title="Remove">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            </span>
                        @empty
                            <p class="text-muted small">No interests added yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 mt-auto">
                    <button type="button" class="btn-add-item" data-bs-toggle="modal" data-bs-target="#addInterestModal">
                        + Add Interest
                    </button>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="hub-card d-flex flex-column justify-content-between">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-3">Completed Projects</h3>
                    @forelse($profile->projects as $project)
                        <div class="project-item">
                            <div class="project-icon">
                                @if(str_contains(strtolower($project->name), 'hub') || str_contains(strtolower($project->name), 'collaborat'))
                                    <i class="bi bi-link-45deg"></i>
                                @elseif(str_contains(strtolower($project->name), 'library'))
                                    <i class="bi bi-journal-bookmark-fill text-warning"></i>
                                @elseif(str_contains(strtolower($project->name), 'ai') || str_contains(strtolower($project->name), 'chat'))
                                    <i class="bi bi-robot text-success"></i>
                                @else
                                    <i class="bi bi-file-earmark-code text-info"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="fw-bold text-dark small mb-0 d-block">{{ $project->name }}</span>
                                    <div>
                                        <button class="btn btn-sm btn-link text-secondary p-0 border-0 me-1" onclick="openEditProjectModal({{ $project->id }}, '{{ addslashes($project->name) }}', '{{ addslashes($project->description) }}', '{{ addslashes($project->technologies) }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="d-inline" onsubmit="return confirm('Delete project?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <span class="text-secondary d-block" style="font-size: 0.78rem;">
                                    {{ $project->description ?: $project->technologies }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">No projects added yet.</p>
                    @endforelse
                </div>

                <div class="pt-3 mt-auto">
                    <button type="button" class="btn-add-item" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                        + Add Project
                    </button>
                </div>
            </div>
        </div>

        <!-- Portfolio Links -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="hub-card d-flex flex-column justify-content-between">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-3">Portfolio Links</h3>
                    @forelse($profile->portfolioLinks as $link)
                        <div class="portfolio-item">
                            <div class="portfolio-icon">
                                @if(strtolower($link->platform) == 'github')
                                    <i class="bi bi-github"></i>
                                @elseif(strtolower($link->platform) == 'linkedin')
                                    <i class="bi bi-linkedin text-primary"></i>
                                @elseif(str_contains(strtolower($link->platform), 'code'))
                                    <i class="bi bi-code-slash text-warning"></i>
                                @else
                                    <i class="bi bi-globe text-info"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="fw-bold text-dark small text-decoration-none text-truncate">
                                        {{ $link->platform }}
                                    </a>
                                    <div>
                                        <button class="btn btn-sm btn-link text-secondary p-0 border-0 me-1" onclick="openEditLinkModal({{ $link->id }}, '{{ addslashes($link->platform) }}', '{{ addslashes($link->url) }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('portfolio-links.destroy', $link) }}" class="d-inline" onsubmit="return confirm('Delete link?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="text-primary d-block text-truncate" style="font-size: 0.75rem;">
                                    {{ preg_replace('#^https?://#', '', $link->url) }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">No portfolio links added yet.</p>
                    @endforelse
                </div>

                <div class="pt-3 mt-auto">
                    <button type="button" class="btn-add-item" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                        + Add Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Preferred Study Location -->
    <div class="row g-3">
        <div class="col-12">
            <div class="hub-card">
                <h3 class="h6 fw-bold text-dark mb-3">Preferred Study Location</h3>
                <div class="row align-items-center g-3">
                    <!-- Leaflet / OpenFreeMap Map Column -->
                    <div class="col-12 col-md-6">
                        <div id="profileDisplayMap" class="rounded-3 overflow-hidden border" style="height: 220px; background: #e2e8f0; position: relative;">
                            @if(!$profile->latitude || !$profile->longitude)
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-3" style="background-color: #f1f5f9;">
                                    <i class="bi bi-geo-alt-fill text-danger fs-1 mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">{{ $profile->preferred_location_name ?? 'BRAC University Library' }}</h6>
                                    <p class="small text-secondary mb-0">{{ $profile->preferred_location_address ?? 'Mohakhali, Dhaka 1212' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Information & Action Column -->
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-column justify-content-between h-100 ps-md-3">
                            <div>
                                <h4 class="h6 fw-bold text-dark mb-1">{{ $profile->preferred_location_name ?? 'BRAC University Library' }}</h4>
                                <p class="text-secondary small mb-3">{{ $profile->preferred_location_address ?? 'Mohakhali, Dhaka 1212' }}</p>

                                <ul class="list-unstyled small text-secondary space-y-1 mb-4">
                                    <li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>Quiet Environment</li>
                                    <li class="mb-1"><i class="bi bi-wifi text-primary me-2"></i>Wifi Available</li>
                                    <li class="mb-1"><i class="bi bi-clock-history text-info me-2"></i>24/7 Access</li>
                                </ul>
                            </div>

                            <div>
                                <button type="button" class="btn btn-hub-outline" data-bs-toggle="modal" data-bs-target="#changeLocationModal">
                                    Change Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('skills.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Technical Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Skill Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Python, Laravel" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Proficiency Percentage (0 - 100)</label>
                    <input type="number" name="proficiency" class="form-control" min="0" max="100" value="80" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Save Skill</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Skill Modal -->
<div class="modal fade" id="editSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editSkillForm" method="POST" action="" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Technical Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Skill Name</label>
                    <input type="text" id="edit_skill_name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Proficiency Percentage</label>
                    <input type="number" id="edit_skill_proficiency" name="proficiency" class="form-control" min="0" max="100" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Update Skill</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Interest Modal — Department-Filtered Catalog -->
<div class="modal fade" id="addInterestModal" tabindex="-1" aria-labelledby="addInterestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addInterestModalLabel">Add Interest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Department context note -->
                <p class="text-secondary small mb-3">
                    Showing suggestions for <strong id="interestDeptLabel">your department</strong>.
                    Click any tag to add it to your profile.
                </p>

                <!-- Search box -->
                <div class="mb-3">
                    <input
                        type="text"
                        id="interestSearchInput"
                        class="form-control"
                        placeholder="Search interests…"
                        autocomplete="off"
                    >
                </div>

                <!-- Suggestion tags grid -->
                <div id="interestSuggestionsContainer" style="max-height: 320px; overflow-y: auto;">
                    <div id="interestTagsGrid" class="d-flex flex-wrap gap-2">
                        <!-- Tags injected by JS -->
                    </div>
                    <p id="interestNoResults" class="text-muted small mt-2 d-none">No matching interests found.</p>
                    <div id="interestLoadingSpinner" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="visually-hidden">Loading…</span>
                        </div>
                        <span class="text-secondary small ms-2">Loading suggestions…</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form used to POST each interest selection -->
<form id="addInterestForm" method="POST" action="{{ route('interests.store') }}" class="d-none">
    @csrf
    <input type="hidden" name="name" id="addInterestNameInput">
</form>


<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('projects.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Completed Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Project Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Project title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Short summary">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Technologies Used</label>
                    <input type="text" name="technologies" class="form-control" placeholder="e.g. Laravel, MySQL, Bootstrap">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Save Project</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editProjectForm" method="POST" action="" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Project Name</label>
                    <input type="text" id="edit_project_name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" id="edit_project_description" name="description" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Technologies Used</label>
                    <input type="text" id="edit_project_technologies" name="technologies" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Update Project</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Portfolio Link Modal -->
<div class="modal fade" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('portfolio-links.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Portfolio Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Platform Name</label>
                    <input type="text" name="platform" class="form-control" placeholder="GitHub, LinkedIn, LeetCode" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">URL</label>
                    <input type="url" name="url" class="form-control" placeholder="https://..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Save Link</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Portfolio Link Modal -->
<div class="modal fade" id="editLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editLinkForm" method="POST" action="" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Portfolio Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Platform Name</label>
                    <input type="text" id="edit_link_platform" name="platform" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">URL</label>
                    <input type="url" id="edit_link_url" name="url" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Update Link</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Location Modal -->
<div class="modal fade" id="changeLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.location') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Select Preferred Study Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Location Name</label>
                    <input type="text" name="preferred_location_name" class="form-control" value="{{ $profile->preferred_location_name ?? 'BRAC University Library' }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Address / Area</label>
                    <div class="input-group">
                        <input type="text" id="profile_location_address" name="preferred_location_address" class="form-control" value="{{ $profile->preferred_location_address ?? 'Mohakhali, Dhaka 1212' }}" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="searchProfilePlace()">Search Place</button>
                    </div>
                </div>

                <!-- Interactive Map Picker inside Modal -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Click or drag marker to set exact location on map</label>
                    <div id="profilePickerMap" class="rounded-3 border" style="height: 200px; width: 100%;"></div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Latitude</label>
                        <input type="number" step="any" id="profile_latitude" name="latitude" class="form-control" value="{{ $profile->latitude ?? '23.7806' }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Longitude</label>
                        <input type="number" step="any" id="profile_longitude" name="longitude" class="form-control" value="{{ $profile->longitude ?? '90.4068' }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-hub-primary">Update Location</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditSkillModal(id, name, proficiency) {
        document.getElementById('editSkillForm').action = '/skills/' + id;
        document.getElementById('edit_skill_name').value = name;
        document.getElementById('edit_skill_proficiency').value = proficiency;
        var modal = new bootstrap.Modal(document.getElementById('editSkillModal'));
        modal.show();
    }

    function openEditProjectModal(id, name, description, technologies) {
        document.getElementById('editProjectForm').action = '/projects/' + id;
        document.getElementById('edit_project_name').value = name;
        document.getElementById('edit_project_description').value = description;
        document.getElementById('edit_project_technologies').value = technologies;
        var modal = new bootstrap.Modal(document.getElementById('editProjectModal'));
        modal.show();
    }

    function openEditLinkModal(id, platform, url) {
        document.getElementById('editLinkForm').action = '/portfolio-links/' + id;
        document.getElementById('edit_link_platform').value = platform;
        document.getElementById('edit_link_url').value = url;
        var modal = new bootstrap.Modal(document.getElementById('editLinkModal'));
        modal.show();
    }

    // ─── Department-Filtered Interest Modal ─────────────────────────────────────
    (function () {
        // Already-selected interest names (from PHP → JS, lowercased for comparison)
        const selectedInterests = new Set(
            @json($profile->interests->pluck('name')->map(fn($n) => strtolower(trim($n))))
        );

        // The student's department (passed from PHP)
        const studentDepartment = @json($profile->department ?? 'General');

        // Preload department suggestions directly from PHP to avoid any delay
        let allSuggestions = @json($departmentSuggestions ?? []);

        const tagsGrid      = document.getElementById('interestTagsGrid');
        const searchInput   = document.getElementById('interestSearchInput');
        const noResults     = document.getElementById('interestNoResults');
        const loadingSpinner = document.getElementById('interestLoadingSpinner');
        const deptLabel     = document.getElementById('interestDeptLabel');

        // Populate suggestions immediately and whenever modal opens
        const addInterestModal = document.getElementById('addInterestModal');
        addInterestModal.addEventListener('show.bs.modal', function () {
            if (deptLabel) {
                deptLabel.textContent = studentDepartment || 'General';
            }
            if (searchInput) searchInput.value = '';

            if (allSuggestions && allSuggestions.length > 0) {
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                renderTags(allSuggestions);
            } else {
                fetchSuggestions();
            }
        });

        function fetchSuggestions() {
            if (loadingSpinner) loadingSpinner.classList.remove('d-none');
            if (noResults) noResults.classList.add('d-none');
            if (tagsGrid)  tagsGrid.innerHTML = '';

            const dept = encodeURIComponent(studentDepartment || 'General');
            fetch('/interests/suggestions?department=' + dept, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function (res) {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(function (data) {
                allSuggestions = Array.isArray(data) ? data : Object.values(data);
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                renderTags(allSuggestions);
            })
            .catch(function (err) {
                console.error('Failed to load interest suggestions:', err);
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                if (tagsGrid) tagsGrid.innerHTML = '<p class="text-danger small">Could not load suggestions. Please try again.</p>';
            });
        }

        function renderTags(list) {
            if (!tagsGrid) return;
            tagsGrid.innerHTML = '';

            const q = (searchInput ? searchInput.value : '').trim().toLowerCase();
            const filtered = (list || []).filter(function (name) {
                return q === '' || String(name).toLowerCase().includes(q);
            });

            if (filtered.length === 0) {
                if (noResults) noResults.classList.remove('d-none');
                return;
            }
            if (noResults) noResults.classList.add('d-none');

            filtered.forEach(function (name) {
                const alreadyAdded = selectedInterests.has(String(name).trim().toLowerCase());

                const pill = document.createElement('button');
                pill.type = 'button';
                pill.classList.add('interest-suggestion-pill');
                if (alreadyAdded) {
                    pill.classList.add('already-added');
                    pill.title = 'Already added to your profile';
                    pill.disabled = true;
                    pill.innerHTML = '<i class="bi bi-check-lg me-1"></i>' + escapeHtml(name);
                } else {
                    pill.innerHTML = '<i class="bi bi-plus-lg me-1"></i>' + escapeHtml(name);
                    pill.addEventListener('click', function () {
                        submitInterest(name, pill);
                    });
                }
                tagsGrid.appendChild(pill);
            });
        }

        function submitInterest(name, pill) {
            // Disable pill immediately to prevent double-click
            pill.disabled = true;
            pill.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>' + escapeHtml(name);

            document.getElementById('addInterestNameInput').value = name;
            document.getElementById('addInterestForm').submit();
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Search filtering
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                renderTags(allSuggestions);
            });
        }
    })();

    // Initialize Profile Display Map
    document.addEventListener('DOMContentLoaded', function () {
        @if($profile->latitude && $profile->longitude)
            HubMap.initDisplayMap(
                'profileDisplayMap',
                {{ (float)$profile->latitude }},
                {{ (float)$profile->longitude }},
                "{{ addslashes($profile->preferred_location_name ?? 'Preferred Study Location') }}"
            );
        @endif
    });

    let profilePickerInstance = null;
    const locModal = document.getElementById('changeLocationModal');
    if (locModal) {
        locModal.addEventListener('shown.bs.modal', function () {
            if (!profilePickerInstance) {
                profilePickerInstance = HubMap.initPickerMap({
                    containerId: 'profilePickerMap',
                    latInputId: 'profile_latitude',
                    lngInputId: 'profile_longitude',
                    addressInputId: 'profile_location_address',
                    initialLat: {{ (float)($profile->latitude ?? 23.7806) }},
                    initialLng: {{ (float)($profile->longitude ?? 90.4068) }}
                });
            } else {
                profilePickerInstance.map.invalidateSize();
            }
        });
    }

    function searchProfilePlace() {
        const query = document.getElementById('profile_location_address').value;
        if (!query) return;

        HubMap.searchNominatim(query, function (results) {
            if (results && results.length > 0) {
                const first = results[0];
                const lat = parseFloat(first.lat);
                const lng = parseFloat(first.lon);

                if (profilePickerInstance) {
                    profilePickerInstance.setLocation(lat, lng);
                }
            } else {
                alert('No location found for this address query.');
            }
        });
    }
</script>
@endpush

