<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    /**
     * Route every channel through the dedicated 'notifications' queue.
     */
    public function viaQueues(): array
    {
        return [
            'database' => 'notifications',
            'fcm'      => 'notifications',
        ];
    }

    /**
     * Stable slug used both as the notification type AND as the lang key.
     */
    abstract protected function slug(): string;

    /**
     * :placeholder variables for the lang string.
     */
    abstract protected function vars(mixed $notifiable): array;

    /**
     * Extra context stored in `notifications.data` and forwarded to FCM.
     * Always include a `deeplink` when possible.
     */
    abstract protected function payload(mixed $notifiable): array;

    /**
     * Channels per recipient type — admins get DB + FCM, everyone else DB only.
     */
    public function via(mixed $notifiable): array
    {
        return match (true) {
            $notifiable instanceof Admin => ['database', 'fcm'],
            default                      => ['database'],
        };
    }

    public function toDatabase(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');
        $slug   = $this->slug();
        $vars   = $this->vars($notifiable);

        return [
            'type'  => $slug,
            'title' => __("notifications.{$slug}.title", $vars, $locale),
            'body'  => __("notifications.{$slug}.body",  $vars, $locale),
            'data'  => $this->payload($notifiable),
        ];
    }

    /**
     * FCM requires `data` values to be strings — flatten and coerce.
     */
    public function toFcm(mixed $notifiable): array
    {
        $db = $this->toDatabase($notifiable);

        $data = array_merge(['type' => $db['type']], $db['data']);
        $data = array_map(fn($v) => (string) ($v ?? ''), $data);

        return [
            'title' => $db['title'],
            'body'  => $db['body'],
            'data'  => $data,
        ];
    }
}
