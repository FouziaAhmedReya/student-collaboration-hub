@extends('layouts.app')

@section('title', 'Edit Student Profile')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Edit Student Profile & Skills</h1>
            <p class="mt-1 text-sm text-slate-600">Update your academic details, skills, projects, address, and bio saved to the database.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Gemini AI Personalized Event Recommendations for Student Profile -->
    @if(isset($aiEventRecommendations) && $aiEventRecommendations)
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-slate-50 p-6 text-slate-900 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-indigo-200/50 pb-3">
                <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                    </svg>
                    <span>Gemini AI Recommended Events for {{ $user->name }}</span>
                </div>
                <a href="{{ route('profile.edit', ['recommend_events' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold shadow-sm transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Refresh Event Recommendations
                </a>
            </div>
            <div id="aiEventProfileContent" class="text-sm text-slate-700 leading-relaxed"></div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rawText = @json($aiEventRecommendations);
                const container = document.getElementById('aiEventProfileContent');
                if (rawText && container) {
                    let text = rawText
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');

                    let lines = text.split('\n');
                    let html = '';
                    let inList = false;

                    lines.forEach(line => {
                        let trimmed = line.trim();
                        if (!trimmed) return;

                        if (trimmed.startsWith('# ')) {
                            if (inList) { html += '</ul>'; inList = false; }
                            let title = trimmed.replace(/^#\s*/, '');
                            html += `<div class="mt-3 mb-2"><h3 class="text-base font-extrabold text-indigo-950">${title}</h3></div>`;
                            return;
                        }

                        if (trimmed.startsWith('## ')) {
                            if (inList) { html += '</ul>'; inList = false; }
                            let title = trimmed.replace(/^##\s*/, '');
                            html += `
                                <div class="mt-3 mb-2">
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold shadow-sm uppercase tracking-wider" style="background-color: #2563eb !important; color: #ffffff !important;">
                                        ${title}
                                    </span>
                                </div>
                            `;
                            return;
                        }

                        if (trimmed.startsWith('* ') || trimmed.startsWith('- ') || trimmed.startsWith('• ')) {
                            if (!inList) { html += '<ul class="space-y-2 my-2 pl-1">'; inList = true; }
                            let itemContent = trimmed.replace(/^[\*\-\•]\s*/, '');
                            itemContent = itemContent.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');
                            html += `
                                <li class="flex items-start gap-2 text-sm text-slate-700">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-blue-600"></span>
                                    <span>${itemContent}</span>
                                </li>
                            `;
                            return;
                        }

                        if (inList) { html += '</ul>'; inList = false; }

                        let paragraphText = trimmed.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');
                        html += `<p class="my-2 text-sm leading-relaxed text-slate-700">${paragraphText}</p>`;
                    });

                    if (inList) { html += '</ul>'; }
                    container.innerHTML = html;
                }
            });
        </script>
    @else
        <!-- AI Event Recommendations CTA Box -->
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/70 via-blue-50/70 to-slate-50 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                    </svg>
                    <span>Gemini AI Event & Workshop Recommendations</span>
                </div>
                <p class="text-xs text-slate-600">Get personalized AI event & workshop recommendations tailored to your registered skills and interests.</p>
            </div>
            <a href="{{ route('profile.edit', ['recommend_events' => 1]) }}" onclick="this.innerHTML='Generating Recommendations...'; this.classList.add('opacity-75', 'pointer-events-none');" class="shrink-0 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                <span style="color: #ffffff !important;">View AI Recommended Events for Me</span>
            </a>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Academic & Personal Details Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 space-y-5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="size-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                Academic & Personal Details
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600">University Email</label>
                    <input type="email" id="email" value="{{ $user->email }}" disabled class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="department" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Department</label>
                    <input type="text" id="department" name="department" value="{{ old('department', $profile->department ?? 'Computer Science & Engineering') }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="location_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Address / Preferred Study Location</label>
                    <input type="text" id="location_name" name="location_name" value="{{ old('location_name', $profile->location_name ?? '') }}" placeholder="e.g. Mohakhali, Dhaka / UB2 BRAC University" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Bio / About Yourself</label>
                <textarea id="bio" name="bio" rows="3" placeholder="Share your academic interests, project experience, or team collaboration preferences..." class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('bio', $profile->bio ?? ($profile->about_me ?? '')) }}</textarea>
            </div>
        </div>

        <!-- Skills & Projects Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 space-y-5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="size-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                </svg>
                Technical Skills, Projects & Interests
            </h2>

            <div>
                <label for="skills" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Technical Skills (Comma Separated)</label>
                <input type="text" id="skills" name="skills" value="{{ old('skills', $skillsStr) }}" placeholder="e.g. Python, React, Laravel, Machine Learning, Gemini API" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="projects" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Completed Projects (Comma Separated)</label>
                <input type="text" id="projects" name="projects" value="{{ old('projects', $projectsStr) }}" placeholder="e.g. AI Study Assistant, Student Collaboration Hub, Portfolio Website" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="interests" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Interests & Domains (Comma Separated)</label>
                <input type="text" id="interests" name="interests" value="{{ old('interests', $interestsStr) }}" placeholder="e.g. Artificial Intelligence, Web Development, Hackathons" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="portfolio" class="block text-xs font-bold uppercase tracking-wider text-slate-600">Portfolio & Links (Comma Separated)</label>
                <input type="text" id="portfolio" name="portfolio" value="{{ old('portfolio', $portfolioStr) }}" placeholder="e.g. https://github.com/tuli, https://linkedin.com/in/tuli" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
        </div>

        <!-- Optional Password Reset Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Change Password (Optional)</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>
        </div>

        <!-- Bottom Action Bar with Save Submit Button -->
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <a href="{{ route('project-ideas.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Back to Dashboard
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                Save All Profile Details & Skills to Database
            </button>
        </div>
    </form>
</div>
@endsection
