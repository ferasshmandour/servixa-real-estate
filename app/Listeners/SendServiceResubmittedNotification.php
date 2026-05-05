<?php

namespace App\Listeners;

use App\Events\ServiceResubmitted;
use App\Models\Admin;
use App\Notifications\ServiceResubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendServiceResubmittedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ServiceResubmitted $event): void
    {
        $admins = Admin::recipientsForPermission('manage-services');

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new ServiceResubmittedNotification($event->service));
    }
}
