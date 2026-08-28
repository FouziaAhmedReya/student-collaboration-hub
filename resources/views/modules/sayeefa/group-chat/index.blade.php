@extends('layouts.app')

@section('title', 'Group Chat')

@section('content')
    <div class="mb-8 flex flex-col gap-1">
        <h1 class="text-2xl font-bold text-slate-950">Group Chat</h1>
        <p class="text-sm text-slate-500">Chat with your study group and keep track of upcoming meetings.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        {{-- Left: group list --}}
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Groups</h2>
                <ul class="mb-4 space-y-1">
                    @foreach ($groups as $group)
                        <li>
                            <a href="{{ route('group-chat.index', ['group_id' => $group->id]) }}"
                                class="block rounded-lg px-3 py-2 text-sm {{ $activeGroup?->id === $group->id ? 'bg-blue-50 font-medium text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                                {{ $group->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <form id="group-form" class="space-y-2 border-t border-slate-100 pt-4">
                    <input type="text" name="name" placeholder="New group name" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                        Create Group
                    </button>
                </form>
            </div>
        </div>

        @if ($activeGroup)
            {{-- Middle: chat thread --}}
            <div class="lg:col-span-2">
                <div class="flex h-[520px] flex-col rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <h2 class="mb-2 text-sm font-semibold text-slate-900">{{ $activeGroup->name }}</h2>
                        <div class="relative">
                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m21 21-4.3-4.3" stroke-linecap="round" />
                            </svg>
                            <input type="text" id="chat-search" placeholder="Search messages or sender..."
                                class="w-full rounded-lg border border-slate-200 py-2 pl-8 pr-8 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <button type="button" id="chat-search-clear"
                                class="absolute right-2.5 top-1/2 hidden -translate-y-1/2 text-xs font-medium text-slate-400 hover:text-slate-600">
                                Clear
                            </button>
                        </div>
                        <p id="chat-search-status" class="mt-1 hidden text-xs text-slate-400"></p>
                    </div>

                    <div id="message-list" class="flex-1 space-y-3 overflow-y-auto px-5 py-4">
                        @if ($isMemberOfActive)
                            @foreach ($activeGroup->messages as $message)
                                <div class="chat-message max-w-[80%] rounded-lg bg-slate-50 px-3 py-2"
                                    data-sender="{{ strtolower($message->sender_name) }}"
                                    data-text="{{ strtolower($message->message) }}">
                                    <p class="text-xs font-medium text-slate-500">{{ $message->sender_name }}</p>
                                    <p class="text-sm text-slate-800">{{ $message->message }}</p>
                                </div>
                            @endforeach
                            <p id="chat-search-empty" class="hidden py-6 text-center text-sm text-slate-400">No messages match your search.</p>
                        @else
                            <p class="py-10 text-center text-sm text-slate-400">Join this group to see its messages.</p>
                        @endif
                    </div>

                    @if ($isMemberOfActive)
                        <form id="message-form" data-group-id="{{ $activeGroup->id }}" class="flex gap-2 border-t border-slate-100 p-3">
                            <input type="text" name="message" placeholder="Type a message..." required
                                class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <button type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                                Send
                            </button>
                        </form>
                    @else
                        <div class="flex items-center justify-between border-t border-slate-100 p-3">
                            <p class="text-xs text-slate-500">Join this group to read and send messages.</p>
                            <button type="button" data-group-id="{{ $activeGroup->id }}"
                                class="group-join-btn rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                                Join Group
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: upcoming meetings --}}
            <div class="lg:col-span-1">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900">Upcoming Meetings</h2>
                    <ul id="meeting-list" class="mb-4 space-y-2">
                        @forelse ($activeGroup->meetings as $meeting)
                            <li class="rounded-lg border border-slate-200 p-3">
                                <p class="text-sm font-medium text-slate-900">{{ $meeting->title }}</p>
                                <p class="text-xs text-slate-500">{{ $meeting->meeting_time->format('M j, Y g:i A') }}</p>
                                @if ($meeting->google_calendar_event_id)
                                    <span class="mt-1 inline-flex items-center gap-1 text-xs text-emerald-600">
                                        <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Synced to Google Calendar
                                    </span>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-slate-400">No meetings scheduled.</li>
                        @endforelse
                    </ul>
                    <form id="meeting-form" data-group-id="{{ $activeGroup->id }}" class="space-y-2 border-t border-slate-100 pt-4">
                        <input type="text" name="title" placeholder="Meeting title" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <input type="datetime-local" name="meeting_time" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="submit"
                            class="w-full rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-200 hover:bg-blue-700">
                            Add Meeting
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="lg:col-span-3">
                <div class="flex h-[300px] items-center justify-center rounded-xl border border-dashed border-slate-200 text-sm text-slate-400">
                    Create a group on the left to start chatting.
                </div>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        };

        // Live search logic
        const chatSearchInput = document.getElementById('chat-search');
        const chatSearchClear = document.getElementById('chat-search-clear');
        const chatSearchStatus = document.getElementById('chat-search-status');
        const chatSearchEmpty = document.getElementById('chat-search-empty');
        const chatMessages = document.querySelectorAll('.chat-message');

        function filterMessages() {
            const query = chatSearchInput.value.trim().toLowerCase();
            chatSearchClear.classList.toggle('hidden', query === '');

            if (query === '') {
                chatMessages.forEach((el) => el.classList.remove('hidden'));
                chatSearchStatus.classList.add('hidden');
                chatSearchEmpty.classList.add('hidden');
                return;
            }

            let matchCount = 0;
            chatMessages.forEach((el) => {
                const matches = el.dataset.sender.includes(query) || el.dataset.text.includes(query);
                el.classList.toggle('hidden', !matches);
                if (matches) matchCount++;
            });

            chatSearchStatus.textContent = `${matchCount} message${matchCount === 1 ? '' : 's'} found`;
            chatSearchStatus.classList.remove('hidden');
            chatSearchEmpty.classList.toggle('hidden', matchCount !== 0);
        }

        chatSearchInput?.addEventListener('input', filterMessages);
        chatSearchClear?.addEventListener('click', () => {
            chatSearchInput.value = '';
            filterMessages();
            chatSearchInput.focus();
        });

        // Form submit handlers
        document.getElementById('group-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(event.target).entries());
            const response = await fetch('/api/chat-groups', { method: 'POST', headers, body: JSON.stringify(payload) });
            if (response.ok) {
                const group = await response.json();
                window.location.href = `{{ route('group-chat.index') }}?group_id=${group.id}`;
            } else {
                const errorBody = await response.text();
                alert('Could not create group (status ' + response.status + '):\n' + errorBody);
                console.error('Create group failed', response.status, errorBody);
            }
        });

        document.getElementById('message-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.target;
            const groupId = form.dataset.groupId;
            const payload = Object.fromEntries(new FormData(form).entries());
            const response = await fetch(`/api/chat-groups/${groupId}/messages`, { method: 'POST', headers, body: JSON.stringify(payload) });
            if (response.ok) {
                window.location.reload();
            } else {
                const errorBody = await response.text();
                alert('Could not send message (status ' + response.status + '):\n' + errorBody);
                console.error('Send message failed', response.status, errorBody);
            }
        });

        document.querySelectorAll('.group-join-btn').forEach((button) => {
            button.addEventListener('click', async (event) => {
                const groupId = event.target.dataset.groupId;
                const response = await fetch(`/api/chat-groups/${groupId}/join`, { method: 'POST', headers });
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Could not join group.');
                }
            });
        });

        document.getElementById('meeting-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.target;
            const groupId = form.dataset.groupId;
            const payload = Object.fromEntries(new FormData(form).entries());
            const response = await fetch(`/api/chat-groups/${groupId}/meetings`, { method: 'POST', headers, body: JSON.stringify(payload) });
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Could not add meeting.');
            }
        });
    });
    </script>
@endsection