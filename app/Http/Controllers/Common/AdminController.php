<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Note;
use App\Models\Report;
use App\Models\ResourceRequest;
use App\Models\ResourceUpload;
use App\Models\TutorMaterial;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class AdminController extends Controller
{
    /**
     * Inject the Cloudinary service.
     */
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {
    }

    /**
     * Display the administrator dashboard.
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();

        $totalStudents = User::where(
            'role',
            'student'
        )->count();

        $totalTutors = User::where(
            'role',
            'tutor'
        )->count();

        $pendingTutors = User::where(
            'role',
            'tutor'
        )
            ->where(
                'status',
                'pending'
            )
            ->latest()
            ->get();

        /*
         * Platform activity information.
         */
        $contentCounts = [
            'notes' =>
                Note::count(),

            'books' =>
                Book::count(),

            'teaching_materials' =>
                TutorMaterial::count(),

            'resource_requests' =>
                ResourceRequest::count(),

            'resource_uploads' =>
                ResourceUpload::count(),
        ];

        return view(
            'common.admin.dashboard',
            compact(
                'totalUsers',
                'totalStudents',
                'totalTutors',
                'pendingTutors',
                'contentCounts'
            )
        );
    }

    /**
     * Approve a pending tutor account.
     */
    public function approveTutor(
        int $id
    ): RedirectResponse {
        $user = User::where(
            'role',
            'tutor'
        )->findOrFail($id);

        $user->update([
            'status' => 'approved',
        ]);

        return back()->with(
            'success',
            'Tutor approved successfully.'
        );
    }

    /**
     * Reject a tutor account.
     */
    public function rejectTutor(
        int $id
    ): RedirectResponse {
        $user = User::where(
            'role',
            'tutor'
        )->findOrFail($id);

        $user->update([
            'status' => 'rejected',
        ]);

        return back()->with(
            'success',
            'Tutor rejected successfully.'
        );
    }

    /**
     * Display all submitted reports.
     */
    public function reports(): View
    {
        $reports = Report::with([
            'reporter',
            'reportedUser',
        ])
            ->latest()
            ->get();

        return view(
            'common.admin.reports',
            compact('reports')
        );
    }

    /**
     * Mark a report as resolved.
     */
    public function resolveReport(
        int $id
    ): RedirectResponse {
        $report = Report::findOrFail($id);

        $report->update([
            'status' => 'resolved',
        ]);

        return back()->with(
            'success',
            'Report marked as resolved.'
        );
    }

    /**
     * Reject a report.
     */
    public function rejectReport(
        int $id
    ): RedirectResponse {
        $report = Report::findOrFail($id);

        $report->update([
            'status' => 'rejected',
        ]);

        return back()->with(
            'success',
            'Report rejected.'
        );
    }

    /**
     * Display content that an administrator can moderate.
     */
    public function content(): View
    {
        $notes = Note::with([
            'user:id,name,email',
        ])
            ->latest()
            ->limit(25)
            ->get();

        $books = Book::with([
            'seller:id,name,email',
        ])
            ->latest()
            ->limit(25)
            ->get();

        $teachingMaterials = TutorMaterial::with([
            'tutor.user:id,name,email',
        ])
            ->latest()
            ->limit(25)
            ->get();

        $resourceUploads = ResourceUpload::with([
            'uploader:id,name,email',

            'resourceRequest:id,course_code,title',
        ])
            ->latest()
            ->limit(25)
            ->get();

        return view(
            'common.admin.content',
            compact(
                'notes',
                'books',
                'teachingMaterials',
                'resourceUploads'
            )
        );
    }

    /**
     * Remove an inappropriate note and its Cloudinary file.
     */
    public function destroyNote(
        Note $note
    ): RedirectResponse {
        $removed = $this->removeCloudinaryFile(
            $note->public_id,
            $note->resource_type
        );

        if (! $removed) {
            return back()->withErrors([
                'moderation' =>
                    'The note file could not be removed from Cloudinary.',
            ]);
        }

        $note->delete();

        return back()->with(
            'success',
            'The note was removed successfully.'
        );
    }

    /**
     * Remove an inappropriate marketplace listing
     * and its Cloudinary image.
     */
    public function destroyBook(
        Book $book
    ): RedirectResponse {
        $removed = $this->removeCloudinaryFile(
            $book->image_public_id,
            $book->image_resource_type
        );

        if (! $removed) {
            return back()->withErrors([
                'moderation' =>
                    'The book image could not be removed from Cloudinary.',
            ]);
        }

        $book->delete();

        return back()->with(
            'success',
            'The book listing was removed successfully.'
        );
    }

    /**
     * Remove an inappropriate teaching material
     * and its Cloudinary file.
     */
    public function destroyTutorMaterial(
        TutorMaterial $material
    ): RedirectResponse {
        $removed = $this->removeCloudinaryFile(
            $material->cloudinary_public_id,
            $material->resource_type
        );

        if (! $removed) {
            return back()->withErrors([
                'moderation' =>
                    'The teaching material could not be removed from Cloudinary.',
            ]);
        }

        $material->delete();

        return back()->with(
            'success',
            'The teaching material was removed successfully.'
        );
    }

    /**
     * Remove an inappropriate resource upload.
     *
     * If the request no longer has any uploads,
     * change its status back to open.
     */
    public function destroyResourceUpload(
        ResourceUpload $upload
    ): RedirectResponse {
        $removed = $this->removeCloudinaryFile(
            $upload->cloudinary_public_id,
            $upload->resource_type
        );

        if (! $removed) {
            return back()->withErrors([
                'moderation' =>
                    'The requested resource could not be removed from Cloudinary.',
            ]);
        }

        $resourceRequest =
            $upload->resourceRequest;

        $upload->delete();

        if (
            $resourceRequest
            && ! $resourceRequest
                ->uploads()
                ->exists()
        ) {
            $resourceRequest->update([
                'status' => 'open',
            ]);
        }

        return back()->with(
            'success',
            'The requested resource was removed successfully.'
        );
    }

    /**
     * Remove a file from Cloudinary.
     */
    private function removeCloudinaryFile(
        string $publicId,
        string $resourceType
    ): bool {
        try {
            $this->cloudinary->destroy(
                $publicId,
                $resourceType
            );

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}