<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;

use App\Models\Tutor;
use App\Models\TutorMaterial;

use App\Services\CloudinaryService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

use Throwable;

class TutorFinderController extends Controller
{
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Show Tutors + Search
    |--------------------------------------------------------------------------
    */
    public function index(
        Request $request
    ): View {

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


        $tutors = Tutor::query()

            ->with('materials')

            /*
            | Search by subject
            */
            ->when(
                $subject !== '',

                fn ($query) =>
                    $query->where(
                        'subject',
                        'like',
                        '%'.$subject.'%'
                    )
            )

            /*
            | Search by availability
            */
            ->when(
                $availability !== '',

                fn ($query) =>
                    $query->where(
                        'availability',
                        'like',
                        '%'.$availability.'%'
                    )
            )

            /*
            | Search by minimum rating
            */
            ->when(
                $minRating !== null
                &&
                $minRating !== '',

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
        |--------------------------------------------------------------------------
        | Determine Which Tutor Belongs to Current Browser
        |--------------------------------------------------------------------------
        */
        foreach ($tutors as $tutor) {

            $tutor->setAttribute(
                'can_manage',

                $this->browserOwnsTutor(
                    $request,
                    $tutor
                )
            );
        }


        return view(
            'tutor-finder.index',
            compact('tutors')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add Tutor
    |--------------------------------------------------------------------------
    */
    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'email' => [
                'nullable',
                'email',
                'max:160',
            ],

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

            'rating' => [
                'required',
                'numeric',
                'min:0',
                'max:5',
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
        |--------------------------------------------------------------------------
        | Upload Tutor Profile Image to Cloudinary
        |--------------------------------------------------------------------------
        */
        if (
            $request->hasFile(
                'profile_image'
            )
        ) {

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
                            'Profile image upload failed: '
                            .$exception->getMessage(),

                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Ownership Token
        |--------------------------------------------------------------------------
        |
        | Actual secret token:
        | stored only in browser cookie.
        |
        | Hash:
        | stored in database.
        |
        */
        $ownerToken =
            bin2hex(
                random_bytes(32)
            );


        $ownerTokenHash =
            hash(
                'sha256',
                $ownerToken
            );


        /*
        |--------------------------------------------------------------------------
        | Create Tutor
        |--------------------------------------------------------------------------
        */
        $tutor = Tutor::create([

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email']
                ?? null,

            'subject' =>
                $validated['subject'],

            'availability' =>
                $validated['availability'],

            'rating' =>
                $validated['rating'],

            'bio' =>
                $validated['bio']
                ?? null,

            'profile_image_url' =>
                $profileUpload[
                    'secure_url'
                ] ?? null,

            'profile_image_public_id' =>
                $profileUpload[
                    'public_id'
                ] ?? null,

            'profile_image_resource_type' =>
                $profileUpload[
                    'resource_type'
                ] ?? null,

            'owner_token_hash' =>
                $ownerTokenHash,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Save Secret Ownership Token in Browser Cookie
        |--------------------------------------------------------------------------
        |
        | Cookie:
        | - lasts 1 year
        | - HttpOnly
        | - SameSite Lax
        |
        */
        Cookie::queue(

            Cookie::make(

                $this->ownerCookieName(
                    $tutor
                ),

                $ownerToken,

                60 * 24 * 365,

                '/',

                null,

                $request->isSecure(),

                true,

                false,

                'lax'
            )
        );


        return redirect()

            ->route('tutors.index')

            ->with(
                'success',
                'Tutor profile added successfully. This browser now owns this tutor profile.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Upload Teaching Material
    |--------------------------------------------------------------------------
    */
    public function uploadMaterial(
        Request $request,
        Tutor $tutor
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Only Tutor Owner Can Upload
        |--------------------------------------------------------------------------
        */
        $this->ensureTutorOwner(
            $request,
            $tutor
        );


        $validated =
            $request->validate([

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


        /*
        |--------------------------------------------------------------------------
        | Upload Material to Cloudinary
        |--------------------------------------------------------------------------
        */
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
                    'Material upload failed: '
                    .$exception->getMessage(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Save Material in Database
        |--------------------------------------------------------------------------
        */
        TutorMaterial::create([

            'tutor_id' =>
                $tutor->id,

            'title' =>
                $validated[
                    'material_title'
                ],

            'file_name' =>
                $request
                    ->file('material_file')
                    ->getClientOriginalName(),

            'file_url' =>
                $uploaded[
                    'secure_url'
                ],

            'cloudinary_public_id' =>
                $uploaded[
                    'public_id'
                ],

            'resource_type' =>
                $uploaded[
                    'resource_type'
                ],
        ]);


        return back()->with(
            'success',
            'Teaching material uploaded successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Teaching Material
    |--------------------------------------------------------------------------
    */
    public function destroyMaterial(
        Request $request,
        Tutor $tutor,
        TutorMaterial $material
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Only Owner Can Delete
        |--------------------------------------------------------------------------
        */
        $this->ensureTutorOwner(
            $request,
            $tutor
        );


        /*
        |--------------------------------------------------------------------------
        | Make Sure Material Belongs to Tutor
        |--------------------------------------------------------------------------
        */
        if (
            (int) $material->tutor_id
            !==
            (int) $tutor->id
        ) {

            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Material from Cloudinary
        |--------------------------------------------------------------------------
        */
        try {

            if (
                $material->cloudinary_public_id
                &&
                $material->resource_type
            ) {

                $this->cloudinary->destroy(

                    $material->cloudinary_public_id,

                    $material->resource_type
                );
            }

        } catch (Throwable $exception) {

            report($exception);


            return back()->withErrors([

                'material_delete' =>
                    'Teaching material could not be deleted: '
                    .$exception->getMessage(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Material from Database
        |--------------------------------------------------------------------------
        */
        $material->delete();


        return back()->with(
            'success',
            'Teaching material deleted successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Tutor
    |--------------------------------------------------------------------------
    */
    public function destroy(
        Request $request,
        Tutor $tutor
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Only Owner Can Delete Tutor
        |--------------------------------------------------------------------------
        */
        $this->ensureTutorOwner(
            $request,
            $tutor
        );


        /*
        | Remember cookie name
        | before deleting tutor.
        */
        $cookieName =
            $this->ownerCookieName(
                $tutor
            );


        /*
        | Load teaching materials.
        */
        $tutor->load(
            'materials'
        );


        /*
        |--------------------------------------------------------------------------
        | Delete Tutor Files from Cloudinary
        |--------------------------------------------------------------------------
        */
        try {

            /*
            | Delete every teaching material.
            */
            foreach (
                $tutor->materials
                as $material
            ) {

                if (
                    $material->cloudinary_public_id
                    &&
                    $material->resource_type
                ) {

                    $this->cloudinary->destroy(

                        $material->cloudinary_public_id,

                        $material->resource_type
                    );
                }
            }


            /*
            | Delete tutor profile image.
            */
            if (
                $tutor->profile_image_public_id
                &&
                $tutor->profile_image_resource_type
            ) {

                $this->cloudinary->destroy(

                    $tutor->profile_image_public_id,

                    $tutor->profile_image_resource_type
                );
            }

        } catch (Throwable $exception) {

            report($exception);


            return back()->withErrors([

                'tutor_delete' =>
                    'Tutor could not be deleted: '
                    .$exception->getMessage(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Tutor from Database
        |--------------------------------------------------------------------------
        |
        | If your tutor_materials foreign key uses
        | cascadeOnDelete(), the material database rows
        | are also automatically removed.
        |
        */
        $tutor->delete();


        /*
        |--------------------------------------------------------------------------
        | Remove Ownership Cookie
        |--------------------------------------------------------------------------
        */
        Cookie::queue(
            Cookie::forget(
                $cookieName
            )
        );


        return redirect()

            ->route('tutors.index')

            ->with(
                'success',
                'Your tutor profile and teaching materials were deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Check Browser Ownership
    |--------------------------------------------------------------------------
    */
    private function browserOwnsTutor(
        Request $request,
        Tutor $tutor
    ): bool {

        /*
        | Tutors created before this system
        | have no ownership token.
        */
        if (
            empty(
                $tutor->owner_token_hash
            )
        ) {

            return false;
        }


        /*
        | Get ownership token from browser cookie.
        */
        $ownerToken =
            $request->cookie(

                $this->ownerCookieName(
                    $tutor
                )
            );


        /*
        | No ownership cookie found.
        */
        if (
            ! is_string(
                $ownerToken
            )
            ||
            $ownerToken === ''
        ) {

            return false;
        }


        /*
        | Hash browser's token.
        */
        $browserHash =
            hash(
                'sha256',
                $ownerToken
            );


        /*
        | Secure comparison.
        */
        return hash_equals(

            $tutor->owner_token_hash,

            $browserHash
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Require Ownership
    |--------------------------------------------------------------------------
    */
    private function ensureTutorOwner(
        Request $request,
        Tutor $tutor
    ): void {

        if (
            ! $this->browserOwnsTutor(
                $request,
                $tutor
            )
        ) {

            abort(
                403,
                'You are not allowed to manage this tutor profile.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Ownership Cookie Name
    |--------------------------------------------------------------------------
    */
    private function ownerCookieName(
        Tutor $tutor
    ): string {

        return
            'tutor_owner_'
            .$tutor->id;
    }
}