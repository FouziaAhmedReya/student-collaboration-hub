<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Student Collaboration Hub')
        · Student Collaboration Hub
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">

    {{-- Main Header --}}
    <header class="border-b border-slate-200 bg-white">

        <div
            class="mx-auto flex min-h-16 max-w-7xl
                   flex-col justify-between gap-3 px-4
                   py-3 sm:px-6 lg:flex-row lg:items-center
                   lg:px-8 lg:py-0"
        >

            {{-- Logo --}}
            <a
                href="{{ route('notes.index') }}"
                class="flex items-center gap-3"
                aria-label="Student Collaboration Hub"
            >

                <span
                    class="grid size-10 place-items-center
                           rounded-xl bg-blue-600 text-white
                           shadow-sm shadow-blue-200"
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="size-6"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >

                        <path
                            d="m2.5 9 9.5-5 9.5 5-9.5 5-9.5-5Z"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M6 11.2V16c2.5 2.2 9.5 2.2 12 0v-4.8M21.5 9v6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                    </svg>

                </span>


                <span class="leading-tight">

                    <span class="block text-sm font-bold text-blue-700">
                        Student
                    </span>

                    <span class="block text-xs font-medium text-slate-500">
                        Collaboration Hub
                    </span>

                </span>

            </a>


            {{-- Navigation --}}
            <nav
                aria-label="Main navigation"
                class="flex items-center gap-1 overflow-x-auto
                       sm:gap-2 lg:h-16"
            >

                {{-- Notes --}}
                <a
                    href="{{ route('notes.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('notes.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Notes
                </a>


                {{-- Marketplace --}}
                <a
                    href="{{ route('marketplace.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('marketplace.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Marketplace
                </a>


                {{-- Tutor Finder --}}
                <a
                    href="{{ route('tutors.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('tutors.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Tutor Finder
                </a>


                {{-- Resource Requests --}}
                <a
                    href="{{ route('resource-requests.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('resource-requests.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Resource Requests
                </a>


                {{-- Project Ideas --}}
                <a
                    href="{{ route('project-ideas.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('project-ideas.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Project Ideas
                </a>


                {{-- Team Matcher --}}
                <a
                    href="{{ route('team-recommendations.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('team-recommendations.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Team Matcher
                </a>


                {{-- Progress Dashboard --}}
                <a
                    href="{{ route('progress-dashboard.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('progress-dashboard.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Progress Dashboard
                </a>


                {{-- Events & Workshops --}}
                <a
                    href="{{ route('events.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('events.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Events & Workshops
                </a>


                {{-- Tasks --}}
                <a
                    href="{{ route('tasks.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('tasks.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Tasks
                </a>


                {{-- Group Chat --}}
                <a
                    href="{{ route('group-chat.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('group-chat.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Group Chat
                </a>


                {{-- Meeting Scheduler --}}
                <a
                    href="{{ route('meetings.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('meetings.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Meetings
                </a>


                {{-- File Sharing --}}
                <a
                    href="{{ route('files.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('files.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Files
                </a>


                {{-- Profile & Skills --}}
                <a
                    href="{{ route('profile.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('profile.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Profile & Skills
                </a>


                {{-- Study Groups --}}
                <a
                    href="{{ route('groups.index') }}"
                    class="flex min-h-12 shrink-0 items-center
                           border-b-2 px-3 text-sm font-medium
                           lg:h-full

                           {{ request()->routeIs('groups.*')
                               ? 'border-blue-600 font-semibold text-blue-700'
                               : 'border-transparent text-slate-600 hover:text-slate-900' }}"
                >
                    Study Groups
                </a>

            </nav>

        </div>

    </header>


    {{-- Main Content --}}
    <main
        class="mx-auto w-full max-w-7xl
               px-4 py-8
               sm:px-6
               lg:px-8 lg:py-10"
    >

        {{-- Success Message --}}
        @if (session('success'))

            <div
                class="mb-6 flex items-start gap-3
                       rounded-xl border
                       border-emerald-200
                       bg-emerald-50
                       px-4 py-3
                       text-sm text-emerald-900"
                role="status"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="mt-0.5 size-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >

                    <path
                        d="m5 12 4 4L19 6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                </svg>


                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- Validation and Other Errors --}}
        @if ($errors->any())

            <div
                class="mb-6 rounded-xl
                       border border-red-200
                       bg-red-50
                       px-4 py-3
                       text-sm text-red-800"
                role="alert"
            >

                <p class="mb-2 font-bold">
                    Please fix the following:
                </p>


                <ul class="list-inside list-disc space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Page Content --}}
        @yield('content')

    </main>

</body>

</html>