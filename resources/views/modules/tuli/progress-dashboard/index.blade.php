@extends('layouts.app')

@section('title', 'Progress Dashboard & AI Insights')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Progress Dashboard</h1>
            <p class="mt-1 text-sm text-slate-600">Track project completion velocity, monitor team member workloads, and view AI-generated productivity insights.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.25-2.142V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08" />
                </svg>
                Manage Tasks
            </a>
        </div>
    </div>

    <!-- Active Project Switcher -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900 mb-3">Filter Dashboard by Project</h2>
        <form method="GET" action="{{ route('progress-dashboard.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <select name="project_id" onchange="this.form.submit()" class="w-full flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="" {{ !$selectedProject ? 'selected' : '' }}>-- All Active Projects Summary --</option>
                @foreach($allProjects as $p)
                    <option value="{{ $p->id }}" {{ $selectedProject && $selectedProject->id == $p->id ? 'selected' : '' }}>
                        {{ $p->title }} ({{ $p->tasks->count() }} Tasks)
                    </option>
                @endforeach
            </select>
            <button type="submit" class="w-full sm:w-auto rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Metrics Stat Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Completion Rate -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completion Rate</span>
                <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $completionPercentage }}%</p>
            <div class="mt-3 w-full rounded-full bg-slate-100 h-2 overflow-hidden">
                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $completionPercentage }}%"></div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Tasks</span>
                <span class="grid size-9 place-items-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75v-.008z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $totalTasksCount }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $completedTasksCount }} completed · {{ $inProgressTasksCount }} in progress</p>
        </div>

        <!-- Pending Work -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Tasks</span>
                <span class="grid size-9 place-items-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $pendingTasksCount }}</p>
            <p class="mt-2 text-xs text-amber-600 font-medium">Requires team attention</p>
        </div>

        <!-- Active Projects / Overdue -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Projects</span>
                <span class="grid size-9 place-items-center rounded-xl bg-purple-50 text-purple-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12.75M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $totalProjectsCount }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $overdueCount }} overdue tasks</p>
        </div>
    </div>

    <!-- Gemini AI Productivity Insights Banner -->
    @if($aiInsights)
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-slate-50 p-6 text-slate-900 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-indigo-200/50 pb-4">
                <div class="flex items-center gap-2 text-indigo-700 font-bold text-base">
                    <svg class="size-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span>Gemini AI Productivity Insights & Velocity Analysis</span>
                </div>
                <a href="{{ route('progress-dashboard.index', ['project_id' => $selectedProject?->id, 'generate_ai' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold shadow-sm transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Refresh AI Insights
                </a>
            </div>
            <div id="aiInsightsContent" class="text-sm text-slate-700 leading-relaxed"></div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rawText = @json($aiInsights);
                const container = document.getElementById('aiInsightsContent');
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
                            html += `<div class="mt-4 mb-2"><h3 class="text-base font-extrabold text-indigo-950">${title}</h3></div>`;
                            return;
                        }

                        if (trimmed.startsWith('## ')) {
                            if (inList) { html += '</ul>'; inList = false; }
                            let title = trimmed.replace(/^##\s*/, '');
                            html += `
                                <div class="mt-4 mb-2">
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold shadow-sm uppercase tracking-wider" style="background-color: #2563eb !important; color: #ffffff !important;">
                                        ${title}
                                    </span>
                                </div>
                            `;
                            return;
                        }

                        if (trimmed.startsWith('### ')) {
                            if (inList) { html += '</ul>'; inList = false; }
                            let title = trimmed.replace(/^###\s*/, '');
                            html += `<h4 class="font-bold text-slate-900 text-sm mt-3 mb-1">${title}</h4>`;
                            return;
                        }

                        if (trimmed.startsWith('* ') || trimmed.startsWith('- ') || trimmed.startsWith('• ')) {
                            if (!inList) { html += '<ul class="space-y-2 my-2.5 pl-1">'; inList = true; }
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

                        if (/^\d+\.\s/.test(trimmed)) {
                            if (!inList) { html += '<ol class="space-y-2 my-2.5 pl-1 list-decimal list-inside text-sm text-slate-700">'; inList = true; }
                            let itemContent = trimmed.replace(/^\d+\.\s/, '');
                            itemContent = itemContent.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');
                            html += `<li class="my-1">${itemContent}</li>`;
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
        <!-- AI Insights Call-to-Action Box (Initial Page Load) -->
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/70 via-blue-50/70 to-slate-50 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span>Gemini AI Productivity Insights & Velocity Analysis</span>
                </div>
                <p class="text-xs text-slate-600">Click below to generate real-time AI health summaries, velocity analysis, and bottleneck warnings for your active projects.</p>
            </div>
            <a href="{{ route('progress-dashboard.index', array_filter(['project_id' => $selectedProject?->id, 'generate_ai' => 1])) }}" onclick="this.innerHTML='Generating AI Insights...'; this.classList.add('opacity-75', 'pointer-events-none');" class="shrink-0 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                <span style="color: #ffffff !important;">Generate AI Insights</span>
            </a>
        </div>
    @endif

    <!-- Projects Progress Breakdown Grid -->
    <div class="space-y-4">
        <h3 class="text-lg font-bold text-slate-900">Project Progress Overview</h3>
        @if($allProjects->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center bg-white">
                <p class="text-sm text-slate-500">No active projects found. Create a project to start tracking progress.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach($allProjects as $proj)
                    @php
                        $pTotal = $proj->tasks->count();
                        $pComp = $proj->tasks->where('status', 'completed')->count();
                        $pPct = $pTotal > 0 ? (int) round(($pComp / $pTotal) * 100) : 0;
                    @endphp
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                Team Size: {{ $proj->team_size }}
                            </span>
                            <span class="text-xs font-bold text-slate-700">{{ $pPct }}% Completed</span>
                        </div>
                        <h4 class="mt-3 text-lg font-bold text-slate-900">{{ $proj->title }}</h4>
                        <p class="mt-1 text-xs text-slate-500">Skills: {{ $proj->required_skills }}</p>

                        <div class="mt-4">
                            <div class="flex justify-between text-xs font-medium text-slate-600 mb-1">
                                <span>{{ $pComp }} of {{ $pTotal }} Tasks Done</span>
                                <span>{{ $pPct }}%</span>
                            </div>
                            <div class="w-full rounded-full bg-slate-100 h-2.5 overflow-hidden">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pPct }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $proj->tasks->where('status', 'in_progress')->count() }} In Progress</span>
                            <a href="{{ route('progress-dashboard.index', ['project_id' => $proj->id]) }}" class="font-semibold text-blue-600 hover:underline">
                                View Details &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Team Member Performance Table -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <h3 class="text-lg font-bold text-slate-900">Team Member Performance & Workload</h3>
        @if(empty($teamPerformance))
            <p class="text-sm text-slate-500">No assigned tasks recorded for team members yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500 font-semibold">
                        <tr>
                            <th class="px-4 py-3">Team Member</th>
                            <th class="px-4 py-3">Assigned Tasks</th>
                            <th class="px-4 py-3">Completed</th>
                            <th class="px-4 py-3">Pending</th>
                            <th class="px-4 py-3">Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($teamPerformance as $tp)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3.5 font-semibold text-slate-900">{{ $tp['member'] }}</td>
                                <td class="px-4 py-3.5">{{ $tp['total_tasks'] }}</td>
                                <td class="px-4 py-3.5 font-medium text-emerald-600">{{ $tp['completed_tasks'] }}</td>
                                <td class="px-4 py-3.5 font-medium text-amber-600">{{ $tp['pending_tasks'] }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-slate-900 min-w-10">{{ $tp['completion_rate'] }}%</span>
                                        <div class="w-24 rounded-full bg-slate-100 h-2 overflow-hidden">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $tp['completion_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
