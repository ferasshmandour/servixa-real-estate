<?php

namespace App\Listeners;

use App\Events\RatingAdded;
use App\Notifications\RatingAddedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRatingAddedNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(RatingAdded $event): void
    {
        $provider = $event->rating->order?->service?->businessAccount?->user;

        if (!$provider) {
            return;
        }

        $provider->notify(new RatingAddedNotification($event->rating));
    }
}
