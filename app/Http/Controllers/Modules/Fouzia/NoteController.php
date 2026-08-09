<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Services\CloudinaryService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NoteController extends Controller
{
    public function __construct(private readonly CloudinaryService $cloudinary) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $department = $request->string('department')->trim()->toString();
        $course = $request->string('course')->trim()->toString();
        $semester = $request->string('semester')->trim()->toString();
        $sort = $request->string('sort', 'latest')->toString();

        $notes = Note::query()
            ->with('user:id,name')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('course', 'like', '%'.$search.'%');
                });
            })
            ->when($department, fn ($query, $value) => $query->where('department', $value))
            ->when($course, fn ($query, $value) => $query->where('course', $value))
            ->when($semester, fn ($query, $value) => $query->where('semester', $value));

        match ($sort) {
            'oldest' => $notes->oldest(),
            'title' => $notes->orderBy('title'),
            'downloads' => $notes->orderByDesc('downloads_count')->latest(),
            default => $notes->latest(),
        };

        return view('notes.index', [
            'notes' => $notes->paginate(8)->withQueryString(),
            'departments' => Note::query()->distinct()->orderBy('department')->pluck('department'),
            'courses' => Note::query()->distinct()->orderBy('course')->pluck('course'),
            'semesters' => Note::query()->distinct()->orderBy('semester')->pluck('semester'),
        ]);
    }

    public function create(): View
    {
        return view('notes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(requireFile: true));

        try {
            $uploaded = $this->cloudinary->upload($request->file('file'));
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors(['file' => $exception->getMessage()]);
        }

        try {
            $note = DB::transaction(fn () => Note::create([
                ...$this->metadata($validated),
                ...$this->fileMetadata($request, $uploaded),
                'user_id' => auth()->id(),
            ]));
        } catch (Throwable $exception) {
            $this->removeUploadedFile($uploaded);
            throw $exception;
        }

        return redirect()->route('notes.index')
            ->with('success', '“'.$note->title.'” was uploaded successfully.');
    }

    public function edit(Note $note): View
    {
        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note): RedirectResponse
    {
        $validated = $request->validate($this->rules(requireFile: false));
        $uploaded = null;

        if ($request->hasFile('file')) {
            try {
                $uploaded = $this->cloudinary->upload($request->file('file'));
            } catch (Throwable $exception) {
                report($exception);

                return back()->withInput()->withErrors(['file' => $exception->getMessage()]);
            }
        }

        $oldFile = [
            'public_id' => $note->public_id,
            'resource_type' => $note->resource_type,
        ];

        try {
            DB::transaction(function () use ($note, $validated, $request, $uploaded) {
                $attributes = $this->metadata($validated);

                if ($uploaded) {
                    $attributes = [...$attributes, ...$this->fileMetadata($request, $uploaded)];
                }

                $note->update($attributes);
            });
        } catch (Throwable $exception) {
            if ($uploaded) {
                $this->removeUploadedFile($uploaded);
            }

            throw $exception;
        }

        if ($uploaded) {
            try {
                $this->cloudinary->destroy($oldFile['public_id'], $oldFile['resource_type']);
            } catch (Throwable $exception) {
                Log::warning('An old Cloudinary note file could not be removed.', [
                    'note_id' => $note->id,
                    'public_id' => $oldFile['public_id'],
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('notes.index')
            ->with('success', '“'.$note->title.'” was updated successfully.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        try {
            $this->cloudinary->destroy($note->public_id, $note->resource_type);
            $note->delete();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'delete' => 'The note could not be deleted safely. '.$exception->getMessage(),
            ]);
        }

        return redirect()->route('notes.index')->with('success', 'The note was deleted.');
    }

    public function preview(Note $note): RedirectResponse
    {
        return redirect()->away($note->secure_url);
    }

    public function download(Note $note): Response|RedirectResponse
    {
        try {
            $response = Http::timeout(90)->get($note->secure_url);
        } catch (ConnectionException) {
            return back()->withErrors(['download' => 'The file service is temporarily unavailable.']);
        }

        if (! $response->successful()) {
            return back()->withErrors(['download' => 'The file could not be downloaded right now.']);
        }

        $note->increment('downloads_count');
        $fallbackName = preg_replace('/[^A-Za-z0-9._-]/', '-', Str::ascii($note->original_name))
            ?: 'note-download'.($note->format ? '.'.$note->format : '');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $note->original_name,
            $fallbackName
        );

        return response($response->body(), 200, [
            'Content-Type' => $response->header('Content-Type') ?: ($note->mime_type ?: 'application/octet-stream'),
            'Content-Disposition' => $disposition,
            'Content-Length' => (string) strlen($response->body()),
        ]);
    }

    private function rules(bool $requireFile): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1500'],
            'department' => ['required', 'string', 'max:100'],
            'course' => ['required', 'string', 'max:120'],
            'semester' => ['required', 'string', 'max:50'],
            'file' => [
                $requireFile ? 'required' : 'nullable',
                File::types(['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp'])
                    ->max('10mb'),
            ],
        ];
    }

    private function metadata(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'department' => $validated['department'],
            'course' => $validated['course'],
            'semester' => $validated['semester'],
        ];
    }

    private function fileMetadata(Request $request, array $uploaded): array
    {
        $file = $request->file('file');

        return [
            'original_name' => $file->getClientOriginalName(),
            'public_id' => $uploaded['public_id'],
            'secure_url' => $uploaded['secure_url'],
            'resource_type' => $uploaded['resource_type'],
            'format' => $uploaded['format'] ?? strtolower($file->getClientOriginalExtension()),
            'mime_type' => $file->getMimeType(),
            'bytes' => (int) $uploaded['bytes'],
        ];
    }

    private function removeUploadedFile(array $uploaded): void
    {
        try {
            $this->cloudinary->destroy($uploaded['public_id'], $uploaded['resource_type']);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
