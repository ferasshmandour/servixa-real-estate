<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'description'        => $this->description,
            'available_quantity' => $this->available_quantity,
            'type'               => $this->type,
            'price_syp'          => $this->price_syp,
            'price_usd'          => $this->price_usd,
            'latitude'           => $this->latitude,
            'longitude'          => $this->longitude,
            'status'             => $this->status,
            'rejection_reason'   => $this->when($this->status === 'rejected', $this->rejection_reason),
            'main_image'         => $this->getFirstMediaUrl('main-image') ?: null,
            'images'             => $this->getMedia('additional-images')->map(fn($m) => [
                'id'  => $m->id,
                'url' => $m->getUrl(),
            ]),
            'dynamic_values'     => ServiceDynamicValueResource::collection($this->whenLoaded('dynamicValues')),
            'business_account'   => new BusinessAccountResource($this->whenLoaded('businessAccount')),
            'category'           => new CategoryResource($this->whenLoaded('category')),
            'subcategory'        => new CategoryResource($this->whenLoaded('subcategory')),
            'avg_rating'         => round($this->ratings()->avg('rating') ?? 0, 1),
            'ratings_count'      => $this->ratings()->count(),
            'is_favorite'        => (bool) $request->user()?->favorites?->contains('service_id', $this->id),
            'created_at'         => $this->created_at->toIso8601String(),
        ];
    }
}
