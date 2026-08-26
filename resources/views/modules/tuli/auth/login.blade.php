@extends('layouts.app')

@section('title', 'Student Login')

@section('content')
<div class="mx-auto max-w-md space-y-6 py-8">
    <div class="text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Student Login</h1>
        <p class="mt-2 text-sm text-slate-600">Access your student collaboration dashboard.</p>
    </div>

    <!-- Session / Validation Errors -->
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">University Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="student@g.bracu.ac.bd" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700">Password <span class="text-red-500">*</span></label>
                <input type="password" id="password" name="password" required placeholder="••••••••" class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl px-5 py-3 text-sm font-bold shadow-md transition-all" style="background-color: #2563eb !important; color: #ffffff !important;">
                    Log In
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
            Don't have an account yet?
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">Register New Account</a>
        </div>
    </div>
</div>
@endsection
