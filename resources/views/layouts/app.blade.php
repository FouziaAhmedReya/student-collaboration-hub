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

@php
    /*
     * First use the normal Laravel authenticated user.
     * JWT is kept as a fallback for the existing Tuli module.
     */
    $currentUser = auth()->user()
        ?: \App\Services\Tuli\JwtService::getUserFromRequest(request());

    $currentRole = $currentUser
        ? strtolower((string) $currentUser->role)
        : null;

    /*
     * Set a safe homepage for each role.
     *
     * Student -> Notes
     * Tutor   -> Tutor Finder
     * Admin   -> Admin Dashboard
     */
    $homeUrl = match ($currentRole) {
        'student' => route('notes.index'),
        'tutor' => route('tutors.index'),
        'admin' => route('admin.dashboard'),
        default => url('/'),
    };
@endphp

<header class="border-b border-slate-200 bg-white">
    <div
        class="mx-auto flex min-h-16 max-w-7xl flex-col
               justify-between gap-3 px-4 py-3 sm:px-6
               lg:flex-row lg:items-center lg:px-8 lg:py-0"
    >
        {{-- Website logo --}}
        <a
            href="{{ $homeUrl }}"
            class="flex items-center gap-3"
            aria-label="Student Collaboration Hub"
        >
            <span
                class="grid size-10 place-items-center rounded-xl
                       bg-blue-600 text-white shadow-sm shadow-blue-200"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="size-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m2.5 9 9.5-5 9.5 5-9.5 5-9.5-5Z" />

                    <path
                        d="M6 11.2V16c2.5 2.2 9.5 2.2 12 0v-4.8M21.5 9v6"
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

        {{-- Main navigation --}}
        <nav
            aria-label="Main navigation"
            class="flex flex-wrap items-center gap-x-1 gap-y-1
                   py-2 text-xs font-medium sm:text-sm lg:h-16 lg:py-0"
        >
            {{-- Admin-only links --}}
            @if ($currentRole === 'admin')
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('admin.dashboard')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Admin Dashboard
                </a>

                <a
                    href="{{ route('admin.reports') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('admin.reports*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Reports
                </a>

                <a
                    href="{{ route('admin.content') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('admin.content*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Content Moderation
                </a>
            @endif

            {{-- Student-only Fouzia features --}}
            @if ($currentRole === 'student')
                <a
                    href="{{ route('notes.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('notes.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Notes
                </a>

                <a
                    href="{{ route('marketplace.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('marketplace.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Marketplace
                </a>
            @endif

            {{-- Student and tutor Fouzia features --}}
            @if (
                $currentUser &&
                in_array($currentRole, ['student', 'tutor'], true)
            )
                <a
                    href="{{ route('tutors.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('tutors.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Tutor Finder
                </a>

                <a
                    href="{{ route('resource-requests.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('resource-requests.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Resource Requests
                </a>
            @endif

            {{-- Other collaboration features --}}
            @if (
                $currentUser &&
                in_array($currentRole, ['student', 'tutor'], true)
            )
                <a
                    href="{{ route('project-ideas.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('project-ideas.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Project Ideas
                </a>

                <a
                    href="{{ route('team-recommendations.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('team-recommendations.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Team Matcher
                </a>

                <a
                    href="{{ route('progress-dashboard.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('progress-dashboard.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('events.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('events.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Events
                </a>

                <a
                    href="{{ route('tasks.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('tasks.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Tasks
                </a>

                <a
                    href="{{ route('group-chat.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('group-chat.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Group Chat
                </a>

                <a
                    href="{{ route('meetings.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('meetings.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Meetings
                </a>

                <a
                    href="{{ route('files.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('files.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Files
                </a>

                <a
                    href="{{ route('groups.index') }}"
                    class="rounded-lg border-b-2 px-2.5 py-1.5 transition-colors
                    {{ request()->routeIs('groups.*')
                        ? 'border-blue-600 bg-blue-50/50 font-bold text-blue-700'
                        : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    Study Groups
                </a>

                <a
                    href="{{ route('report.create', $currentUser->id) }}"
                    class="rounded-lg border-b-2 border-transparent
                           px-2.5 py-1.5 text-red-600 transition-colors
                           hover:bg-red-50 hover:text-red-700"
                >
                    Report
                </a>
            @endif
        </nav>

        {{-- Authentication section --}}
        <div
            class="flex shrink-0 items-center gap-2
                   border-l border-slate-200 pl-3"
        >
            @if ($currentUser)
                <div class="flex items-center gap-2">

                    {{-- Notification bell --}}
                    <div
                        class="relative"
                        id="notification-bell-wrapper"
                    >
                        <button
                            type="button"
                            id="notification-bell-btn"
                            class="relative inline-flex size-8 items-center
                                   justify-center rounded-xl text-slate-600
                                   hover:bg-slate-100"
                            aria-label="Open notifications"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="size-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />

                                <path
                                    d="M13.73 21a2 2 0 0 1-3.46 0"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span
                                id="notification-badge"
                                class="absolute -right-0.5 -top-0.5 hidden
                                       min-w-[16px] rounded-full bg-red-500
                                       px-1 text-center text-[10px] font-bold
                                       leading-4 text-white"
                            ></span>
                        </button>

                        <div
                            id="notification-dropdown"
                            class="absolute right-0 z-50 mt-2 hidden w-80
                                   rounded-xl border border-slate-200
                                   bg-white p-2 shadow-lg"
                        >
                            <div class="flex items-center justify-between px-2 py-1">
                                <p class="text-xs font-semibold text-slate-900">
                                    Notifications
                                </p>

                                <a
                                    href="{{ route('notifications.index') }}"
                                    class="text-[11px] font-medium text-blue-600 hover:underline"
                                >
                                    View all
                                </a>
                            </div>

                            <ul
                                id="notification-items"
                                class="mt-1 max-h-80 space-y-1 overflow-y-auto"
                            >
                                <li class="px-2 py-4 text-center text-xs text-slate-400">
                                    Loading...
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- User profile link --}}
                    <a
                        href="{{ route('profile.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-xl
                               bg-slate-100 px-3 py-1.5 text-xs font-bold
                               text-slate-800 hover:bg-slate-200"
                    >
                        <span class="size-2 rounded-full bg-emerald-500"></span>

                        {{ $currentUser->name }}

                        <span
                            class="rounded-full bg-white px-1.5 py-0.5
                                   text-[10px] uppercase text-slate-500"
                        >
                            {{ $currentRole }}
                        </span>
                    </a>

                    {{-- Logout --}}
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="inline"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="rounded-xl border border-slate-200
                                   px-3 py-1.5 text-xs font-semibold
                                   text-slate-600 hover:bg-slate-100"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <a
                    href="{{ route('login') }}"
                    class="rounded-xl border border-slate-200
                           px-3.5 py-1.5 text-xs font-semibold
                           text-slate-700 hover:bg-slate-50"
                >
                    Log In
                </a>

                <a
                    href="{{ route('register') }}"
                    class="rounded-xl bg-blue-600 px-3.5 py-1.5
                           text-xs font-bold text-white hover:bg-blue-700"
                >
                    Register
                </a>
            @endif
        </div>
    </div>
</header>

{{-- Main content --}}
<main
    class="mx-auto w-full max-w-7xl px-4 py-8
           sm:px-6 lg:px-8 lg:py-10"
>
    @if (session('success'))
        <div
            class="mb-6 rounded-xl border border-emerald-200
                   bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
        >
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div
            class="mb-6 rounded-xl border border-red-200
                   bg-red-50 px-4 py-3 text-sm text-red-800"
        >
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

@if ($currentUser)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bellButton = document.getElementById(
                'notification-bell-btn'
            );

            const dropdown = document.getElementById(
                'notification-dropdown'
            );

            const badge = document.getElementById(
                'notification-badge'
            );

            const itemsList = document.getElementById(
                'notification-items'
            );

            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            )?.content;

            let loaded = false;

            function escapeHtml(value) {
                const element = document.createElement('div');

                element.textContent = value ?? '';

                return element.innerHTML;
            }

            function renderNotifications(payload) {
                const unreadCount =
                    Number(payload.unread_count ?? 0);

                const notifications =
                    Array.isArray(payload.notifications)
                        ? payload.notifications
                        : [];

                if (unreadCount > 0) {
                    badge.textContent =
                        unreadCount > 9
                            ? '9+'
                            : unreadCount;

                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (notifications.length === 0) {
                    itemsList.innerHTML = `
                        <li class="px-2 py-4 text-center text-xs text-slate-400">
                            No notifications yet.
                        </li>
                    `;

                    return;
                }

                itemsList.innerHTML = notifications
                    .map((notification) => {
                        const isUnread =
                            ! notification.read_at;

                        const title = escapeHtml(
                            notification.data?.title
                            ?? 'Notification'
                        );

                        const body = escapeHtml(
                            notification.data?.body
                            ?? ''
                        );

                        const url = escapeHtml(
                            notification.data?.url
                            ?? '#'
                        );

                        const notificationId =
                            escapeHtml(notification.id);

                        return `
                            <li>
                                <a
                                    href="${url}"
                                    data-notification-id="${notificationId}"
                                    class="notification-item block rounded-lg
                                           px-2 py-2 text-xs hover:bg-slate-50
                                           ${isUnread ? 'bg-blue-50' : ''}"
                                >
                                    <p class="font-medium text-slate-900">
                                        ${title}
                                    </p>

                                    <p class="mt-0.5 text-slate-500">
                                        ${body}
                                    </p>
                                </a>
                            </li>
                        `;
                    })
                    .join('');

                itemsList
                    .querySelectorAll('.notification-item')
                    .forEach((link) => {
                        link.addEventListener(
                            'click',
                            async () => {
                                const notificationId =
                                    link.dataset.notificationId;

                                try {
                                    await fetch(
                                        `/api/notifications/${notificationId}/read`,
                                        {
                                            method: 'POST',

                                            headers: {
                                                'X-CSRF-TOKEN':
                                                    csrfToken,

                                                'Accept':
                                                    'application/json',
                                            },
                                        }
                                    );
                                } catch (error) {
                                    console.error(
                                        'Failed to mark notification as read',
                                        error
                                    );
                                }
                            }
                        );
                    });
            }

            async function loadNotifications() {
                try {
                    const response = await fetch(
                        '/api/notifications',
                        {
                            headers: {
                                'Accept': 'application/json',
                            },
                        }
                    );

                    if (! response.ok) {
                        return;
                    }

                    const payload = await response.json();

                    renderNotifications(payload);
                } catch (error) {
                    console.error(
                        'Failed to load notifications',
                        error
                    );
                }
            }

            bellButton?.addEventListener('click', () => {
                dropdown?.classList.toggle('hidden');

                if (
                    dropdown &&
                    ! dropdown.classList.contains('hidden') &&
                    ! loaded
                ) {
                    loaded = true;

                    loadNotifications();
                }
            });

            document.addEventListener('click', (event) => {
                const wrapper = document.getElementById(
                    'notification-bell-wrapper'
                );

                if (
                    wrapper &&
                    ! wrapper.contains(event.target)
                ) {
                    dropdown?.classList.add('hidden');
                }
            });

            /*
             * Load once immediately and refresh every minute.
             */
            loadNotifications();

            setInterval(loadNotifications, 60000);
        });
    </script>
@endif

</body>

</html>