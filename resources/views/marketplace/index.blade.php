@extends('layouts.app')

@section('title', 'Used Book Marketplace')

@section('content')
<div class="mb-7 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
    <div>
        <p class="mb-1 text-sm font-semibold text-blue-700">
            Student-to-student marketplace
        </p>

        <h1 class="text-3xl font-bold tracking-tight text-slate-950">
            Used Book Marketplace
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Buy and sell used academic books.
        </p>
    </div>

    <div class="flex flex-wrap gap-3">
        <a
            href="{{ route('marketplace.manage') }}"
            class="inline-flex min-h-11 items-center justify-center
                   rounded-xl border border-slate-300 bg-white px-5
                   text-sm font-bold text-slate-700 transition
                   hover:border-blue-300 hover:text-blue-700"
        >
            My Activity
        </a>

        <a
            href="{{ route('marketplace.create') }}"
            class="inline-flex min-h-11 items-center justify-center
                   rounded-xl bg-blue-600 px-5 text-sm font-bold
                   text-white transition hover:bg-blue-700"
        >
            + Sell a Book
        </a>
    </div>
</div>

<form
    method="GET"
    action="{{ route('marketplace.index') }}"
    class="mb-7 rounded-2xl border border-slate-200
           bg-white p-5 shadow-sm"
>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-6">

        {{-- Search --}}
        <div class="lg:col-span-2">
            <label
                for="search"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-wide text-slate-600"
            >
                Search
            </label>

            <input
                id="search"
                name="search"
                type="search"
                value="{{ request('search') }}"
                placeholder="Title, author, or course"
                class="min-h-11 w-full rounded-xl border
                       border-slate-300 px-4 text-sm outline-none
                       focus:border-blue-500 focus:ring-4
                       focus:ring-blue-100"
            >
        </div>

        {{-- Category --}}
        <div>
            <label
                for="category"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-wide text-slate-600"
            >
                Category
            </label>

            <select
                id="category"
                name="category"
                class="min-h-11 w-full rounded-xl border
                       border-slate-300 bg-white px-3 text-sm"
            >
                <option value="">
                    All Categories
                </option>

                @foreach ($categories as $category)
                    <option
                        value="{{ $category }}"
                        @selected(request('category') === $category)
                    >
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Course --}}
        <div>
            <label
                for="course"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-wide text-slate-600"
            >
                Course
            </label>

            <select
                id="course"
                name="course"
                class="min-h-11 w-full rounded-xl border
                       border-slate-300 bg-white px-3 text-sm"
            >
                <option value="">
                    All Courses
                </option>

                @foreach ($courses as $course)
                    <option
                        value="{{ $course }}"
                        @selected(request('course') === $course)
                    >
                        {{ $course }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Condition --}}
        <div>
            <label
                for="condition"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-wide text-slate-600"
            >
                Condition
            </label>

            <select
                id="condition"
                name="condition"
                class="min-h-11 w-full rounded-xl border
                       border-slate-300 bg-white px-3 text-sm"
            >
                <option value="">
                    All Conditions
                </option>

                @foreach ($conditions as $value => $label)
                    <option
                        value="{{ $value }}"
                        @selected(request('condition') === $value)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sort --}}
        <div>
            <label
                for="sort"
                class="mb-2 block text-xs font-bold uppercase
                       tracking-wide text-slate-600"
            >
                Sort By
            </label>

            <select
                id="sort"
                name="sort"
                class="min-h-11 w-full rounded-xl border
                       border-slate-300 bg-white px-3 text-sm"
            >
                <option
                    value="latest"
                    @selected(request('sort', 'latest') === 'latest')
                >
                    Latest
                </option>

                <option
                    value="price_low"
                    @selected(request('sort') === 'price_low')
                >
                    Lowest Price
                </option>

                <option
                    value="price_high"
                    @selected(request('sort') === 'price_high')
                >
                    Highest Price
                </option>

                <option
                    value="title"
                    @selected(request('sort') === 'title')
                >
                    Title A–Z
                </option>
            </select>
        </div>
    </div>

    <div class="mt-4 flex justify-end gap-3">
        <a
            href="{{ route('marketplace.index') }}"
            class="rounded-lg px-4 py-2 text-sm font-bold
                   text-slate-600 hover:bg-slate-100"
        >
            Clear
        </a>

        <button
            type="submit"
            class="rounded-lg bg-slate-900 px-5 py-2
                   text-sm font-bold text-white hover:bg-slate-700"
        >
            Apply Filters
        </button>
    </div>
</form>

@if ($books->isEmpty())

    <div class="rounded-2xl border border-dashed border-slate-300
                bg-white px-6 py-16 text-center">
        <div class="mx-auto flex h-14 w-14 items-center
                    justify-center rounded-2xl bg-blue-50
                    text-2xl text-blue-600">
            📚
        </div>

        <h2 class="mt-4 text-lg font-bold text-slate-900">
            No books found
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            Try changing the filters or list the first book.
        </p>

        <a
            href="{{ route('marketplace.create') }}"
            class="mt-5 inline-flex rounded-lg bg-blue-600
                   px-5 py-2 text-sm font-bold text-white
                   hover:bg-blue-700"
        >
            Sell a Book
        </a>
    </div>

@else

    <div class="mb-4">
        <p class="text-sm text-slate-500">
            <span class="font-bold text-slate-800">
                {{ $books->total() }}
            </span>

            {{ Str::plural('book', $books->total()) }} found
        </p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        @foreach ($books as $book)

            @php
                $statusColor = match ($book->status) {
                    'active' => 'bg-emerald-100 text-emerald-800',
                    'reserved' => 'bg-amber-100 text-amber-800',
                    default => 'bg-slate-200 text-slate-700',
                };
            @endphp

            <article
                class="flex flex-col overflow-hidden rounded-2xl
                       border border-slate-200 bg-white shadow-sm
                       transition hover:-translate-y-1
                       hover:border-blue-200 hover:shadow-md"
            >
                <a
                    href="{{ route('marketplace.show', $book) }}"
                    class="relative block bg-slate-100"
                >
                    <img
                        src="{{ $book->image_url }}"
                        alt="Cover of {{ $book->title }}"
                        class="h-60 w-full object-contain p-5"
                        loading="lazy"
                    >

                    <span
                        class="absolute right-3 top-3 rounded-full
                               px-3 py-1 text-xs font-bold
                               {{ $statusColor }}"
                    >
                        {{ $book->status_label }}
                    </span>
                </a>

                <div class="flex flex-1 flex-col p-5">
                    <p class="text-xs font-bold uppercase
                              tracking-wide text-blue-700">
                        {{ $book->category }}
                    </p>

                    <h2 class="mt-1 text-lg font-bold leading-snug
                               text-slate-950">
                        {{ $book->title }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Author: {{ $book->author }}
                    </p>

                    <div class="mt-4 space-y-1.5 text-sm">
                        <p>
                            <span class="font-semibold text-slate-500">
                                Course:
                            </span>

                            {{ $book->course }}
                        </p>

                        <p>
                            <span class="font-semibold text-slate-500">
                                Condition:
                            </span>

                            {{ $book->condition_label }}
                        </p>
                    </div>

                    <div class="mt-auto flex items-center justify-between
                                gap-3 border-t border-slate-100 pt-4">
                        <p class="text-xl font-black text-emerald-600">
                            ৳{{ number_format((float) $book->price, 0) }}
                        </p>

                        <a
                            href="{{ route('marketplace.show', $book) }}"
                            class="rounded-lg bg-blue-600 px-4 py-2
                                   text-sm font-bold text-white
                                   hover:bg-blue-700"
                        >
                            View Details
                        </a>
                    </div>
                </div>
            </article>

        @endforeach

    </div>

    @if ($books->hasPages())
        <div class="mt-8">
            {{ $books->onEachSide(1)->links() }}
        </div>
    @endif

@endif
@endsection