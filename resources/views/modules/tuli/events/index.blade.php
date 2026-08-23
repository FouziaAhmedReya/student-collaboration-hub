@extends('layouts.app')

@section('title', 'Event & Workshop Announcements')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Event & Workshop Announcements</h1>
            <p class="mt-1 text-sm text-slate-600">Discover upcoming workshops, seminars, and hackathons, or receive personalized Gemini AI event recommendations.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('createEventModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Publish New Event
            </button>
        </div>
    </div>

    <!-- Alert notifications -->
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Category Tabs Navigation -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            @php
                $tabs = [
                    'All' => 'All Announcements',
                    'Workshop' => 'Workshops',
                    'Seminar' => 'Seminars',
                    'Hackathon' => 'Hackathons',
                    'Webinar' => 'Webinars',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                @php
                    $count = ($key === 'All') ? $allEvents->count() : $allEvents->where('type', $key)->count();
                    $isActive = ($selectedType === $key);
                @endphp
                <a href="{{ route('events.index', ['type' => $key]) }}"
                   class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-semibold transition-colors flex items-center gap-2
                          {{ $isActive ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                    <span>{{ $label }}</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $isActive ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $count }}
                    </span>
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Gemini AI Event Recommendations Banner -->
    @if($aiRecommendations)
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-slate-50 p-6 text-slate-900 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-indigo-200/50 pb-4">
                <div class="flex items-center gap-2 text-indigo-700 font-bold text-base">
                    <svg class="size-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span>Gemini AI Event & Workshop Recommendations</span>
                </div>
                <a href="{{ route('events.index', ['type' => $selectedType, 'generate_ai' => 1]) }}" class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold shadow-sm transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    <svg class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Refresh Recommendations
                </a>
            </div>
            <div id="aiRecommendationsContent" class="text-sm text-slate-700 leading-relaxed"></div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rawText = @json($aiRecommendations);
                const container = document.getElementById('aiRecommendationsContent');
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
        <!-- AI Recommendations Call-to-Action Box -->
        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/70 via-blue-50/70 to-slate-50 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-800 font-bold text-base">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                    </svg>
                    <span>Gemini AI Event & Workshop Recommendations</span>
                </div>
                <p class="text-xs text-slate-600">Click below to generate personalized AI event recommendations tailored to your registered skills and interests.</p>
            </div>
            <a href="{{ route('events.index', ['type' => $selectedType, 'generate_ai' => 1]) }}" onclick="this.innerHTML='Generating AI Recommendations...'; this.classList.add('opacity-75', 'pointer-events-none');" class="shrink-0 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                <span style="color: #ffffff !important;">Generate AI Event Recommendations</span>
            </a>
        </div>
    @endif

    <!-- Events Grid -->
    @if($events->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center bg-white shadow-sm">
            <div class="mx-auto grid size-12 place-items-center rounded-full bg-blue-50 text-blue-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-semibold text-slate-900">No announcements found</h3>
            <p class="mt-1 text-sm text-slate-500">There are no {{ strtolower($selectedType) !== 'all' ? strtolower($selectedType) : '' }} announcements published yet.</p>
            <div class="mt-6">
                <button onclick="document.getElementById('createEventModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold shadow-md" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Publish Event Announcement
                </button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $ev)
                @php
                    $badgeStyle = match($ev->type) {
                        'Hackathon' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
                        'Seminar' => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
                        'Webinar' => 'bg-amber-50 text-amber-700 ring-amber-700/10',
                        default => 'bg-blue-50 text-blue-700 ring-blue-700/10',
                    };
                @endphp
                <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $badgeStyle }}">
                                {{ $ev->type }}
                            </span>
                            <div class="flex items-center gap-2">
                                <button onclick='openEditEventModal({{ json_encode($ev) }})' class="text-slate-400 hover:text-blue-600 transition-colors p-1" title="Edit Event">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('events.destroy', $ev->id) }}" onsubmit="return confirm('Delete event announcement \'{{ addslashes($ev->title) }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors p-1" title="Delete Event">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h3 class="mt-3 text-lg font-bold text-slate-900 leading-snug">{{ $ev->title }}</h3>
                        <p class="mt-2 text-xs text-slate-600 line-clamp-3 leading-relaxed">{{ $ev->description }}</p>

                        @if(!empty($ev->target_skills))
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach(array_map('trim', explode(',', $ev->target_skills)) as $sk)
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                        {{ $sk }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 text-xs space-y-1.5 text-slate-500">
                        @if($ev->organizer)
                            <div class="flex items-center gap-1.5">
                                <strong class="font-semibold text-slate-700">Organizer:</strong> {{ $ev->organizer }}
                            </div>
                        @endif
                        @if($ev->event_date)
                            <div class="flex items-center gap-1.5">
                                <strong class="font-semibold text-slate-700">Date:</strong> {{ $ev->event_date->format('M d, Y @ h:i A') }}
                            </div>
                        @endif
                        @if($ev->location)
                            <div class="flex items-center gap-1.5">
                                <strong class="font-semibold text-slate-700">Location:</strong> {{ $ev->location }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal for Publishing New Event -->
<div id="createEventModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20">
    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-xl font-bold text-slate-900">Publish New Event Announcement</h2>
            <button onclick="document.getElementById('createEventModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('events.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700">Event Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required placeholder="e.g. AI & Machine Learning Workshop" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700">Event Type <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Hackathon">Hackathon</option>
                        <option value="Webinar">Webinar</option>
                    </select>
                </div>
                <div>
                    <label for="organizer" class="block text-sm font-semibold text-slate-700">Organizer</label>
                    <input type="text" id="organizer" name="organizer" placeholder="e.g. CSE Department" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label for="target_skills" class="block text-sm font-semibold text-slate-700">Target Skills / Tags</label>
                <input type="text" id="target_skills" name="target_skills" placeholder="e.g. Python, Machine Learning, React" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="event_date" class="block text-sm font-semibold text-slate-700">Event Date & Time</label>
                    <input type="datetime-local" id="event_date" name="event_date" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="location" class="block text-sm font-semibold text-slate-700">Location</label>
                    <input type="text" id="location" name="location" placeholder="e.g. UB20101 Lab / Zoom" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700">Event Description <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="3" required placeholder="Describe topics covered, registration details, prerequisites..." class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
                <button type="button" onclick="document.getElementById('createEventModal').classList.add('hidden')" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Publish Event
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Editing Event -->
<div id="editEventModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20">
    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-xl font-bold text-slate-900">Edit Event Announcement</h2>
            <button onclick="document.getElementById('editEventModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editEventForm" method="POST" action="" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="edit_title" class="block text-sm font-semibold text-slate-700">Event Title <span class="text-red-500">*</span></label>
                <input type="text" id="edit_title" name="title" required class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="edit_type" class="block text-sm font-semibold text-slate-700">Event Type <span class="text-red-500">*</span></label>
                    <select id="edit_type" name="type" required class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Hackathon">Hackathon</option>
                        <option value="Webinar">Webinar</option>
                    </select>
                </div>
                <div>
                    <label for="edit_organizer" class="block text-sm font-semibold text-slate-700">Organizer</label>
                    <input type="text" id="edit_organizer" name="organizer" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label for="edit_target_skills" class="block text-sm font-semibold text-slate-700">Target Skills / Tags</label>
                <input type="text" id="edit_target_skills" name="target_skills" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="edit_event_date" class="block text-sm font-semibold text-slate-700">Event Date & Time</label>
                    <input type="datetime-local" id="edit_event_date" name="event_date" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label for="edit_location" class="block text-sm font-semibold text-slate-700">Location</label>
                    <input type="text" id="edit_location" name="location" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label for="edit_description" class="block text-sm font-semibold text-slate-700">Event Description <span class="text-red-500">*</span></label>
                <textarea id="edit_description" name="description" rows="3" required class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
                <button type="button" onclick="document.getElementById('editEventModal').classList.add('hidden')" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditEventModal(ev) {
        const form = document.getElementById('editEventForm');
        form.action = `/events/${ev.id}`;
        document.getElementById('edit_title').value = ev.title || '';
        document.getElementById('edit_type').value = ev.type || 'Workshop';
        document.getElementById('edit_organizer').value = ev.organizer || '';
        document.getElementById('edit_target_skills').value = ev.target_skills || '';
        document.getElementById('edit_location').value = ev.location || '';
        document.getElementById('edit_description').value = ev.description || '';

        if (ev.event_date) {
            const d = new Date(ev.event_date);
            const isoStr = d.toISOString().slice(0, 16);
            document.getElementById('edit_event_date').value = isoStr;
        } else {
            document.getElementById('edit_event_date').value = '';
        }

        document.getElementById('editEventModal').classList.remove('hidden');
    }
</script>
@endsection
