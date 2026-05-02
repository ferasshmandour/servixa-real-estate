<?php

namespace App\Listeners;

use App\Events\OrderReceived;
use App\Notifications\OrderReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderReceivedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(OrderReceived $event): void
    {
        $provider = $event->order->service?->businessAccount?->user;

        if (!$provider) {
            return;
        }

        $provider->notify(new OrderReceivedNotification($event->order));
    }
}
