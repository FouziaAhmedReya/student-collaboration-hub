@extends('layouts.app')

@section('title', 'AI Project Idea Generator')

@section('content')
    <div class="space-y-8">
        <!-- Flash Success Alert -->
        @if(session('success'))
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-medium text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="size-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Header banner & Actions -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">AI Project Idea Generator</h1>
                <p class="mt-1 text-sm text-slate-600">Explore domain-specific project ideas or generate custom suggestions
                    powered by AI.</p>
            </div>
            <button onclick="openGenerateModal()"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Generate New Idea
            </button>
        </div>

        <!-- Search / Filter Bar -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <form method="GET" action="{{ route('project-ideas.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input type="text" name="domain" value="{{ $domain }}"
                        placeholder="Filter by domain or keyword (e.g. productivity, food, mobile)..."
                        class="w-full rounded-xl border border-slate-300 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="w-full sm:w-auto rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                        Filter
                    </button>
                    @if($domain)
                        <a href="{{ route('project-ideas.index') }}"
                            class="w-full sm:w-auto rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
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
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">No project ideas found</h3>
                <p class="mt-1 text-sm text-slate-500">Try searching for a different domain or generate a new project idea.</p>
                <div class="mt-6">
                    <button onclick="openGenerateModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        Generate Idea
                    </button>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($ideas as $idea)
                    @php
                        // Clean preview text by stripping markdown symbols
                        $cleanPreview = preg_replace('/[\*\#\`\_]/', '', $idea->description);
                    @endphp
                    <div
                        class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span
                                    class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ Str::title($idea->domain) }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="openEditIdeaModal({{ json_encode([
                        'id' => $idea->id,
                        'title' => $idea->title,
                        'domain' => $idea->domain,
                        'tech_stack' => $idea->tech_stack,
                        'description' => $idea->description
                    ]) }})" title="Edit Project Idea"
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-blue-600 transition-colors">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('project-ideas.destroy', $idea->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this project idea?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete Project Idea"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                    <span class="text-xs text-slate-400 ml-1">#{{ $idea->id }}</span>
                                </div>
                            </div>
                            <h3 class="mt-3 text-lg font-bold text-slate-900 leading-snug"
                                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $idea->title }}</h3>
                            <p class="mt-2.5 text-sm text-slate-600 leading-relaxed"
                                style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit($cleanPreview, 180) }}</p>

                            <button type="button" onclick="openIdeaDetailModal({{ json_encode([
                        'title' => $idea->title,
                        'domain' => Str::title($idea->domain),
                        'tech_stack' => $idea->tech_stack,
                        'description' => $idea->description,
                        'id' => $idea->id
                    ]) }})"
                                class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline focus:outline-none">
                                <span>Show Full Details</span>
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </button>
                        </div>
                        @if($idea->tech_stack)
                            <div class="mt-5 pt-4 border-t border-slate-100">
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
    <div id="generateModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20">
        <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-xl font-bold text-slate-900">Generate AI Project Idea</h2>
                <button onclick="document.getElementById('generateModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="generateForm" method="POST" action="{{ route('project-ideas.generate') }}"
                onsubmit="startGenerateLoading()" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="domain" class="block text-sm font-semibold text-slate-700">Domain / Focus Area <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="domain" name="domain" value="{{ old('domain') }}" required
                        placeholder="e.g. campus food ordering, student productivity, AI healthcare"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    @error('domain')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subDomain" class="block text-sm font-semibold text-slate-700">Sub-Domain / Specific Work
                        Area (Optional)</label>
                    <input type="text" id="subDomain" name="subDomain" value="{{ old('subDomain') }}"
                        placeholder="e.g. Mobile App, Machine Learning, Web Platform, Blockchain, IoT"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    @error('subDomain')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="techStack" class="block text-sm font-semibold text-slate-700">Tech Stack (Optional)</label>
                    <input type="text" id="techStack" name="techStack" value="{{ old('techStack') }}"
                        placeholder="e.g. Node.js, MongoDB, React, Python"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    @error('techStack')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-slate-700">Notes / Constraints
                        (Optional)</label>
                    <textarea id="notes" name="notes" rows="3"
                        placeholder="e.g. Focus on group orders for hostel students or real-time notification integration."
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" id="generateSubmitBtn"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-all disabled:opacity-75 disabled:cursor-not-allowed">
                        <svg id="generateBtnSpinner" class="hidden size-4 animate-spin text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span id="generateBtnText">Generate Idea</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Editing Existing Idea -->
    <div id="editIdeaModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20">
        <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-xl font-bold text-slate-900">Edit Project Idea</h2>
                <button onclick="document.getElementById('editIdeaModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editIdeaForm" method="POST" action="" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_title" class="block text-sm font-semibold text-slate-700">Project Title <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="edit_title" name="title" required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="edit_domain" class="block text-sm font-semibold text-slate-700">Domain / Focus Area <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="edit_domain" name="domain" required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="edit_tech_stack" class="block text-sm font-semibold text-slate-700">Tech Stack</label>
                    <input type="text" id="edit_tech_stack" name="tech_stack"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="edit_description" class="block text-sm font-semibold text-slate-700">Description <span
                            class="text-red-500">*</span></label>
                    <textarea id="edit_description" name="description" rows="4" required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('editIdeaModal').classList.add('hidden')"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Displaying Full Idea Details -->
    <div id="ideaDetailModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-12">
        <div class="mx-auto max-w-2xl rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span id="detailModalDomain"
                            class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10"></span>
                        <span id="detailModalId" class="text-xs text-slate-400"></span>
                    </div>
                    <h2 id="detailModalTitle" class="text-xl font-bold text-slate-900 leading-snug sm:text-2xl"></h2>
                </div>
                <button onclick="document.getElementById('ideaDetailModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-6 space-y-6">
                <div id="detailModalTechContainer">
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Suggested Tech
                        Stack</span>
                    <div id="detailModalTech" class="flex flex-wrap gap-2"></div>
                </div>

                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Project
                        Description & Architecture</span>
                    <div id="detailModalDescription"
                        class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-700 bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-2">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('ideaDetailModal').classList.add('hidden')"
                    class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Format markdown to clean HTML with styled section labels and structured lists
        function formatMarkdownText(text) {
            if (!text) return '';

            let raw = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            // Known section header labels
            const labels = [
                'Executive Summary',
                'Problem Statement',
                'Key Features',
                'Key Modules',
                'Technical Architecture',
                'System Architecture',
                'Expected Impact',
                'Target Audience',
                'Future Scope'
            ];

            // Replace inline or bold section titles with header break markers
            labels.forEach(label => {
                const regex = new RegExp(`(?:^|\\s|(?:\\*\\*|\\*|#+)*)(?:\\*\\*|\\*|#+)*\\s*(${label})\\s*(?:\\*\\*|\\*)*\\s*:`, 'gi');
                raw = raw.replace(regex, '\n\n__SECTION_HEADER__:$1:\n');
            });

            // Match generic bold titles at start of line or sentence like **Title:**
            raw = raw.replace(/(?:\r\n|\r|\n)*\*\*(.*?)\*\*\s*:/g, '\n\n__SECTION_HEADER__:$1:\n');

            let lines = raw.split('\n');
            let html = '';
            let inList = false;

            lines.forEach(line => {
                let trimmed = line.trim();
                if (!trimmed) return;

                if (trimmed.startsWith('__SECTION_HEADER__:')) {
                    if (inList) { html += '</ul>'; inList = false; }
                    let headerTitle = trimmed.replace('__SECTION_HEADER__:', '').replace(/:$/, '').trim();
                    html += `
                        <div class="mt-5 mb-3 flex items-center gap-2 border-b border-slate-200 pb-2">
                            <span class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white shadow-sm">
                                ${headerTitle}
                            </span>
                        </div>
                    `;
                    return;
                }

                if (trimmed.startsWith('#')) {
                    if (inList) { html += '</ul>'; inList = false; }
                    let headerText = trimmed.replace(/^#+\s*/, '');
                    html += `
                        <div class="mt-4 mb-2 flex items-center gap-2">
                            <h4 class="text-base font-bold text-slate-900">${headerText}</h4>
                        </div>
                    `;
                    return;
                }

                // Bullet items
                if (trimmed.startsWith('* ') || trimmed.startsWith('- ') || trimmed.startsWith('• ')) {
                    if (!inList) { html += '<ul class="space-y-2 my-3 pl-1">'; inList = true; }
                    let itemContent = trimmed.replace(/^[\*\-\•]\s*/, '');
                    itemContent = itemContent.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');
                    html += `
                        <li class="flex items-start gap-2.5 text-sm text-slate-700">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-blue-500"></span>
                            <span>${itemContent}</span>
                        </li>
                    `;
                    return;
                }

                // Numbered items
                if (/^\d+\.\s/.test(trimmed)) {
                    if (!inList) { html += '<ol class="space-y-2 my-3 pl-1 list-decimal list-inside text-sm text-slate-700">'; inList = true; }
                    let itemContent = trimmed.replace(/^\d+\.\s/, '');
                    itemContent = itemContent.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');
                    html += `<li class="my-1">${itemContent}</li>`;
                    return;
                }

                if (inList) { html += '</ul>'; inList = false; }

                let paragraphText = trimmed.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900">$1</strong>');
                html += `<p class="my-2.5 text-sm leading-relaxed text-slate-700">${paragraphText}</p>`;
            });

            if (inList) { html += '</ul>'; }

            return html;
        }

        function openIdeaDetailModal(data) {
            document.getElementById('detailModalTitle').innerText = data.title;
            document.getElementById('detailModalDomain').innerText = data.domain;
            document.getElementById('detailModalId').innerText = '#' + data.id;

            // Render tech stack badges
            const techContainer = document.getElementById('detailModalTech');
            techContainer.innerHTML = '';
            if (data.tech_stack) {
                document.getElementById('detailModalTechContainer').classList.remove('hidden');
                data.tech_stack.split(',').forEach(tech => {
                    const badge = document.createElement('span');
                    badge.className = 'rounded-lg bg-blue-100/70 px-3 py-1 text-xs font-semibold text-blue-800 border border-blue-200/50';
                    badge.innerText = tech.trim();
                    techContainer.appendChild(badge);
                });
            } else {
                document.getElementById('detailModalTechContainer').classList.add('hidden');
            }

            // Render formatted description
            document.getElementById('detailModalDescription').innerHTML = formatMarkdownText(data.description);

            document.getElementById('ideaDetailModal').classList.remove('hidden');
        }

        function openEditIdeaModal(data) {
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_domain').value = data.domain;
            document.getElementById('edit_tech_stack').value = data.tech_stack || '';
            document.getElementById('edit_description').value = data.description;

            const form = document.getElementById('editIdeaForm');
            form.action = '/project-ideas/' + data.id;

            document.getElementById('editIdeaModal').classList.remove('hidden');
        }

        function openGenerateModal() {
            const btn = document.getElementById('generateSubmitBtn');
            const spinner = document.getElementById('generateBtnSpinner');
            const text = document.getElementById('generateBtnText');

            if (btn && spinner && text) {
                btn.disabled = false;
                spinner.classList.add('hidden');
                text.innerText = 'Generate Idea';
            }
            document.getElementById('generateModal').classList.remove('hidden');
        }

        function startGenerateLoading() {
            const btn = document.getElementById('generateSubmitBtn');
            const spinner = document.getElementById('generateBtnSpinner');
            const text = document.getElementById('generateBtnText');

            if (btn && spinner && text) {
                spinner.classList.remove('hidden');
                text.innerText = 'Generating Idea with AI...';
                setTimeout(() => {
                    btn.disabled = true;
                }, 50);
            }
        }
    </script>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('generateModal').classList.remove('hidden');
            });
        </script>
    @endif
@endsection