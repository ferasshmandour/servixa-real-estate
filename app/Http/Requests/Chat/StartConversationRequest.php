<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'service_id'                    => ['required', 'integer', 'exists:services,id'],
            // Ownership + approved-status enforced authoritatively in ChatService.
            'initiator_business_account_id' => ['nullable', 'integer', 'exists:business_accounts,id'],
        ];
    }
}
