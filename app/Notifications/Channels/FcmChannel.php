<?php

namespace App\Notifications\Channels;

use App\Models\AdminDeviceToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
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

        $message = CloudMessage::new()
            ->withNotification(FcmNotification::create(
                $payload['title'] ?? '',
                $payload['body']  ?? ''
            ))
            ->withData($payload['data'] ?? []);

        try {
            $report = $this->messaging->sendMulticast($message, $tokens);
        } catch (MessagingException|Throwable $e) {
            Log::warning('[FcmChannel] sendMulticast failed', [
                'error'   => $e->getMessage(),
                'tokens'  => count($tokens),
            ]);
            return;
        }

        $this->pruneInvalidTokens($report->responses());
    }

    /**
     * Delete tokens that Firebase reports as invalid / unregistered.
     */
    private function pruneInvalidTokens(array $responses): void
    {
        $invalid = [];

        foreach ($responses as $response) {
            if ($response->isFailure() && $response->error() !== null) {
                $code = $response->error()->code() ?? '';

                if (in_array($code, [
                    'messaging/registration-token-not-registered',
                    'messaging/invalid-registration-token',
                    'messaging/invalid-argument',
                    'NOT_FOUND',
                    'UNREGISTERED',
                    'INVALID_ARGUMENT',
                ], true)) {
                    $invalid[] = $response->target()?->value();
                }
            }
        }

        $invalid = array_filter($invalid);

        if (!empty($invalid)) {
            AdminDeviceToken::whereIn('token', $invalid)->delete();
            Log::info('[FcmChannel] pruned invalid tokens', ['count' => count($invalid)]);
        }
    }
}
