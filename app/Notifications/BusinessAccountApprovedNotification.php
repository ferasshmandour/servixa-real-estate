<?php

namespace App\Notifications;

use App\Models\BusinessAccount;

class BusinessAccountApprovedNotification extends BaseNotification
{
    public function __construct(public BusinessAccount $businessAccount) {}

    protected function slug(): string
    {
        return 'business_account_approved';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');

        return [
            'name' => $this->businessAccount->getTranslation('name', $locale),
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'business_account_id' => $this->businessAccount->id,
            'deeplink'            => '/business-accounts/' . $this->businessAccount->id,
        ];
    }
}
