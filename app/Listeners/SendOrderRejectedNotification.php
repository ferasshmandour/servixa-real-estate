<?php

namespace App\Listeners;

use App\Events\OrderRejected;
use App\Notifications\OrderRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderRejectedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(OrderRejected $event): void
    {
        $requester = $event->order->requesterBusiness?->user;

        if (!$requester) {
            return;
        }

        $requester->notify(new OrderRejectedNotification($event->order));
    }
}
