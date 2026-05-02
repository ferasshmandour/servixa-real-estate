<?php

namespace App\Notifications;

use App\Models\Service;

class ServiceResubmittedNotification extends BaseNotification
{
    public function __construct(public Service $service) {}

    protected function slug(): string
    {
        return 'service_resubmitted';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');
        $owner  = $this->service->businessAccount?->user;

        return [
            'user'  => trim(($owner->first_name ?? '') . ' ' . ($owner->last_name ?? '')),
            'title' => $this->service->getTranslation('title', $locale),
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'service_id' => $this->service->id,
            'deeplink'   => '/admin/services/' . $this->service->id,
        ];
    }
}
