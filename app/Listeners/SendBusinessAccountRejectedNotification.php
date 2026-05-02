<?php

namespace App\Listeners;

use App\Events\BusinessAccountRejected;
use App\Notifications\BusinessAccountRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBusinessAccountRejectedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(BusinessAccountRejected $event): void
    {
        $owner = $event->businessAccount->user;

        if (!$owner) {
            return;
        }

        $owner->notify(new BusinessAccountRejectedNotification($event->businessAccount));
    }
}
