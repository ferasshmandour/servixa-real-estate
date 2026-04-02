<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AdminAuthService
{
    public function login(string $email, string $password): bool
    {
        return Auth::guard('admin')->attempt(['email' => $email, 'password' => $password]);
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }
}
