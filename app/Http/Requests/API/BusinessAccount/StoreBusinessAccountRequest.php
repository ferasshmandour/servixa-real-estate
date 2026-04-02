<?php

namespace App\Http\Requests\API\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'name_ar'          => ['required', 'string', 'max:200'],
            'name_en'          => ['required', 'string', 'max:200'],
            'activities_ar'    => ['required', 'string'],
            'activities_en'    => ['required', 'string'],
            'details_ar'       => ['required', 'string'],
            'details_en'       => ['required', 'string'],
            'city_id'          => ['required', 'exists:cities,id'],
            'activity_type_id' => ['required', 'exists:activity_types,id'],
            'license_number'   => ['required', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:255'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'files'            => ['nullable', 'array', 'max:10'],
            'files.*'          => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }
}
