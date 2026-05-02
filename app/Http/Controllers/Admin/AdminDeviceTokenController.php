<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminDeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDeviceTokenController extends Controller
{
    /**
     * Register / refresh an FCM token for the current admin.
     * Called by the dashboard JS after Firebase Web SDK getToken().
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'    => ['required', 'string', 'max:255'],
            'platform' => ['nullable', Rule::in(['web', 'android', 'ios'])],
        ]);

        $admin = $request->user('admin');

        AdminDeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'admin_id'     => $admin->id,
                'platform'     => $data['platform'] ?? 'web',
                'last_used_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Detach a token (called on logout / unsubscribe).
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
        ]);

        AdminDeviceToken::where('admin_id', $request->user('admin')->id)
            ->where('token', $data['token'])
            ->delete();

        return response()->json(['success' => true]);
    }
}
