<?php

namespace App\Notifications;

use App\Models\Service;

class ServiceApprovedNotification extends BaseNotification
{
    public function __construct(public Service $service) {}

    protected function slug(): string
    {
        return 'service_approved';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');

        return [
            'title' => $this->service->getTranslation('title', $locale),
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'deeplink'   => '/services/' . $this->service->id,
        ];
    }
}
