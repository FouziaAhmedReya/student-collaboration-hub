@extends('layouts.app')

@section('title', 'Event & Workshop Announcements')

@section('content')
<div class="space-y-8">
    <!-- Header banner -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Event & Workshop Announcements</h1>
            <p class="mt-1 text-sm text-slate-600">Discover upcoming workshops, seminars, and hackathons hosted across campus.</p>
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

    <!-- Announcements Roster Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Upcoming Events ({{ $events->count() }})</h2>
        </div>

        @if($events->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center bg-white shadow-sm">
                <div class="mx-auto grid size-12 place-items-center rounded-full bg-slate-100 text-slate-500">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">No events found</h3>
                <p class="mt-1 text-sm text-slate-500">No event announcements available under this category yet.</p>
                <div class="mt-6">
                    <button onclick="document.getElementById('createEventModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        Publish First Event
                    </button>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($events as $event)
                    <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all">
                        <div>
                            <!-- Type Badge & Actions -->
                            <div class="flex items-center justify-between">
                                <span class="rounded-lg px-2.5 py-1 text-xs font-bold uppercase tracking-wider
                                      {{ $event->type === 'Workshop' ? 'bg-blue-100 text-blue-700' : '' }}
                                      {{ $event->type === 'Seminar' ? 'bg-purple-100 text-purple-700' : '' }}
                                      {{ $event->type === 'Hackathon' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                      {{ $event->type === 'Webinar' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ $event->type }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button onclick="openEditEventModal({{ json_encode($event) }})" class="p-1 text-slate-400 hover:text-blue-600 transition-colors" title="Edit Event">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Delete this event announcement?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition-colors" title="Delete Event">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Title & Description -->
                            <h3 class="mt-3 text-lg font-bold text-slate-900 leading-snug">{{ $event->title }}</h3>
                            <p class="mt-2 text-xs text-slate-600 leading-relaxed line-clamp-3">{{ $event->description }}</p>

                            <!-- Target Skills -->
                            @if(!empty($event->target_skills))
                                <div class="mt-4 flex flex-wrap gap-1">
                                    @foreach(array_map('trim', explode(',', $event->target_skills)) as $skill)
                                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Footer Details -->
                        <div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-500 space-y-1.5">
                            <div class="flex items-center gap-1.5 font-medium text-slate-700">
                                <svg class="size-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <span>{{ $event->event_date ? $event->event_date->format('M d, Y @ h:i A') : 'TBA' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span class="truncate">{{ $event->location }}</span>
                            </div>
                            @if(!empty($event->organizer))
                                <div class="text-slate-400 text-xs">
                                    Organized by: <span class="font-medium text-slate-600">{{ $event->organizer }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Create Event -->
<div id="createEventModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Publish New Event Announcement</h3>
            <button onclick="document.getElementById('createEventModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Event Title *</label>
                <input type="text" name="title" required placeholder="e.g. Fullstack AI Hackathon 2026" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Event Type *</label>
                    <select name="type" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden">
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Hackathon">Hackathon</option>
                        <option value="Webinar">Webinar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Organizer</label>
                    <input type="text" name="organizer" placeholder="e.g. BRACU Computer Club" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target / Prerequisite Skills</label>
                <input type="text" name="target_skills" placeholder="e.g. Python, Laravel, AI" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Event Date & Time *</label>
                    <input type="datetime-local" name="event_date" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Location / Venue *</label>
                    <input type="text" name="location" required placeholder="e.g. UB201 / Zoom Link" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description *</label>
                <textarea name="description" rows="3" required placeholder="Event agenda, requirements, and instructions..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createEventModal').classList.add('hidden')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Publish Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Event -->
<div id="editEventModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Edit Event Announcement</h3>
            <button onclick="document.getElementById('editEventModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="editEventForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Event Title *</label>
                <input type="text" id="editEventTitle" name="title" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Event Type *</label>
                    <select id="editEventType" name="type" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden">
                        <option value="Workshop">Workshop</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Hackathon">Hackathon</option>
                        <option value="Webinar">Webinar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Organizer</label>
                    <input type="text" id="editEventOrganizer" name="organizer" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target / Prerequisite Skills</label>
                <input type="text" id="editEventTargetSkills" name="target_skills" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Event Date & Time</label>
                    <input type="datetime-local" id="editEventDate" name="event_date" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Location / Venue *</label>
                    <input type="text" id="editEventLocation" name="location" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description *</label>
                <textarea id="editEventDescription" name="description" rows="3" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-blue-600 focus:outline-hidden"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editEventModal').classList.add('hidden')" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">Update Event</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditEventModal(event) {
        document.getElementById('editEventForm').action = "{{ url('events') }}/" + event.id;
        document.getElementById('editEventTitle').value = event.title;
        document.getElementById('editEventType').value = event.type;
        document.getElementById('editEventOrganizer').value = event.organizer || '';
        document.getElementById('editEventTargetSkills').value = event.target_skills || '';
        document.getElementById('editEventLocation').value = event.location;
        document.getElementById('editEventDescription').value = event.description;
        document.getElementById('editEventModal').classList.remove('hidden');
    }
</script>
@endsection
