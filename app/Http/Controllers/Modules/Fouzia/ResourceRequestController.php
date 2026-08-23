<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;

use App\Models\ResourceRequest as ResourceRequestModel;
use App\Models\ResourceUpload;

use App\Services\CloudinaryService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Throwable;

class ResourceRequestController extends Controller
{
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | Show Resource Requests
    |--------------------------------------------------------------------------
    */
    public function index(
        Request $request
    ): View {

        $course = $request
            ->string('course')
            ->trim()
            ->toString();


        $status = $request
            ->string('status')
            ->trim()
            ->toString();


        $resourceRequests = ResourceRequestModel::query()

            ->with('uploads')

            // Search by course code or course name
            ->when(
                $course !== '',

                function ($query) use ($course) {

                    $query->where(
                        function ($innerQuery) use ($course) {

                            $innerQuery
                                ->where(
                                    'course_code',
                                    'like',
                                    '%'.$course.'%'
                                )

                                ->orWhere(
                                    'course_name',
                                    'like',
                                    '%'.$course.'%'
                                );
                        }
                    );
                }
            )

            // Filter by status
            ->when(
                in_array(
                    $status,
                    ['open', 'fulfilled'],
                    true
                ),

                fn ($query) =>
                    $query->where(
                        'status',
                        $status
                    )
            )

            ->latest()

            ->get();


        return view(
            'resource-requests.index',
            compact('resourceRequests')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Resource Request
    |--------------------------------------------------------------------------
    */
    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate([

            'requester_name' => [
                'required',
                'string',
                'max:120',
            ],

            'course_code' => [
                'required',
                'string',
                'max:60',
            ],

            'course_name' => [
                'nullable',
                'string',
                'max:160',
            ],

            'title' => [
                'required',
                'string',
                'max:180',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1500',
            ],
        ]);


        ResourceRequestModel::create([

            'requester_name' =>
                $validated['requester_name'],

            'course_code' =>
                $validated['course_code'],

            'course_name' =>
                $validated['course_name']
                ?? null,

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description']
                ?? null,

            'status' =>
                'open',
        ]);


        return redirect()

            ->route('resource-requests.index')

            ->with(
                'success',
                'Resource request created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Upload Requested Resource
    |--------------------------------------------------------------------------
    */
    public function upload(
        Request $request,
        ResourceRequestModel $resourceRequest
    ): RedirectResponse {

        $validated = $request->validate([

            'uploader_name' => [
                'required',
                'string',
                'max:120',
            ],

            'upload_title' => [
                'required',
                'string',
                'max:180',
            ],

            'resource_file' => [
                'required',
                'file',

                'mimes:pdf,doc,docx,ppt,pptx,txt,zip,jpg,jpeg,png,webp',

                'max:20480',
            ],
        ]);


        try {

            $uploaded =
                $this->cloudinary->upload(

                    $request->file(
                        'resource_file'
                    ),

                    'student-collaboration-hub/resource-requests'
                );

        } catch (Throwable $exception) {

            report($exception);


            return back()->withErrors([

                'resource_file' =>
                    'Resource upload failed: '
                    .$exception->getMessage(),

            ]);
        }


        ResourceUpload::create([

            'resource_request_id' =>
                $resourceRequest->id,

            'uploader_name' =>
                $validated['uploader_name'],

            'title' =>
                $validated['upload_title'],

            'file_name' =>
                $request
                    ->file('resource_file')
                    ->getClientOriginalName(),

            'file_url' =>
                $uploaded['secure_url'],

            'cloudinary_public_id' =>
                $uploaded['public_id'],

            'resource_type' =>
                $uploaded['resource_type'],
        ]);


        // Once a resource is uploaded,
        // mark the request as fulfilled.
        $resourceRequest->update([
            'status' => 'fulfilled',
        ]);


        return back()->with(
            'success',
            'Requested resource uploaded successfully.'
        );
    }
}