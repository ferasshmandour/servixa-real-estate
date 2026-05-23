<?php

namespace App\Services;

use App\Events\ReportApproved;
use App\Events\ReportRejected;
use App\Events\ServiceRejected;
use App\Events\ServiceReported;
use App\Models\Report;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ─── API — User submission ───────────────────────────────────────────────

    public function create(User $user, array $data): Report
    {
        $service = Service::with('businessAccount')->findOrFail($data['service_id']);

        abort_if($service->status !== 'approved', 422, 'You can only report approved services.');
        abort_if(
            $service->businessAccount?->user_id === $user->id,
            422,
            'You cannot report your own service.'
        );

        $report = Report::create([
            'user_id'    => $user->id,
            'service_id' => $service->id,
            'reason'     => $data['reason'],
            'status'     => 'pending',
        ]);

        ServiceReported::dispatch($report);

        return $report;
    }

    // ─── Admin ───────────────────────────────────────────────────────────────

    public function listForAdmin(?string $status = null, ?string $search = null): LengthAwarePaginator
    {
        return Report::with(['user', 'service.businessAccount'])
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->whereHas(
                'service',
                fn($s) => $s->whereRaw("JSON_SEARCH(LOWER(title), 'one', LOWER(?)) IS NOT NULL", ["%{$search}%"])
            ))
            ->latest()
            ->paginate(15);
    }

    public function approveReport(Report $report, ?string $adminNote): void
    {
        abort_if($report->status !== 'pending', 422, 'This report has already been reviewed.');

        DB::transaction(function () use ($report, $adminNote) {
            $report->update([
                'status'     => 'approved',
                'admin_note' => $adminNote,
            ]);

            $service = $report->service;

            if ($service && $service->status === 'approved') {
                $service->update([
                    'status'           => 'rejected',
                    'rejection_reason' => 'Removed due to user reports.',
                ]);

                ServiceRejected::dispatch($service->fresh());
            }
        });

        ReportApproved::dispatch($report->fresh()->load('service'));
    }

    public function rejectReport(Report $report, string $adminNote): void
    {
        abort_if($report->status !== 'pending', 422, 'This report has already been reviewed.');

        $report->update([
            'status'     => 'rejected',
            'admin_note' => $adminNote,
        ]);

        ReportRejected::dispatch($report->fresh()->load('service'));
    }
}
