@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="mb-8 flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-950">Notifications</h1>
        <p class="text-sm text-slate-500">Task and meeting reminders synced from your Google Calendar deadlines.</p>
    </div>

    <div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <ul class="space-y-3">
            @forelse ($notifications as $notification)
                <li class="flex items-start justify-between gap-3 rounded-lg border border-slate-200 p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    <a href="{{ $notification->data['url'] ?? '#' }}" class="flex-1">
                        <p class="text-sm font-medium text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $notification->data['body'] ?? '' }}</p>
                        <p class="mt-1 text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                    @unless ($notification->read_at)
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                    @endunless
                </li>
            @empty
                <li class="py-10 text-center text-sm text-slate-400">No notifications yet.</li>
            @endforelse
        </ul>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
