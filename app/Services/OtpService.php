<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OtpService
{
    public function generateAndSend(string $phone): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("otp_{$phone}", $otp, now()->addMinutes(10));

        Http::post('https://api.ultramsg.com/' . config('services.ultramsg.instance_id') . '/messages/chat', [
            'token' => config('services.ultramsg.token'),
            'to' => $phone,
            'body' => __('auth.otp_message', ['otp' => $otp]),
        ]);
    }

    public function verify(string $phone, string $otp): bool
    {
        $cached = Cache::get("otp_{$phone}");

        if ($cached && $cached === $otp) {
            Cache::forget("otp_{$phone}");
            return true;
        }

        return false;
    }
}
