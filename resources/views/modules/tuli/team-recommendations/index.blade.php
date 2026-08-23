@extends('layouts.app')

@section('title', 'Team Recommendation & Matcher')

@section('content')
<div class="space-y-8">
    <!-- Flash Success Alert -->
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-medium text-sm flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Header banner -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Team Recommendation & Matcher</h1>
            <p class="mt-1 text-sm text-slate-600">Match students to projects based on skill alignment, background, and AI-powered recommendations.</p>
        </div>
        <button onclick="openMatchModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
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
                <option value="" {{ !$project ? 'selected' : '' }}>-- Select a Project to View Teammate Recommendations --</option>
                @foreach($allProjects as $p)
                    <option value="{{ $p->id }}" {{ $project && $project->id == $p->id ? 'selected' : '' }}>
                        {{ $p->title }} (Skills: {{ $p->required_skills }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="w-full sm:w-auto rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition-colors">
                View Recommendations
            </button>
        </form>
    </div>

    @if(!$project)
        <!-- Placeholder when no project is selected -->
        <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center bg-white shadow-sm">
            <div class="mx-auto grid size-12 place-items-center rounded-full bg-blue-50 text-blue-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a5.97 5.97 0 0 0-.942 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-semibold text-slate-900">No project selected</h3>
            <p class="mt-1 text-sm text-slate-500">Please select a project from the dropdown above to view teammate recommendations, or match a new project team.</p>
            <div class="mt-6">
                <button onclick="openMatchModal()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    Match New Team
                </button>
            </div>
        </div>
    @else
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
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-slate-50 p-6 text-slate-900 shadow-sm">
                <div class="flex items-center gap-2 text-indigo-700 font-bold text-sm mb-3">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span>Gemini AI Team Composition Insights</span>
                </div>
                <div id="aiAnalysisContent" class="text-sm text-slate-700 leading-relaxed space-y-2"></div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const rawText = @json($aiAnalysis);
                    const container = document.getElementById('aiAnalysisContent');
                    if (rawText && container) {
                        let html = rawText
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/###\s*(.*?)$/gm, '<h4 class="font-bold text-slate-900 text-base mt-3 mb-1">$1</h4>')
                            .replace(/####\s*(.*?)$/gm, '<h5 class="font-bold text-slate-800 text-sm mt-2 mb-1">$1</h5>')
                            .replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-slate-900">$1</strong>');
                        
                        let lines = html.split('\n');
                        let formatted = lines.map(line => {
                            let trimmed = line.trim();
                            if (!trimmed) return '';
                            if (trimmed.startsWith('* ') || trimmed.startsWith('- ')) {
                                return `<li class="ml-4 list-disc text-slate-700 my-1">${trimmed.substring(2)}</li>`;
                            }
                            if (/^\d+\.\s/.test(trimmed)) {
                                return `<li class="ml-4 list-decimal text-slate-700 my-1">${trimmed.replace(/^\d+\.\s/, '')}</li>`;
                            }
                            if (trimmed.startsWith('<h')) return trimmed;
                            return `<p class="my-1.5 leading-relaxed">${line}</p>`;
                        }).join('');

                        container.innerHTML = formatted;
                    }
                });
            </script>
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
                            @if(!empty($student['bio']))
                                <p><strong class="font-semibold text-slate-700">Bio:</strong> {{ Str::limit($student['bio'], 100) }}</p>
                            @endif
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

        <form method="POST" action="{{ route('team-recommendations.match') }}" onsubmit="startMatchLoading()" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="projectTitle" class="block text-sm font-semibold text-slate-700">Project Title <span class="text-red-500">*</span></label>
                <input type="text" id="projectTitle" name="projectTitle" value="{{ old('projectTitle') }}" required placeholder="e.g. Campus Lost & Found App" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                @error('projectTitle')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="requiredSkills" class="block text-sm font-semibold text-slate-700">Required Skills (comma separated) <span class="text-red-500">*</span></label>
                <input type="text" id="requiredSkills" name="requiredSkills" value="{{ old('requiredSkills') }}" required placeholder="e.g. Figma, React, UI Design" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                @error('requiredSkills')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="teamSize" class="block text-sm font-semibold text-slate-700">Desired Team Size <span class="text-red-500">*</span></label>
                <input type="number" id="teamSize" name="teamSize" value="{{ old('teamSize', 3) }}" min="1" max="10" required class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                @error('teamSize')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('matchModal').classList.add('hidden')" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" id="matchSubmitBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-all disabled:opacity-75 disabled:cursor-not-allowed">
                    <svg id="matchBtnSpinner" class="hidden size-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="matchBtnText">Match Team</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMatchModal() {
        const btn = document.getElementById('matchSubmitBtn');
        const spinner = document.getElementById('matchBtnSpinner');
        const text = document.getElementById('matchBtnText');

        if (btn && spinner && text) {
            btn.disabled = false;
            spinner.classList.add('hidden');
            text.innerText = 'Match Team';
        }
        document.getElementById('matchModal').classList.remove('hidden');
    }

    function startMatchLoading() {
        const btn = document.getElementById('matchSubmitBtn');
        const spinner = document.getElementById('matchBtnSpinner');
        const text = document.getElementById('matchBtnText');

        if (btn && spinner && text) {
            spinner.classList.remove('hidden');
            text.innerText = 'Matching Team with AI...';
            setTimeout(() => {
                btn.disabled = true;
            }, 50);
        }
    }
</script>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('matchModal').classList.remove('hidden');
        });
    </script>
@endif
@endsection
