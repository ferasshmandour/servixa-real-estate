<?php

namespace App\Notifications;

use App\Models\Order;

class OrderAcceptedNotification extends BaseNotification
{
    public function __construct(public Order $order) {}

    protected function slug(): string
    {
        return 'order_accepted';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');

        return [
            'title' => $this->order->service?->getTranslation('title', $locale) ?? '',
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'order_id'   => $this->order->id,
            'service_id' => $this->order->service_id,
            'deeplink'   => '/orders/sent/' . $this->order->id,
        ];
    }
}
