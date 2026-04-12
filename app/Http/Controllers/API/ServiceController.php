<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Service\StoreServiceRequest;
use App\Http\Requests\API\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $service) {}

    public function index(Request $request): JsonResponse
    {
        $filters  = $request->only([
            'category_id', 'subcategory_id', 'type',
            'price_syp_min', 'price_syp_max',
            'price_usd_min', 'price_usd_max',
            'search',
        ]);
        $services = $this->service->listPublic($filters);

        return $this->success(ServiceResource::collection($services));
    }

    public function show(Service $service): JsonResponse
    {
        abort_if($service->status !== 'approved', 404, 'Service not found.');

        $service->load(['businessAccount', 'category', 'subcategory', 'dynamicValues.dynamicField']);

        return $this->success(new ServiceResource($service));
    }

    public function myServices(Request $request): JsonResponse
    {
        $services = $this->service->listForUser($request->user());

        return $this->success(ServiceResource::collection($services));
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->service->create($request->user(), $request->validated());

        return $this->success(new ServiceResource($service), 'Service submitted for review.', 201);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $updated = $this->service->update($request->user(), $service, $request->validated());

        return $this->success(new ServiceResource($updated), 'Service updated successfully.');
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        $this->service->delete($request->user(), $service);

        return $this->success(null, 'Service deleted successfully.');
    }
}
