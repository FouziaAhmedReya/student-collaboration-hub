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


<header class="border-b border-slate-200 bg-white">


<div
class="mx-auto flex min-h-16 max-w-7xl
flex-col justify-between gap-3 px-4
py-3 sm:px-6 lg:flex-row
lg:items-center lg:px-8 lg:py-0"
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
>


<path
d="m2.5 9 9.5-5 9.5 5-9.5 5-9.5-5Z"
/>


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




{{-- Current User --}}

@php

$currentUser =
\App\Services\Tuli\JwtService::getUserFromRequest(request())
?: auth()->user();

@endphp




<nav
aria-label="Main navigation"
class="flex flex-wrap items-center
gap-x-1 gap-y-1 py-2
text-xs sm:text-sm font-medium
lg:h-16 lg:py-0"
>
{{-- Admin Navigation --}}

@if($currentUser && $currentUser->role === 'admin')


<a
href="{{ route('admin.dashboard') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50"
>
    Admin Dashboard
</a>


<a
href="{{ route('admin.reports') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50"
>
    Reports
</a>


@endif



{{-- Notes --}}
<a
href="{{ route('notes.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('notes.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Notes
</a>



{{-- Marketplace --}}
<a
href="{{ route('marketplace.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('marketplace.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Marketplace
</a>



{{-- Tutor Finder --}}
<a
href="{{ route('tutors.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('tutors.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Tutors
</a>



{{-- Requests --}}
<a
href="{{ route('resource-requests.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('resource-requests.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Requests
</a>



{{-- Project Ideas --}}
<a
href="{{ route('project-ideas.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('project-ideas.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Project Ideas
</a>



{{-- Team Matcher --}}
<a
href="{{ route('team-recommendations.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('team-recommendations.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Team Matcher
</a>



{{-- Dashboard --}}
<a
href="{{ route('progress-dashboard.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('progress-dashboard.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Dashboard
</a>



{{-- Events --}}
<a
href="{{ route('events.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('events.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Events & Workshops
</a>



{{-- Tasks --}}
<a
href="{{ route('tasks.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('tasks.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Tasks
</a>
{{-- Group Chat --}}
<a
href="{{ route('group-chat.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('group-chat.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Group Chat
</a>



{{-- Meetings --}}
<a
href="{{ route('meetings.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('meetings.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Meetings
</a>



{{-- Files --}}
<a
href="{{ route('files.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('files.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Files
</a>



{{-- Study Groups --}}
<a
href="{{ route('groups.index') }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 transition-colors
{{ request()->routeIs('groups.*')
? 'border-blue-600 font-bold text-blue-700 bg-blue-50/50'
: 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
>
    Study Groups
</a>



{{-- Report User (Student/Tutor only) --}}

@if($currentUser && $currentUser->role !== 'admin')

<a
href="{{ route('report.create', $currentUser->id) }}"
class="rounded-lg px-2.5 py-1.5 border-b-2 border-transparent
text-red-600 hover:text-red-700 hover:bg-red-50"
>
    Report
</a>

@endif


</nav>




{{-- Auth Section --}}

<div class="flex items-center gap-2 shrink-0 border-l border-slate-200 pl-3">


@if($currentUser)

<div class="flex items-center gap-2">

{{-- Notification bell --}}
<div class="relative" id="notification-bell-wrapper">
    <button type="button" id="notification-bell-btn"
        class="relative inline-flex size-8 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100">
        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span id="notification-badge"
            class="absolute -right-0.5 -top-0.5 hidden min-w-[16px] rounded-full bg-red-500 px-1 text-center text-[10px] font-bold leading-4 text-white"></span>
    </button>
    <div id="notification-dropdown"
        class="absolute right-0 z-50 mt-2 hidden w-80 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
        <div class="flex items-center justify-between px-2 py-1">
            <p class="text-xs font-semibold text-slate-900">Notifications</p>
            <a href="{{ route('notifications.index') }}" class="text-[11px] font-medium text-blue-600 hover:underline">View all</a>
        </div>
        <ul id="notification-items" class="mt-1 max-h-80 space-y-1 overflow-y-auto">
            <li class="px-2 py-4 text-center text-xs text-slate-400">Loading...</li>
        </ul>
    </div>
</div>

<a
href="{{ route('profile.index') }}"
class="inline-flex items-center gap-1.5
rounded-xl bg-slate-100 px-3 py-1.5
text-xs font-bold text-slate-800
hover:bg-slate-200"
>

<span class="size-2 rounded-full bg-emerald-500"></span>

{{ $currentUser->name }}

</a>



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
class="rounded-xl border border-slate-200 px-3.5 py-1.5 text-xs font-semibold text-slate-700"
>
Log In
</a>



<a
href="{{ route('register') }}"
class="rounded-xl bg-blue-600 px-3.5 py-1.5 text-xs font-bold text-white"
>
Register
</a>



@endif


</div>



</div>


</header>




{{-- Main Content --}}

<main
class="mx-auto w-full max-w-7xl
px-4 py-8
sm:px-6
lg:px-8 lg:py-10"
>


@if(session('success'))

<div
class="mb-6 rounded-xl border border-emerald-200
bg-emerald-50 px-4 py-3 text-sm text-emerald-900"
>

{{ session('success') }}

</div>

@endif



@if($errors->any())

<div
class="mb-6 rounded-xl border border-red-200
bg-red-50 px-4 py-3 text-sm text-red-800"
>


<ul class="list-disc pl-5">

@foreach($errors->all() as $error)

<li>
{{ $error }}
</li>

@endforeach

</ul>


</div>

@endif



@yield('content')


</main>

@if($currentUser)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bellBtn = document.getElementById('notification-bell-btn');
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    const itemsList = document.getElementById('notification-items');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let loaded = false;

    function renderNotifications(payload) {
        if (payload.unread_count > 0) {
            badge.textContent = payload.unread_count > 9 ? '9+' : payload.unread_count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

        if (payload.notifications.length === 0) {
            itemsList.innerHTML = '<li class="px-2 py-4 text-center text-xs text-slate-400">No notifications yet.</li>';
            return;
        }

        itemsList.innerHTML = payload.notifications.map((n) => {
            const isUnread = !n.read_at;
            const title = n.data.title ?? 'Notification';
            const body = n.data.body ?? '';
            const url = n.data.url ?? '#';
            return `
                <li>
                    <a href="${url}" data-notification-id="${n.id}"
                       class="notification-item block rounded-lg px-2 py-2 text-xs hover:bg-slate-50 ${isUnread ? 'bg-blue-50' : ''}">
                        <p class="font-medium text-slate-900">${title}</p>
                        <p class="mt-0.5 text-slate-500">${body}</p>
                    </a>
                </li>
            `;
        }).join('');

        itemsList.querySelectorAll('.notification-item').forEach((link) => {
            link.addEventListener('click', async () => {
                const id = link.dataset.notificationId;
                await fetch(`/api/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
            });
        });
    }

    async function loadNotifications() {
        try {
            const response = await fetch('/api/notifications', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) return;
            renderNotifications(await response.json());
        } catch (error) {
            console.error('Failed to load notifications', error);
        }
    }

    bellBtn?.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden') && !loaded) {
            loaded = true;
            loadNotifications();
        }
    });

    document.addEventListener('click', (event) => {
        const wrapper = document.getElementById('notification-bell-wrapper');
        if (wrapper && !wrapper.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Poll for the unread badge count every 60 seconds so it stays fresh
    // even if the dropdown is never opened.
    loadNotifications();
    setInterval(loadNotifications, 60000);
});
</script>
@endif

</body>

</html>