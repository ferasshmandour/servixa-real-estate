<?php

namespace App\Http\Requests\API\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'business_account_id' => ['required', 'integer', 'exists:business_accounts,id'],
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id'      => ['nullable', 'integer', 'exists:categories,id'],
            'title_ar'            => ['required', 'string', 'max:255'],
            'title_en'            => ['required', 'string', 'max:255'],
            'description_ar'      => ['required', 'string'],
            'description_en'      => ['required', 'string'],
            'available_quantity'  => ['nullable', 'integer', 'min:1'],
            'main_image'          => ['required', 'image', 'max:5120'],
            'images'              => ['nullable', 'array'],
            'images.*'            => ['image', 'max:5120'],
            'type'                => ['required', 'in:sale,rent'],
            'price_syp'           => ['nullable', 'numeric', 'min:0'],
            'price_usd'           => ['nullable', 'numeric', 'min:0'],
            'latitude'            => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'           => ['nullable', 'numeric', 'between:-180,180'],
            'dynamic_values'      => ['nullable', 'array'],
            'dynamic_values.*.field_id' => ['required', 'integer', 'exists:dynamic_fields,id'],
            'dynamic_values.*.value'    => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->price_syp) && empty($this->price_usd)) {
                $validator->errors()->add('price_syp', 'At least one price (SYP or USD) must be provided.');
            }

            // Verify business account belongs to the authenticated user and is approved
            if ($this->business_account_id) {
                $account = $this->user()->businessAccounts()->find($this->business_account_id);
                if (!$account) {
                    $validator->errors()->add('business_account_id', 'This business account does not belong to you.');
                } elseif ($account->status !== 'approved') {
                    $validator->errors()->add('business_account_id', 'You can only post services through an approved business account.');
                }
            }
        });
    }
}
