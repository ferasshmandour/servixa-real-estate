<?php

namespace App\Listeners;

use App\Events\ReportRejected;
use App\Notifications\ReportRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReportRejectedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ReportRejected $event): void
    {
        $reporter = $event->report->user;

        if (!$reporter) {
            return;
        }

        $reporter->notify(new ReportRejectedNotification($event->report));
    }
}
