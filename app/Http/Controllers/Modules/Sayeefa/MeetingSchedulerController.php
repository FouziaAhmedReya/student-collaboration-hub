<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMeeting;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingSchedulerController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('title')->get();

        $meetings = ProjectMeeting::with('project:id,title')
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->orderBy('meeting_time')
            ->get();

        return view('modules.sayeefa.meeting-scheduler.index', [
            'projects' => $projects,
            'meetings' => $meetings,
            'selectedProjectId' => $request->integer('project_id'),
        ]);
    }

    /** GET /api/meetings?project_id= */
    public function apiMeetings(Request $request): JsonResponse
    {
        $meetings = ProjectMeeting::with('project:id,title')
            ->when($request->integer('project_id'), fn ($q, $id) => $q->where('project_id', $id))
            ->orderBy('meeting_time')
            ->get();

        return response()->json($meetings);
    }

    /** POST /api/meetings */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:150',
            'agenda' => 'nullable|string',
            'organizer' => 'required|string|max:120',
            'meeting_time' => 'required|date',
            'deadline' => 'nullable|date',
        ]);

        $meeting = ProjectMeeting::create([
            ...$data,
            'google_calendar_event_id' => $this->syncToGoogleCalendar($data),
        ]);

        return response()->json($meeting->load('project:id,title'), 201);
    }

    /** PUT /api/meetings/{meeting} */
    public function update(Request $request, ProjectMeeting $meeting): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:150',
            'agenda' => 'sometimes|nullable|string',
            'meeting_time' => 'sometimes|date',
            'deadline' => 'sometimes|nullable|date',
        ]);

        $meeting->update($data);

        return response()->json($meeting->load('project:id,title'));
    }

    /** DELETE /api/meetings/{meeting} */
    public function destroy(ProjectMeeting $meeting): JsonResponse
    {
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