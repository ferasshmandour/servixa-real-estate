<?php

namespace App\Http\Requests\API\Service;

use Illuminate\Foundation\Http\FormRequest;

class ListServicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'      => ['nullable', 'integer', 'exists:categories,id'],
            'subcategory_id'   => ['nullable', 'integer', 'exists:categories,id'],
            'city_id'          => ['nullable', 'integer', 'exists:cities,id'],
            'activity_type_id' => ['nullable', 'integer', 'exists:activity_types,id'],
            'type'             => ['nullable', 'string', 'in:sale,rent'],
            'price_syp_min'    => ['nullable', 'numeric', 'min:0'],
            'price_syp_max'    => ['nullable', 'numeric', 'min:0'],
            'price_usd_min'    => ['nullable', 'numeric', 'min:0'],
            'price_usd_max'    => ['nullable', 'numeric', 'min:0'],
            'min_rating'       => ['nullable', 'numeric', 'min:1', 'max:5'],
            'sort_by'          => ['nullable', 'string', 'in:newest,oldest,price_asc,price_desc'],
            'search'           => ['nullable', 'string', 'max:200'],
        ];
    }
}
