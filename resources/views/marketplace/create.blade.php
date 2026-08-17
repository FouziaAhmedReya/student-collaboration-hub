@extends('layouts.app')

@section('title', 'Sell Your Book')

@section('content')
<div class="mb-7">
    <a
        href="{{ route('marketplace.index') }}"
        class="mb-4 inline-flex items-center gap-2
               text-sm font-bold text-blue-700 hover:text-blue-900"
    >
        ← Back to Marketplace
    </a>

    <h1 class="text-3xl font-bold tracking-tight text-slate-950">
        Sell Your Book
    </h1>

    <p class="mt-2 text-sm text-slate-500">
        List your used academic book for other students.
    </p>
</div>

<form
    method="POST"
    action="{{ route('marketplace.store') }}"
    enctype="multipart/form-data"
    class="rounded-2xl border border-slate-200
           bg-white p-5 shadow-sm sm:p-8"
>
    @csrf

    @include('marketplace._form')
</form>
@endsection