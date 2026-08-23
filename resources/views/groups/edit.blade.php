@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Back Link -->
    <div class="mb-3">
        <a href="{{ route('groups.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Study Groups
        </a>
    </div>

    <!-- Page Header Title & Subtitle -->
    <div class="mb-4">
        <h1 class="h2 fw-bold text-dark mb-1">Edit Study Group</h1>
        <p class="text-secondary mb-0 fs-6">Update study group details, schedule, and meeting location.</p>
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

    <form method="POST" action="{{ route('groups.update', $group) }}" id="editGroupForm">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Left Column: Main Group Details -->
            <div class="col-12 col-lg-7">
                <div class="hub-card p-4 shadow-sm">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">Group Information</h5>

                    <!-- Group Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-dark">
                            Group Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               placeholder="e.g. Algorithms & Complexity Study Circle"
                               value="{{ old('name', $group->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Course / Subject -->
                        <div class="col-12 col-md-7">
                            <label for="course" class="form-label fw-semibold text-dark">
                                Course / Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   list="courseList"
                                   class="form-control @error('course') is-invalid @enderror"
                                   id="course"
                                   name="course"
                                   placeholder="Select or enter course name"
                                   value="{{ old('course', $group->course) }}"
                                   required>
                            <datalist id="courseList">
                                @foreach($suggestedCourses as $c)
                                    <option value="{{ $c }}"></option>
                                @endforeach
                            </datalist>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Maximum Members -->
                        <div class="col-12 col-md-5">
                            <label for="max_members" class="form-label fw-semibold text-dark">
                                Max Members <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('max_members') is-invalid @enderror" id="max_members" name="max_members" required>
                                @for($i = 2; $i <= 30; $i++)
                                    <option value="{{ $i }}" {{ old('max_members', $group->max_members) == $i ? 'selected' : '' }}>
                                        {{ $i }} Members
                                    </option>
                                @endfor
                                <option value="40" {{ old('max_members', $group->max_members) == 40 ? 'selected' : '' }}>40 Members</option>
                                <option value="50" {{ old('max_members', $group->max_members) == 50 ? 'selected' : '' }}>50 Members</option>
                            </select>
                            @error('max_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Meeting Date -->
                        <div class="col-12 col-md-6">
                            <label for="meeting_date" class="form-label fw-semibold text-dark">
                                Meeting Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control @error('meeting_date') is-invalid @enderror"
                                   id="meeting_date"
                                   name="meeting_date"
                                   value="{{ old('meeting_date', $group->meeting_date ? $group->meeting_date->format('Y-m-d') : '') }}"
                                   required>
                            @error('meeting_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Meeting Time -->
                        <div class="col-12 col-md-6">
                            <label for="meeting_time" class="form-label fw-semibold text-dark">
                                Meeting Time <span class="text-danger">*</span>
                            </label>
                            <input type="time"
                                   class="form-control @error('meeting_time') is-invalid @enderror"
                                   id="meeting_time"
                                   name="meeting_time"
                                   value="{{ old('meeting_time', $group->meeting_time ? date('H:i', strtotime($group->meeting_time)) : '15:00') }}"
                                   required>
                            @error('meeting_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold text-dark">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Describe the study goals, topics to cover, and weekly preparation expectations..."
                                  required>{{ old('description', $group->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Visibility Selection -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-dark mb-2">
                            Group Visibility <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="card p-3 h-100 border cursor-pointer hover-shadow transition {{ old('visibility', $group->visibility) === 'public' ? 'border-primary bg-light' : '' }}" id="publicCard">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="visibility" id="visibilityPublic" value="public" {{ old('visibility', $group->visibility) === 'public' ? 'checked' : '' }} onchange="updateVisibilityCards()">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                                <i class="bi bi-globe text-primary"></i> Public
                                            </div>
                                            <small class="text-secondary">Anyone can discover and join instantly.</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="card p-3 h-100 border cursor-pointer hover-shadow transition {{ old('visibility', $group->visibility) === 'private' ? 'border-success bg-light' : '' }}" id="privateCard">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="visibility" id="visibilityPrivate" value="private" {{ old('visibility', $group->visibility) === 'private' ? 'checked' : '' }} onchange="updateVisibilityCards()">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                                <i class="bi bi-lock-fill text-success"></i> Private
                                            </div>
                                            <small class="text-secondary">Only invited/approved members can join.</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Meeting Location & Google Maps -->
            <div class="col-12 col-lg-5">
                <div class="hub-card p-4 shadow-sm">
                    <h5 class="fw-bold text-dark mb-2 pb-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Meeting Location</span>
                        <span class="badge bg-light text-secondary border fw-normal small">Google Maps</span>
                    </h5>
                    <p class="text-secondary small mb-3">Specify where group members will meet for study sessions.</p>

                    <!-- Campus Quick Location Presets -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Quick Select Campus Location:</label>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-2.5 small" onclick="setLocationPreset('BRAC University Library', 'UB02 Building, 3rd Floor, Mohakhali, Dhaka', 23.7806, 90.4068)">
                                📚 Library
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-2.5 small" onclick="setLocationPreset('UB02 Study Lounge', 'UB02 Building, 7th Floor Lounge, Dhaka', 23.7808, 90.4072)">
                                🛋️ Study Lounge
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-2.5 small" onclick="setLocationPreset('Khaas Food Cafe / Cafeteria', 'Ground Floor Cafeteria, BRACU Campus', 23.7802, 90.4065)">
                                ☕ Cafeteria
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-2.5 small" onclick="setLocationPreset('Online via Google Meet / Zoom', 'Virtual Link will be shared with members', 23.7800, 90.4000)">
                                💻 Virtual Meet
                            </button>
                        </div>
                    </div>

                    <!-- Map Preview Container -->
                    <div class="rounded-3 overflow-hidden border mb-3" style="height: 200px; background-color: #f1f5f9; position: relative;">
                        <div id="mapPreviewContainer" class="w-100 h-100">
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
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-3" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                                    <i class="bi bi-geo-alt-fill text-danger fs-1 mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1" id="previewLocationNameDisplay">{{ old('location_name', $group->location_name ?? 'BRAC University Library') }}</h6>
                                    <p class="small text-secondary mb-0" id="previewLocationAddressDisplay">{{ old('location_address', $group->location_address ?? 'UB02 Building, 3rd Floor, Mohakhali, Dhaka') }}</p>
                                    <span class="badge bg-white text-secondary border mt-2">Interactive Location Selected</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Name Input -->
                    <div class="mb-3">
                        <label for="location_name" class="form-label fw-semibold text-dark small">
                            Location Name
                        </label>
                        <input type="text"
                               class="form-control form-control-sm @error('location_name') is-invalid @enderror"
                               id="location_name"
                               name="location_name"
                               placeholder="e.g. Central Library Study Zone"
                               value="{{ old('location_name', $group->location_name) }}"
                               oninput="updateDisplayPreview()">
                        @error('location_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Location Address Input -->
                    <div class="mb-3">
                        <label for="location_address" class="form-label fw-semibold text-dark small">
                            Address / Room Details
                        </label>
                        <input type="text"
                               class="form-control form-control-sm @error('location_address') is-invalid @enderror"
                               id="location_address"
                               name="location_address"
                               placeholder="e.g. UB02 Building, 3rd Floor, Mohakhali"
                               value="{{ old('location_address', $group->location_address) }}"
                               oninput="updateDisplayPreview()">
                        @error('location_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Coordinates (Latitude & Longitude) -->
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="latitude" class="form-label small fw-semibold text-muted">Latitude</label>
                            <input type="number"
                                   step="any"
                                   class="form-control form-control-sm"
                                   id="latitude"
                                   name="latitude"
                                   value="{{ old('latitude', $group->latitude ?? '23.7806') }}">
                        </div>
                        <div class="col-6">
                            <label for="longitude" class="form-label small fw-semibold text-muted">Longitude</label>
                            <input type="number"
                                   step="any"
                                   class="form-control form-control-sm"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude', $group->longitude ?? '90.4068') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
            <a href="{{ route('groups.index') }}" class="btn btn-light border px-4">
                Cancel
            </a>
            <button type="submit" class="btn btn-hub-primary px-4 py-2">
                <i class="bi bi-check-lg me-1"></i> Update Group
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function updateVisibilityCards() {
        const isPublic = document.getElementById('visibilityPublic').checked;
        const pubCard = document.getElementById('publicCard');
        const privCard = document.getElementById('privateCard');

        if (isPublic) {
            pubCard.classList.add('border-primary', 'bg-light');
            privCard.classList.remove('border-success', 'bg-light');
        } else {
            privCard.classList.add('border-success', 'bg-light');
            pubCard.classList.remove('border-primary', 'bg-light');
        }
    }

    function setLocationPreset(name, address, lat, lng) {
        document.getElementById('location_name').value = name;
        document.getElementById('location_address').value = address;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        updateDisplayPreview();
    }

    function updateDisplayPreview() {
        const name = document.getElementById('location_name').value || 'Selected Location';
        const address = document.getElementById('location_address').value || 'Location Address';

        const nameDisplay = document.getElementById('previewLocationNameDisplay');
        const addrDisplay = document.getElementById('previewLocationAddressDisplay');

        if (nameDisplay) nameDisplay.innerText = name;
        if (addrDisplay) addrDisplay.innerText = address;
    }
</script>
@endpush
