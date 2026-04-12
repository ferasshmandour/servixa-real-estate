<?php

namespace App\Http\Requests\API\Service;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'category_id'         => ['sometimes', 'integer', 'exists:categories,id'],
            'subcategory_id'      => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'title_ar'            => ['sometimes', 'string', 'max:255'],
            'title_en'            => ['sometimes', 'string', 'max:255'],
            'description_ar'      => ['sometimes', 'string'],
            'description_en'      => ['sometimes', 'string'],
            'available_quantity'  => ['sometimes', 'integer', 'min:1'],
            'main_image'          => ['sometimes', 'image', 'max:5120'],
            'images'              => ['sometimes', 'array'],
            'images.*'            => ['image', 'max:5120'],
            'type'                => ['sometimes', 'in:sale,rent'],
            'price_syp'           => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_usd'           => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'latitude'            => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude'           => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'dynamic_values'      => ['sometimes', 'array'],
            'dynamic_values.*.field_id' => ['required', 'integer', 'exists:dynamic_fields,id'],
            'dynamic_values.*.value'    => ['required', 'string'],
        ];
    }
}
