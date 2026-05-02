<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $service) {}

    public function index(Request $request): View
    {
        $admin         = $request->user('admin');
        $notifications = $this->service->list($admin, 20);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Bell-icon dropdown: returns latest 10 unread + count, as JSON.
     */
    public function dropdown(Request $request): JsonResponse
    {
        $admin = $request->user('admin');

        return response()->json([
            'count'         => $this->service->unreadCount($admin),
            'notifications' => $this->service->latestRecent($admin, 10)->map(fn($n) => [
                'id'         => $n->id,
                'data'       => $n->data,
                'read_at'    => $n->read_at?->toIso8601String(),
                'created_at' => $n->created_at?->diffForHumans(),
            ])->values(),
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $this->service->markRead($request->user('admin'), $id);

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): RedirectResponse|JsonResponse
    {
        $count = $this->service->markAllRead($request->user('admin'));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'marked' => $count]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
