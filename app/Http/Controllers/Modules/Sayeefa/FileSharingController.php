<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectMeeting;
use App\Models\Task;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FileSharingController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('title')->get();

        $files = ProjectFile::with(['project:id,title', 'meeting:id,title', 'task:id,title'])
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->latest()
            ->get();

        return view('modules.sayeefa.file-sharing.index', [
            'projects' => $projects,
            'files' => $files,
            'selectedProjectId' => $request->integer('project_id'),
        ]);
    }

    /** GET /api/files?project_id= */
    public function apiFiles(Request $request): JsonResponse
    {
        $files = ProjectFile::with(['project:id,title', 'meeting:id,title', 'task:id,title'])
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->latest()
            ->get();

        return response()->json($files);
    }

    /** GET /api/projects/{project}/meetings-and-tasks (for the upload form dropdowns) */
    public function apiProjectContext(Project $project): JsonResponse
    {
        return response()->json([
            'meetings' => ProjectMeeting::where('project_id', $project->id)->orderBy('meeting_time')->get(['id', 'title']),
            'tasks' => Task::where('project_id', $project->id)->orderBy('deadline')->get(['id', 'title']),
        ]);
    }

    /** POST /api/files */
    public function store(Request $request, CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'meeting_id' => 'nullable|exists:project_meetings,id',
            'task_id' => 'nullable|exists:tasks,id',
            'uploaded_by' => 'required|string|max:120',
            'file' => 'required|file|max:20480', // 20MB
        ]);

        $result = $cloudinary->upload($request->file('file'), 'project-files');

        $file = ProjectFile::create([
            'project_id' => $data['project_id'],
            'meeting_id' => $data['meeting_id'] ?? null,
            'task_id' => $data['task_id'] ?? null,
            'uploaded_by' => $data['uploaded_by'],
            'original_name' => $request->file('file')->getClientOriginalName(),
            'cloudinary_public_id' => $result['public_id'],
            'secure_url' => $result['secure_url'],
            'resource_type' => $result['resource_type'],
            'bytes' => $result['bytes'],
        ]);

        return response()->json($file->load(['project:id,title', 'meeting:id,title', 'task:id,title']), 201);
    }

    /** DELETE /api/files/{file} */
    public function destroy(ProjectFile $file, CloudinaryService $cloudinary): JsonResponse
    {
        $cloudinary->destroy($file->cloudinary_public_id, $file->resource_type);
        $file->delete();

        return response()->json(['deleted' => true]);
    }
}