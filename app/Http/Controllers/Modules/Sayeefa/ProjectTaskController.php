<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectTaskController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()->orderBy('title')->get();
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        $tasks = Task::query()
            ->with(['project:id,title', 'assignedUser:id,name,email', 'createdBy:id,name'])
            ->when($request->integer('project_id'), fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->orderBy('deadline')
            ->get();

        $selectedProjectId = $request->integer('project_id');
        $selectedProject = $selectedProjectId ? Project::find($selectedProjectId) : null;

        return view('modules.sayeefa.project-tasks.index', [
            'projects' => $projects,
            'users' => $users,
            'tasks' => $tasks,
            'selectedProjectId' => $selectedProjectId,
            'isProjectMember' => $selectedProject?->isMember(Auth::user()) ?? true,
            'canCreateProjects' => Auth::user()->role === 'student',
        ]);
    }

    /**
     * POST /api/projects — only students may create a project. The creator
     * automatically becomes that project's team leader.
     */
    public function storeProject(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->role === 'student', 403, 'Only students can create projects.');

        $data = $request->validate([
            'title' => 'required|string|max:150',
            'required_skills' => 'nullable|string|max:255',
            'team_size' => 'nullable|integer|min:1|max:50',
        ]);

        $project = Project::create([
            'title' => $data['title'],
            'required_skills' => $data['required_skills'] ?? '',
            'team_size' => $data['team_size'] ?? 4,
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'is_leader' => true,
        ]);

        return response()->json($project, 201);
    }

    public function apiProjects(): JsonResponse
    {
        return response()->json(Project::orderBy('title')->get());
    }

    /**
     * POST /api/projects/{project}/join — any student can join a project's
     * team (as a regular member, not leader) so they can add tasks / files.
     */
    public function joinProject(Project $project): JsonResponse
    {
        abort_unless(Auth::user()->role === 'student', 403, 'Only students can join a project team.');

        ProjectMember::firstOrCreate([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
        ], ['is_leader' => false]);

        return response()->json(['joined' => true]);
    }

    public function apiTasks(Request $request): JsonResponse
    {
        $tasks = Task::query()
            ->with(['project:id,title', 'assignedUser:id,name,email'])
            ->when($request->integer('project_id'), fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->orderBy('deadline')
            ->get();

        return response()->json($tasks);
    }

    /**
     * POST /api/tasks — only students, and only if they're a member of the
     * task's project team.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->role === 'student', 403, 'Only students can add tasks.');

        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'assigned_user_id' => 'required|exists:users,id',
            'deadline' => 'required|date',
            'notify_at' => 'nullable|date',
        ]);

        $project = Project::findOrFail($data['project_id']);
        abort_unless($project->isMember(Auth::user()), 403, 'Join this project\'s team before adding tasks to it.');

        $assignedUser = User::findOrFail($data['assigned_user_id']);

        $task = Task::create([
            ...$data,
            'assigned_to' => $assignedUser->name,
            'created_by_id' => Auth::id(),
            'status' => 'pending',
            'google_calendar_event_id' => $this->syncTaskToGoogleCalendar($data),
        ]);

        return response()->json($task->load(['project:id,title', 'assignedUser:id,name,email']), 201);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        abort_unless(Auth::user()->role === 'student', 403, 'Only students can manage tasks.');
        abort_unless($task->project->isMember(Auth::user()), 403, 'You are not a member of this project.');

        $data = $request->validate([
            'title' => 'sometimes|string|max:150',
            'description' => 'sometimes|nullable|string',
            'assigned_user_id' => 'sometimes|exists:users,id',
            'deadline' => 'sometimes|date',
            'notify_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|in:pending,in_progress,completed',
        ]);

        if (isset($data['assigned_user_id'])) {
            $data['assigned_to'] = User::findOrFail($data['assigned_user_id'])->name;
        }

        if (isset($data['notify_at'])) {
            $data['reminder_sent_at'] = null;
        }

        $task->update($data);

        return response()->json($task->load(['project:id,title', 'assignedUser:id,name,email']));
    }

    public function destroy(Task $task): JsonResponse
    {
        abort_unless(Auth::user()->role === 'student', 403, 'Only students can manage tasks.');
        abort_unless($task->project->isMember(Auth::user()), 403, 'You are not a member of this project.');

        $task->delete();

        return response()->json(['deleted' => true]);
    }

    private function syncTaskToGoogleCalendar(array $data): ?string
    {
        return app(GoogleCalendarService::class)->createEvent(
            title: $data['title'],
            description: $data['description'] ?? null,
            start: Carbon::parse($data['deadline'])->setTime(9, 0),
        );
    }
}
