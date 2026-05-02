<?php

namespace App\Listeners;

use App\Events\BusinessAccountApproved;
use App\Notifications\BusinessAccountApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBusinessAccountApprovedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(BusinessAccountApproved $event): void
    {
        $owner = $event->businessAccount->user;

        if (!$owner) {
            return;
        }

        $owner->notify(new BusinessAccountApprovedNotification($event->businessAccount));
    }
}
