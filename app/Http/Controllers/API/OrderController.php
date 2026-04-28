<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreOrderRequest;
use App\Http\Requests\API\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated(), $request->user());

        return $this->success(new OrderResource($order), 'Order placed successfully.', 201);
    }

    public function received(Request $request): JsonResponse
    {
        $orders = $this->orderService->getReceivedOrders($request->user());

        return $this->success(OrderResource::collection($orders));
    }

    public function sent(Request $request): JsonResponse
    {
        $orders = $this->orderService->getSentOrders($request->user());

        return $this->success(OrderResource::collection($orders));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $status = $request->validated()['status'];

        $order = $status === 'accepted'
            ? $this->orderService->acceptOrder($id, $request->user())
            : $this->orderService->rejectOrder($id, $request->user());

        $message = $status === 'accepted' ? 'Order accepted.' : 'Order rejected.';

        return $this->success(new OrderResource($order), $message);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->orderService->deleteOrder($id, $request->user());

        return $this->success(null, 'Order deleted successfully.');
    }
}
