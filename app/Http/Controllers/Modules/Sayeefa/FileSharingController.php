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
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FileSharingController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('title')->get();

        $files = ProjectFile::with(['project:id,title', 'meeting:id,title', 'task:id,title', 'createdBy:id,name'])
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->latest()
            ->get();

        $selectedProjectId = $request->integer('project_id');
        $selectedProject = $selectedProjectId ? Project::find($selectedProjectId) : null;

        // Only projects the current user is actually a member of can be
        // picked in the upload form.
        $memberProjects = $projects->filter(fn ($project) => $project->isMember(Auth::user()));

        return view('modules.sayeefa.file-sharing.index', [
            'projects' => $projects,
            'memberProjects' => $memberProjects,
            'files' => $files,
            'selectedProjectId' => $selectedProjectId,
            'isMemberOfSelected' => $selectedProject?->isMember(Auth::user()) ?? true,
        ]);
    }

    public function apiFiles(Request $request): JsonResponse
    {
        $files = ProjectFile::with(['project:id,title', 'meeting:id,title', 'task:id,title', 'createdBy:id,name'])
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->latest()
            ->get();

        return response()->json($files);
    }

    public function apiProjectContext(Project $project): JsonResponse
    {
        return response()->json([
            'meetings' => ProjectMeeting::where('project_id', $project->id)->orderBy('meeting_time')->get(['id', 'title']),
            'tasks' => Task::where('project_id', $project->id)->orderBy('deadline')->get(['id', 'title']),
        ]);
    }

    /**
     * POST /api/files — only members of the file's project team may upload.
     */
    public function store(Request $request, CloudinaryService $cloudinary): JsonResponse
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'meeting_id' => 'nullable|exists:project_meetings,id',
            'task_id' => 'nullable|exists:tasks,id',
            'file' => 'required|file|max:20480',
        ]);

        $project = Project::findOrFail($data['project_id']);
        abort_unless($project->isMember(Auth::user()), 403, 'Only members of this project\'s team can upload files.');

        $result = $cloudinary->upload($request->file('file'), 'project-files');

        $file = ProjectFile::create([
            'project_id' => $data['project_id'],
            'meeting_id' => $data['meeting_id'] ?? null,
            'task_id' => $data['task_id'] ?? null,
            'uploaded_by' => Auth::user()->name,
            'created_by_id' => Auth::id(),
            'original_name' => $request->file('file')->getClientOriginalName(),
            'cloudinary_public_id' => $result['public_id'],
            'secure_url' => $result['secure_url'],
            'resource_type' => $result['resource_type'],
            'bytes' => $result['bytes'],
        ]);

        return response()->json($file->load(['project:id,title', 'meeting:id,title', 'task:id,title', 'createdBy:id,name']), 201);
    }

    public function destroy(ProjectFile $file, CloudinaryService $cloudinary): JsonResponse
    {
        abort_unless($file->project->isMember(Auth::user()), 403, 'Only members of this project\'s team can manage its files.');

        $cloudinary->destroy($file->cloudinary_public_id, $file->resource_type);
        $file->delete();

        return response()->json(['deleted' => true]);
    }
}
