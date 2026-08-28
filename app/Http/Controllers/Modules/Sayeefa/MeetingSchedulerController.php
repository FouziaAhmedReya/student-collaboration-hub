<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMeeting;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MeetingSchedulerController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('title')->get();

        $meetings = ProjectMeeting::with(['project:id,title', 'createdBy:id,name'])
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->orderBy('meeting_time')
            ->get();

        $selectedProjectId = $request->integer('project_id');
        $selectedProject = $selectedProjectId ? Project::find($selectedProjectId) : null;

        // Projects the current user actually leads — only these can be picked
        // in the "New Meeting" form.
        $leadableProjects = $projects->filter(fn ($project) => $project->isLeader(Auth::user()));

        return view('modules.sayeefa.meeting-scheduler.index', [
            'projects' => $projects,
            'leadableProjects' => $leadableProjects,
            'meetings' => $meetings,
            'selectedProjectId' => $selectedProjectId,
            'isLeaderOfSelected' => $selectedProject?->isLeader(Auth::user()) ?? true,
        ]);
    }

    public function apiMeetings(Request $request): JsonResponse
    {
        $meetings = ProjectMeeting::with(['project:id,title', 'createdBy:id,name'])
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->orderBy('meeting_time')
            ->get();

        return response()->json($meetings);
    }

    /**
     * POST /api/meetings — only the project's team leader may schedule a
     * meeting for that project.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:150',
            'agenda' => 'nullable|string',
            'meeting_time' => 'required|date',
            'deadline' => 'nullable|date',
        ]);

        $project = Project::findOrFail($data['project_id']);
        abort_unless($project->isLeader(Auth::user()), 403, 'Only this project\'s team leader can schedule meetings.');

        $meeting = ProjectMeeting::create([
            ...$data,
            'organizer' => Auth::user()->name,
            'created_by_id' => Auth::id(),
            'google_calendar_event_id' => $this->syncToGoogleCalendar($data),
        ]);

        return response()->json($meeting->load(['project:id,title', 'createdBy:id,name']), 201);
    }

    public function update(Request $request, ProjectMeeting $meeting): JsonResponse
    {
        abort_unless($meeting->project->isLeader(Auth::user()), 403, 'Only this project\'s team leader can edit meetings.');

        $data = $request->validate([
            'title' => 'sometimes|string|max:150',
            'agenda' => 'sometimes|nullable|string',
            'meeting_time' => 'sometimes|date',
            'deadline' => 'sometimes|nullable|date',
        ]);

        if (isset($data['meeting_time'])) {
            $data['reminder_sent_at'] = null;
        }

        $meeting->update($data);

        return response()->json($meeting->load(['project:id,title', 'createdBy:id,name']));
    }

    public function destroy(ProjectMeeting $meeting): JsonResponse
    {
        abort_unless($meeting->project->isLeader(Auth::user()), 403, 'Only this project\'s team leader can delete meetings.');

        $meeting->delete();

        return response()->json(['deleted' => true]);
    }

    private function syncToGoogleCalendar(array $data): ?string
    {
        return app(GoogleCalendarService::class)->createEvent(
            title: $data['title'],
            description: $data['agenda'] ?? null,
            start: Carbon::parse($data['meeting_time']),
        );
    }
}
