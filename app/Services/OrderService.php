<?php

namespace App\Services;

use App\Events\OrderAccepted;
use App\Events\OrderReceived;
use App\Events\OrderRejected;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function createOrder(array $data, User $user): Order
    {
        $requesterBusiness = $user->businessAccounts()->findOrFail($data['requester_business_id']);

        abort_if(
            $requesterBusiness->status !== 'approved',
            422,
            'You can only place orders through an approved business account.'
        );

        $service = \App\Models\Service::findOrFail($data['service_id']);

        abort_if(
            $service->status !== 'approved',
            422,
            'This service is not available.'
        );

        abort_if(
            $service->business_account_id === $requesterBusiness->id,
            422,
            'You cannot order your own service.'
        );

        abort_if(
            $data['quantity'] > $service->available_quantity,
            422,
            "Requested quantity exceeds the available quantity ({$service->available_quantity})."
        );

        $order = Order::create([
            'service_id'            => $service->id,
            'requester_business_id' => $requesterBusiness->id,
            'needed_at'             => $data['needed_at'],
            'quantity'              => $data['quantity'],
            'details'               => $data['details'] ?? null,
            'status'                => 'pending',
        ]);

        OrderReceived::dispatch($order);

        return $order->load(['service', 'requesterBusiness', 'rating.user']);
    }

    public function getReceivedOrders(User $user): LengthAwarePaginator
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');

        return Order::with(['service', 'requesterBusiness', 'rating.user'])
            ->whereHas('service', fn($q) => $q->whereIn('business_account_id', $businessAccountIds))
            ->latest()
            ->paginate(15);
    }

    public function getSentOrders(User $user): LengthAwarePaginator
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');

        return Order::with(['service', 'requesterBusiness', 'rating.user'])
            ->whereIn('requester_business_id', $businessAccountIds)
            ->latest()
            ->paginate(15);
    }

    public function getOrder(int $orderId, User $user): Order
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');

        $order = Order::with(['service', 'requesterBusiness', 'rating.user'])->findOrFail($orderId);

        abort_if(
            !$businessAccountIds->contains($order->requester_business_id) &&
            !$businessAccountIds->contains($order->service->business_account_id),
            403,
            'You do not have permission to view this order.'
        );

        return $order;
    }

    public function acceptOrder(int $orderId, User $user): Order
    {
        $order = Order::with(['service.businessAccount', 'requesterBusiness', 'rating.user'])->findOrFail($orderId);

        abort_if(
            $order->service->businessAccount->user_id !== $user->id,
            403,
            'You do not have permission to accept this order.'
        );

        abort_if(
            $order->status !== 'pending',
            422,
            'Only pending orders can be accepted.'
        );

        $order->update(['status' => 'accepted']);

        OrderAccepted::dispatch($order->fresh());

        return $order->fresh(['service', 'requesterBusiness', 'rating.user']);
    }

    public function rejectOrder(int $orderId, User $user): Order
    {
        $order = Order::with(['service.businessAccount', 'requesterBusiness', 'rating.user'])->findOrFail($orderId);

        abort_if(
            $order->service->businessAccount->user_id !== $user->id,
            403,
            'You do not have permission to reject this order.'
        );

        abort_if(
            $order->status !== 'pending',
            422,
            'Only pending orders can be rejected.'
        );

        $order->update(['status' => 'rejected']);

        OrderRejected::dispatch($order->fresh());

        return $order->fresh(['service', 'requesterBusiness', 'rating.user']);
    }

    public function deleteOrder(int $orderId, User $user): void
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');

        $order = Order::findOrFail($orderId);

        abort_if(
            !$businessAccountIds->contains($order->requester_business_id),
            403,
            'You do not have permission to delete this order.'
        );

        abort_if(
            $order->status !== 'pending',
            422,
            'Only pending orders can be deleted.'
        );

        $order->delete();
    }
}
