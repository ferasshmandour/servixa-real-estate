<?php

namespace App\Notifications;

use App\Models\Rating;

class RatingAddedNotification extends BaseNotification
{
    public function __construct(public Rating $rating) {}

    protected function slug(): string
    {
        return 'rating_added';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');
        $rater  = $this->rating->user;
        $title  = $this->rating->service?->getTranslation('title', $locale) ?? '';

        return [
            'user'   => trim(($rater->first_name ?? '') . ' ' . ($rater->last_name ?? '')),
            'title'  => $title,
            'rating' => $this->rating->rating,
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'rating_id'  => $this->rating->id,
            'service_id' => $this->rating->service_id,
            'order_id'   => $this->rating->order_id,
            'deeplink'   => '/services/' . $this->rating->service_id,
        ];
    }
}
