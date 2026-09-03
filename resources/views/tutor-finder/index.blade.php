@extends('layouts.app')

@section('title', 'Tutor Finder')

@section('content')
<div class="space-y-7">

    {{-- Page heading --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Tutor Finder
        </h1>

        <p class="mt-2 text-slate-600">
            Students can search approved tutors by subject, availability, and student rating.
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

    {{-- Tutor search --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-xl font-bold text-slate-900">
            Search Tutors
        </h2>

        <form
            method="GET"
            action="{{ route('tutors.index') }}"
            class="mt-5 grid gap-4 md:grid-cols-4"
        >
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Subject
                </label>

                <input
                    type="text"
                    name="subject"
                    value="{{ request('subject') }}"
                    placeholder="Example: CSE or Mathematics"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Availability
                </label>

                <input
                    type="text"
                    name="availability"
                    value="{{ request('availability') }}"
                    placeholder="Example: Sunday Evening"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">
                    Minimum Rating
                </label>

                <select
                    name="min_rating"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                >
                    <option value="">Any rating</option>

                    @foreach ([1, 2, 3, 4, 4.5, 5] as $ratingOption)
                        <option
                            value="{{ $ratingOption }}"
                            @selected((string) request('min_rating') === (string) $ratingOption)
                        >
                            {{ $ratingOption }}+
                        </option>
                    @endforeach
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
                    href="{{ route('tutors.index') }}"
                    class="rounded-xl border border-slate-300 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Clear
                </a>
            </div>
        </form>
    </section>

    {{-- Tutor profile creation --}}
    @if (auth()->user()->role === 'tutor' && ! $currentTutor)
        <section class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">
                Create My Tutor Profile
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                Your registered account name and email will be used automatically.
            </p>

            <form
                method="POST"
                action="{{ route('tutors.store') }}"
                enctype="multipart/form-data"
                class="mt-5 grid gap-4 md:grid-cols-2"
            >
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Subject *
                    </label>

                    <input
                        type="text"
                        name="subject"
                        value="{{ old('subject') }}"
                        required
                        maxlength="160"
                        placeholder="Example: Data Structures"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Availability *
                    </label>

                    <input
                        type="text"
                        name="availability"
                        value="{{ old('availability') }}"
                        required
                        maxlength="255"
                        placeholder="Example: Sunday and Tuesday, 6 PM"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Bio
                    </label>

                    <textarea
                        name="bio"
                        rows="3"
                        maxlength="1000"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                        placeholder="Describe your teaching experience"
                    >{{ old('bio') }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">
                        Profile Image
                    </label>

                    <input
                        type="file"
                        name="profile_image"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        The image will be uploaded to Cloudinary. Maximum size: 5 MB.
                    </p>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 font-semibold text-white hover:bg-emerald-700"
                    >
                        Create Tutor Profile
                    </button>
                </div>
            </form>
        </section>
    @endif

    {{-- Approved tutors --}}
    <section>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-900">
                Approved Tutors
            </h2>

            <span class="text-sm text-slate-500">
                {{ $tutors->count() }} tutor(s)
            </span>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            @forelse ($tutors as $tutor)
                @php
                    $myRating = $tutor->ratings->firstWhere(
                        'user_id',
                        auth()->id()
                    );
                @endphp

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    {{-- Tutor information --}}
                    <div class="flex gap-4">
                        @if ($tutor->profile_image_url)
                            <img
                                src="{{ $tutor->profile_image_url }}"
                                alt="{{ $tutor->name }}"
                                class="h-20 w-20 rounded-2xl object-cover"
                            >
                        @else
                            <div
                                class="grid h-20 w-20 place-items-center rounded-2xl
                                       bg-blue-100 text-2xl font-bold text-blue-700"
                            >
                                {{ strtoupper(substr($tutor->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">
                                        {{ $tutor->name }}
                                    </h3>

                                    <p class="font-semibold text-blue-700">
                                        {{ $tutor->subject }}
                                    </p>
                                </div>

                                @if ($tutor->can_manage)
                                    <span
                                        class="rounded-full bg-emerald-100 px-3 py-1
                                               text-xs font-bold text-emerald-800"
                                    >
                                        My Profile
                                    </span>
                                @endif
                            </div>

                            <p class="mt-2 text-sm text-slate-600">
                                <strong>Availability:</strong>
                                {{ $tutor->availability }}
                            </p>

                            <p class="mt-1 text-sm text-amber-600">
                                <strong>
                                    {{ number_format((float) $tutor->rating, 1) }}/5
                                </strong>

                                from {{ $tutor->ratings->count() }} student rating(s)
                            </p>
                        </div>
                    </div>

                    {{-- Tutor bio --}}
                    @if ($tutor->bio)
                        <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-600">
                            {{ $tutor->bio }}
                        </p>
                    @endif

                    {{-- Teaching materials --}}
                    <div class="mt-5 border-t border-slate-200 pt-4">
                        <h4 class="font-bold text-slate-900">
                            Teaching Materials
                        </h4>

                        @if ($tutor->materials->isEmpty())
                            <p class="mt-2 text-sm text-slate-500">
                                No teaching materials uploaded yet.
                            </p>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach ($tutor->materials as $material)
                                    <div
                                        class="flex items-center justify-between gap-3
                                               rounded-xl bg-slate-50 p-3"
                                    >
                                        <a
                                            href="{{ $material->file_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="min-w-0 break-words font-semibold text-blue-700 hover:underline"
                                        >
                                            {{ $material->title }}
                                            -
                                            {{ $material->file_name }}
                                        </a>

                                        @if ($tutor->can_manage)
                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'tutors.materials.destroy',
                                                    [$tutor, $material]
                                                ) }}"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-bold text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Delete this material?')"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Tutor management --}}
                    @if ($tutor->can_manage)
                        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <h4 class="font-bold text-emerald-900">
                                Upload Teaching Material
                            </h4>

                            <form
                                method="POST"
                                action="{{ route('tutors.materials.store', $tutor) }}"
                                enctype="multipart/form-data"
                                class="mt-3 space-y-3"
                            >
                                @csrf

                                <input
                                    type="text"
                                    name="material_title"
                                    value="{{ old('material_title') }}"
                                    required
                                    maxlength="160"
                                    placeholder="Material title"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5"
                                >

                                <input
                                    type="file"
                                    name="material_file"
                                    required
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5"
                                >

                                <p class="text-xs text-slate-600">
                                    Accepted: PDF, DOC, DOCX, PPT, PPTX, TXT and ZIP.
                                </p>

                                <button
                                    type="submit"
                                    class="rounded-xl bg-emerald-600 px-4 py-2
                                           font-semibold text-white hover:bg-emerald-700"
                                >
                                    Upload to Cloudinary
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('tutors.destroy', $tutor) }}"
                                class="mt-4 border-t border-emerald-200 pt-4"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="font-bold text-red-700 hover:text-red-900"
                                    onclick="return confirm(
                                        'Delete your profile and all teaching materials?'
                                    )"
                                >
                                    Delete My Tutor Profile
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Student rating form --}}
                    @if (auth()->user()->role === 'student')
                        <div class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <h4 class="font-bold text-blue-900">
                                Rate This Tutor
                            </h4>

                            <form
                                method="POST"
                                action="{{ route('tutors.ratings.store', $tutor) }}"
                                class="mt-3 space-y-3"
                            >
                                @csrf

                                <select
                                    name="rating"
                                    required
                                    class="w-full rounded-xl border border-slate-300
                                           bg-white px-3 py-2.5"
                                >
                                    <option value="">Choose rating</option>

                                    @foreach ([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5] as $value)
                                        <option
                                            value="{{ $value }}"
                                            @selected(
                                                (string) old(
                                                    'rating',
                                                    $myRating?->rating
                                                ) === (string) $value
                                            )
                                        >
                                            {{ $value }}/5
                                        </option>
                                    @endforeach
                                </select>

                                <textarea
                                    name="review"
                                    rows="2"
                                    maxlength="1000"
                                    placeholder="Optional review"
                                    class="w-full rounded-xl border border-slate-300
                                           bg-white px-3 py-2.5"
                                >{{ old('review', $myRating?->review) }}</textarea>

                                <button
                                    type="submit"
                                    class="rounded-xl bg-blue-600 px-4 py-2
                                           font-semibold text-white hover:bg-blue-700"
                                >
                                    {{ $myRating ? 'Update My Rating' : 'Submit Rating' }}
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Student reviews --}}
                    @if ($tutor->ratings->whereNotNull('review')->isNotEmpty())
                        <div class="mt-5 border-t border-slate-200 pt-4">
                            <h4 class="font-bold text-slate-900">
                                Student Reviews
                            </h4>

                            <div class="mt-3 space-y-3">
                                @foreach (
                                    $tutor->ratings
                                        ->whereNotNull('review')
                                        ->take(3)
                                    as $rating
                                )
                                    <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
                                        <strong>
                                            {{ $rating->student?->name ?? 'Student' }}
                                        </strong>

                                        -
                                        {{ number_format((float) $rating->rating, 1) }}/5

                                        <p class="mt-1 whitespace-pre-line">
                                            {{ $rating->review }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-300
                           bg-white p-10 text-center text-slate-500 lg:col-span-2"
                >
                    No approved tutor matched your search.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection