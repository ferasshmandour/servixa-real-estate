<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'activities'       => $this->activities,
            'details'          => $this->details,
            'address'          => $this->address,
            'latitude'         => $this->latitude,
            'longitude'        => $this->longitude,
            'license_number'   => $this->license_number,
            'status'           => $this->status,
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),
            'city'             => new CityResource($this->whenLoaded('city')),
            'activity_type'    => new ActivityTypeResource($this->whenLoaded('activityType')),
            'files'            => BusinessAccountFileResource::collection($this->whenLoaded('files')),
            'created_at'       => $this->created_at->toIso8601String(),
        ];
    }
}
