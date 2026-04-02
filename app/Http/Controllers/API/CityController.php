<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Services\CityService;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function __construct(private CityService $service) {}

    public function index(): JsonResponse
    {
        $cities = $this->service->allForApi();

        return $this->success(CityResource::collection($cities));
    }
}
