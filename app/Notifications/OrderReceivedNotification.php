<?php

namespace App\Notifications;

use App\Models\Order;

class OrderReceivedNotification extends BaseNotification
{
    public function __construct(public Order $order) {}

    protected function slug(): string
    {
        return 'order_received';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale    = $notifiable->locale ?? config('app.fallback_locale', 'ar');
        $requester = $this->order->requesterBusiness?->user;

        return [
            'user'     => trim(($requester->first_name ?? '') . ' ' . ($requester->last_name ?? '')),
            'title'    => $this->order->service?->getTranslation('title', $locale) ?? '',
            'quantity' => $this->order->quantity,
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'order_id'   => $this->order->id,
            'service_id' => $this->order->service_id,
            'deeplink'   => '/orders/received/' . $this->order->id,
        ];
    }
}
