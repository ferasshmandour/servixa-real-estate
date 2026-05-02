<?php

namespace App\Notifications;

use App\Models\BusinessAccount;

class BusinessAccountSubmittedNotification extends BaseNotification
{
    public function __construct(public BusinessAccount $businessAccount) {}

    protected function slug(): string
    {
        return 'business_account_submitted';
    }

    protected function vars(mixed $notifiable): array
    {
        $owner = $this->businessAccount->user;

        return [
            'user'    => trim(($owner->first_name ?? '') . ' ' . ($owner->last_name ?? '')),
            'license' => $this->businessAccount->license_number,
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'business_account_id' => $this->businessAccount->id,
            'deeplink'            => '/admin/business-accounts/' . $this->businessAccount->id,
        ];
    }
}
