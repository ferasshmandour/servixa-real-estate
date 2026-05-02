<?php

namespace App\Notifications;

use App\Models\BusinessAccount;

class BusinessAccountRejectedNotification extends BaseNotification
{
    public function __construct(public BusinessAccount $businessAccount) {}

    protected function slug(): string
    {
        return 'business_account_rejected';
    }

    protected function vars(mixed $notifiable): array
    {
        return [
            'reason' => $this->businessAccount->rejection_reason ?? '-',
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'business_account_id' => $this->businessAccount->id,
            'rejection_reason'    => $this->businessAccount->rejection_reason,
            'deeplink'            => '/business-accounts/' . $this->businessAccount->id,
        ];
    }
}
