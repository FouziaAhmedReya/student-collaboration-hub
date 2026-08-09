@extends('layouts.app')

@section('title', 'Edit Note')

@section('content')
    <div class="mb-7 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('notes.index') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-blue-700">
                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Back to notes
            </a>
            <p class="mb-1 text-sm font-semibold text-blue-700">Update resource</p>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950">Edit Note</h1>
            <p class="mt-2 text-sm text-slate-500">Change the information or replace the attached file.</p>
        </div>

        <form method="POST" action="{{ route('notes.destroy', $note) }}" data-confirm-delete>
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-2.5 text-sm font-bold text-red-700 transition hover:bg-red-50 sm:w-auto">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-1 14H7L6 7m4 4v6m4-6v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Delete Note
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('notes.update', $note) }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 lg:p-8">
        @csrf
        @method('PUT')
        @include('notes._form', ['note' => $note])
    </form>
@endsection
