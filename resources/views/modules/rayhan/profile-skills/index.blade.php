@extends('layouts.app')

@section('title', 'Student Profile & Skill Management')

@section('content')
<div class="space-y-8">

    {{-- Profile Header Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="h-32 bg-linear-to-r from-blue-600 via-indigo-600 to-sky-500 sm:h-40"></div>
        <div class="relative px-4 pb-6 sm:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                {{-- Avatar and Details --}}
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-16 sm:-mt-20">
                    <div class="grid size-24 sm:size-32 place-items-center rounded-2xl border-4 border-white bg-slate-900 text-2xl sm:text-3xl font-bold text-white shadow-md shadow-slate-200">
                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' ') ?: ' ', 1, 1)) ?: 'S' }}
                    </div>
                    <div class="pt-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $user->name }}</h1>
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                Student
                            </span>
                        </div>
                        <p class="text-sm text-slate-500">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600">
                            @if($profile->department)
                                <span class="flex items-center gap-1.5 font-medium text-slate-700">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $profile->department }}
                                </span>
                            @endif
                            @if($profile->semester)
                                <span class="flex items-center gap-1.5">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $profile->semester }}
                                </span>
                            @endif
                            @if($profile->university)
                                <span class="flex items-center gap-1.5">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                    {{ $profile->university }}
                                </span>
                            @endif
                            @if($profile->phone)
                                <span class="flex items-center gap-1.5">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $profile->phone }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="mt-4 sm:mt-0 flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-blue-700 transition">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile & Location
                    </a>
                </div>
            </div>

            {{-- Bio / About Me --}}
            @if($profile->bio || $profile->about_me)
                <div class="mt-6 rounded-xl bg-slate-50 p-4 border border-slate-100 text-sm text-slate-700 leading-relaxed">
                    <p class="font-medium text-slate-900 mb-1">About Me</p>
                    {{ $profile->bio ?: $profile->about_me }}
                </div>
            @endif
        </div>
    </div>

    {{-- Gemini AI Personalized Event Recommendations for Student Profile --}}
    @if(isset($aiEventRecommendations) && $aiEventRecommendations)
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-slate-50 p-6 text-slate-900 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-indigo-200/50 pb-3">
                <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                    </svg>
                    <span>Gemini AI Recommended Events & Workshops for {{ $user->name }}</span>
                </div>
                <a href="{{ route('profile.index', ['recommend_events' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold shadow-sm transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Refresh Recommendations
                </a>
            </div>
            <div id="aiEventMainProfileContent" class="text-sm text-slate-700 leading-relaxed"></div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rawText = @json($aiEventRecommendations);
                const container = document.getElementById('aiEventMainProfileContent');
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
                <p class="text-xs text-slate-600">Click below to generate personalized AI event & workshop recommendations tailored to your registered skills and interests.</p>
            </div>
            <a href="{{ route('profile.index', ['recommend_events' => 1]) }}" onclick="this.innerHTML='Generating Recommendations...'; this.classList.add('opacity-75', 'pointer-events-none');" class="shrink-0 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                <span style="color: #ffffff !important;">View AI Recommended Events for Me</span>
            </a>
        </div>
    @endif

    {{-- Profile Completion Percentage Widget --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Profile Completion Status</h2>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $completionPercentage >= 100 ? 'bg-emerald-100 text-emerald-800' : ($completionPercentage >= 60 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $completionPercentage }}% Complete
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Keep your profile updated to increase visibility for team matching and collaboration.</p>
            </div>
            <div class="w-full md:w-64">
                <div class="h-3 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full transition-all duration-500 rounded-full {{ $completionPercentage >= 100 ? 'bg-emerald-500' : ($completionPercentage >= 60 ? 'bg-blue-600' : 'bg-amber-500') }}" style="width: {{ $completionPercentage }}%"></div>
                </div>
            </div>
        </div>

        {{-- Completion Checklist Grid --}}
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 pt-4 border-t border-slate-100">
            @foreach($completionDetails as $key => $item)
                <div class="flex items-center gap-2 text-xs">
                    @if($item['completed'])
                        <span class="grid size-5 place-items-center rounded-full bg-emerald-100 text-emerald-600 shrink-0">
                            <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span class="text-slate-700 font-medium line-through opacity-75">{{ $item['label'] }}</span>
                    @else
                        <span class="grid size-5 place-items-center rounded-full bg-slate-100 text-slate-400 shrink-0">
                            <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
                                <circle cx="10" cy="10" r="4" />
                            </svg>
                        </span>
                        <span class="text-slate-600 font-medium">{{ $item['label'] }} <span class="text-slate-400 font-normal">(+{{ $item['weight'] }}%)</span></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- 2-Column Grid for Sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column: Skills & Interests & Portfolio (1/3) --}}
        <div class="space-y-8 lg:col-span-1">

            {{-- Technical Skills Section --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-blue-50 text-blue-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-900">Technical Skills</h2>
                    </div>
                    <button onclick="toggleModal('addSkillModal')" class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">
                        + Add Skill
                    </button>
                </div>

                @if($profile->skills->isEmpty())
                    <p class="text-xs text-slate-500 py-4 text-center">No technical skills added yet. Add your core programming languages, frameworks, or tools.</p>
                @else
                    <div class="space-y-3">
                        @foreach($profile->skills as $skill)
                            <div class="rounded-xl bg-slate-50 p-3 border border-slate-100 hover:border-slate-200 transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-slate-900">{{ $skill->name }}</span>
                                            @if($skill->category)
                                                <span class="text-xs text-slate-400 font-normal">({{ $skill->category }})</span>
                                            @endif
                                        </div>
                                        @php
                                            $levelColors = [
                                                'Beginner' => 'bg-slate-100 text-slate-700',
                                                'Intermediate' => 'bg-blue-100 text-blue-700',
                                                'Advanced' => 'bg-indigo-100 text-indigo-700',
                                                'Expert' => 'bg-purple-100 text-purple-700',
                                            ];
                                        @endphp
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-block rounded-md px-2 py-0.5 text-[11px] font-medium {{ $levelColors[$skill->proficiency_level] ?? 'bg-blue-100 text-blue-700' }}">
                                                {{ $skill->proficiency_level }}
                                            </span>
                                            @if($skill->proficiency)
                                                <span class="text-[11px] font-mono text-slate-500">{{ $skill->proficiency }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button onclick="editSkill({{ $skill->id }}, '{{ addslashes($skill->name) }}', {{ $skill->proficiency ?? 50 }}, '{{ $skill->proficiency_level }}', '{{ addslashes($skill->category ?? '') }}')" class="p-1 text-slate-400 hover:text-blue-600 transition" title="Edit">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('profile.skills.destroy', $skill) }}" method="POST" onsubmit="return confirm('Remove skill {{ $skill->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Delete">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($skill->proficiency)
                                    <div class="mt-2 h-1.5 w-full rounded-full bg-slate-200 overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ $skill->proficiency }}%"></div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Interests Section --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-900">Interests & Domains</h2>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button onclick="fetchSuggestions()" class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200 transition" title="Show department suggestions">
                            💡 Suggestions
                        </button>
                        <button onclick="toggleModal('addInterestModal')" class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                            + Add
                        </button>
                    </div>
                </div>

                {{-- Department suggestions dropdown / container --}}
                <div id="suggestionsBox" class="hidden mb-3 p-3 rounded-xl bg-indigo-50/70 border border-indigo-100 text-xs">
                    <p class="font-bold text-indigo-900 mb-1.5">Suggested for {{ $profile->department ?? 'your department' }}:</p>
                    <div id="suggestionsList" class="flex flex-wrap gap-1.5">
                        <span class="text-slate-500">Loading suggestions...</span>
                    </div>
                </div>

                @if($profile->interests->isEmpty())
                    <p class="text-xs text-slate-500 py-4 text-center">No interests added yet. Add research areas or career interests (e.g. AI, Cyber Security, Web Dev).</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($profile->interests as $interest)
                            <div class="group inline-flex items-center gap-1.5 rounded-xl bg-slate-50 px-3 py-1.5 border border-slate-200 text-xs font-medium text-slate-800">
                                <span>{{ $interest->name }}</span>
                                @if($interest->category)
                                    <span class="text-slate-400 font-normal">({{ $interest->category }})</span>
                                @endif
                                <form action="{{ route('profile.interests.destroy', $interest) }}" method="POST" class="inline" onsubmit="return confirm('Remove interest {{ $interest->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition ml-1" title="Delete">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Portfolio Links Section --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-sky-50 text-sky-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-900">Portfolio & Links</h2>
                    </div>
                    <button onclick="toggleModal('addPortfolioModal')" class="rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-100 transition">
                        + Add Link
                    </button>
                </div>

                @if($profile->portfolioLinks->isEmpty())
                    <p class="text-xs text-slate-500 py-4 text-center">No portfolio links added. Add your GitHub, LinkedIn, personal website, or LeetCode profile.</p>
                @else
                    <div class="space-y-2">
                        @foreach($profile->portfolioLinks as $link)
                            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-2.5 border border-slate-100 hover:border-slate-200 transition">
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 text-xs font-semibold text-blue-700 hover:underline truncate max-w-[200px]">
                                    @if(stripos($link->platform ?? $link->title, 'github') !== false)
                                        <span class="inline-block font-mono bg-slate-800 text-white rounded px-1.5 py-0.5 text-[10px]">GH</span>
                                    @elseif(stripos($link->platform ?? $link->title, 'linkedin') !== false)
                                        <span class="inline-block font-mono bg-blue-700 text-white rounded px-1.5 py-0.5 text-[10px]">IN</span>
                                    @elseif(stripos($link->platform ?? $link->title, 'leetcode') !== false)
                                        <span class="inline-block font-mono bg-amber-600 text-white rounded px-1.5 py-0.5 text-[10px]">LC</span>
                                    @else
                                        <svg class="size-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    @endif
                                    <span class="truncate">{{ $link->display_title }}</span>
                                </a>
                                <div class="flex items-center gap-1">
                                    <button onclick="editPortfolioLink({{ $link->id }}, '{{ addslashes($link->title ?? '') }}', '{{ addslashes($link->platform ?? '') }}', '{{ addslashes($link->url) }}')" class="p-1 text-slate-400 hover:text-blue-600 transition" title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('profile.portfolio-links.destroy', $link) }}" method="POST" onsubmit="return confirm('Remove link {{ $link->title }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Delete">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Right Column: Completed Projects & Preferred Study Location (2/3) --}}
        <div class="space-y-8 lg:col-span-2">

            {{-- Completed Projects Section --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Completed Projects</h2>
                            <p class="text-xs text-slate-500">Showcase past academic, team, and personal portfolio projects</p>
                        </div>
                    </div>
                    <button onclick="toggleModal('addProjectModal')" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                        + Add Project
                    </button>
                </div>

                @if($profile->studentProjects->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                        <svg class="mx-auto size-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="mt-2 text-sm font-medium text-slate-700">No completed projects added yet</p>
                        <p class="mt-1 text-xs text-slate-400">Add projects you built to demonstrate your hands-on engineering skills.</p>
                        <button onclick="toggleModal('addProjectModal')" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-xs hover:bg-blue-700 transition">
                            Add Your First Project
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($profile->studentProjects as $project)
                            <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 hover:border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-bold text-sm text-slate-900">{{ $project->title }}</h3>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button onclick="editProject({{ $project->id }}, '{{ addslashes($project->title) }}', '{{ addslashes($project->description ?? '') }}', '{{ addslashes($project->technologies ?? '') }}', '{{ addslashes($project->project_url ?? '') }}', '{{ addslashes($project->repo_url ?? '') }}', '{{ addslashes($project->completed_date ?? '') }}')" class="p-1 text-slate-400 hover:text-blue-600 transition" title="Edit">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('profile.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete project {{ $project->title }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Delete">
                                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @if($project->description)
                                        <p class="mt-2 text-xs text-slate-600 line-clamp-3 leading-relaxed">{{ $project->description }}</p>
                                    @endif
                                    @if($project->technologies)
                                        <div class="mt-3 flex flex-wrap gap-1">
                                            @foreach(explode(',', $project->technologies) as $tech)
                                                <span class="rounded bg-white px-2 py-0.5 text-[11px] font-medium text-slate-700 border border-slate-200">
                                                    {{ trim($tech) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs">
                                    <span class="text-slate-400">{{ $project->completed_date ?? 'Completed' }}</span>
                                    <div class="flex items-center gap-3">
                                        @if($project->repo_url)
                                            <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-slate-700 hover:text-blue-600 inline-flex items-center gap-1">
                                                Code &rarr;
                                            </a>
                                        @endif
                                        @if($project->project_url)
                                            <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-700 hover:underline inline-flex items-center gap-1">
                                                Live Demo &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Preferred Study Location Section --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-blue-50 text-blue-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Preferred Study Location / Address</h2>
                            <p class="text-xs text-slate-500">Campus study spot and address</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-blue-600 hover:underline">
                        Edit Location &rarr;
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-xs">
                    @if($profile->preferred_location_name || $profile->location_name)
                        <span class="font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                            📍 {{ $profile->preferred_location_name ?: $profile->location_name }}
                        </span>
                    @endif
                    @if($profile->preferred_location_address)
                        <span class="text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                            {{ $profile->preferred_location_address }}
                        </span>
                    @endif
                    @if(!$profile->preferred_location_name && !$profile->location_name && !$profile->preferred_location_address)
                        <span class="text-slate-500 font-medium">No study address saved yet. <a href="{{ route('profile.edit') }}" class="text-blue-600 underline font-bold">Add Address</a></span>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

{{-- MODALS FOR ADD/EDIT --}}

{{-- Add Skill Modal --}}
<div id="addSkillModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Add Technical Skill</h3>
            <button onclick="toggleModal('addSkillModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ route('profile.skills.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Skill Name *</label>
                <input type="text" name="name" required placeholder="e.g. PHP, React, Python, Docker" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Proficiency Level *</label>
                    <select id="addSkillLevelSelect" name="proficiency_level" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate" selected>Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Proficiency (0-100)</label>
                    <input type="number" min="0" max="100" name="proficiency" placeholder="e.g. 75" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category (Optional)</label>
                <input type="text" name="category" placeholder="e.g. Backend, Frontend, DevOps" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('addSkillModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Save to Database</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Skill Modal --}}
<div id="editSkillModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Edit Technical Skill</h3>
            <button onclick="toggleModal('editSkillModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="editSkillForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Skill Name *</label>
                <input type="text" id="editSkillName" name="name" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Proficiency Level *</label>
                    <select id="editSkillLevel" name="proficiency_level" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Proficiency (0-100)</label>
                    <input type="number" min="0" max="100" id="editSkillProficiency" name="proficiency" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category (Optional)</label>
                <input type="text" id="editSkillCategory" name="category" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('editSkillModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">Update Skill</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Interest Modal --}}
<div id="addInterestModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Add Academic / Career Interest</h3>
            <button onclick="toggleModal('addInterestModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ route('profile.interests.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Interest / Domain Name *</label>
                <input type="text" id="addInterestName" name="name" required placeholder="e.g. Artificial Intelligence, Cloud Computing" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category (Optional)</label>
                <input type="text" id="addInterestCategory" name="category" placeholder="e.g. Research, Career, Hobby" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('addInterestModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Save Interest to Database</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Project Modal --}}
<div id="addProjectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl my-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Add Completed Student Project</h3>
            <button onclick="toggleModal('addProjectModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ route('profile.projects.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Project Title *</label>
                <input type="text" name="title" required placeholder="e.g. Student Collaboration Hub" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Briefly describe what you built and your role..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Technologies Used</label>
                <input type="text" name="technologies" placeholder="e.g. Laravel, PHP, SQLite, Tailwind CSS" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Project / Demo URL</label>
                    <input type="url" name="project_url" placeholder="https://example.com" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Code / Repo URL</label>
                    <input type="url" name="repo_url" placeholder="https://github.com/..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Completed Date / Term</label>
                <input type="text" name="completed_date" placeholder="e.g. Fall 2025, August 2026" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('addProjectModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Save Project to Database</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Project Modal --}}
<div id="editProjectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl my-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Edit Completed Project</h3>
            <button onclick="toggleModal('editProjectModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="editProjectForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Project Title *</label>
                <input type="text" id="editProjectTitle" name="title" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description</label>
                <textarea id="editProjectDescription" name="description" rows="3" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Technologies Used</label>
                <input type="text" id="editProjectTechnologies" name="technologies" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Project / Demo URL</label>
                    <input type="url" id="editProjectUrl" name="project_url" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Code / Repo URL</label>
                    <input type="url" id="editProjectRepo" name="repo_url" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Completed Date / Term</label>
                <input type="text" id="editProjectDate" name="completed_date" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('editProjectModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Update Project in Database</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Portfolio Modal --}}
<div id="addPortfolioModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Add Portfolio / Social Link</h3>
            <button onclick="toggleModal('addPortfolioModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ route('profile.portfolio-links.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Platform (Optional)</label>
                <select name="platform" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden">
                    <option value="">-- Select Platform --</option>
                    <option value="GitHub">GitHub</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="LeetCode">LeetCode</option>
                    <option value="Website">Personal Website</option>
                    <option value="Behance">Behance / Dribbble</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Title / Label (Optional)</label>
                <input type="text" name="title" placeholder="e.g. My GitHub Profile, Personal Portfolio" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">URL *</label>
                <input type="url" name="url" required placeholder="https://github.com/username" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('addPortfolioModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Save Link to Database</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Portfolio Modal --}}
<div id="editPortfolioModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Edit Portfolio Link</h3>
            <button onclick="toggleModal('editPortfolioModal')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="editPortfolioForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Platform (Optional)</label>
                <select id="editPortfolioPlatform" name="platform" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden">
                    <option value="">-- Select Platform --</option>
                    <option value="GitHub">GitHub</option>
                    <option value="LinkedIn">LinkedIn</option>
                    <option value="LeetCode">LeetCode</option>
                    <option value="Website">Personal Website</option>
                    <option value="Behance">Behance / Dribbble</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Title / Label (Optional)</label>
                <input type="text" id="editPortfolioTitle" name="title" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">URL *</label>
                <input type="url" id="editPortfolioUrl" name="url" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="toggleModal('editPortfolioModal')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Update Link in Database</button>
            </div>
        </form>
    </div>
</div>

{{-- Leaflet Maps CDN & Scripts --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function editSkill(id, name, proficiency, level, category) {
        document.getElementById('editSkillForm').action = "{{ url('profile/skills') }}/" + id;
        document.getElementById('editSkillName').value = name;
        document.getElementById('editSkillProficiency').value = proficiency;
        document.getElementById('editSkillLevel').value = level;
        document.getElementById('editSkillCategory').value = category;
        toggleModal('editSkillModal');
    }

    function editPortfolioLink(id, title, platform, url) {
        document.getElementById('editPortfolioForm').action = "{{ url('profile/portfolio-links') }}/" + id;
        document.getElementById('editPortfolioTitle').value = title;
        document.getElementById('editPortfolioPlatform').value = platform;
        document.getElementById('editPortfolioUrl').value = url;
        toggleModal('editPortfolioModal');
    }

    function editProject(id, title, description, technologies, project_url, repo_url, completed_date) {
        document.getElementById('editProjectForm').action = "{{ url('profile/projects') }}/" + id;
        document.getElementById('editProjectTitle').value = title;
        document.getElementById('editProjectDescription').value = description;
        document.getElementById('editProjectTechnologies').value = technologies;
        document.getElementById('editProjectUrl').value = project_url;
        document.getElementById('editProjectRepo').value = repo_url;
        document.getElementById('editProjectDate').value = completed_date;
        toggleModal('editProjectModal');
    }

    // Fetch department interest suggestions via AJAX
    function fetchSuggestions() {
        const box = document.getElementById('suggestionsBox');
        const list = document.getElementById('suggestionsList');

        if (!box.classList.contains('hidden')) {
            box.classList.add('hidden');
            return;
        }

        box.classList.remove('hidden');
        list.innerHTML = '<span class="text-slate-500">Loading suggestions...</span>';

        fetch("{{ route('profile.interests.suggestions') }}")
            .then(res => res.json())
            .then(data => {
                if (data.suggestions && data.suggestions.length > 0) {
                    list.innerHTML = '';
                    data.suggestions.forEach(item => {
                        const chip = document.createElement('button');
                        chip.type = 'button';
                        chip.className = 'rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200 hover:bg-indigo-600 hover:text-white transition';
                        chip.textContent = '+ ' + item;
                        chip.onclick = function () {
                            document.getElementById('addInterestName').value = item;
                            document.getElementById('addInterestCategory').value = data.department || 'Academic';
                            toggleModal('addInterestModal');
                        };
                        list.appendChild(chip);
                    });
                } else {
                    list.innerHTML = '<span class="text-slate-500">No suggestions available.</span>';
                }
            })
            .catch(err => {
                list.innerHTML = '<span class="text-red-500">Could not load suggestions.</span>';
            });
    }
</script>
@endsection
