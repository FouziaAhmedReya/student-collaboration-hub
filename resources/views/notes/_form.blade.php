@php($editing = isset($note))

<div class="grid gap-7 lg:grid-cols-[minmax(0,1.2fr)_minmax(340px,.8fr)]">
    <div class="space-y-5">
        <div>
            <label for="department" class="mb-2 block text-sm font-bold text-slate-800">Department <span class="text-red-500">*</span></label>
            <input id="department" name="department" value="{{ old('department', $note->department ?? '') }}" type="text" maxlength="100" required placeholder="e.g. Computer Science and Engineering" class="min-h-11 w-full rounded-xl border bg-white px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 {{ $errors->has('department') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}">
            @error('department')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="course" class="mb-2 block text-sm font-bold text-slate-800">Course <span class="text-red-500">*</span></label>
                <input id="course" name="course" value="{{ old('course', $note->course ?? '') }}" type="text" maxlength="120" required placeholder="e.g. CSE421 Computer Networks" class="min-h-11 w-full rounded-xl border bg-white px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 {{ $errors->has('course') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}">
                @error('course')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="semester" class="mb-2 block text-sm font-bold text-slate-800">Semester <span class="text-red-500">*</span></label>
                <input id="semester" name="semester" value="{{ old('semester', $note->semester ?? '') }}" type="text" maxlength="50" required placeholder="e.g. Spring 2026" class="min-h-11 w-full rounded-xl border bg-white px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 {{ $errors->has('semester') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}">
                @error('semester')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="title" class="mb-2 block text-sm font-bold text-slate-800">Title <span class="text-red-500">*</span></label>
            <input id="title" name="title" value="{{ old('title', $note->title ?? '') }}" type="text" maxlength="150" required placeholder="Enter a clear note title" class="min-h-11 w-full rounded-xl border bg-white px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 {{ $errors->has('title') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}">
            @error('title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-4">
                <label for="description" class="block text-sm font-bold text-slate-800">Description</label>
                <span class="text-xs text-slate-400">Up to 1,500 characters</span>
            </div>
            <textarea id="description" name="description" rows="7" maxlength="1500" placeholder="Describe what this note covers" class="w-full resize-y rounded-xl border bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:ring-4 {{ $errors->has('description') ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-100' }}">{{ old('description', $note->description ?? '') }}</textarea>
            @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-bold text-slate-800">{{ $editing ? 'Replace file' : 'Note file' }} @unless($editing)<span class="text-red-500">*</span>@endunless</label>
        <label for="file" data-file-drop data-dragging="false" class="group flex min-h-80 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-blue-400 hover:bg-blue-50/50 data-[dragging=true]:border-blue-500 data-[dragging=true]:bg-blue-50">
            <span class="grid size-16 place-items-center rounded-2xl bg-white text-blue-600 shadow-sm ring-1 ring-slate-200 transition group-hover:scale-105">
                <svg viewBox="0 0 24 24" class="size-9" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M7 18H5.5A3.5 3.5 0 0 1 5 11a7 7 0 0 1 13.5-1.5A4.5 4.5 0 0 1 18 18h-1" stroke-linecap="round"/>
                    <path d="M12 20V10m0 0-3.5 3.5M12 10l3.5 3.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="mt-5 text-base font-bold text-slate-900">Drag and drop your file here</span>
            <span class="my-2 text-xs font-medium uppercase tracking-wide text-slate-400">or</span>
            <span class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-blue-200 transition group-hover:bg-blue-700">Choose file</span>
            <span data-file-name class="mt-4 max-w-full break-all text-sm font-semibold text-slate-600">
                @if ($editing)
                    Current: {{ $note->original_name }}
                @else
                    No file selected
                @endif
            </span>
            <span class="mt-2 text-xs leading-5 text-slate-500">PDF, DOC, DOCX, PPT, PPTX, JPG, PNG, or WEBP · maximum 10 MB</span>
            <input id="file" name="file" type="file" class="sr-only" @required(!$editing) accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.webp">
        </label>
        @error('file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

        @if ($editing)
            <p class="mt-3 text-xs leading-5 text-slate-500">Leave this empty to keep the existing Cloudinary file.</p>
        @endif
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
    <a href="{{ route('notes.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Cancel</a>
    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm shadow-blue-200 transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M12 16V4m0 0L8 8m4-4 4 4M5 14v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ $editing ? 'Save Changes' : 'Upload Note' }}
    </button>
</div>
