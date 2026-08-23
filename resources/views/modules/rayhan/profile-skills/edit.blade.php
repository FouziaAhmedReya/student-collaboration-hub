@extends('layouts.app')

@section('title', 'Edit Profile & Study Location')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 hover:text-blue-800 transition">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Profile
        </a>
        <h1 class="text-xl font-bold text-slate-900">Edit Student Profile</h1>
    </div>

    {{-- Edit Form Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Personal & Academic Section --}}
            <div>
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Academic & Personal Details</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Department *</label>
                        <select name="department" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden">
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ old('department', $profile->department) === $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Current Semester *</label>
                        <input type="text" name="semester" value="{{ old('semester', $profile->semester) }}" placeholder="e.g. Fall 2026, 8th Semester" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">University / Institution</label>
                        <input type="text" name="university" value="{{ old('university', $profile->university ?? 'University of Dhaka') }}" placeholder="e.g. University of Dhaka" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone / Contact (Optional)</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="e.g. +880 1712-345678" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bio / About Me</label>
                        <textarea name="bio" rows="4" placeholder="Share your academic background, passions, and what projects you're interested in working on..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden">{{ old('bio', $profile->bio ?: $profile->about_me) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Preferred Study Location Section --}}
            <div class="pt-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Preferred Study Location</h2>
                        <p class="text-xs text-slate-500">Click on the map or drag the pin to set your latitude and longitude coordinates.</p>
                    </div>
                    <button type="button" onclick="locateMe()" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                        <svg class="size-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Use My Current Location
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Location Name / Spot</label>
                            <input type="text" id="preferred_location_name" name="preferred_location_name" value="{{ old('preferred_location_name', $profile->preferred_location_name) }}" placeholder="e.g. Central Campus Library, Study Hall A" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Address / Floor (Optional)</label>
                            <input type="text" id="preferred_location_address" name="preferred_location_address" value="{{ old('preferred_location_address', $profile->preferred_location_address) }}" placeholder="e.g. Central Library, 2nd Floor West Wing" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Latitude</label>
                            <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $profile->latitude ?? 23.777176) }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono focus:border-blue-600 focus:outline-hidden" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Longitude</label>
                            <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $profile->longitude ?? 90.399452) }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono focus:border-blue-600 focus:outline-hidden" />
                        </div>
                    </div>

                    {{-- Map Picker Container --}}
                    <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-slate-100 h-80 w-full">
                        <div id="locationPickerMap" class="h-full w-full"></div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('profile.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Cancel
                </a>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-blue-700 transition">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

</div>

{{-- Leaflet Maps CDN & Picker Scripts --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    let map, marker;

    document.addEventListener('DOMContentLoaded', function () {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        let initialLat = parseFloat(latInput.value) || 23.777176;
        let initialLng = parseFloat(lngInput.value) || 90.399452;

        try {
            map = L.map('locationPickerMap').setView([initialLat, initialLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
            marker.bindPopup("Drag me or click anywhere on the map!").openPopup();

            // Update inputs on marker drag
            marker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                latInput.value = pos.lat.toFixed(7);
                lngInput.value = pos.lng.toFixed(7);
            });

            // Move marker and update inputs on map click
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                latInput.value = e.latlng.lat.toFixed(7);
                lngInput.value = e.latlng.lng.toFixed(7);
            });

            // Listen to manual input changes
            latInput.addEventListener('input', updateMarkerFromInputs);
            lngInput.addEventListener('input', updateMarkerFromInputs);

        } catch (e) {
            console.error("Map initialization failed", e);
        }
    });

    function updateMarkerFromInputs() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        if (!isNaN(lat) && !isNaN(lng) && marker && map) {
            marker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }
    }

    function locateMe() {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
                if (marker && map) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    marker.bindPopup("Your detected location!").openPopup();
                }
            }, function (error) {
                alert("Could not retrieve your location: " + error.message);
            });
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    }
</script>
@endsection
