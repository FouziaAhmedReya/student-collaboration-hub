@extends('layouts.app')

@section('title', 'Meeting Scheduler')

@section('content')
    <div class="mb-8 flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-950">Meeting Scheduler</h1>
        <p class="text-sm text-slate-500">Schedule project meetings and deadlines, synced to the shared Google Calendar.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">New Meeting</h2>
                @if ($leadableProjects->isEmpty())
                    <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
                        You're not the team leader of any project yet. Only a project's leader (the student who created it) can schedule its meetings.
                    </p>
                @endif
                <form id="meeting-form" class="space-y-3 {{ $leadableProjects->isEmpty() ? 'pointer-events-none opacity-40' : '' }}">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Project (you must be the leader)</label>
                        <select name="project_id" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach ($leadableProjects as $project)
                                <option value="{{ $project->id }}" @selected($selectedProjectId === $project->id)>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Title</label>
                        <input type="text" name="title" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Agenda</label>
                        <textarea name="agenda" rows="2"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <p class="text-xs text-slate-500">Organizer: <span class="font-medium text-slate-700">{{ auth()->user()->name }}</span></p>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Meeting time</label>
                        <input type="datetime-local" name="meeting_time" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Deadline (optional)</label>
                        <input type="date" name="deadline"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                        Schedule Meeting
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">Upcoming Meetings</h2>
                    <span class="text-xs text-slate-500">{{ $meetings->count() }} meeting(s)</span>
                </div>
                <ul id="meeting-list" class="space-y-3">
                    @forelse ($meetings as $meeting)
                        <li data-meeting-id="{{ $meeting->id }}"
                            class="rounded-lg border border-slate-200 p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $meeting->title }}</p>
                                    <p class="text-xs text-slate-500">{{ $meeting->project->title }} &middot; {{ $meeting->organizer }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $meeting->meeting_time->format('D, M j, Y g:i A') }}</p>
                                    @if ($meeting->deadline)
                                        <p class="mt-1 text-xs text-amber-600">Deadline: {{ $meeting->deadline->format('M j, Y') }}</p>
                                    @endif
                                    @if ($meeting->agenda)
                                        <p class="mt-2 text-xs text-slate-600">{{ $meeting->agenda }}</p>
                                    @endif
                                    @if ($meeting->google_calendar_event_id)
                                        <span class="mt-1 inline-flex items-center gap-1 text-xs text-emerald-600">
                                            <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Synced to Google Calendar
                                        </span>
                                    @else
                                        <span class="mt-1 text-[11px] font-medium text-slate-400">Not synced</span>
                                    @endif
                                </div>
                                <button type="button" class="meeting-delete-btn rounded-lg border border-red-200 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">
                                    Delete
                                </button>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-sm text-slate-400">No meetings scheduled yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    };

    document.getElementById('meeting-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const payload = Object.fromEntries(new FormData(form).entries());

        const response = await fetch('/api/meetings', { method: 'POST', headers, body: JSON.stringify(payload) });
        if (response.ok) {
            window.location.reload();
        } else {
            const errorBody = await response.text();
            alert('Could not create meeting (status ' + response.status + '):\n' + errorBody);
            console.error('Create meeting failed', response.status, errorBody);
        }
    });

    document.querySelectorAll('.meeting-delete-btn').forEach((button) => {
        button.addEventListener('click', async (event) => {
            if (!window.confirm('Delete this meeting?')) return;
            const li = event.target.closest('[data-meeting-id]');
            const meetingId = li.dataset.meetingId;
            const response = await fetch(`/api/meetings/${meetingId}`, { method: 'DELETE', headers });
            if (response.ok) {
                li.remove();
            }
        });
    });
});
    </script>
@endsection