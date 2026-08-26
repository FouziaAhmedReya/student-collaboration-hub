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
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Team Recommendation & Matcher</h1>
        <p class="text-sm text-slate-600">Match students to projects based on skill alignment, academic background, and AI-powered recommendations.</p>
    </div>

    <!-- Active Project Switcher -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-3">Select Project for Teammate Recommendations</h2>
        <form method="GET" action="{{ route('team-recommendations.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <select name="project_id" onchange="this.form.submit()" class="w-full flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="" {{ !$project ? 'selected' : '' }}>-- Select a Project to View Teammate Recommendations --</option>
                @foreach($allProjects as $p)
                    <option value="{{ $p->id }}" {{ $project && $project->id == $p->id ? 'selected' : '' }}>
                        {{ $p->title }} (Skills: {{ $p->required_skills }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="w-full sm:w-auto rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                View Teammates
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
            <p class="mt-1 text-sm text-slate-500">Please select a project from the dropdown above to view teammate recommendations.</p>
        </div>
    @else
        <!-- Current Selected Project Details -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-900 to-slate-900 p-6 text-white shadow-md">
            <div class="flex items-center justify-between">
                <span class="rounded-md bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-200 border border-blue-400/20">
                    Target Project
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

        <!-- On-Demand Gemini AI Recommendations Container -->
        @if($aiAnalysis)
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-slate-50 p-6 text-slate-900 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-indigo-200/50 pb-4">
                    <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                        <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                        </svg>
                        <span>Gemini AI Humanized Teammate Analysis</span>
                    </div>
                    <a href="{{ route('team-recommendations.index', ['project_id' => $project->id, 'generate_ai' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold shadow-sm transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                        Refresh Recommendations
                    </a>
                </div>
                <div id="aiAnalysisContent" class="text-sm text-slate-700 leading-relaxed"></div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const rawText = @json($aiAnalysis);
                    const container = document.getElementById('aiAnalysisContent');
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
            <!-- AI Recommendations Call-to-Action Box -->
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/70 via-blue-50/70 to-slate-50 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                        <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                        </svg>
                        <span>Gemini AI Humanized Teammate Recommendation</span>
                    </div>
                    <p class="text-xs text-slate-600">Click below to generate minimal, humanized AI recommendations explaining why candidates fit {{ $project->title }}.</p>
                </div>
                <a href="{{ route('team-recommendations.index', ['project_id' => $project->id, 'generate_ai' => 1]) }}" onclick="this.innerHTML='Generating AI Teammate Insights...'; this.classList.add('opacity-75', 'pointer-events-none');" class="shrink-0 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    <span style="color: #ffffff !important;">Generate AI Team Recommendations</span>
                </a>
            </div>
        @endif

        <!-- Candidate Students List -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Recommended Candidate Teammates</h3>
                <span class="text-xs font-semibold text-slate-500">{{ count($recommendedTeammates) }} Candidates Evaluated</span>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($recommendedTeammates as $candidate)
                    <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all">
                        <div>
                            <!-- Header with Avatar and Match Bar -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="grid size-12 place-items-center rounded-xl bg-blue-600 text-white font-bold text-base shadow-sm">
                                        {{ strtoupper(substr($candidate['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-base leading-snug">{{ $candidate['name'] }}</h4>
                                        <p class="text-xs text-slate-500">{{ $candidate['department'] }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Skill Match Badge -->
                            <div class="mt-4 rounded-xl bg-slate-50 p-3 border border-slate-100 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-700">Skill Match</span>
                                    <span class="font-bold text-blue-600">{{ $candidate['match_percent'] }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-blue-600 transition-all duration-500" style="width: {{ $candidate['match_percent'] }}%"></div>
                                </div>
                            </div>

                            <!-- Skills Tags -->
                            @if(!empty($candidate['skills']))
                                <div class="mt-4 space-y-1">
                                    <span class="text-xs font-semibold text-slate-500">Skills:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_map('trim', explode(',', $candidate['skills'])) as $sk)
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                                {{ $sk }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Student Bio / Background Snippet -->
                            @if(!empty($candidate['bio']))
                                <div class="mt-3 text-xs text-slate-600 line-clamp-3 bg-slate-50/50 p-2.5 rounded-lg border border-slate-100">
                                    <strong class="font-bold text-slate-800">Bio:</strong> {{ $candidate['bio'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
