@extends('layouts.app')

@section('title', 'Marketplace Activity')

@section('content')
<div class="mb-8 flex flex-col gap-5 md:flex-row
            md:items-end md:justify-between">

    <div>
        <p class="mb-1 text-sm font-semibold text-blue-700">
            Buying and Selling
        </p>

        <h1 class="text-3xl font-bold tracking-tight text-slate-950">
            My Marketplace Activity
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Manage your listings and purchase requests.
        </p>
    </div>

    <div class="flex flex-wrap gap-3">
        <a
            href="{{ route('marketplace.index') }}"
            class="inline-flex min-h-11 items-center justify-center
                   rounded-xl border border-slate-300 bg-white px-5
                   text-sm font-bold text-slate-700 hover:bg-slate-50"
        >
            Browse Books
        </a>

        <a
            href="{{ route('marketplace.create') }}"
            class="inline-flex min-h-11 items-center justify-center
                   rounded-xl bg-blue-600 px-5 text-sm font-bold
                   text-white hover:bg-blue-700"
        >
            Sell a Book
        </a>
    </div>
</div>

{{-- Selling section --}}
<section>
    <div class="mb-4 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-950">
                Selling
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Your listings and requests from buyers.
            </p>
        </div>

        <span class="rounded-full bg-blue-100 px-3 py-1
                     text-xs font-bold text-blue-800">
            {{ $sellingBooks->count() }}
            {{ Str::plural('listing', $sellingBooks->count()) }}
        </span>
    </div>

    @forelse ($sellingBooks as $book)

        @php
            $bookStatusColor = match ($book->status) {
                'active' => 'bg-emerald-100 text-emerald-800',
                'reserved' => 'bg-amber-100 text-amber-800',
                default => 'bg-slate-200 text-slate-700',
            };
        @endphp

        <article class="mb-5 overflow-hidden rounded-2xl
                        border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center">

                <img
                    src="{{ $book->image_url }}"
                    alt="{{ $book->title }}"
                    class="h-32 w-full rounded-xl bg-slate-100
                           object-contain p-2 sm:w-24"
                >

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1
                                     text-xs font-bold
                                     {{ $bookStatusColor }}">
                            {{ $book->status_label }}
                        </span>

                        <span class="text-xs font-bold uppercase
                                     tracking-wide text-blue-700">
                            {{ $book->course }}
                        </span>
                    </div>

                    <h3 class="mt-2 text-lg font-bold text-slate-950">
                        {{ $book->title }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        ৳{{ number_format((float) $book->price, 0) }}
                        · {{ $book->condition_label }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ route('marketplace.show', $book) }}"
                        class="rounded-lg border border-slate-300
                               px-4 py-2 text-sm font-bold
                               text-slate-700 hover:bg-slate-50"
                    >
                        View
                    </a>

                    <a
                        href="{{ route('marketplace.edit', $book) }}"
                        class="rounded-lg border border-blue-200
                               px-4 py-2 text-sm font-bold
                               text-blue-700 hover:bg-blue-50"
                    >
                        Edit
                    </a>

                    @if ($book->status === 'sold')
                        <form
                            method="POST"
                            action="{{ route('marketplace.relist', $book) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="rounded-lg bg-slate-900
                                       px-4 py-2 text-sm font-bold
                                       text-white hover:bg-slate-700"
                            >
                                Relist
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Incoming buyer requests --}}
            @if ($book->orders->isNotEmpty())
                <div class="border-t border-slate-200 bg-slate-50 p-5">

                    <h4 class="text-sm font-bold uppercase
                               tracking-wide text-slate-600">
                        Purchase Requests
                    </h4>

                    <div class="mt-3 space-y-3">
                        @foreach ($book->orders as $order)

                            @php
                                $orderBoxColor = match ($order->status) {
                                    'pending' => 'border-amber-200 bg-amber-50',
                                    'accepted' => 'border-emerald-200 bg-emerald-50',
                                    default => 'border-slate-200 bg-white',
                                };
                            @endphp

                            <div class="rounded-xl border p-4
                                        {{ $orderBoxColor }}">

                                <div class="flex flex-col gap-4
                                            md:flex-row md:items-start
                                            md:justify-between">

                                    <div class="text-sm leading-6 text-slate-700">
                                        <p>
                                            <span class="font-bold text-slate-950">
                                                {{ $order->buyer_name }}
                                            </span>

                                            · {{ $order->status_label }}
                                        </p>

                                        <p>
                                            Email:

                                            <a
                                                href="mailto:{{ $order->buyer_email }}"
                                                class="font-semibold text-blue-700
                                                       hover:underline"
                                            >
                                                {{ $order->buyer_email }}
                                            </a>
                                        </p>

                                        @if ($order->buyer_phone)
                                            <p>
                                                Phone:

                                                <a
                                                    href="tel:{{ $order->buyer_phone }}"
                                                    class="font-semibold
                                                           text-blue-700"
                                                >
                                                    {{ $order->buyer_phone }}
                                                </a>
                                            </p>
                                        @endif

                                        @if ($order->message)
                                            <p class="mt-2 text-slate-600">
                                                “{{ $order->message }}”
                                            </p>
                                        @endif

                                        <p class="mt-1 text-xs text-slate-500">
                                            Requested
                                            {{ $order->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    @if ($order->status === 'pending')
                                        <div class="flex shrink-0 gap-2">

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'marketplace.orders.reject',
                                                    $order
                                                ) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="rounded-lg border
                                                           border-red-200 bg-white
                                                           px-4 py-2 text-sm
                                                           font-bold text-red-700
                                                           hover:bg-red-50"
                                                >
                                                    Decline
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'marketplace.orders.accept',
                                                    $order
                                                ) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="rounded-lg
                                                           bg-emerald-600
                                                           px-4 py-2 text-sm
                                                           font-bold text-white
                                                           hover:bg-emerald-700"
                                                >
                                                    Accept
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>

    @empty

        <div class="rounded-2xl border border-dashed
                    border-slate-300 bg-white px-6 py-12 text-center">

            <h3 class="text-lg font-bold text-slate-900">
                You have no book listings
            </h3>

            <p class="mt-2 text-sm text-slate-500">
                Add a used book to start selling.
            </p>

            <a
                href="{{ route('marketplace.create') }}"
                class="mt-5 inline-flex rounded-lg bg-blue-600
                       px-5 py-2 text-sm font-bold text-white"
            >
                Sell a Book
            </a>
        </div>

    @endforelse
</section>

{{-- Buying section --}}
<section class="mt-12">
    <div class="mb-4 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-950">
                Buying
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Purchase requests you sent to sellers.
            </p>
        </div>

        <span class="rounded-full bg-violet-100 px-3 py-1
                     text-xs font-bold text-violet-800">
            {{ $buyingOrders->count() }}
            {{ Str::plural('request', $buyingOrders->count()) }}
        </span>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">

        @forelse ($buyingOrders as $order)

            @php
                $orderStatusColor = match ($order->status) {
                    'pending' => 'bg-amber-100 text-amber-800',
                    'accepted' => 'bg-emerald-100 text-emerald-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-slate-200 text-slate-700',
                };
            @endphp

            <article class="rounded-2xl border border-slate-200
                            bg-white p-5 shadow-sm">

                <div class="flex gap-4">
                    <img
                        src="{{ $order->book->image_url }}"
                        alt="{{ $order->book->title }}"
                        class="h-28 w-20 shrink-0 rounded-lg
                               bg-slate-100 object-contain p-1"
                    >

                    <div class="min-w-0 flex-1">
                        <span class="inline-flex rounded-full px-3 py-1
                                     text-xs font-bold
                                     {{ $orderStatusColor }}">
                            {{ $order->status_label }}
                        </span>

                        <h3 class="mt-2 text-lg font-bold
                                   leading-snug text-slate-950">
                            {{ $order->book->title }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            ৳{{ number_format(
                                (float) $order->book->price,
                                0
                            ) }}

                            · Seller:
                            {{ $order->book->seller_name }}
                        </p>
                    </div>
                </div>

                {{-- Seller contact appears after acceptance --}}
                @if ($order->status === 'accepted')
                    <div class="mt-4 rounded-xl border
                                border-emerald-200 bg-emerald-50
                                p-4 text-sm leading-6 text-emerald-950">

                        <p class="font-bold">
                            Contact the seller:
                        </p>

                        <p>
                            <a
                                href="mailto:{{ $order->book->seller_email }}"
                                class="font-semibold underline"
                            >
                                {{ $order->book->seller_email }}
                            </a>
                        </p>

                        @if ($order->book->seller_phone)
                            <p>
                                <a
                                    href="tel:{{ $order->book->seller_phone }}"
                                    class="font-semibold underline"
                                >
                                    {{ $order->book->seller_phone }}
                                </a>
                            </p>
                        @endif
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between
                            gap-3 border-t border-slate-100 pt-4">

                    <a
                        href="{{ route(
                            'marketplace.show',
                            $order->book
                        ) }}"
                        class="text-sm font-bold text-blue-700
                               hover:underline"
                    >
                        View Listing
                    </a>

                    @if ($order->status === 'pending')
                        <form
                            method="POST"
                            action="{{ route(
                                'marketplace.orders.cancel',
                                $order
                            ) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="rounded-lg border border-red-200
                                       px-3 py-2 text-sm font-bold
                                       text-red-700 hover:bg-red-50"
                            >
                                Cancel Request
                            </button>
                        </form>
                    @endif
                </div>
            </article>

        @empty

            <div class="rounded-2xl border border-dashed
                        border-slate-300 bg-white px-6 py-12
                        text-center lg:col-span-2">

                <h3 class="text-lg font-bold text-slate-900">
                    You have not requested any books
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    Browse the marketplace and request a book.
                </p>

                <a
                    href="{{ route('marketplace.index') }}"
                    class="mt-5 inline-flex rounded-lg bg-blue-600
                           px-5 py-2 text-sm font-bold text-white"
                >
                    Browse Books
                </a>
            </div>

        @endforelse
    </div>
</section>
@endsection