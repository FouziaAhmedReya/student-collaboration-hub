@extends('layouts.app')

@section('title', 'Upload New Note')

@section('content')
    <div class="mb-7">
        <a href="{{ route('notes.index') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-blue-700">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Back to notes
        </a>
        <p class="mb-1 text-sm font-semibold text-blue-700">Share a resource</p>
        <h1 class="text-3xl font-bold tracking-tight text-slate-950">Upload New Note</h1>
        <p class="mt-2 text-sm text-slate-500">Add useful details so other students can find your material easily.</p>
    </div>

    <form method="POST" action="{{ route('notes.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7 lg:p-8">
        @csrf
        @include('notes._form')
    </form>
@endsection
