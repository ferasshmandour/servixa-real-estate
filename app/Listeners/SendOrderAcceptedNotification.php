<?php

namespace App\Listeners;

use App\Events\OrderAccepted;
use App\Notifications\OrderAcceptedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderAcceptedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(OrderAccepted $event): void
    {
        $requester = $event->order->requesterBusiness?->user;

        if (!$requester) {
            return;
        }

        $requester->notify(new OrderAcceptedNotification($event->order));
    }
}
