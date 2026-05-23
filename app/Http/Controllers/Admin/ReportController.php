<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\ApproveReportRequest;
use App\Http\Requests\Admin\Report\RejectReportRequest;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $service) {}

    public function index(Request $request): View
    {
        $reports = $this->service->listForAdmin(
            status: $request->get('status'),
            search: $request->get('search')
        );

        return view('reports.index', compact('reports'));
    }

    public function show(Report $report): View
    {
        $report->load(['user', 'service.businessAccount.user', 'service.media']);

        return view('reports.show', compact('report'));
    }

    public function approve(ApproveReportRequest $request, Report $report): RedirectResponse
    {
        $this->service->approveReport($report, $request->validated()['admin_note'] ?? null);

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Report approved and the reported service has been removed.');
    }

    public function reject(RejectReportRequest $request, Report $report): RedirectResponse
    {
        $this->service->rejectReport($report, $request->validated()['admin_note']);

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Report dismissed.');
    }
}
