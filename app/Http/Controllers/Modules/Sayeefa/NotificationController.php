<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /** Full notifications page. */
    public function index(): View
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('modules.sayeefa.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /** GET /api/notifications — used by the nav bell dropdown. */
    public function apiIndex(): JsonResponse
    {
        return response()->json([
            'unread_count' => Auth::user()->unreadNotifications()->count(),
            'notifications' => Auth::user()->notifications()->take(8)->get(),
        ]);
    }

    /** POST /api/notifications/{id}/read */
    public function markRead(string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['read' => true]);
    }

    /** POST /api/notifications/read-all */
    public function markAllRead(Request $request): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['read' => true]);
    }
}
