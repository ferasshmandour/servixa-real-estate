<?php

namespace App\Listeners;

use App\Events\ServiceApproved;
use App\Notifications\ServiceApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendServiceApprovedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ServiceApproved $event): void
    {
        $owner = $event->service->businessAccount?->user;

        if (!$owner) {
            return;
        }

        $owner->notify(new ServiceApprovedNotification($event->service));
    }
}
