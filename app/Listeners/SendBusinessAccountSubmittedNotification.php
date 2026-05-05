<?php

namespace App\Listeners;

use App\Events\BusinessAccountSubmitted;
use App\Models\Admin;
use App\Notifications\BusinessAccountSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendBusinessAccountSubmittedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(BusinessAccountSubmitted $event): void
    {
        $admins = Admin::recipientsForPermission('manage-business-accounts');

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new BusinessAccountSubmittedNotification($event->businessAccount));
    }
}
