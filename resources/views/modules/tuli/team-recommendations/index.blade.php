@extends('layouts.app')

@section('title', 'Team Recommendation & Matcher')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Team Recommendation & Matcher</h1>
            <p class="mt-1 text-sm text-slate-600">Match students to projects based on skill alignment, background, and AI-powered recommendations.</p>
        </div>
        <button onclick="document.getElementById('matchModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a5.97 5.97 0 0 0-.942 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            Match New Team
        </button>
    </div>

    <!-- Active Project Switcher -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900 mb-3">Select Project for Teammate Recommendations</h2>
        <form method="GET" action="{{ route('team-recommendations.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <select name="project_id" onchange="this.form.submit()" class="w-full flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                @foreach($allProjects as $p)
                    <option value="{{ $p->id }}" {{ $project && $project->id == $p->id ? 'selected' : '' }}>
                        {{ $p->title }} (Required: {{ $p->required_skills }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="w-full sm:w-auto rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition-colors">
                View Recommendations
            </button>
        </form>
    </div>

    @if($project)
        <!-- Current Selected Project Details -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-900 to-slate-900 p-6 text-white shadow-md">
            <div class="flex items-center justify-between">
                <span class="rounded-md bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-200 border border-blue-400/20">
                    Project Target
                </span>
                <span class="text-xs text-slate-300">Team Size: {{ $project->team_size }}</span>
            </div>
            <h2 class="mt-3 text-xl font-bold sm:text-2xl">{{ $project->title }}</h2>
            <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-slate-300">
                <span class="font-medium text-white">Required Skills:</span>
                @foreach(array_map('trim', explode(',', $project->required_skills)) as $skill)
                    <span class="rounded-lg bg-white/10 px-2.5 py-1 text-xs font-medium text-white border border-white/10">
                        {{ $skill }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- AI Analysis Banner if present -->
        @if($aiAnalysis)
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-blue-50 p-6 text-slate-900 shadow-sm">
                <div class="flex items-center gap-2 text-indigo-700 font-bold text-sm mb-2">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span>Gemini AI Team Composition Insights</span>
                </div>
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $aiAnalysis }}</p>
            </div>
        @endif

        <!-- Recommended Teammates List -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Recommended Teammates</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($recommendedTeammates as $student)
                    <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-slate-500">{{ $student['department'] }}</span>
                                @if($student['match_percent'] >= 100)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        {{ $student['match_percent'] }}% Match
                                    </span>
                                @elseif($student['match_percent'] >= 50)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                        {{ $student['match_percent'] }}% Match
                                    </span>
                                @elseif($student['match_percent'] > 0)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                        {{ $student['match_percent'] }}% Match
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-400">
                                        0% Match
                                    </span>
                                @endif
                            </div>

                            <h4 class="mt-3 text-lg font-bold text-slate-900">{{ $student['name'] }}</h4>

                            <div class="mt-3">
                                <span class="block text-xs font-medium text-slate-400 mb-1">Skills:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_map('trim', explode(',', $student['skills'])) as $sk)
                                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                            {{ $sk }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 text-xs space-y-1 text-slate-500">
                            @if(!empty($student['_interests']))
                                <p><strong class="font-semibold text-slate-700">Interests:</strong> {{ $student['_interests'] }}</p>
                            @endif
                            @if(!empty($student['_completed_projects']))
                                <p><strong class="font-semibold text-slate-700">Completed:</strong> {{ $student['_completed_projects'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Modal for Dynamic Team Matcher -->
<div id="matchModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20">
    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-xl font-bold text-slate-900">Find a Team (Dynamic Matcher)</h2>
            <button onclick="document.getElementById('matchModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('team-recommendations.match') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="projectTitle" class="block text-sm font-semibold text-slate-700">Project Title <span class="text-red-500">*</span></label>
                <input type="text" id="projectTitle" name="projectTitle" required placeholder="e.g. Campus Lost & Found App" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="requiredSkills" class="block text-sm font-semibold text-slate-700">Required Skills (comma separated) <span class="text-red-500">*</span></label>
                <input type="text" id="requiredSkills" name="requiredSkills" required placeholder="e.g. Figma, React, UI Design" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="teamSize" class="block text-sm font-semibold text-slate-700">Desired Team Size</label>
                <input type="number" id="teamSize" name="teamSize" value="3" min="1" max="10" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('matchModal').classList.add('hidden')" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    Match Team
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
