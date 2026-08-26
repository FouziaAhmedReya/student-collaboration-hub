@extends('layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="mx-auto max-w-lg space-y-6 py-8">
    <div class="text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Student Registration</h1>
        <p class="mt-2 text-sm text-slate-600">Create your account to access project ideas, team matching, and dashboard.</p>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">Full Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Tuli Saha" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">University Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="student@g.bracu.ac.bd" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label for="department" class="block text-sm font-semibold text-slate-700">Department</label>
                <input type="text" id="department" name="department" value="{{ old('department', 'Computer Science & Engineering') }}" placeholder="e.g. Computer Science & Engineering" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="skills" class="block text-sm font-semibold text-slate-700">Technical Skills (Comma Separated)</label>
                <input type="text" id="skills" name="skills" value="{{ old('skills') }}" placeholder="e.g. Python, React, Laravel, Machine Learning" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="interests" class="block text-sm font-semibold text-slate-700">Interests (Comma Separated)</label>
                <input type="text" id="interests" name="interests" value="{{ old('interests') }}" placeholder="e.g. AI, Fullstack Web Apps, Hackathons" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="bio" class="block text-sm font-semibold text-slate-700">Bio / About Yourself</label>
                <textarea id="bio" name="bio" rows="3" placeholder="Brief description of your academic background and project goals..." class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('bio') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Register Account
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
            Already registered?
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">Log In Here</a>
        </div>
    </div>
</div>
@endsection
