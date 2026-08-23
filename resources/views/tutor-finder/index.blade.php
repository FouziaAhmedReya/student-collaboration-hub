@extends('layouts.app')

@section('title', 'Tutor Finder')

@section('content')

<div class="space-y-8">

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="rounded-xl bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR MESSAGES --}}
    @if ($errors->any())
        <div class="rounded-xl bg-red-100 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- PAGE TITLE --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Tutor Finder
        </h1>

        <p class="mt-2 text-slate-600">
            Find tutors by subject, availability and rating.
        </p>
    </div>


    {{-- SEARCH --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-xl font-bold text-slate-900">
            Search Tutors
        </h2>

        <form
            method="GET"
            action="{{ route('tutors.index') }}"
            class="mt-5 grid gap-4 md:grid-cols-4"
        >

            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Subject
                </label>

                <input
                    type="text"
                    name="subject"
                    value="{{ request('subject') }}"
                    placeholder="Example: Data Structures"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Availability
                </label>

                <input
                    type="text"
                    name="availability"
                    value="{{ request('availability') }}"
                    placeholder="Example: Evening"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Minimum Rating
                </label>

                <select
                    name="min_rating"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
                    <option value="">
                        Any Rating
                    </option>

                    <option value="1" @selected(request('min_rating') == '1')>
                        1+
                    </option>

                    <option value="2" @selected(request('min_rating') == '2')>
                        2+
                    </option>

                    <option value="3" @selected(request('min_rating') == '3')>
                        3+
                    </option>

                    <option value="4" @selected(request('min_rating') == '4')>
                        4+
                    </option>

                    <option value="4.5" @selected(request('min_rating') == '4.5')>
                        4.5+
                    </option>

                    <option value="5" @selected(request('min_rating') == '5')>
                        5
                    </option>
                </select>
            </div>


            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-2 text-white hover:bg-blue-700"
                >
                    Search
                </button>

                <a
                    href="{{ route('tutors.index') }}"
                    class="rounded-xl border border-slate-300 px-5 py-2"
                >
                    Clear
                </a>

            </div>

        </form>

    </div>


    {{-- ADD TUTOR --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-xl font-bold text-slate-900">
            Add Tutor Profile
        </h2>

        <form
            method="POST"
            action="{{ route('tutors.store') }}"
            enctype="multipart/form-data"
            class="mt-5 grid gap-4 md:grid-cols-2"
        >

            @csrf


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Tutor Name *
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Subject *
                </label>

                <input
                    type="text"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="Example: CSE"
                    required
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Availability *
                </label>

                <input
                    type="text"
                    name="availability"
                    value="{{ old('availability') }}"
                    placeholder="Example: Sunday Evening"
                    required
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Rating *
                </label>

                <input
                    type="number"
                    name="rating"
                    value="{{ old('rating', 5) }}"
                    min="0"
                    max="5"
                    step="0.1"
                    required
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div>
                <label class="mb-1 block text-sm font-semibold">
                    Profile Photo
                </label>

                <input
                    type="file"
                    name="profile_image"
                    accept="image/*"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >
            </div>


            <div class="md:col-span-2">

                <label class="mb-1 block text-sm font-semibold">
                    Bio
                </label>

                <textarea
                    name="bio"
                    rows="3"
                    placeholder="Write something about the tutor"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2"
                >{{ old('bio') }}</textarea>

            </div>


            <div class="md:col-span-2">

                <button
                    type="submit"
                    class="rounded-xl bg-green-600 px-5 py-2 text-white hover:bg-green-700"
                >
                    Add Tutor
                </button>

            </div>

        </form>

    </div>


    {{-- AVAILABLE TUTORS --}}
    <div>

        <h2 class="mb-4 text-xl font-bold text-slate-900">
            Available Tutors
        </h2>


        <div class="grid gap-5 lg:grid-cols-2">

            @forelse ($tutors as $tutor)

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                    <div class="flex gap-4">

                        {{-- PROFILE IMAGE --}}
                        @if ($tutor->profile_image_url)

                            <img
                                src="{{ $tutor->profile_image_url }}"
                                alt="{{ $tutor->name }}"
                                class="h-20 w-20 rounded-xl object-cover"
                            >

                        @else

                            <div class="flex h-20 w-20 items-center justify-center rounded-xl bg-blue-100 text-2xl font-bold text-blue-700">
                                {{ strtoupper(substr($tutor->name, 0, 1)) }}
                            </div>

                        @endif


                        <div>

                            <h3 class="text-lg font-bold">
                                {{ $tutor->name }}
                            </h3>

                            <p class="font-semibold text-blue-600">
                                {{ $tutor->subject }}
                            </p>

                            <p class="text-sm text-slate-600">
                                Rating:
                                ⭐ {{ number_format((float) $tutor->rating, 1) }}
                            </p>

                            <p class="text-sm text-slate-600">
                                Availability:
                                {{ $tutor->availability }}
                            </p>

                            @if ($tutor->email)
                                <p class="text-sm text-slate-600">
                                    Email:
                                    {{ $tutor->email }}
                                </p>
                            @endif

                        </div>

                    </div>


                    @if ($tutor->bio)

                        <p class="mt-4 text-sm text-slate-600">
                            {{ $tutor->bio }}
                        </p>

                    @endif


                    {{-- TEACHING MATERIAL --}}
                    <div class="mt-5 border-t pt-4">

                        <h4 class="font-bold">
                            Teaching Materials
                        </h4>


                        @if ($tutor->materials->count() > 0)

                            <div class="mt-3 space-y-2">

                                @foreach ($tutor->materials as $material)

                                    <div class="flex items-center justify-between rounded-lg bg-slate-100 p-3">

                                        <span>
                                            {{ $material->title }}
                                        </span>

                                        <a
                                            href="{{ $material->file_url }}"
                                            target="_blank"
                                            class="font-semibold text-blue-600"
                                        >
                                            Open
                                        </a>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p class="mt-2 text-sm text-slate-500">
                                No teaching materials uploaded yet.
                            </p>

                        @endif


                        {{-- UPLOAD MATERIAL --}}
                        <form
                            method="POST"
                            action="{{ route('tutors.materials.store', $tutor) }}"
                            enctype="multipart/form-data"
                            class="mt-4 space-y-3"
                        >

                            @csrf


                            <input
                                type="text"
                                name="material_title"
                                placeholder="Material title"
                                required
                                class="w-full rounded-xl border border-slate-300 px-3 py-2"
                            >


                            <input
                                type="file"
                                name="material_file"
                                required
                                class="w-full rounded-xl border border-slate-300 px-3 py-2"
                            >


                            <button
                                type="submit"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-white"
                            >
                                Upload Material
                            </button>

                        </form>

                    </div>

                </div>

            @empty

                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500 lg:col-span-2">
                    No tutors found.
                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection