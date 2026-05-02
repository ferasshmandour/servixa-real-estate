<?php

namespace App\Notifications;

use App\Models\Service;

class ServiceRejectedNotification extends BaseNotification
{
    public function __construct(public Service $service) {}

    protected function slug(): string
    {
        return 'service_rejected';
    }

    protected function vars(mixed $notifiable): array
    {
        return [
            'reason' => $this->service->rejection_reason ?? '-',
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'service_id'       => $this->service->id,
            'rejection_reason' => $this->service->rejection_reason,
            'deeplink'         => '/services/' . $this->service->id,
        ];
    }
}
