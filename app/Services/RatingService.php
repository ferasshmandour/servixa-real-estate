<?php

namespace App\Services;

use App\Events\RatingAdded;
use App\Models\Order;
use App\Models\Rating;
use App\Models\User;

class RatingService
{
    public function createRating(array $data, User $user): Rating
    {
        $order = Order::with('requesterBusiness')->findOrFail($data['order_id']);

        abort_if(
            $order->status !== 'accepted',
            422,
            'You can only rate an order that has been accepted.'
        );

        abort_if(
            $order->requesterBusiness->user_id !== $user->id,
            403,
            'You can only rate orders you have placed.'
        );

        abort_if(
            Rating::where('order_id', $order->id)->exists(),
            422,
            'You have already rated this order.'
        );

        $rating = Rating::create([
            'order_id'   => $order->id,
            'service_id' => $order->service_id,
            'user_id'    => $user->id,
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null,
        ]);

        RatingAdded::dispatch($rating);

        return $rating->load('user');
    }
}
