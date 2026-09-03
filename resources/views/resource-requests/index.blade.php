@extends('layouts.app')

@section('title', 'Resource Requests')

@section('content')
<div class="space-y-7">

    {{-- Page heading --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Resource Request System
        </h1>

        <p class="mt-2 text-slate-600">
            Students can request course materials. Registered students and approved tutors can upload the requested resources through Cloudinary.
        </p>
    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search and filter --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-xl font-bold text-slate-900">
            Find Resource Requests
        </h2>

        <form
            method="GET"
            action="{{ route('resource-requests.index') }}"
            class="mt-5 grid gap-4 md:grid-cols-3"
        >
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Course
                </label>

                <input
                    type="text"
                    name="course"
                    value="{{ request('course') }}"
                    placeholder="Course code or course name"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Request Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                >
                    <option value="">All requests</option>

                    <option
                        value="open"
                        @selected(request('status') === 'open')
                    >
                        Open
                    </option>

                    <option
                        value="fulfilled"
                        @selected(request('status') === 'fulfilled')
                    >
                        Fulfilled
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 font-semibold text-white hover:bg-blue-700"
                >
                    Search
                </button>

                <a
                    href="{{ route('resource-requests.index') }}"
                    class="rounded-xl border border-slate-300 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Clear
                </a>
            </div>
        </form>
    </section>

    {{-- Student resource request form --}}
    @if (auth()->user()->role === 'student')
        <section class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">
                Request a Resource
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                Requester:
                <strong>{{ auth()->user()->name }}</strong>
            </p>

            <form
                method="POST"
                action="{{ route('resource-requests.store') }}"
                class="mt-5 grid gap-4 md:grid-cols-2"
            >
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Course Code *
                    </label>

                    <input
                        type="text"
                        name="course_code"
                        value="{{ old('course_code') }}"
                        required
                        maxlength="60"
                        placeholder="Example: CSE220"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Course Name
                    </label>

                    <input
                        type="text"
                        name="course_name"
                        value="{{ old('course_name') }}"
                        maxlength="160"
                        placeholder="Example: Data Structures"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Requested Material *
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        maxlength="180"
                        placeholder="Example: Midterm lecture notes"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="3"
                        maxlength="1500"
                        placeholder="Explain what material you need"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 font-semibold text-white hover:bg-blue-700"
                    >
                        Submit Request
                    </button>
                </div>
            </form>
        </section>
    @endif

    {{-- Resource request listing --}}
    <section>
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-2xl font-bold text-slate-900">
                Resource Requests
            </h2>

            <span class="text-sm text-slate-500">
                {{ $resourceRequests->count() }} request(s)
            </span>
        </div>

        <div class="space-y-5">
            @forelse ($resourceRequests as $resourceRequest)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    {{-- Request information --}}
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800"
                                >
                                    {{ $resourceRequest->course_code }}
                                </span>

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold
                                    {{ $resourceRequest->status === 'fulfilled'
                                        ? 'bg-emerald-100 text-emerald-800'
                                        : 'bg-amber-100 text-amber-800' }}"
                                >
                                    {{ ucfirst($resourceRequest->status) }}
                                </span>
                            </div>

                            <h3 class="mt-3 text-xl font-bold text-slate-900">
                                {{ $resourceRequest->title }}
                            </h3>

                            @if ($resourceRequest->course_name)
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $resourceRequest->course_name }}
                                </p>
                            @endif

                            <p class="mt-2 text-sm text-slate-500">
                                Requested by

                                <strong>
                                    {{
                                        $resourceRequest->requester?->name
                                        ?? $resourceRequest->requester_name
                                    }}
                                </strong>

                                on

                                {{ $resourceRequest->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Request description --}}
                    @if ($resourceRequest->description)
                        <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-600">
                            {{ $resourceRequest->description }}
                        </p>
                    @endif

                    {{-- Uploaded resources --}}
                    <div class="mt-5 border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-slate-900">
                            Uploaded Resources
                        </h4>

                        @if ($resourceRequest->uploads->isEmpty())
                            <p class="mt-2 text-sm text-slate-500">
                                No resource has been uploaded yet.
                            </p>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach ($resourceRequest->uploads as $upload)
                                    <div
                                        class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-3"
                                    >
                                        <div class="min-w-0">
                                            <p class="break-words font-semibold text-slate-900">
                                                {{ $upload->title }}
                                            </p>

                                            <p class="mt-1 break-words text-xs text-slate-500">
                                                Uploaded by

                                                {{
                                                    $upload->uploader?->name
                                                    ?? $upload->uploader_name
                                                }}

                                                @if ($upload->uploader?->role)
                                                    (
                                                    {{ ucfirst($upload->uploader->role) }}
                                                    )
                                                @endif

                                                — {{ $upload->file_name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-400">
                                                {{
                                                    $upload->created_at
                                                        ->format('d M Y, h:i A')
                                                }}
                                            </p>
                                        </div>

                                        <a
                                            href="{{ $upload->file_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700"
                                        >
                                            Open Resource
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Upload resource form --}}
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <h4 class="font-bold text-emerald-900">
                            Upload Requested Resource
                        </h4>

                        <p class="mt-1 text-xs text-emerald-800">
                            Uploading as

                            <strong>{{ auth()->user()->name }}</strong>

                            ({{ ucfirst(auth()->user()->role) }})
                        </p>

                        <form
                            method="POST"
                            action="{{ route(
                                'resource-requests.uploads.store',
                                $resourceRequest
                            ) }}"
                            enctype="multipart/form-data"
                            class="mt-3 grid gap-3 lg:grid-cols-3"
                        >
                            @csrf

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-emerald-900">
                                    Resource Title *
                                </label>

                                <input
                                    type="text"
                                    name="upload_title"
                                    required
                                    maxlength="180"
                                    placeholder="Resource title"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5"
                                >
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-emerald-900">
                                    Resource File *
                                </label>

                                <input
                                    type="file"
                                    name="resource_file"
                                    required
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.jpg,.jpeg,.png,.webp"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5"
                                >
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-emerald-600 px-5 py-2.5 font-semibold text-white hover:bg-emerald-700"
                                >
                                    Upload to Cloudinary
                                </button>
                            </div>
                        </form>

                        <p class="mt-2 text-xs text-emerald-700">
                            Accepted formats: PDF, DOC, DOCX, PPT, PPTX, TXT,
                            ZIP, JPG, JPEG, PNG and WEBP. Maximum size: 20 MB.
                        </p>
                    </div>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500"
                >
                    No resource requests found.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection