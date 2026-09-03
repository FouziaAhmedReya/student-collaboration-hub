@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Dashboard heading --}}
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Admin Dashboard
        </h1>

        <p class="mt-1 text-sm text-slate-600">
            Verify tutor registrations and monitor platform users.
        </p>
    </div>

    {{-- User statistics --}}
    <section>
        <h2 class="mb-4 text-xl font-bold text-slate-900">
            User Statistics
        </h2>

        <div class="grid gap-4 sm:grid-cols-3">

            {{-- Total users --}}
            <div
                class="rounded-2xl border border-slate-200
                       bg-white p-6 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Users
                        </p>

                        <p class="mt-2 text-3xl font-black text-slate-900">
                            {{ $totalUsers }}
                        </p>
                    </div>

                    <div
                        class="grid size-12 place-items-center
                               rounded-xl bg-slate-100 text-2xl"
                    >
                        👥
                    </div>
                </div>
            </div>

            {{-- Total students --}}
            <div
                class="rounded-2xl border border-blue-200
                       bg-white p-6 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Students
                        </p>

                        <p class="mt-2 text-3xl font-black text-blue-700">
                            {{ $totalStudents }}
                        </p>
                    </div>

                    <div
                        class="grid size-12 place-items-center
                               rounded-xl bg-blue-100 text-2xl"
                    >
                        🎓
                    </div>
                </div>
            </div>

            {{-- Total tutors --}}
            <div
                class="rounded-2xl border border-violet-200
                       bg-white p-6 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Tutors
                        </p>

                        <p class="mt-2 text-3xl font-black text-violet-700">
                            {{ $totalTutors }}
                        </p>
                    </div>

                    <div
                        class="grid size-12 place-items-center
                               rounded-xl bg-violet-100 text-2xl"
                    >
                        👨‍🏫
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pending tutor registrations --}}
    <section
        class="rounded-2xl border border-slate-200
               bg-white p-5 shadow-sm sm:p-6"
    >
        <div
            class="mb-5 flex flex-wrap
                   items-center justify-between gap-3"
        >
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Pending Tutor Requests
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Tutors cannot log in until an administrator approves their registration.
                </p>
            </div>

            <span
                class="rounded-full bg-amber-100 px-3 py-1
                       text-xs font-bold text-amber-800"
            >
                {{ $pendingTutors->count() }} pending
            </span>
        </div>

        @if ($pendingTutors->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr
                            class="text-left text-xs font-bold
                                   uppercase tracking-wide text-slate-500"
                        >
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pendingTutors as $tutor)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4 font-semibold text-slate-900">
                                    {{ $tutor->name }}
                                </td>

                                <td class="px-4 py-4 text-slate-600">
                                    {{ $tutor->email }}
                                </td>

                                <td class="px-4 py-4 text-slate-600">
                                    {{ $tutor->department ?: 'Not provided' }}
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="rounded-full bg-amber-100
                                               px-3 py-1 text-xs font-bold
                                               text-amber-800"
                                    >
                                        Pending
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.tutor.approve',
                                                $tutor->id
                                            ) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-emerald-600
                                                       px-3 py-2 text-xs font-bold
                                                       text-white hover:bg-emerald-700"
                                                onclick="return confirm(
                                                    'Approve this tutor registration?'
                                                )"
                                            >
                                                Approve
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.tutor.reject',
                                                $tutor->id
                                            ) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-red-600
                                                       px-3 py-2 text-xs font-bold
                                                       text-white hover:bg-red-700"
                                                onclick="return confirm(
                                                    'Reject this tutor registration?'
                                                )"
                                            >
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div
                class="rounded-xl border border-dashed
                       border-slate-300 bg-slate-50
                       p-8 text-center"
            >
                <div class="text-3xl">✓</div>

                <p class="mt-2 font-semibold text-slate-700">
                    No pending tutors
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    All tutor registration requests have been reviewed.
                </p>
            </div>
        @endif
    </section>
</div>
@endsection