<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\TutorMaterial;
use App\Models\TutorRating;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TutorFinderController extends Controller
{
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {}

    /**
     * Display approved Tutors and apply search filters.
     */
    public function index(Request $request): View
    {
        $subject = $request
            ->string('subject')
            ->trim()
            ->toString();

        $availability = $request
            ->string('availability')
            ->trim()
            ->toString();

        $minRating = $request->input(
            'min_rating'
        );

        /*
         * Only display Tutor profiles connected to
         * approved registered Tutor accounts.
         */
        $tutors = Tutor::query()
            ->with([
                'user:id,name,email,role,status',

                'materials',

                'ratings.student:id,name',
            ])
            ->whereHas(
                'user',
                function ($query) {
                    $query
                        ->where('role', 'tutor')
                        ->where('status', 'approved');
                }
            )
            ->when(
                $subject !== '',
                fn ($query) =>
                    $query->where(
                        'subject',
                        'like',
                        '%'.$subject.'%'
                    )
            )
            ->when(
                $availability !== '',
                fn ($query) =>
                    $query->where(
                        'availability',
                        'like',
                        '%'.$availability.'%'
                    )
            )
            ->when(
                $minRating !== null
                && $minRating !== '',
                fn ($query) =>
                    $query->where(
                        'rating',
                        '>=',
                        (float) $minRating
                    )
            )
            ->orderByDesc('rating')
            ->latest()
            ->get();

        /*
         * Mark only the logged-in Tutor's own profile
         * as manageable.
         */
        $tutors->each(
            function (Tutor $tutor): void {
                $tutor->setAttribute(
                    'can_manage',
                    auth()->user()->role === 'tutor'
                    && (int) $tutor->user_id
                        === (int) auth()->id()
                );
            }
        );

        /*
         * Check whether the logged-in Tutor already
         * has a Tutor Finder profile.
         */
        $currentTutor =
            auth()->user()->role === 'tutor'
                ? Tutor::where(
                    'user_id',
                    auth()->id()
                )->first()
                : null;

        return view(
            'tutor-finder.index',
            compact(
                'tutors',
                'currentTutor'
            )
        );
    }

    /**
     * Create a Tutor Finder profile.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $user = auth()->user();

        /*
         * Only an approved registered Tutor
         * can create a Tutor Finder profile.
         */
        abort_unless(
            $user->role === 'tutor'
            && $user->status === 'approved',
            403,
            'Only an approved Tutor can create a Tutor profile.'
        );

        /*
         * One registered Tutor can have
         * only one Tutor Finder profile.
         */
        if (
            Tutor::where(
                'user_id',
                $user->id
            )->exists()
        ) {
            return back()->withErrors([
                'profile' =>
                    'You already have a Tutor Finder profile.',
            ]);
        }

        $validated = $request->validate([
            'subject' => [
                'required',
                'string',
                'max:160',
            ],

            'availability' => [
                'required',
                'string',
                'max:160',
            ],

            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $profileUpload = null;

        /*
         * Upload the optional profile image
         * through Cloudinary.
         */
        if ($request->hasFile('profile_image')) {
            try {
                $profileUpload =
                    $this->cloudinary->upload(
                        $request->file(
                            'profile_image'
                        ),
                        'student-collaboration-hub/tutors/profile-images'
                    );
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withInput()
                    ->withErrors([
                        'profile_image' =>
                            'Profile image upload failed. '.
                            $exception->getMessage(),
                    ]);
            }
        }

        try {
            Tutor::create([
                'user_id' => $user->id,

                /*
                 * Name and email come from the
                 * registered Tutor account.
                 */
                'name' => $user->name,

                'email' => $user->email,

                'subject' =>
                    $validated['subject'],

                'availability' =>
                    $validated['availability'],

                'rating' => 0,

                'bio' =>
                    $validated['bio'] ?? null,

                'profile_image_url' =>
                    $profileUpload['secure_url']
                    ?? null,

                'profile_image_public_id' =>
                    $profileUpload['public_id']
                    ?? null,

                'profile_image_resource_type' =>
                    $profileUpload['resource_type']
                    ?? null,
            ]);
        } catch (Throwable $exception) {
            /*
             * Remove the Cloudinary image when
             * database creation fails.
             */
            if ($profileUpload) {
                $this->removeUploadedFile(
                    $profileUpload
                );
            }

            throw $exception;
        }

        return redirect()
            ->route('tutors.index')
            ->with(
                'success',
                'Your Tutor profile was created successfully.'
            );
    }

    /**
     * Upload a teaching material.
     */
    public function uploadMaterial(
        Request $request,
        Tutor $tutor
    ): RedirectResponse {
        /*
         * The logged-in Tutor must own this profile.
         */
        $this->ensureTutorOwner($tutor);

        $validated = $request->validate([
            'material_title' => [
                'required',
                'string',
                'max:160',
            ],

            'material_file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,txt,zip',
                'max:20480',
            ],
        ]);

        try {
            $uploaded =
                $this->cloudinary->upload(
                    $request->file(
                        'material_file'
                    ),
                    'student-collaboration-hub/tutors/materials'
                );
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'material_file' =>
                    'Teaching material upload failed. '.
                    $exception->getMessage(),
            ]);
        }

        try {
            TutorMaterial::create([
                'tutor_id' => $tutor->id,

                'title' =>
                    $validated['material_title'],

                'file_name' => $request
                    ->file('material_file')
                    ->getClientOriginalName(),

                'file_url' =>
                    $uploaded['secure_url'],

                'cloudinary_public_id' =>
                    $uploaded['public_id'],

                'resource_type' =>
                    $uploaded['resource_type'],
            ]);
        } catch (Throwable $exception) {
            /*
             * Remove the Cloudinary file if
             * database creation fails.
             */
            $this->removeUploadedFile(
                $uploaded
            );

            throw $exception;
        }

        return back()->with(
            'success',
            'Teaching material uploaded successfully.'
        );
    }

    /**
     * Delete one teaching material.
     */
    public function destroyMaterial(
        Tutor $tutor,
        TutorMaterial $material
    ): RedirectResponse {
        $this->ensureTutorOwner($tutor);

        /*
         * Confirm the material belongs
         * to the selected Tutor profile.
         */
        abort_unless(
            (int) $material->tutor_id
                === (int) $tutor->id,
            404
        );

        try {
            $this->cloudinary->destroy(
                $material->cloudinary_public_id,
                $material->resource_type
            );

            $material->delete();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'material_delete' =>
                    'Teaching material could not be deleted. '.
                    $exception->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            'Teaching material deleted successfully.'
        );
    }

    /**
     * Submit or update a Student rating.
     */
    public function rate(
        Request $request,
        Tutor $tutor
    ): RedirectResponse {
        /*
         * The route is Student-only, but also confirm
         * that the selected Tutor is approved.
         */
        abort_unless(
            $tutor->user
            && $tutor->user->role === 'tutor'
            && $tutor->user->status === 'approved',
            404
        );

        $validated = $request->validate([
            'rating' => [
                'required',
                'numeric',
                'min:1',
                'max:5',
            ],

            'review' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * One rating per Student and Tutor.
         * Submitting again updates the previous rating.
         */
        TutorRating::updateOrCreate(
            [
                'tutor_id' => $tutor->id,

                'user_id' => auth()->id(),
            ],
            [
                'rating' =>
                    $validated['rating'],

                'review' =>
                    $validated['review'] ?? null,
            ]
        );

        /*
         * Recalculate the Tutor's average rating.
         */
        $tutor->refreshAverageRating();

        return back()->with(
            'success',
            'Your Tutor rating was saved.'
        );
    }

    /**
     * Delete the logged-in Tutor's profile.
     */
    public function destroy(
        Tutor $tutor
    ): RedirectResponse {
        $this->ensureTutorOwner($tutor);

        $tutor->load('materials');

        try {
            /*
             * Delete all teaching materials
             * from Cloudinary.
             */
            foreach (
                $tutor->materials
                as $material
            ) {
                $this->cloudinary->destroy(
                    $material->cloudinary_public_id,
                    $material->resource_type
                );
            }

            /*
             * Delete the profile image
             * from Cloudinary.
             */
            if (
                $tutor->profile_image_public_id
                && $tutor->profile_image_resource_type
            ) {
                $this->cloudinary->destroy(
                    $tutor->profile_image_public_id,
                    $tutor->profile_image_resource_type
                );
            }

            /*
             * Tutor materials and ratings are deleted
             * automatically through database cascades.
             */
            $tutor->delete();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'tutor_delete' =>
                    'Tutor profile could not be deleted. '.
                    $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('tutors.index')
            ->with(
                'success',
                'Your Tutor profile was deleted.'
            );
    }

    /**
     * Confirm the logged-in approved Tutor
     * owns the selected profile.
     */
    private function ensureTutorOwner(
        Tutor $tutor
    ): void {
        abort_unless(
            auth()->user()->role === 'tutor'
            && auth()->user()->status === 'approved'
            && (int) $tutor->user_id
                === (int) auth()->id(),
            403,
            'You can only manage your own Tutor profile.'
        );
    }

    /**
     * Remove a Cloudinary upload after a failed operation.
     */
    private function removeUploadedFile(
        array $uploaded
    ): void {
        try {
            $this->cloudinary->destroy(
                $uploaded['public_id'],
                $uploaded['resource_type']
            );
        } catch (Throwable $exception) {
            Log::warning(
                'A Tutor Finder Cloudinary file could not be removed.',
                [
                    'public_id' =>
                        $uploaded['public_id']
                        ?? null,

                    'error' =>
                        $exception->getMessage(),
                ]
            );
        }
    }
}