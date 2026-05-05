<?php

namespace App\Listeners;

use App\Events\ServiceSubmitted;
use App\Models\Admin;
use App\Notifications\ServiceSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendServiceSubmittedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ServiceSubmitted $event): void
    {
        $admins = Admin::recipientsForPermission('manage-services');

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new ServiceSubmittedNotification($event->service));
    }
}
