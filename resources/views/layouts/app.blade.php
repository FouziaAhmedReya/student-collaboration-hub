<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Notes') · Student Collaboration Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('notes.index') }}" class="flex items-center gap-3" aria-label="Student Collaboration Hub notes">
                <span class="grid size-10 place-items-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-200">
                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="m2.5 9 9.5-5 9.5 5-9.5 5-9.5-5Z" stroke-linejoin="round"/>
                        <path d="M6 11.2V16c2.5 2.2 9.5 2.2 12 0v-4.8M21.5 9v6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-bold text-blue-700">Student</span>
                    <span class="block text-xs font-medium text-slate-500">Collaboration Hub</span>
                </span>
            </a>

            <nav aria-label="Main navigation" class="flex h-full items-center">
                <a href="{{ route('notes.index') }}" class="flex h-full items-center border-b-2 border-blue-600 px-4 text-sm font-semibold text-blue-700">
                    Notes
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
                <svg viewBox="0 0 24 24" class="mt-0.5 size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->has('delete') || $errors->has('download'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                {{ $errors->first('delete') ?: $errors->first('download') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
