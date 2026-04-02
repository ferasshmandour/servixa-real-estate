<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreDynamicFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label_ar'    => ['required', 'string', 'max:150'],
            'label_en'    => ['required', 'string', 'max:150'],
            'field_type'  => ['required', 'in:text,number,select,textarea,boolean'],
            'options_raw' => ['nullable', 'required_if:field_type,select', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ];
    }
}
