<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityTypeResource;
use App\Services\ActivityTypeService;
use Illuminate\Http\JsonResponse;

class ActivityTypeController extends Controller
{
    public function __construct(private ActivityTypeService $service) {}

    public function index(): JsonResponse
    {
        $activityTypes = $this->service->allForApi();
        return $this->success(ActivityTypeResource::collection($activityTypes));
    }
}
