@extends('layouts.app')

@section('title', 'Content Moderation')

@section('content')
<div class="space-y-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Content Moderation
        </h1>

        <p class="mt-1 text-sm text-slate-600">
            Review and remove inappropriate notes, marketplace books,
            tutor materials and requested resources.
        </p>
    </div>

    @php
        $sections = [
            [
                'title' => 'Notes',
                'items' => $notes,
                'route' => 'admin.content.notes.destroy',
                'name' => fn ($item) => $item->title,
                'owner' => fn ($item) =>
                    $item->user?->name ?? 'Unknown student',
                'detail' => fn ($item) =>
                    $item->course . ' - ' . $item->semester,
                'url' => fn ($item) => $item->secure_url,
            ],

            [
                'title' => 'Marketplace Books',
                'items' => $books,
                'route' => 'admin.content.books.destroy',
                'name' => fn ($item) => $item->title,
                'owner' => fn ($item) =>
                    $item->seller?->name ?? $item->seller_name,
                'detail' => fn ($item) =>
                    $item->course . ' - ' . $item->status_label,
                'url' => fn ($item) => $item->image_url,
            ],

            [
                'title' => 'Tutor Teaching Materials',
                'items' => $teachingMaterials,
                'route' => 'admin.content.tutor-materials.destroy',
                'name' => fn ($item) => $item->title,
                'owner' => fn ($item) =>
                    $item->tutor?->user?->name
                    ?? $item->tutor?->name
                    ?? 'Unknown tutor',
                'detail' => fn ($item) =>
                    $item->file_name,
                'url' => fn ($item) =>
                    $item->file_url,
            ],

            [
                'title' => 'Requested Resource Uploads',
                'items' => $resourceUploads,
                'route' => 'admin.content.resource-uploads.destroy',
                'name' => fn ($item) => $item->title,
                'owner' => fn ($item) =>
                    $item->uploader?->name
                    ?? $item->uploader_name,
                'detail' => fn ($item) =>
                    ($item->resourceRequest?->course_code ?? 'Course')
                    . ' - ' . $item->file_name,
                'url' => fn ($item) =>
                    $item->file_url,
            ],
        ];
    @endphp

    @foreach ($sections as $section)
        <section
            class="rounded-2xl border border-slate-200
                   bg-white p-5 shadow-sm"
        >
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-slate-900">
                    {{ $section['title'] }}
                </h2>

                <span
                    class="rounded-full bg-slate-100
                           px-3 py-1 text-xs font-bold text-slate-700"
                >
                    {{ $section['items']->count() }} item(s)
                </span>
            </div>

            @if ($section['items']->isEmpty())
                <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">
                    No content is available in this section.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr
                                class="text-left text-xs font-bold
                                       uppercase tracking-wide text-slate-500"
                            >
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Details</th>
                                <th class="px-4 py-3">Created</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($section['items'] as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $section['name']($item) }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $section['owner']($item) }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $section['detail']($item) }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $item->created_at?->diffForHumans() }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <a
                                                href="{{ $section['url']($item) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="rounded-lg border
                                                       border-slate-300 px-3 py-2
                                                       text-xs font-bold text-slate-700
                                                       hover:bg-slate-50"
                                            >
                                                View
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    $section['route'],
                                                    $item
                                                ) }}"
                                                onsubmit="return confirm(
                                                    'Remove this content permanently?'
                                                )"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-red-600
                                                           px-3 py-2 text-xs
                                                           font-bold text-white
                                                           hover:bg-red-700"
                                                >
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endforeach
</div>
@endsection