<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Service\ListServicesRequest;
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

    public function index(ListServicesRequest $request): JsonResponse
    {
        $request->user()?->loadMissing('favorites');

        $services = $this->service->listPublic($request->validated());

        return $this->success(ServiceResource::collection($services));
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        abort_if($service->status !== 'approved', 404, 'Service not found.');

        $service->load(['businessAccount', 'category', 'subcategory', 'dynamicValues.dynamicField']);
        $request->user()?->loadMissing('favorites');

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
