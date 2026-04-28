<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'status'    => $this->status,
            'needed_at' => $this->needed_at?->toDateString(),
            'quantity'  => $this->quantity,
            'details'   => $this->details,
            'created_at' => $this->created_at->toIso8601String(),
            'service'   => $this->when($this->relationLoaded('service'), fn() => [
                'id'        => $this->service->id,
                'title'     => $this->service->title,
                'type'      => $this->service->type,
                'price_syp' => $this->service->price_syp,
                'price_usd' => $this->service->price_usd,
                'main_image' => $this->service->getFirstMediaUrl('main-image') ?: null,
            ]),
            'requester_business' => $this->when($this->relationLoaded('requesterBusiness'), fn() => [
                'id'   => $this->requesterBusiness->id,
                'name' => $this->requesterBusiness->name,
            ]),
            'rating' => $this->when(
                $this->relationLoaded('rating') && $this->rating,
                fn() => new RatingResource($this->rating)
            ),
        ];
    }
}
