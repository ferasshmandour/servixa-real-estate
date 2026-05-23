<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'service_id' => $this->service_id,
            'reason'     => $this->reason,
            'status'     => $this->status,
            'admin_note' => $this->when(in_array($this->status, ['approved', 'rejected'], true), $this->admin_note),
            'service'    => new ServiceResource($this->whenLoaded('service')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
