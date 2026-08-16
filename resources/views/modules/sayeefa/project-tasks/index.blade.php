@extends('layouts.app')

@section('title', 'Project Task Management')

@section('content')
    <div class="mb-8 flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-950">Project Task Management</h1>
        <p class="text-sm text-slate-500">Create projects, assign tasks, track deadlines, and monitor completion.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left column: create project + create task --}}
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">New Project</h2>
                <form id="project-form" class="space-y-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Title</label>
                        <input type="text" name="title" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Required skills</label>
                        <input type="text" name="required_skills" placeholder="e.g. Laravel, MySQL"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Team size</label>
                        <input type="number" name="team_size" value="4" min="1"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                        Create Project
                    </button>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">New Task</h2>
                <form id="task-form" class="space-y-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Project</label>
                        <select name="project_id" id="task-project-select" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach ($projects as $project)
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
                        <label class="mb-1 block text-xs font-medium text-slate-600">Assigned to</label>
                        <input type="text" name="assigned_to" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-600">Deadline</label>
                            <input type="date" name="deadline" required
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-600">Notify at</label>
                            <input type="datetime-local" name="notify_at"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                        Create Task
                    </button>
                </form>
            </div>
        </div>

        {{-- Right column: task list --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">Tasks</h2>
                    <span id="task-count" class="text-xs text-slate-500">{{ $tasks->count() }} task(s)</span>
                </div>
                <ul id="task-list" class="space-y-3">
                    @foreach ($tasks as $task)
                        <li data-task-id="{{ $task->id }}"
                            class="flex flex-col gap-2 rounded-lg border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-medium text-slate-900">{{ $task->title }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $task->project->title ?? 'No project' }} · Assigned to {{ $task->assigned_to }}
                                    · Due {{ $task->deadline?->format('M j, Y') }}
                                </p>
                                @if ($task->google_calendar_event_id)
                                    <span class="mt-1 inline-flex items-center gap-1 text-xs text-emerald-600">
                                        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Synced to Google Calendar
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <select class="task-status-select rounded-lg border border-slate-200 px-2 py-1 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="pending" @selected($task->status === 'pending')>Pending</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>In Progress</option>
                                    <option value="completed" @selected($task->status === 'completed')>Completed</option>
                                </select>
                                <button type="button" class="task-delete-btn rounded-lg border border-red-200 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">
                                    Delete
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p id="task-empty" class="py-6 text-center text-sm text-slate-400" style="display: {{ $tasks->isEmpty() ? 'block' : 'none' }}">
                    No tasks yet — create one on the left.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    };

    document.getElementById('project-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const payload = Object.fromEntries(new FormData(form).entries());

        const response = await fetch('/api/projects', { method: 'POST', headers, body: JSON.stringify(payload) });
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Could not create project. Check the fields and try again.');
        }
    });

    document.getElementById('task-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const payload = Object.fromEntries(new FormData(form).entries());

        const response = await fetch('/api/tasks', { method: 'POST', headers, body: JSON.stringify(payload) });
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Could not create task. Check the fields and try again.');
        }
    });

    document.querySelectorAll('.task-status-select').forEach((select) => {
        select.addEventListener('change', async (event) => {
            const taskId = event.target.closest('[data-task-id]').dataset.taskId;
            await fetch(`/api/tasks/${taskId}`, {
                method: 'PUT',
                headers,
                body: JSON.stringify({ status: event.target.value }),
            });
        });
    });

    document.querySelectorAll('.task-delete-btn').forEach((button) => {
        button.addEventListener('click', async (event) => {
            if (!window.confirm('Delete this task?')) return;
            const li = event.target.closest('[data-task-id]');
            const taskId = li.dataset.taskId;
            const response = await fetch(`/api/tasks/${taskId}`, { method: 'DELETE', headers });
            if (response.ok) {
                li.remove();
            }
        });
    });
});
</script>
@endpush
