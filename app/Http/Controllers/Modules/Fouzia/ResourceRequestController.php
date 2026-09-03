<?php

namespace App\Http\Controllers\Modules\Fouzia;

use App\Http\Controllers\Controller;
use App\Models\ResourceRequest as ResourceRequestModel;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class ResourceRequestController extends Controller
{
    /**
     * Cloudinary service is automatically injected.
     */
    public function __construct(
        private readonly CloudinaryService $cloudinary
    ) {
    }

    /**
     * Display all resource requests.
     *
     * Students and tutors can search requests by course
     * and filter them by status.
     */
    public function index(Request $request): View
    {
        $course = $request->string('course')
            ->trim()
            ->toString();

        $status = $request->string('status')
            ->trim()
            ->toString();

        $resourceRequests = ResourceRequestModel::query()
            ->with([
                'requester:id,name,role',
                'uploads' => function ($query) {
                    $query->latest();
                },
                'uploads.uploader:id,name,role',
            ])

            // Search using course code or course name.
            ->when($course !== '', function ($query) use ($course) {
                $query->where(function ($innerQuery) use ($course) {
                    $innerQuery
                        ->where(
                            'course_code',
                            'like',
                            '%' . $course . '%'
                        )
                        ->orWhere(
                            'course_name',
                            'like',
                            '%' . $course . '%'
                        );
                });
            })

            // Filter using open or fulfilled status.
            ->when(
                in_array($status, ['open', 'fulfilled'], true),
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            )

            ->latest()
            ->get();

        return view(
            'resource-requests.index',
            compact('resourceRequests')
        );
    }

    /**
     * Create a resource request.
     *
     * Only a logged-in student can access this method
     * because the route uses the student role middleware.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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
            'user_id' => auth()->id(),

            'requester_name' => auth()->user()->name,

            'course_code' => $validated['course_code'],

            'course_name' => $validated['course_name'] ?? null,

            'title' => $validated['title'],

            'description' => $validated['description'] ?? null,

            'status' => 'open',
        ]);

        return redirect()
            ->route('resource-requests.index')
            ->with(
                'success',
                'Resource request created successfully.'
            );
    }

    /**
     * Upload a requested resource.
     *
     * Both students and approved tutors can access this method
     * through the student,tutor role middleware.
     */
    public function upload(
        Request $request,
        ResourceRequestModel $resourceRequest
    ): RedirectResponse {
        $validated = $request->validate([
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

        /*
         * Upload the selected file to Cloudinary.
         */
        try {
            $uploaded = $this->cloudinary->upload(
                $request->file('resource_file'),
                'student-collaboration-hub/resource-requests'
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'resource_file' =>
                        'Resource upload failed: ' .
                        $exception->getMessage(),
                ]);
        }

        /*
         * Save the uploaded resource information in the database.
         */
        try {
            DB::transaction(function () use (
                $request,
                $resourceRequest,
                $validated,
                $uploaded
            ): void {
                $resourceRequest->uploads()->create([
                    'user_id' => auth()->id(),

                    'uploader_name' => auth()->user()->name,

                    'title' => $validated['upload_title'],

                    'file_name' => $request
                        ->file('resource_file')
                        ->getClientOriginalName(),

                    'file_url' => $uploaded['secure_url'],

                    'cloudinary_public_id' =>
                        $uploaded['public_id'],

                    'resource_type' =>
                        $uploaded['resource_type'],
                ]);

                /*
                 * Once at least one resource has been uploaded,
                 * mark the request as fulfilled.
                 */
                $resourceRequest->update([
                    'status' => 'fulfilled',
                ]);
            });
        } catch (Throwable $exception) {
            /*
             * If database saving fails, remove the uploaded
             * Cloudinary file to prevent unused files.
             */
            try {
                $this->cloudinary->destroy(
                    $uploaded['public_id'],
                    $uploaded['resource_type']
                );
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            report($exception);

            return back()->withErrors([
                'resource_file' =>
                    'The file was uploaded, but its information could not be saved.',
            ]);
        }

        return redirect()
            ->route('resource-requests.index')
            ->with(
                'success',
                'Requested resource uploaded successfully.'
            );
    }
}