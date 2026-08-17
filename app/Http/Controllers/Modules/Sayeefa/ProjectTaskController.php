<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectTaskController extends Controller
{
    /**
     * Web page: shows all projects, a create-project form, a create-task
     * form, and the full task list with status controls.
     */
    public function index(Request $request): View
    {
        $projects = Project::query()->orderBy('title')->get();

        $tasks = Task::query()
            ->with('project:id,title')
            ->when($request->integer('project_id'), fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->orderBy('deadline')
            ->get();

        return view('modules.sayeefa.project-tasks.index', [
            'projects' => $projects,
            'tasks' => $tasks,
            'selectedProjectId' => $request->integer('project_id'),
        ]);
    }

    /**
     * POST /api/projects
     * Covers the "create projects" part of the feature spec.
     */
    public function storeProject(Request $request): JsonResponse
    {
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

        return response()->json($project, 201);
    }

    /** GET /api/projects */
    public function apiProjects(): JsonResponse
    {
        return response()->json(Project::orderBy('title')->get());
    }

    /** GET /api/tasks?project_id= */
    public function apiTasks(Request $request): JsonResponse
    {
        $tasks = Task::query()
            ->with('project:id,title')
            ->when($request->integer('project_id'), fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->orderBy('deadline')
            ->get();

        return response()->json($tasks);
    }

    /** POST /api/tasks */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'assigned_to' => 'required|string|max:120',
            'deadline' => 'required|date',
            'notify_at' => 'nullable|date',
        ]);

        $task = Task::create([
            ...$data,
            'status' => 'pending',
            'google_calendar_event_id' => $this->syncTaskToGoogleCalendar($data),
        ]);

        return response()->json($task->load('project:id,title'), 201);
    }

    /** PUT /api/tasks/{task} — used both for status changes and edits */
    public function update(Request $request, Task $task): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:150',
            'description' => 'sometimes|nullable|string',
            'assigned_to' => 'sometimes|string|max:120',
            'deadline' => 'sometimes|date',
            'notify_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|in:pending,in_progress,completed',
        ]);

        $task->update($data);

        return response()->json($task->load('project:id,title'));
    }

    /** DELETE /api/tasks/{task} */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Creates a matching event on the shared Google group calendar.
     * Returns null (instead of throwing) if the calendar isn't configured
     * yet, so task creation always still works.
     */
    private function syncTaskToGoogleCalendar(array $data): ?string
    {
        return app(GoogleCalendarService::class)->createEvent(
            title: $data['title'],
            description: $data['description'] ?? null,
            start: Carbon::parse($data['deadline'])->setTime(9, 0),
        );
    }
}
