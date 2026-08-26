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

            {{-- Preferred Study Location Section (Simple Text Inputs) --}}
            <div class="pt-4">
                <div class="border-b border-slate-100 pb-2">
                    <h2 class="text-base font-bold text-slate-900">Preferred Study Location</h2>
                    <p class="text-xs text-slate-500">Specify your study location or campus spot address.</p>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Location Name / Spot</label>
                        <input type="text" id="preferred_location_name" name="preferred_location_name" value="{{ old('preferred_location_name', $profile->preferred_location_name) }}" placeholder="e.g. Central Campus Library, Study Hall A" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Address / Floor</label>
                        <input type="text" id="preferred_location_address" name="preferred_location_address" value="{{ old('preferred_location_address', $profile->preferred_location_address) }}" placeholder="e.g. Central Library, 2nd Floor, Mohakhali, Dhaka" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-600 focus:outline-hidden" />
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                <a href="{{ route('profile.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Save All Profile Details & Location to Database
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
