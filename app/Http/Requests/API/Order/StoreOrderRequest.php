<?php

namespace App\Http\Requests\API\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'service_id'            => ['required', 'integer', 'exists:services,id'],
            'requester_business_id' => ['required', 'integer', 'exists:business_accounts,id'],
            'needed_at'             => ['required', 'date', 'after:today'],
            'quantity'              => ['required', 'integer', 'min:1'],
            'details'               => ['nullable', 'string', 'max:2000'],
        ];
    }
}
