<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $service) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->service->list($request->user(), $request->integer('per_page') ?: 15);

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved.',
            'data'    => $notifications,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Unread count retrieved.',
            'data'    => ['count' => $this->service->unreadCount($request->user())],
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $this->service->markRead($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'data'    => null,
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $count = $this->service->markAllRead($request->user());

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'data'    => ['marked' => $count],
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->service->delete($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
            'data'    => null,
        ]);
    }
}
