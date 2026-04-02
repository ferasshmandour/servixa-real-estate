<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\BusinessAccount\StoreBusinessAccountRequest;
use App\Http\Requests\API\BusinessAccount\UpdateBusinessAccountRequest;
use App\Http\Resources\BusinessAccountResource;
use App\Models\BusinessAccount;
use App\Services\BusinessAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessAccountController extends Controller
{
    public function __construct(private BusinessAccountService $service) {}

    public function index(Request $request): JsonResponse
    {
        $accounts = $this->service->listForUser($request->user());

        return $this->success(BusinessAccountResource::collection($accounts));
    }

    public function store(StoreBusinessAccountRequest $request): JsonResponse
    {
        $account = $this->service->create($request->user(), $request->validated());

        return $this->success(new BusinessAccountResource($account), 'Business account submitted for review.', 201);
    }

    public function show(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        abort_if($businessAccount->user_id !== $request->user()->id, 403, 'You do not own this business account.');

        $businessAccount->load(['city', 'activityType', 'files']);

        return $this->success(new BusinessAccountResource($businessAccount));
    }

    public function update(UpdateBusinessAccountRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $account = $this->service->update($request->user(), $businessAccount, $request->validated());

        return $this->success(new BusinessAccountResource($account), 'Business account updated successfully.');
    }
}
