@extends('layouts.app')

@section('content')
<div class="container-fluid px-lg-4" style="max-width: 900px;">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Edit Student Profile</h1>
            <p class="text-secondary small mb-0">Update your personal and academic information.</p>
        </div>
        <a href="{{ route('profile.show') }}" class="btn btn-hub-outline btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <div class="hub-card mb-4">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Profile Picture Section -->
            <div class="mb-4 text-center border-bottom pb-4">
                <div class="mb-3 position-relative d-inline-block">
                    @if($profile->profile_photo)
                        <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Avatar" class="profile-avatar-large shadow-sm">
                    @else
                        <div class="profile-avatar-large d-flex align-items-center justify-content-center text-secondary mx-auto">
                            <i class="bi bi-person-fill fs-1"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <label for="profile_photo" class="btn btn-sm btn-hub-outline me-2">
                        <i class="bi bi-camera me-1"></i> Choose New Photo
                    </label>
                    <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*" onchange="previewPhoto(this)">
                    <small class="text-secondary d-block mt-2">Recommended: Square JPG, PNG, or WEBP (Max 2MB)</small>
                </div>
            </div>

            <!-- Name -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">University Email</label>
                    <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled readonly>
                    <small class="text-muted">Email cannot be changed.</small>
                </div>
            </div>

            <!-- Department, Semester, University, Joined Date -->
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="e.g. CSE" value="{{ old('department', $profile->department) }}">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Semester</label>
                    <input type="text" name="semester" class="form-control" placeholder="e.g. 10th Semester" value="{{ old('semester', $profile->semester) }}">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">University</label>
                    <input type="text" name="university" class="form-control" placeholder="e.g. BRAC University" value="{{ old('university', $profile->university) }}">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">Joined Date</label>
                    <input type="date" name="joined_date" class="form-control" value="{{ old('joined_date', $profile->joined_date ? \Carbon\Carbon::parse($profile->joined_date)->format('Y-m-d') : '') }}">
                </div>
            </div>

            <!-- About Me -->
            <div class="mb-4">
                <label class="form-label fw-semibold">About Me</label>
                <textarea name="about_me" rows="4" class="form-control" placeholder="Write a short summary about yourself, your goals and passion...">{{ old('about_me', $profile->about_me) }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <a href="{{ route('profile.show') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-hub-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var avatars = document.querySelectorAll('.profile-avatar-large');
                avatars.forEach(function(img) {
                    if (img.tagName === 'IMG') {
                        img.src = e.target.result;
                    }
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
