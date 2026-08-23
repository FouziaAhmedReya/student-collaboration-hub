@extends('layouts.app')

@section('title', 'Resource Requests')

@section('content')

<div class="space-y-8">

    {{-- PAGE TITLE --}}
    <div>

        <h1 class="text-3xl font-bold text-slate-900">
            Resource Request System
        </h1>

        <p class="mt-2 text-slate-600">
            Request notes and study materials for a course,
            or upload resources requested by other students.
        </p>

    </div>


    {{-- SEARCH REQUESTS --}}
    <div
        class="rounded-2xl border border-slate-200
               bg-white p-6 shadow-sm"
    >

        <h2 class="text-xl font-bold text-slate-900">
            Find Resource Requests
        </h2>


        <form
            method="GET"
            action="{{ route('resource-requests.index') }}"
            class="mt-5 grid gap-4 md:grid-cols-3"
        >

            {{-- Course --}}
            <div>

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    Course
                </label>

                <input
                    type="text"
                    name="course"
                    value="{{ request('course') }}"
                    placeholder="Example: CSE220"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Status --}}
            <div>

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

                    <option value="">
                        All Requests
                    </option>

                    <option
                        value="open"
                        @selected(
                            request('status') === 'open'
                        )
                    >
                        Open
                    </option>

                    <option
                        value="fulfilled"
                        @selected(
                            request('status') === 'fulfilled'
                        )
                    >
                        Fulfilled
                    </option>

                </select>

            </div>


            {{-- Buttons --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600
                           px-5 py-2.5 font-semibold
                           text-white hover:bg-blue-700"
                >
                    Search
                </button>


                <a
                    href="{{ route('resource-requests.index') }}"
                    class="rounded-xl border
                           border-slate-300 px-5 py-2.5
                           font-semibold text-slate-700"
                >
                    Clear
                </a>

            </div>

        </form>

    </div>


    {{-- CREATE RESOURCE REQUEST --}}
    <div
        class="rounded-2xl border border-slate-200
               bg-white p-6 shadow-sm"
    >

        <h2 class="text-xl font-bold text-slate-900">
            Request a Resource
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Request notes or study materials for a specific course.
        </p>


        <form
            method="POST"
            action="{{ route('resource-requests.store') }}"
            class="mt-5 grid gap-4 md:grid-cols-2"
        >

            @csrf


            {{-- Requester Name --}}
            <div>

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    Your Name *
                </label>

                <input
                    type="text"
                    name="requester_name"
                    value="{{ old('requester_name') }}"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Course Code --}}
            <div>

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    Course Code *
                </label>

                <input
                    type="text"
                    name="course_code"
                    value="{{ old('course_code') }}"
                    placeholder="Example: CSE220"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Course Name --}}
            <div>

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    Course Name
                </label>

                <input
                    type="text"
                    name="course_name"
                    value="{{ old('course_name') }}"
                    placeholder="Example: Data Structures"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Resource Title --}}
            <div>

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    What Do You Need? *
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Example: Midterm Notes"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Description --}}
            <div class="md:col-span-2">

                <label
                    class="mb-1 block text-sm font-semibold
                           text-slate-700"
                >
                    Description
                </label>

                <textarea
                    name="description"
                    rows="3"
                    placeholder="Describe the notes or material you need"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >{{ old('description') }}</textarea>

            </div>


            <div class="md:col-span-2">

                <button
                    type="submit"
                    class="rounded-xl bg-emerald-600
                           px-5 py-2.5 font-semibold
                           text-white hover:bg-emerald-700"
                >
                    Submit Request
                </button>

            </div>

        </form>

    </div>


    {{-- RESOURCE REQUEST LIST --}}
    <div>

        <div
            class="mb-4 flex items-center
                   justify-between"
        >

            <h2 class="text-xl font-bold text-slate-900">
                Resource Requests
            </h2>

            <span class="text-sm text-slate-500">
                {{ $resourceRequests->count() }}
                request(s)
            </span>

        </div>


        <div class="space-y-5">

            @forelse ($resourceRequests as $resourceRequest)

                <div
                    class="rounded-2xl border
                           border-slate-200
                           bg-white p-6 shadow-sm"
                >

                    {{-- REQUEST HEADER --}}
                    <div
                        class="flex flex-col
                               justify-between gap-4
                               md:flex-row"
                    >

                        <div>

                            <div
                                class="flex flex-wrap
                                       items-center gap-2"
                            >

                                {{-- Course Code --}}
                                <span
                                    class="rounded-full
                                           bg-blue-100
                                           px-3 py-1
                                           text-xs font-bold
                                           text-blue-800"
                                >
                                    {{ $resourceRequest->course_code }}
                                </span>


                                {{-- Status --}}
                                @if ($resourceRequest->status === 'fulfilled')

                                    <span
                                        class="rounded-full
                                               bg-emerald-100
                                               px-3 py-1
                                               text-xs font-bold
                                               text-emerald-800"
                                    >
                                        Fulfilled
                                    </span>

                                @else

                                    <span
                                        class="rounded-full
                                               bg-amber-100
                                               px-3 py-1
                                               text-xs font-bold
                                               text-amber-800"
                                    >
                                        Open
                                    </span>

                                @endif

                            </div>


                            <h3
                                class="mt-3 text-xl
                                       font-bold text-slate-900"
                            >
                                {{ $resourceRequest->title }}
                            </h3>


                            @if ($resourceRequest->course_name)

                                <p
                                    class="mt-1 font-medium
                                           text-slate-600"
                                >
                                    {{ $resourceRequest->course_name }}
                                </p>

                            @endif


                            <p
                                class="mt-2 text-sm
                                       text-slate-500"
                            >
                                Requested by
                                <strong>
                                    {{ $resourceRequest->requester_name }}
                                </strong>
                            </p>

                        </div>


                        <div
                            class="text-sm text-slate-400"
                        >
                            {{ $resourceRequest->created_at->format('d M Y, h:i A') }}
                        </div>

                    </div>


                    {{-- DESCRIPTION --}}
                    @if ($resourceRequest->description)

                        <p
                            class="mt-4 rounded-xl
                                   bg-slate-50 p-4
                                   text-sm leading-6
                                   text-slate-600"
                        >
                            {{ $resourceRequest->description }}
                        </p>

                    @endif


                    {{-- UPLOADED RESOURCES --}}
                    <div
                        class="mt-5 border-t
                               border-slate-200 pt-5"
                    >

                        <h4
                            class="font-bold text-slate-900"
                        >
                            Uploaded Resources
                        </h4>


                        @if ($resourceRequest->uploads->isNotEmpty())

                            <div class="mt-3 space-y-2">

                                @foreach ($resourceRequest->uploads as $upload)

                                    <div
                                        class="flex flex-col
                                               justify-between gap-3
                                               rounded-xl bg-slate-50
                                               p-3 sm:flex-row
                                               sm:items-center"
                                    >

                                        <div>

                                            <p
                                                class="font-semibold
                                                       text-slate-800"
                                            >
                                                {{ $upload->title }}
                                            </p>


                                            <p
                                                class="text-xs
                                                       text-slate-500"
                                            >
                                                Uploaded by
                                                {{ $upload->uploader_name }}

                                                ·

                                                {{ $upload->file_name }}
                                            </p>

                                        </div>


                                        <a
                                            href="{{ $upload->file_url }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="font-semibold
                                                   text-blue-700
                                                   hover:underline"
                                        >
                                            Open Resource
                                        </a>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p
                                class="mt-2 text-sm
                                       text-slate-500"
                            >
                                No resource has been uploaded yet.
                            </p>

                        @endif

                    </div>


                    {{-- UPLOAD RESOURCE --}}
                    <div
                        class="mt-5 border-t
                               border-slate-200 pt-5"
                    >

                        <h4
                            class="font-bold text-slate-900"
                        >
                            Upload Requested Resource
                        </h4>


                        <form
                            method="POST"

                            action="{{
                                route(
                                    'resource-requests.uploads.store',
                                    $resourceRequest
                                )
                            }}"

                            enctype="multipart/form-data"

                            class="mt-4 grid gap-3
                                   lg:grid-cols-4"
                        >

                            @csrf


                            <input
                                type="text"
                                name="uploader_name"
                                placeholder="Your name"
                                required
                                class="rounded-xl border
                                       border-slate-300
                                       px-3 py-2.5"
                            >


                            <input
                                type="text"
                                name="upload_title"
                                placeholder="Resource title"
                                required
                                class="rounded-xl border
                                       border-slate-300
                                       px-3 py-2.5"
                            >


                            <input
                                type="file"
                                name="resource_file"
                                required
                                class="rounded-xl border
                                       border-slate-300
                                       px-3 py-2"
                            >


                            <button
                                type="submit"
                                class="rounded-xl
                                       bg-slate-900
                                       px-4 py-2.5
                                       font-semibold
                                       text-white
                                       hover:bg-slate-700"
                            >
                                Upload Resource
                            </button>

                        </form>

                    </div>

                </div>

            @empty

                <div
                    class="rounded-2xl border
                           border-dashed
                           border-slate-300
                           bg-white p-10
                           text-center
                           text-slate-500"
                >
                    No resource requests found.
                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection