<?php

namespace App\Notifications;

use App\Models\Report;

class ReportApprovedNotification extends BaseNotification
{
    public function __construct(public Report $report) {}

    protected function slug(): string
    {
        return 'report_approved';
    }

    protected function vars(mixed $notifiable): array
    {
        $locale = $notifiable->locale ?? config('app.fallback_locale', 'ar');

        return [
            'title' => $this->report->service?->getTranslation('title', $locale) ?? '',
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'report_id'  => $this->report->id,
            'service_id' => $this->report->service_id,
            'deeplink'   => '/reports/' . $this->report->id,
        ];
    }
}
