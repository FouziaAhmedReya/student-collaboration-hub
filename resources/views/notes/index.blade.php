@extends('layouts.app')

@section('title', 'Browse Notes')

@section('content')
    <div class="mb-7 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="mb-1 text-sm font-semibold text-blue-700">Academic resources</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Browse Notes</h1>
            <p class="mt-2 text-sm text-slate-500">Find and download lecture notes shared by students.</p>
        </div>

        <a href="{{ route('notes.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-blue-200 transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
            <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
            </svg>
            Upload Notes
        </a>
    </div>

    <form method="GET" action="{{ route('notes.index') }}" class="mb-7 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="grid gap-4 lg:grid-cols-[minmax(240px,1.7fr)_repeat(4,minmax(140px,1fr))]">
            <div>
                <label for="search" class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-600">Search</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-4-4" stroke-linecap="round"/>
                    </svg>
                    <input id="search" name="search" value="{{ request('search') }}" type="search" placeholder="Title, course, or keyword…" class="min-h-11 w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>
            </div>

            <div>
                <label for="department" class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-600">Department</label>
                <select id="department" name="department" class="min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <option value="">All departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="course" class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-600">Course</label>
                <select id="course" name="course" class="min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <option value="">All courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course }}" @selected(request('course') === $course)>{{ $course }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="semester" class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-600">Semester</label>
                <select id="semester" name="semester" class="min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <option value="">All semesters</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester }}" @selected(request('semester') === $semester)>{{ $semester }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sort" class="mb-2 block text-xs font-bold uppercase tracking-wide text-slate-600">Sort by</label>
                <select id="sort" name="sort" class="min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Latest</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                    <option value="title" @selected(request('sort') === 'title')>Title A–Z</option>
                    <option value="downloads" @selected(request('sort') === 'downloads')>Most downloaded</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-end gap-3">
            @if (request()->hasAny(['search', 'department', 'course', 'semester', 'sort']))
                <a href="{{ route('notes.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Clear filters</a>
            @endif
            <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2 text-sm font-bold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-200">
                Apply filters
            </button>
        </div>
    </form>

    @if ($notes->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-blue-50 text-blue-600">
                <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M7 3.5h7l4 4V20a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1Z" stroke-linejoin="round"/>
                    <path d="M14 3.5V8h4M9 13h6M9 17h4" stroke-linecap="round"/>
                </svg>
            </span>
            <h2 class="mt-4 text-lg font-bold text-slate-900">No notes found</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                {{ request()->hasAny(['search', 'department', 'course', 'semester']) ? 'Try changing your search or filters.' : 'Be the first student to share a lecture note or study material.' }}
            </p>
            <a href="{{ route('notes.create') }}" class="mt-5 inline-flex min-h-10 items-center justify-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-bold text-white transition hover:bg-blue-700">Upload a note</a>
        </div>
    @else
        <div class="mb-4 flex items-center justify-between gap-4">
            <p class="text-sm text-slate-500"><span class="font-bold text-slate-800">{{ $notes->total() }}</span> {{ Str::plural('note', $notes->total()) }} found</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($notes as $note)
                @php
                    $extension = strtoupper($note->format ?: pathinfo($note->original_name, PATHINFO_EXTENSION));
                    $isImage = in_array(strtolower($note->format), ['jpg', 'jpeg', 'png', 'webp']);
                @endphp
                <article class="flex min-h-56 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                    <div class="flex items-start gap-4">
                        <span class="grid size-14 shrink-0 place-items-center rounded-xl {{ $isImage ? 'bg-violet-100 text-violet-700' : 'bg-blue-100 text-blue-700' }}">
                            @if ($isImage)
                                <svg viewBox="0 0 24 24" class="size-7" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m5 18 5-5 3 3 2-2 4 4" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <span class="text-xs font-black tracking-wide">{{ $extension ?: 'FILE' }}</span>
                            @endif
                        </span>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold uppercase tracking-wide text-blue-700">{{ $note->course }}</p>
                                    <h2 class="mt-1 break-words text-lg font-bold leading-snug text-slate-950">{{ $note->title }}</h2>
                                </div>
                                <a href="{{ route('notes.edit', $note) }}" aria-label="Edit {{ $note->title }}" class="grid size-9 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="m4 20 4.2-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" stroke-linejoin="round"/><path d="m13.8 7.5 3 3"/>
                                    </svg>
                                </a>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ Str::limit($note->description ?: 'No description was added.', 120) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                        <span class="rounded-full bg-slate-100 px-3 py-1.5">{{ $note->department }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1.5">{{ $note->semester }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1.5">{{ $note->file_size }}</span>
                    </div>

                    <div class="mt-auto flex flex-col gap-4 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-slate-500">
                            Shared by <span class="font-semibold text-slate-700">{{ $note->user?->name ?? 'Student' }}</span><br>
                            {{ $note->created_at->diffForHumans() }} · {{ $note->downloads_count }} {{ Str::plural('download', $note->downloads_count) }}
                        </p>
                        <div class="flex gap-2">
                            <a href="{{ route('notes.preview', $note) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg border border-blue-200 px-4 py-2 text-sm font-bold text-blue-700 transition hover:bg-blue-50 sm:flex-none">Preview</a>
                            <a href="{{ route('notes.download', $note) }}" class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-700 sm:flex-none">
                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 20h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Download
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($notes->hasPages())
            <div class="mt-8">{{ $notes->onEachSide(1)->links() }}</div>
        @endif
    @endif
@endsection
