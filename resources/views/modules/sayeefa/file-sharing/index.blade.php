@extends('layouts.app')

@section('title', 'File Sharing')

@section('content')
    <div class="mb-8 flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-950">File Sharing</h1>
        <p class="text-sm text-slate-500">Upload project documents and link them to a meeting or a task deadline.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Upload File</h2>
                <form id="file-form" class="space-y-3" enctype="multipart/form-data">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Project</label>
                        <select name="project_id" id="file-project-select" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected($selectedProjectId === $project->id)>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Link to meeting (optional)</label>
                        <select name="meeting_id" id="file-meeting-select"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">None</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Link to task/deadline (optional)</label>
                        <select name="task_id" id="file-task-select"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">None</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">Your name</label>
                        <input type="text" name="uploaded_by" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-600">File</label>
                        <input type="file" name="file" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                        Upload File
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">Shared Files</h2>
                    <span class="text-xs text-slate-500">{{ $files->count() }} file(s)</span>
                </div>
                <ul id="file-list" class="space-y-3">
                    @forelse ($files as $file)
                        <li data-file-id="{{ $file->id }}"
                            class="flex items-start justify-between rounded-lg border border-slate-200 p-4">
                            <div>
                                <a href="{{ $file->secure_url }}" target="_blank" class="font-medium text-blue-600 hover:underline">
                                    {{ $file->original_name }}
                                </a>
                                <p class="text-xs text-slate-500">
                                    {{ $file->project->title }}
                                    @if ($file->meeting) &middot; Meeting: {{ $file->meeting->title }} @endif
                                    @if ($file->task) &middot; Task: {{ $file->task->title }} @endif
                                </p>
                                <p class="mt-1 text-xs text-slate-400">
                                    Uploaded by {{ $file->uploaded_by }} &middot; {{ round($file->bytes / 1024) }} KB
                                </p>
                            </div>
                            <button type="button" class="file-delete-btn rounded-lg border border-red-200 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">
                                Delete
                            </button>
                        </li>
                    @empty
                        <li class="py-6 text-center text-sm text-slate-400">No files uploaded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    async function loadMeetingsAndTasks(projectId) {
        const meetingSelect = document.getElementById('file-meeting-select');
        const taskSelect = document.getElementById('file-task-select');
        meetingSelect.innerHTML = '<option value="">None</option>';
        taskSelect.innerHTML = '<option value="">None</option>';

        if (!projectId) return;

        const response = await fetch(`/api/projects/${projectId}/meetings-and-tasks`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await response.json();

        data.meetings.forEach((meeting) => {
            const option = document.createElement('option');
            option.value = meeting.id;
            option.textContent = meeting.title;
            meetingSelect.appendChild(option);
        });

        data.tasks.forEach((task) => {
            const option = document.createElement('option');
            option.value = task.id;
            option.textContent = task.title;
            taskSelect.appendChild(option);
        });
    }

    const projectSelect = document.getElementById('file-project-select');
    projectSelect.addEventListener('change', () => loadMeetingsAndTasks(projectSelect.value));
    if (projectSelect.value) loadMeetingsAndTasks(projectSelect.value);

    document.getElementById('file-form').addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        const response = await fetch('/api/files', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData,
        });

        if (response.ok) {
            window.location.reload();
        } else {
            const errorBody = await response.text();
            alert('Could not upload file (status ' + response.status + '):\n' + errorBody);
            console.error('Upload failed', response.status, errorBody);
        }
    });

    document.querySelectorAll('.file-delete-btn').forEach((button) => {
        button.addEventListener('click', async (event) => {
            if (!window.confirm('Delete this file?')) return;
            const li = event.target.closest('[data-file-id]');
            const fileId = li.dataset.fileId;
            const response = await fetch(`/api/files/${fileId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
            if (response.ok) {
                li.remove();
            }
        });
    });
});
    </script>
@endsection