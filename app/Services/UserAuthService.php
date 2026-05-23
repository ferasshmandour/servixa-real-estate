<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Session login for marketplace users on the web chat UI (phone + password,
 * no OTP). Uses the `web` guard — entirely separate from Passport (mobile API)
 * and the admin `tab-session` guard.
 */
class UserAuthService
{
    public function login(string $phone, string $password, bool $remember = false): bool
    {
        return Auth::guard('web')->attempt(
            ['phone' => $phone, 'password' => $password],
            $remember
        );
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}
