<?php

namespace App\Notifications\Channels;

use App\Models\AdminDeviceToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

class FcmChannel
{
    public function __construct(private Messaging $messaging) {}

    /**
     * Send the given notification.
     *
     * Notification class must implement toFcm($notifiable):
     *   ['title' => string, 'body' => string, 'data' => array<string,string>]
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $tokens = $notifiable->routeNotificationFor('fcm', $notification);

        if (empty($tokens)) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        // Data-only message — NO `notification` block. With a `notification`
        // block the browser auto-displays the message, AND the service worker's
        // onBackgroundMessage runs, AND the foreground onMessage runs — which
        // can show the same notification two or three times. By sending data
        // only, our service worker (background) and inline onMessage handler
        // (foreground) each fire exactly once and we control display ourselves.
        $data = array_merge(
            ['title' => $payload['title'] ?? '', 'body' => $payload['body'] ?? ''],
            $payload['data'] ?? []
        );
        $data = array_map(fn($v) => (string) ($v ?? ''), $data);

        $message = CloudMessage::new()->withData($data);

        try {
            $report = $this->messaging->sendMulticast($message, $tokens);
            Log::info('[FcmChannel] sent', [
                'tokens'    => count($tokens),
                'successes' => $report->successes()->count(),
                'failures'  => $report->failures()->count(),
            ]);
            $this->pruneInvalidTokens($report);
        } catch (MessagingException|Throwable $e) {
            Log::warning('[FcmChannel] sendMulticast failed', [
                'error'  => $e->getMessage(),
                'tokens' => count($tokens),
            ]);
        }
    }

    /**
     * Delete tokens that Firebase reports as invalid / unregistered.
     * Uses MulticastSendReport::invalidTokens() and unknownTokens() — the
     * only public API on this SDK version (responses() does not exist).
     */
    private function pruneInvalidTokens(\Kreait\Firebase\Messaging\MulticastSendReport $report): void
    {
        if (!$report->hasFailures()) {
            return;
        }

        $invalid = array_merge(
            $report->invalidTokens(),
            $report->unknownTokens(),
        );

        $invalid = array_filter(array_unique($invalid));

        if (!empty($invalid)) {
            AdminDeviceToken::whereIn('token', $invalid)->delete();
            Log::info('[FcmChannel] pruned invalid tokens', ['count' => count($invalid)]);
        }
    }
}
