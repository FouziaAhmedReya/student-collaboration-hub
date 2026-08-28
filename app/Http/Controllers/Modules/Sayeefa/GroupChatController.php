<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\Meeting;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GroupChatController extends Controller
{
    public function index(Request $request): View
    {
        $groups = ChatGroup::query()->orderBy('name')->get();

        $activeGroupId = $request->integer('group_id') ?: $groups->first()?->id;
        $activeGroup = $activeGroupId
            ? ChatGroup::with(['messages.createdBy:id,name', 'meetings'])->find($activeGroupId)
            : null;

        return view('modules.sayeefa.group-chat.index', [
            'groups' => $groups,
            'activeGroup' => $activeGroup,
            'isMemberOfActive' => $activeGroup?->isMember(Auth::user()) ?? false,
        ]);
    }

    /** POST /api/chat-groups — creator automatically becomes the first member. */
    public function storeGroup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
        ]);

        $group = ChatGroup::create($data);

        ChatGroupMember::create([
            'chat_group_id' => $group->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json($group, 201);
    }

    public function apiGroups(): JsonResponse
    {
        return response()->json(ChatGroup::orderBy('name')->get());
    }

    /** POST /api/chat-groups/{group}/join */
    public function joinGroup(ChatGroup $group): JsonResponse
    {
        ChatGroupMember::firstOrCreate([
            'chat_group_id' => $group->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['joined' => true]);
    }

    public function apiMessages(ChatGroup $group): JsonResponse
    {
        abort_unless($group->isMember(Auth::user()), 403, 'Join this group to view its messages.');

        return response()->json($group->messages()->with('createdBy:id,name')->get());
    }

    /**
     * POST /api/chat-groups/{group}/messages — only members of this group
     * can post.
     */
    public function sendMessage(Request $request, ChatGroup $group): JsonResponse
    {
        abort_unless($group->isMember(Auth::user()), 403, 'Join this group before sending messages.');

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $group->messages()->create([
            'sender_name' => Auth::user()->name,
            'created_by_id' => Auth::id(),
            'message' => $request->input('message'),
        ]);

        return response()->json($message->load('createdBy:id,name'), 201);
    }

    public function apiMeetings(ChatGroup $group): JsonResponse
    {
        return response()->json($group->meetings()->get());
    }

    public function storeMeeting(Request $request, ChatGroup $group): JsonResponse
    {
        abort_unless($group->isMember(Auth::user()), 403, 'Join this group before scheduling its meetings.');

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

    public function destroyMeeting(Meeting $meeting): JsonResponse
    {
        abort_unless($meeting->chatGroup->isMember(Auth::user()), 403, 'Join this group to manage its meetings.');

        $meeting->delete();

        return response()->json(['deleted' => true]);
    }

    private function syncMeetingToGoogleCalendar(array $data): ?string
    {
        return app(GoogleCalendarService::class)->createEvent(
            title: $data['title'],
            description: null,
            start: Carbon::parse($data['meeting_time']),
        );
    }
}
