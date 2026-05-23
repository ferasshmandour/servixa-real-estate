<?php

namespace App\Http\Requests\API\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'reason'     => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }
}
