<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceDynamicValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'field_id'    => $this->dynamic_field_id,
            'field_label' => $this->whenLoaded('dynamicField', fn() => $this->dynamicField->label),
            'value'       => $this->value,
        ];
    }
}
