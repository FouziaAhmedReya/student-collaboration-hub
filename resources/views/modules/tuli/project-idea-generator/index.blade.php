@extends('layouts.app')

@section('title', 'AI Project Idea Generator')

@section('content')
<div class="space-y-8">
    <!-- Header banner & Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">AI Project Idea Generator</h1>
            <p class="mt-1 text-sm text-slate-600">Explore domain-specific project ideas or generate custom suggestions powered by AI.</p>
        </div>
        <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Generate New Idea
        </button>
    </div>

    <!-- Search / Filter Bar -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="GET" action="{{ route('project-ideas.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input type="text" name="domain" value="{{ $domain }}" placeholder="Filter by domain or keyword (e.g. productivity, food, mobile)..." class="w-full rounded-xl border border-slate-300 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full sm:w-auto rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                    Filter
                </button>
                @if($domain)
                    <a href="{{ route('project-ideas.index') }}" class="w-full sm:w-auto rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Project Ideas Grid -->
    @if($ideas->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center">
            <div class="mx-auto grid size-12 place-items-center rounded-full bg-slate-100 text-slate-500">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                </svg>
            </div>
            <h3 class="mt-4 text-base font-semibold text-slate-900">No project ideas found</h3>
            <p class="mt-1 text-sm text-slate-500">Try searching for a different domain or generate a new project idea.</p>
            <div class="mt-6">
                <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    Generate Idea
                </button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($ideas as $idea)
                <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ Str::title($idea->domain) }}
                            </span>
                            <span class="text-xs text-slate-400">#{{ $idea->id }}</span>
                        </div>
                        <h3 class="mt-3 text-lg font-bold text-slate-900 leading-snug">{{ $idea->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $idea->description }}</p>
                    </div>
                    @if($idea->tech_stack)
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <span class="block text-xs font-medium text-slate-400 mb-2">Tech Stack:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_map('trim', explode(',', $idea->tech_stack)) as $tech)
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $tech }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal for Generating New Idea -->
<div id="generateModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20">
    <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-xl font-bold text-slate-900">Generate AI Project Idea</h2>
            <button onclick="document.getElementById('generateModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('project-ideas.generate') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="domain" class="block text-sm font-semibold text-slate-700">Domain / Focus Area <span class="text-red-500">*</span></label>
                <input type="text" id="domain" name="domain" required placeholder="e.g. campus food ordering, student productivity, AI healthcare" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="techStack" class="block text-sm font-semibold text-slate-700">Tech Stack <span class="text-red-500">*</span></label>
                <input type="text" id="techStack" name="techStack" required placeholder="e.g. Node.js, MongoDB, React, Python" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700">Notes / Constraints (Optional)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="e.g. Focus on group orders for hostel students or real-time notification integration." class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    Generate Idea
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
