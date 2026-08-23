<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;

use App\Models\Tutor;
use App\Models\TutorMaterial;

use App\Services\CloudinaryService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    | Show tutors + search
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

            // Search subject
            ->when(
                $subject !== '',

                fn ($query) =>
                    $query->where(
                        'subject',
                        'like',
                        '%'.$subject.'%'
                    )
            )

            // Search availability
            ->when(
                $availability !== '',

                fn ($query) =>
                    $query->where(
                        'availability',
                        'like',
                        '%'.$availability.'%'
                    )
            )

            // Search minimum rating
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


        return view(
            'tutor-finder.index',

            compact('tutors')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Add tutor
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
        | Upload profile photo
        | to Cloudinary
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


        Tutor::create([

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
        ]);


        return redirect()

            ->route('tutors.index')

            ->with(
                'success',
                'Tutor profile added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Upload teaching material
    |--------------------------------------------------------------------------
    */
    public function uploadMaterial(
        Request $request,
        Tutor $tutor
    ): RedirectResponse {

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
                $uploaded['secure_url'],

            'cloudinary_public_id' =>
                $uploaded['public_id'],

            'resource_type' =>
                $uploaded['resource_type'],
        ]);


        return back()->with(
            'success',
            'Teaching material uploaded successfully.'
        );
    }
}