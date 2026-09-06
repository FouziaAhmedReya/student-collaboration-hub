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
        .hub-card { background: #fff; border-radius: 1rem; border: 1px solid #e5e7eb; padding: 1.5rem; }
        .btn-hub-primary { background-color: #2563eb; color: #fff; border: none; border-radius: 0.5rem; padding: 0.5rem 1rem; font-weight: 600; }
        .btn-hub-primary:hover { background-color: #1d4ed8; color: #fff; }
        .btn-hub-outline { background-color: transparent; color: #2563eb; border: 1px solid #2563eb; border-radius: 0.5rem; padding: 0.5rem 1rem; font-weight: 600; }
        .btn-hub-outline:hover { background-color: #eff6ff; }
    
        header a {
            text-decoration: none !important;
            color: rgb(71 85 105);
        }

        header a:hover {
            color: rgb(15 23 42);
        }

        header a.text-blue-700 {
            color: rgb(29 78 216) !important;
        }

        header a.text-red-600 {
            color: rgb(220 38 38) !important;
        }
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
    <div class="mb-3">
        <a href="{{ route('groups.index') }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Back to Study Groups
        </a>
    </div>

    <!-- Page Header Title & Subtitle -->
    <div class="mb-4">
        <h1 class="h2 fw-bold text-dark mb-1">Create New Study Group</h1>
        <p class="text-secondary mb-0 fs-6">Fill the details to create a new study group.</p>
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

    <form method="POST" action="{{ route('groups.store') }}" id="createGroupForm">
        @csrf
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
                               value="{{ old('name') }}"
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
                                   value="{{ old('course') }}"
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
                                    <option value="{{ $i }}" {{ old('max_members', 8) == $i ? 'selected' : '' }}>
                                        {{ $i }} Members
                                    </option>
                                @endfor
                                <option value="40" {{ old('max_members') == 40 ? 'selected' : '' }}>40 Members</option>
                                <option value="50" {{ old('max_members') == 50 ? 'selected' : '' }}>50 Members</option>
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
                                   value="{{ old('meeting_date', date('Y-m-d', strtotime('+1 day'))) }}"
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
                                   value="{{ old('meeting_time', '15:00') }}"
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
                                  required>{{ old('description') }}</textarea>
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
                                <label class="card p-3 h-100 border cursor-pointer hover-shadow transition {{ old('visibility', 'public') === 'public' ? 'border-primary bg-light' : '' }}" id="publicCard">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="visibility" id="visibilityPublic" value="public" {{ old('visibility', 'public') === 'public' ? 'checked' : '' }} onchange="updateVisibilityCards()">
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
                                <label class="card p-3 h-100 border cursor-pointer hover-shadow transition {{ old('visibility') === 'private' ? 'border-success bg-light' : '' }}" id="privateCard">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="radio" name="visibility" id="visibilityPrivate" value="private" {{ old('visibility') === 'private' ? 'checked' : '' }} onchange="updateVisibilityCards()">
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
                    <div class="rounded-3 overflow-hidden border mb-3" style="height: 200px; position: relative;">
                        <div id="mapPreviewContainer" class="w-100 h-100"></div>
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
                               value="{{ old('location_name', 'BRAC University Library') }}"
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
                               value="{{ old('location_address', 'UB02 Building, 3rd Floor, Mohakhali, Dhaka') }}"
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
                                   value="{{ old('latitude', '23.7806') }}">
                        </div>
                        <div class="col-6">
                            <label for="longitude" class="form-label small fw-semibold text-muted">Longitude</label>
                            <input type="number"
                                   step="any"
                                   class="form-control form-control-sm"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude', '90.4068') }}">
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
                <i class="bi bi-check-lg me-1"></i> Create Group
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

    let groupPickerInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        groupPickerInstance = HubMap.initPickerMap({
            containerId: 'mapPreviewContainer',
            latInputId: 'latitude',
            lngInputId: 'longitude',
            nameInputId: 'location_name',
            addressInputId: 'location_address',
            initialLat: parseFloat(document.getElementById('latitude').value) || 23.7806,
            initialLng: parseFloat(document.getElementById('longitude').value) || 90.4068
        });
    });

    function setLocationPreset(name, address, lat, lng) {
        document.getElementById('location_name').value = name;
        document.getElementById('location_address').value = address;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        if (groupPickerInstance) {
            groupPickerInstance.setLocation(lat, lng);
        }
    }
</script>
@endpush
