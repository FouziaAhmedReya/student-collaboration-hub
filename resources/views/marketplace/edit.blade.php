@extends('layouts.app')

@section('title', 'Edit Book Listing')

@section('content')
<div class="mb-7">
    <a
        href="{{ route('marketplace.show', $book) }}"
        class="mb-4 inline-flex items-center gap-2
               text-sm font-bold text-blue-700 hover:text-blue-900"
    >
        ← Back to Book Details
    </a>

    <h1 class="text-3xl font-bold tracking-tight text-slate-950">
        Edit Book Listing
    </h1>

    <p class="mt-2 text-sm text-slate-500">
        Update your book information or replace its image.
    </p>
</div>

<form
    method="POST"
    action="{{ route('marketplace.update', $book) }}"
    enctype="multipart/form-data"
    class="rounded-2xl border border-slate-200
           bg-white p-5 shadow-sm sm:p-8"
>
    @csrf
    @method('PUT')

    @include('marketplace._form')
</form>
@endsection