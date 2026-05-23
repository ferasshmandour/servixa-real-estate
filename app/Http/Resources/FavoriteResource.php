<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'service_id' => $this->service_id,
            'service'    => new ServiceResource($this->whenLoaded('service')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
