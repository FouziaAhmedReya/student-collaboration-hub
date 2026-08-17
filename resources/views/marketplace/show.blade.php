@extends('layouts.app')

@section('title', $book->title)

@section('content')
@php
    $statusColor = match ($book->status) {
        'active' => 'bg-emerald-100 text-emerald-800',
        'reserved' => 'bg-amber-100 text-amber-800',
        default => 'bg-slate-200 text-slate-700',
    };
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <a
        href="{{ route('marketplace.index') }}"
        class="text-sm font-bold text-blue-700 hover:text-blue-900"
    >
        ← Back to Marketplace
    </a>

    <a
        href="{{ route('marketplace.manage') }}"
        class="text-sm font-bold text-slate-600 hover:text-slate-950"
    >
        My Buying & Selling Activity
    </a>
</div>

<div class="grid gap-8 lg:grid-cols-2">

    {{-- Book image --}}
    <section class="overflow-hidden rounded-2xl border
                    border-slate-200 bg-white shadow-sm">
        <div class="relative flex min-h-[480px] items-center
                    justify-center bg-slate-100 p-8">

            <img
                src="{{ $book->image_url }}"
                alt="Cover of {{ $book->title }}"
                class="max-h-[520px] w-full object-contain"
            >

            <span
                class="absolute right-5 top-5 rounded-full
                       px-3 py-1.5 text-xs font-bold
                       {{ $statusColor }}"
            >
                {{ $book->status_label }}
            </span>
        </div>

        @if ($book->description)
            <div class="border-t border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-950">
                    Seller's Description
                </h2>

                <p class="mt-3 whitespace-pre-line text-sm
                          leading-7 text-slate-600">
                    {{ $book->description }}
                </p>
            </div>
        @endif
    </section>

    <div class="space-y-6">

        {{-- Book information --}}
        <section class="rounded-2xl border border-slate-200
                        bg-white p-6 shadow-sm sm:p-8">

            <p class="text-xs font-bold uppercase tracking-wide text-blue-700">
                {{ $book->category }}
            </p>

            <h1 class="mt-2 text-3xl font-bold leading-tight
                       tracking-tight text-slate-950">
                {{ $book->title }}
            </h1>

            <p class="mt-2 text-base text-slate-500">
                by {{ $book->author }}
            </p>

            <p class="mt-6 text-4xl font-black text-emerald-600">
                ৳{{ number_format((float) $book->price, 0) }}
            </p>

            <div class="mt-7 divide-y divide-slate-100
                        rounded-xl border border-slate-200">

                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <span class="font-semibold text-slate-500">
                        Course
                    </span>

                    <span class="font-bold text-slate-900">
                        {{ $book->course }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <span class="font-semibold text-slate-500">
                        Condition
                    </span>

                    <span class="font-bold text-slate-900">
                        {{ $book->condition_label }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <span class="font-semibold text-slate-500">
                        Status
                    </span>

                    <span class="font-bold text-slate-900">
                        {{ $book->status_label }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <span class="font-semibold text-slate-500">
                        Seller
                    </span>

                    <span class="font-bold text-slate-900">
                        {{ $book->seller_name }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                    <span class="font-semibold text-slate-500">
                        Listed
                    </span>

                    <span class="font-bold text-slate-900">
                        {{ $book->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            {{-- Seller's controls --}}
            @if ($isOwner)
                <div class="mt-6 rounded-xl border border-blue-200
                            bg-blue-50 p-4 text-sm text-blue-900">
                    This is your listing. Purchase requests will appear
                    inside My Activity.
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <a
                        href="{{ route('marketplace.edit', $book) }}"
                        class="inline-flex min-h-11 flex-1 items-center
                               justify-center rounded-xl bg-blue-600 px-5
                               text-sm font-bold text-white hover:bg-blue-700"
                    >
                        Edit Listing
                    </a>

                    <form
                        method="POST"
                        action="{{ route('marketplace.destroy', $book) }}"
                        onsubmit="return confirm('Are you sure you want to delete this book listing?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex min-h-11 items-center
                                   justify-center rounded-xl border
                                   border-red-200 px-5 text-sm
                                   font-bold text-red-700 hover:bg-red-50"
                        >
                            Delete
                        </button>
                    </form>
                </div>

            @elseif ($book->status !== 'active')

                <div class="mt-6 rounded-xl border border-amber-200
                            bg-amber-50 p-4 text-sm text-amber-900">

                    @if ($book->status === 'reserved')
                        Another student has already requested this book.
                    @else
                        This book has already been sold.
                    @endif
                </div>
            @endif
        </section>

        {{-- Buyer form --}}
        @if (! $isOwner && $book->status === 'active')
            <section class="rounded-2xl border border-slate-200
                            bg-white p-6 shadow-sm sm:p-8">

                <h2 class="text-xl font-bold text-slate-950">
                    Request to Buy
                </h2>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Send your information to the seller.
                </p>

                <form
                    method="POST"
                    action="{{ route('marketplace.orders.store', $book) }}"
                    class="mt-5 space-y-4"
                >
                    @csrf

                    <div>
                        <label
                            for="buyer_name"
                            class="mb-2 block text-sm font-bold
                                   text-slate-700"
                        >
                            Your Name <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="buyer_name"
                            name="buyer_name"
                            type="text"
                            maxlength="120"
                            value="{{ old(
                                'buyer_name',
                                auth()->user()?->name
                            ) }}"
                            required
                            class="min-h-11 w-full rounded-xl border
                                   border-slate-300 px-4 text-sm"
                        >
                    </div>

                    <div>
                        <label
                            for="buyer_email"
                            class="mb-2 block text-sm font-bold
                                   text-slate-700"
                        >
                            Email <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="buyer_email"
                            name="buyer_email"
                            type="email"
                            maxlength="150"
                            value="{{ old(
                                'buyer_email',
                                auth()->user()?->email
                            ) }}"
                            required
                            class="min-h-11 w-full rounded-xl border
                                   border-slate-300 px-4 text-sm"
                        >
                    </div>

                    <div>
                        <label
                            for="buyer_phone"
                            class="mb-2 block text-sm font-bold
                                   text-slate-700"
                        >
                            Phone Number
                        </label>

                        <input
                            id="buyer_phone"
                            name="buyer_phone"
                            type="text"
                            maxlength="30"
                            value="{{ old('buyer_phone') }}"
                            placeholder="01XXXXXXXXX"
                            class="min-h-11 w-full rounded-xl border
                                   border-slate-300 px-4 text-sm"
                        >
                    </div>

                    <div>
                        <label
                            for="message"
                            class="mb-2 block text-sm font-bold
                                   text-slate-700"
                        >
                            Message to Seller
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="3"
                            maxlength="1000"
                            placeholder="When would you like to collect the book?"
                            class="w-full rounded-xl border
                                   border-slate-300 px-4 py-3 text-sm"
                        >{{ old('message') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex min-h-11 w-full items-center
                               justify-center rounded-xl bg-blue-600 px-5
                               text-sm font-bold text-white hover:bg-blue-700"
                    >
                        Send Purchase Request
                    </button>
                </form>
            </section>
        @endif
    </div>
</div>
@endsection