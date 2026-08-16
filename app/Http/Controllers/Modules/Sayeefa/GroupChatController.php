<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupChatController extends Controller
{
    /**
     * Web page: group list on the side, active group's chat thread and
     * upcoming meetings in the main panel.
     */
    public function index(Request $request): View
    {
        $groups = ChatGroup::query()->orderBy('name')->get();

        $activeGroupId = $request->integer('group_id') ?: $groups->first()?->id;
        $activeGroup = $activeGroupId
            ? ChatGroup::with(['messages', 'meetings'])->find($activeGroupId)
            : null;

        return view('modules.sayeefa.group-chat.index', [
            'groups' => $groups,
            'activeGroup' => $activeGroup,
        ]);
    }

    /** POST /api/chat-groups */
    public function storeGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        $group = ChatGroup::create($data);

        return response()->json($group, 201);
    }

    /** GET /api/chat-groups */
    public function apiGroups(): JsonResponse
    {
        return response()->json(ChatGroup::orderBy('name')->get());
    }

    /** GET /api/chat-groups/{group}/messages */
    public function apiMessages(ChatGroup $group): JsonResponse
    {
        return response()->json($group->messages()->get());
    }

    /** POST /api/chat-groups/{group}/messages */
    public function sendMessage(Request $request, ChatGroup $group): JsonResponse
    {
        $data = $request->validate([
            'sender_name' => 'required|string|max:120',
            'message' => 'required|string|max:2000',
        ]);

        $message = $group->messages()->create($data);

        return response()->json($message, 201);
    }

    /** GET /api/chat-groups/{group}/meetings */
    public function apiMeetings(ChatGroup $group): JsonResponse
    {
        return response()->json($group->meetings()->get());
    }

    /** POST /api/chat-groups/{group}/meetings */
    public function storeMeeting(Request $request, ChatGroup $group): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'meeting_time' => 'required|date',
        ]);

        $meeting = $group->meetings()->create([
            ...$data,
            'google_calendar_event_id' => $this->syncMeetingToGoogleCalendar($data),
        ]);

        return response()->json($meeting, 201);
    }

    /** DELETE /api/meetings/{meeting} */
    public function destroyMeeting(Meeting $meeting): JsonResponse
    {
        $meeting->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Placeholder Google Calendar sync — see the matching method in
     * ProjectTaskController for the same TODO (real OAuth + Calendar API
     * call to be wired in once credentials exist).
     */
    private function syncMeetingToGoogleCalendar(array $data): string
    {
        return 'gcal_'.uniqid();
    }
}
