<?php

namespace App\Listeners;

use App\Events\ReportApproved;
use App\Notifications\ReportApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReportApprovedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ReportApproved $event): void
    {
        $reporter = $event->report->user;

        if (!$reporter) {
            return;
        }

        $reporter->notify(new ReportApprovedNotification($event->report));
    }
}
