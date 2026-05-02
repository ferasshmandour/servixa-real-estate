<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    /**
     * Paginated list of notifications for any notifiable (User or Admin).
     * Newest first.
     */
    public function list(Model $notifiable, int $perPage = 15): LengthAwarePaginator
    {
        return $notifiable->notifications()->paginate($perPage);
    }

    /**
     * Latest unread notifications, capped — used by the dashboard bell icon.
     */
    public function latestUnread(Model $notifiable, int $limit = 10)
    {
        return $notifiable->unreadNotifications()->limit($limit)->get();
    }

    /**
     * Latest notifications (read + unread) — used by the dropdown to show all recent activity.
     */
    public function latestRecent(Model $notifiable, int $limit = 10)
    {
        return $notifiable->notifications()->limit($limit)->get();
    }

    public function unreadCount(Model $notifiable): int
    {
        return $notifiable->unreadNotifications()->count();
    }

    /**
     * Mark one notification as read. Throws 404 if not owned by $notifiable.
     */
    public function markRead(Model $notifiable, string $notificationId): DatabaseNotification
    {
        /** @var DatabaseNotification|null $notification */
        $notification = $notifiable->notifications()->whereKey($notificationId)->first();

        abort_if($notification === null, 404, 'Notification not found.');

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return $notification;
    }

    public function markAllRead(Model $notifiable): int
    {
        return $notifiable->unreadNotifications()->update(['read_at' => now()]);
    }

    public function delete(Model $notifiable, string $notificationId): void
    {
        $notification = $notifiable->notifications()->whereKey($notificationId)->first();

        abort_if($notification === null, 404, 'Notification not found.');

        $notification->delete();
    }
}
