<?php

namespace App\Listeners;

use App\Events\ServiceReported;
use App\Models\Admin;
use App\Notifications\ServiceReportedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendServiceReportedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ServiceReported $event): void
    {
        $admins = Admin::recipientsForPermission('manage-reports');

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new ServiceReportedNotification($event->report));
    }
}
