<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Report\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function store(StoreReportRequest $request): JsonResponse
    {
        $report = $this->reportService->create($request->user(), $request->validated());

        return $this->success(
            new ReportResource($report),
            'Report submitted. Our team will review it shortly.',
            201
        );
    }
}
