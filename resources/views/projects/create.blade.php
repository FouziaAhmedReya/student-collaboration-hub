@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Back Link -->
    <div class="mb-3">
        <a href="{{ route('projects.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Project Team Finder
        </a>
    </div>

    <!-- Page Header Title -->
    <div class="mb-4">
        <h1 class="h2 fw-bold text-dark mb-1">Post a Project Recruitment</h1>
        <p class="text-secondary mb-0 fs-6">Find qualified students and assemble your dream team for course projects, thesis, or hackathons.</p>
    </div>

    <!-- Validation Errors Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Please correct the following errors:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.recruitment.store') }}">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Project Details & Skills -->
            <div class="col-12 col-lg-7">
                <div class="hub-card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Project Information</h5>

                    <!-- Project Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold text-dark">
                            Project Title <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               placeholder="e.g. AI-Powered Medical Diagnosis System"
                               value="{{ old('title') }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Course / Subject -->
                        <div class="col-12 col-md-6">
                            <label for="course" class="form-label fw-semibold text-dark">
                                Course / Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   list="courseList"
                                   class="form-control @error('course') is-invalid @enderror"
                                   id="course"
                                   name="course"
                                   placeholder="e.g. CSE470 - Software Engineering"
                                   value="{{ old('course') }}"
                                   required>
                            <datalist id="courseList">
                                @foreach($courses as $c)
                                    <option value="{{ $c }}"></option>
                                @endforeach
                            </datalist>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Project Category / Type -->
                        <div class="col-12 col-md-6">
                            <label for="project_type" class="form-label fw-semibold text-dark">
                                Project Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('project_type') is-invalid @enderror" id="project_type" name="project_type" required>
                                @foreach($projectTypes as $type)
                                    <option value="{{ $type }}" {{ old('project_type', 'Course Project') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Team Capacity -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="required_members" class="form-label fw-semibold text-dark">
                                Required Team Size <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('required_members') is-invalid @enderror" id="required_members" name="required_members" required>
                                @for($i = 2; $i <= 15; $i++)
                                    <option value="{{ $i }}" {{ old('required_members', 4) == $i ? 'selected' : '' }}>
                                        {{ $i }} Members
                                    </option>
                                @endfor
                            </select>
                            @error('required_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="current_members" class="form-label fw-semibold text-dark">
                                Current Team Count
                            </label>
                            <input type="number"
                                   class="form-control @error('current_members') is-invalid @enderror"
                                   id="current_members"
                                   name="current_members"
                                   min="1"
                                   max="15"
                                   value="{{ old('current_members', 1) }}">
                            <div class="form-text small">Including yourself.</div>
                        </div>
                    </div>

                    <!-- Required Skills -->
                    <div class="mb-3">
                        <label for="required_skills" class="form-label fw-semibold text-dark">
                            Required Technical Skills / Tech Stack
                        </label>
                        <input type="text"
                               class="form-control @error('required_skills') is-invalid @enderror"
                               id="required_skills"
                               name="required_skills"
                               placeholder="e.g. Laravel, React, MySQL, Python, Tailwind CSS (comma-separated)"
                               value="{{ old('required_skills') }}">
                        <div class="form-text small">Separate skills with commas (e.g. PHP, Vue.js, Docker).</div>
                        @error('required_skills')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-dark">
                            Project Description & Goal <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="5"
                                  placeholder="Describe what you plan to build, expected role contributions, and project expectations..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recruitment Status -->
                    <input type="hidden" name="recruitment_status" value="open">
                </div>
            </div>

            <!-- Right Column: Meeting Schedule & Location -->
            <div class="col-12 col-lg-5">
                <div class="hub-card p-4 shadow-sm mb-4">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Meeting & Team Sync Details</h5>

                    <!-- Meeting Date -->
                    <div class="mb-3">
                        <label for="meeting_date" class="form-label fw-semibold text-dark">
                            First Team Sync Date
                        </label>
                        <input type="date"
                               class="form-control @error('meeting_date') is-invalid @enderror"
                               id="meeting_date"
                               name="meeting_date"
                               value="{{ old('meeting_date', date('Y-m-d', strtotime('+2 days'))) }}">
                        @error('meeting_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Meeting Time -->
                    <div class="mb-3">
                        <label for="meeting_time" class="form-label fw-semibold text-dark">
                            Meeting Time / Frequency
                        </label>
                        <input type="text"
                               class="form-control @error('meeting_time') is-invalid @enderror"
                               id="meeting_time"
                               name="meeting_time"
                               placeholder="e.g. 3:30 PM (Weekly Mondays)"
                               value="{{ old('meeting_time', '15:30') }}">
                        @error('meeting_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Location Search with Nominatim Geocoding -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Search Place / Address</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="projectLocationSearchInput" class="form-control" placeholder="e.g. UB02 Campus, Mohakhali...">
                            <button type="button" class="btn btn-outline-primary" onclick="searchProjectPlace()">Search</button>
                        </div>
                    </div>

                    <!-- Interactive Leaflet Map Picker Container -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Click or drag marker on map to set meeting location</label>
                        <div id="projectCreateMap" class="rounded-3 border" style="height: 200px; width: 100%;"></div>
                    </div>

                    <!-- Meeting Venue Name -->
                    <div class="mb-3">
                        <label for="location_name" class="form-label fw-semibold text-dark">
                            Meeting Venue / Room Name
                        </label>
                        <input type="text"
                               class="form-control @error('location_name') is-invalid @enderror"
                               id="location_name"
                               name="location_name"
                               placeholder="e.g. UB02 7th Floor Study Lounge"
                               value="{{ old('location_name', 'BRAC University UB02 Campus') }}">
                        @error('location_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="location_address" class="form-label fw-semibold text-dark">
                            Address / Campus Location Details
                        </label>
                        <input type="text"
                               class="form-control @error('location_address') is-invalid @enderror"
                               id="location_address"
                               name="location_address"
                               placeholder="e.g. 66 Mohakhali, Dhaka 1212"
                               value="{{ old('location_address', '66 Mohakhali, Dhaka') }}">
                        @error('location_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Coordinates -->
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="latitude" class="form-label small fw-semibold text-muted">Latitude</label>
                            <input type="number" step="any" class="form-control form-control-sm" id="latitude" name="latitude" value="{{ old('latitude', '23.7806') }}">
                        </div>
                        <div class="col-6">
                            <label for="longitude" class="form-label small fw-semibold text-muted">Longitude</label>
                            <input type="number" step="any" class="form-control form-control-sm" id="longitude" name="longitude" value="{{ old('longitude', '90.4068') }}">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route('projects.index') }}" class="btn btn-light border px-3">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-hub-primary px-4">
                        <i class="bi bi-send-fill me-1"></i> Publish Post
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let projectPickerInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        projectPickerInstance = HubMap.initPickerMap({
            containerId: 'projectCreateMap',
            latInputId: 'latitude',
            lngInputId: 'longitude',
            nameInputId: 'location_name',
            addressInputId: 'location_address',
            initialLat: {{ (float)old('latitude', 23.7806) }},
            initialLng: {{ (float)old('longitude', 90.4068) }}
        });
    });

    function searchProjectPlace() {
        const query = document.getElementById('projectLocationSearchInput').value;
        if (!query) return;

        HubMap.searchNominatim(query, function (results) {
            if (results && results.length > 0) {
                const first = results[0];
                const lat = parseFloat(first.lat);
                const lng = parseFloat(first.lon);

                document.getElementById('location_address').value = first.display_name;
                if (!document.getElementById('location_name').value) {
                    document.getElementById('location_name').value = first.name || query;
                }

                if (projectPickerInstance) {
                    projectPickerInstance.setLocation(lat, lng);
                }
            } else {
                alert('No location found for this search.');
            }
        });
    }
</script>
@endpush
