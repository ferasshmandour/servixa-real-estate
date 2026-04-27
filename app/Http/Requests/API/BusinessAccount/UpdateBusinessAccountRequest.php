<?php

namespace App\Http\Requests\API\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'name_ar'          => ['sometimes', 'string', 'max:200'],
            'name_en'          => ['sometimes', 'string', 'max:200'],
            'activities_ar'    => ['sometimes', 'string'],
            'activities_en'    => ['sometimes', 'string'],
            'details_ar'       => ['sometimes', 'string'],
            'details_en'       => ['sometimes', 'string'],
            'city_id'          => ['sometimes', 'exists:cities,id'],
            'activity_type_id' => ['sometimes', 'exists:activity_types,id'],
            'license_number'   => ['sometimes', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:255'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'files'            => ['nullable', 'array', 'max:10'],
            'files.*'          => ['file', 'max:10240', 'mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/zip,application/octet-stream'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $account = $this->route('businessAccount');

            if (!$account) {
                return;
            }

            if ($account->user_id !== auth('api')->id()) {
                $validator->errors()->add('authorization', 'You do not own this business account.');
            }

            if ($account->status === 'approved') {
                $validator->errors()->add('status', 'Approved accounts cannot be edited.');
            }
        });
    }
}
