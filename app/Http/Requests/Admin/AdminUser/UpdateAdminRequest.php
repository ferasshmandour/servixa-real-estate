<?php

namespace App\Http\Requests\Admin\AdminUser;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:admins,email,' . $this->route('admin')->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id'  => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
