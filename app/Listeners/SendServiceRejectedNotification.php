<?php

namespace App\Listeners;

use App\Events\ServiceRejected;
use App\Notifications\ServiceRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendServiceRejectedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(ServiceRejected $event): void
    {
        $owner = $event->service->businessAccount?->user;

        if (!$owner) {
            return;
        }

        $owner->notify(new ServiceRejectedNotification($event->service));
    }
}
