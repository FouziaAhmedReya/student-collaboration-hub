@extends('layouts.app')

@section('title', 'Tutor Finder')

@section('content')

<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}

    <section>

        <h1 class="text-3xl font-bold text-slate-900">
            Tutor Finder
        </h1>

        <p class="mt-2 text-slate-600">
            Find tutors by subject, availability and rating.
        </p>

    </section>


    {{-- ========================================================= --}}
    {{-- SEARCH TUTORS --}}
    {{-- ========================================================= --}}

    <section
        class="rounded-2xl border border-slate-200
               bg-white p-6 shadow-sm"
    >

        <h2 class="text-xl font-bold text-slate-900">
            Search Tutors
        </h2>


        <form
            method="GET"
            action="{{ route('tutors.index') }}"
            class="mt-5 grid gap-4 md:grid-cols-4"
        >

            {{-- Subject --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Subject
                </label>

                <input
                    type="text"
                    name="subject"
                    value="{{ request('subject') }}"
                    placeholder="Example: Data Structures"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Availability --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Availability
                </label>

                <input
                    type="text"
                    name="availability"
                    value="{{ request('availability') }}"
                    placeholder="Example: Evening"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Minimum Rating --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Minimum Rating
                </label>

                <select
                    name="min_rating"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

                    <option value="">
                        Any Rating
                    </option>

                    <option
                        value="1"
                        @selected(request('min_rating') == '1')
                    >
                        1+
                    </option>

                    <option
                        value="2"
                        @selected(request('min_rating') == '2')
                    >
                        2+
                    </option>

                    <option
                        value="3"
                        @selected(request('min_rating') == '3')
                    >
                        3+
                    </option>

                    <option
                        value="4"
                        @selected(request('min_rating') == '4')
                    >
                        4+
                    </option>

                    <option
                        value="4.5"
                        @selected(request('min_rating') == '4.5')
                    >
                        4.5+
                    </option>

                    <option
                        value="5"
                        @selected(request('min_rating') == '5')
                    >
                        5
                    </option>

                </select>

            </div>


            {{-- Buttons --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600
                           px-5 py-2.5
                           font-semibold text-white
                           hover:bg-blue-700"
                >
                    Search
                </button>


                <a
                    href="{{ route('tutors.index') }}"
                    class="rounded-xl border
                           border-slate-300
                           px-5 py-2.5
                           font-semibold text-slate-700
                           hover:bg-slate-50"
                >
                    Clear
                </a>

            </div>

        </form>

    </section>


    {{-- ========================================================= --}}
    {{-- ADD TUTOR --}}
    {{-- ========================================================= --}}

    <section
        class="rounded-2xl border border-slate-200
               bg-white p-6 shadow-sm"
    >

        <h2 class="text-xl font-bold text-slate-900">
            Add Tutor Profile
        </h2>


        <p class="mt-1 text-sm text-slate-500">
            The browser used to create the profile becomes the owner
            of that tutor profile.
        </p>


        <form
            method="POST"
            action="{{ route('tutors.store') }}"
            enctype="multipart/form-data"
            class="mt-5 grid gap-4 md:grid-cols-2"
        >

            @csrf


            {{-- Tutor Name --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Tutor Name *
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Email --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Subject --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Subject *
                </label>

                <input
                    type="text"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="Example: CSE"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Availability --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Availability *
                </label>

                <input
                    type="text"
                    name="availability"
                    value="{{ old('availability') }}"
                    placeholder="Example: Sunday Evening"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Rating --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Rating (0-5) *
                </label>

                <input
                    type="number"
                    name="rating"
                    value="{{ old('rating', 5) }}"
                    min="0"
                    max="5"
                    step="0.1"
                    required
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >

            </div>


            {{-- Profile Photo --}}
            <div>

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Profile Photo
                </label>

                <input
                    type="file"
                    name="profile_image"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2"
                >

                <p class="mt-1 text-xs text-slate-500">
                    JPG, JPEG, PNG or WebP. Maximum 5 MB.
                </p>

            </div>


            {{-- Bio --}}
            <div class="md:col-span-2">

                <label
                    class="mb-1 block text-sm
                           font-semibold text-slate-700"
                >
                    Bio
                </label>

                <textarea
                    name="bio"
                    rows="3"
                    placeholder="Write something about the tutor"
                    class="w-full rounded-xl border
                           border-slate-300 px-3 py-2.5"
                >{{ old('bio') }}</textarea>

            </div>


            {{-- Submit --}}
            <div class="md:col-span-2">

                <button
                    type="submit"
                    class="rounded-xl bg-emerald-600
                           px-5 py-2.5
                           font-semibold text-white
                           hover:bg-emerald-700"
                >
                    Add Tutor
                </button>

            </div>

        </form>

    </section>


    {{-- ========================================================= --}}
    {{-- AVAILABLE TUTORS --}}
    {{-- ========================================================= --}}

    <section>

        <div
            class="mb-4 flex items-center
                   justify-between"
        >

            <h2 class="text-2xl font-bold text-slate-900">
                Available Tutors
            </h2>

            <span class="text-sm text-slate-500">
                {{ $tutors->count() }} tutor(s)
            </span>

        </div>


        <div class="grid gap-6 lg:grid-cols-2">

            @forelse ($tutors as $tutor)

                <article
                    class="rounded-2xl border
                           border-slate-200 bg-white
                           p-6 shadow-sm"
                >

                    {{-- ================================================= --}}
                    {{-- TUTOR INFORMATION --}}
                    {{-- ================================================= --}}

                    <div class="flex gap-4">

                        {{-- Profile Image --}}
                        @if ($tutor->profile_image_url)

                            <img
                                src="{{ $tutor->profile_image_url }}"
                                alt="{{ $tutor->name }}"
                                class="h-24 w-24 shrink-0
                                       rounded-2xl object-cover"
                            >

                        @else

                            <div
                                class="flex h-24 w-24 shrink-0
                                       items-center justify-center
                                       rounded-2xl bg-blue-100
                                       text-3xl font-bold
                                       text-blue-700"
                            >

                                {{
                                    strtoupper(
                                        substr(
                                            $tutor->name,
                                            0,
                                            1
                                        )
                                    )
                                }}

                            </div>

                        @endif


                        <div class="min-w-0 flex-1">

                            <div
                                class="flex flex-wrap
                                       items-start justify-between
                                       gap-3"
                            >

                                <div>

                                    <h3
                                        class="text-xl font-bold
                                               text-slate-900"
                                    >
                                        {{ $tutor->name }}
                                    </h3>


                                    <p
                                        class="mt-1 font-semibold
                                               text-blue-600"
                                    >
                                        {{ $tutor->subject }}
                                    </p>

                                </div>


                                <span
                                    class="rounded-full bg-amber-100
                                           px-3 py-1 text-sm
                                           font-bold text-amber-800"
                                >
                                    ⭐
                                    {{
                                        number_format(
                                            (float) $tutor->rating,
                                            1
                                        )
                                    }}
                                </span>

                            </div>


                            <p
                                class="mt-2 text-sm
                                       text-slate-600"
                            >
                                <strong>
                                    Availability:
                                </strong>

                                {{ $tutor->availability }}
                            </p>


                            @if ($tutor->email)

                                <p
                                    class="mt-1 text-sm
                                           text-slate-600"
                                >
                                    <strong>
                                        Email:
                                    </strong>

                                    {{ $tutor->email }}
                                </p>

                            @endif


                            {{-- Owner Badge --}}
                            @if ($tutor->can_manage)

                                <span
                                    class="mt-3 inline-flex
                                           rounded-full
                                           bg-emerald-100
                                           px-3 py-1
                                           text-xs font-semibold
                                           text-emerald-700"
                                >
                                    Your Tutor Profile
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- Bio --}}
                    @if ($tutor->bio)

                        <p
                            class="mt-4 rounded-xl
                                   bg-slate-50 p-4
                                   text-sm leading-6
                                   text-slate-600"
                        >
                            {{ $tutor->bio }}
                        </p>

                    @endif


                    {{-- ================================================= --}}
                    {{-- OWNER DELETE TUTOR BUTTON --}}
                    {{-- ================================================= --}}

                    @if ($tutor->can_manage)

                        <div class="mt-4">

                            <form
                                method="POST"
                                action="{{ route('tutors.destroy', $tutor) }}"
                                onsubmit="
                                    return confirm(
                                        'Are you sure you want to delete your tutor profile? Your profile image and all teaching materials will also be deleted.'
                                    );
                                "
                            >

                                @csrf
                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="rounded-lg bg-red-600
                                           px-4 py-2
                                           text-sm font-semibold
                                           text-white
                                           hover:bg-red-700"
                                >
                                    Delete My Tutor Profile
                                </button>

                            </form>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- TEACHING MATERIALS --}}
                    {{-- ================================================= --}}

                    <div
                        class="mt-6 border-t
                               border-slate-200 pt-5"
                    >

                        <h4
                            class="text-lg font-bold
                                   text-slate-900"
                        >
                            Teaching Materials
                        </h4>


                        @if ($tutor->materials->isNotEmpty())

                            <div class="mt-3 space-y-3">

                                @foreach ($tutor->materials as $material)

                                    @php

                                        /*
                                        | Get original extension.
                                        */
                                        $extension = strtolower(
                                            pathinfo(
                                                $material->file_name,
                                                PATHINFO_EXTENSION
                                            )
                                        );


                                        /*
                                        | Office files.
                                        */
                                        $officeExtensions = [
                                            'ppt',
                                            'pptx',
                                            'doc',
                                            'docx',
                                            'xls',
                                            'xlsx',
                                        ];


                                        /*
                                        | Office files use Microsoft viewer.
                                        | PDF and others use Cloudinary URL.
                                        */
                                        if (
                                            in_array(
                                                $extension,
                                                $officeExtensions,
                                                true
                                            )
                                        ) {

                                            $openUrl =
                                                'https://view.officeapps.live.com/op/view.aspx?src='
                                                .rawurlencode(
                                                    $material->file_url
                                                );

                                        } else {

                                            $openUrl =
                                                $material->file_url;
                                        }

                                    @endphp


                                    <div
                                        class="flex flex-col
                                               justify-between
                                               gap-3 rounded-xl
                                               bg-slate-100 p-4
                                               sm:flex-row
                                               sm:items-center"
                                    >

                                        {{-- File Information --}}
                                        <div class="min-w-0">

                                            <p
                                                class="font-semibold
                                                       text-slate-800"
                                            >
                                                {{ $material->title }}
                                            </p>


                                            <p
                                                class="mt-1 truncate
                                                       text-xs
                                                       text-slate-500"
                                            >
                                                {{ $material->file_name }}
                                            </p>

                                        </div>


                                        <div
                                            class="flex shrink-0
                                                   items-center gap-4"
                                        >

                                            {{-- Anyone Can Open --}}
                                            <a
                                                href="{{ $openUrl }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="font-semibold
                                                       text-blue-600
                                                       hover:underline"
                                            >
                                                Open
                                            </a>


                                            {{-- Only Tutor Owner Can Delete --}}
                                            @if ($tutor->can_manage)

                                                <form
                                                    method="POST"
                                                    action="{{
                                                        route(
                                                            'tutors.materials.destroy',
                                                            [
                                                                $tutor,
                                                                $material
                                                            ]
                                                        )
                                                    }}"
                                                    onsubmit="
                                                        return confirm(
                                                            'Are you sure you want to delete this teaching material?'
                                                        );
                                                    "
                                                >

                                                    @csrf
                                                    @method('DELETE')


                                                    <button
                                                        type="submit"
                                                        class="font-semibold
                                                               text-red-600
                                                               hover:underline"
                                                    >
                                                        Delete
                                                    </button>

                                                </form>

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p
                                class="mt-2 text-sm
                                       text-slate-500"
                            >
                                No teaching materials uploaded yet.
                            </p>

                        @endif


                        {{-- ================================================= --}}
                        {{-- OWNER ONLY UPLOAD --}}
                        {{-- ================================================= --}}

                        @if ($tutor->can_manage)

                            <div
                                class="mt-5 rounded-xl
                                       border border-blue-100
                                       bg-blue-50 p-4"
                            >

                                <h5
                                    class="font-semibold
                                           text-slate-900"
                                >
                                    Upload Teaching Material
                                </h5>


                                <p
                                    class="mt-1 text-xs
                                           text-slate-500"
                                >
                                    Only you can upload or delete
                                    teaching materials for this tutor profile.
                                </p>


                                <form
                                    method="POST"
                                    action="{{
                                        route(
                                            'tutors.materials.store',
                                            $tutor
                                        )
                                    }}"
                                    enctype="multipart/form-data"
                                    class="mt-4 space-y-3"
                                >

                                    @csrf


                                    {{-- Material Title --}}
                                    <div>

                                        <label
                                            class="mb-1 block
                                                   text-sm font-semibold
                                                   text-slate-700"
                                        >
                                            Material Title
                                        </label>

                                        <input
                                            type="text"
                                            name="material_title"
                                            placeholder="Example: Chapter 1 Notes"
                                            required
                                            class="w-full rounded-xl
                                                   border
                                                   border-slate-300
                                                   bg-white
                                                   px-3 py-2.5"
                                        >

                                    </div>


                                    {{-- File --}}
                                    <div>

                                        <label
                                            class="mb-1 block
                                                   text-sm font-semibold
                                                   text-slate-700"
                                        >
                                            Choose Material
                                        </label>

                                        <input
                                            type="file"
                                            name="material_file"
                                            accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip"
                                            required
                                            class="w-full rounded-xl
                                                   border
                                                   border-slate-300
                                                   bg-white
                                                   px-3 py-2.5"
                                        >


                                        <p
                                            class="mt-1 text-xs
                                                   text-slate-500"
                                        >
                                            PDF, DOC, DOCX, PPT,
                                            PPTX, TXT or ZIP.
                                            Maximum 20 MB.
                                        </p>

                                    </div>


                                    <button
                                        type="submit"
                                        class="rounded-xl
                                               bg-slate-900
                                               px-5 py-2.5
                                               font-semibold
                                               text-white
                                               hover:bg-slate-700"
                                    >
                                        Upload Material
                                    </button>

                                </form>

                            </div>

                        @else

                            <div
                                class="mt-5 rounded-xl
                                       border border-slate-200
                                       bg-slate-50 p-3"
                            >

                                <p
                                    class="text-xs
                                           text-slate-500"
                                >
                                    Only the tutor who created this
                                    profile can upload or delete
                                    teaching materials.
                                </p>

                            </div>

                        @endif

                    </div>

                </article>

            @empty

                <div
                    class="rounded-2xl
                           border border-dashed
                           border-slate-300
                           bg-white p-10
                           text-center
                           text-slate-500
                           lg:col-span-2"
                >
                    No tutors found.
                </div>

            @endforelse

        </div>

    </section>

</div>

@endsection