<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private OtpService $otpService,
    ) {}

    public function register(array $data): void
    {
        $user = User::create($data);
        $this->otpService->generateAndSend($user->phone);
    }

    public function verifyOtp(string $phone, string $otp): array
    {
        abort_unless($this->otpService->verify($phone, $otp), 422, __('auth.otp_invalid'));

        $user = User::where('phone', $phone)->firstOrFail();
        $user->update(['is_verified' => true]);

        return $this->buildTokenResponse($user);
    }

    public function login(string $phone, string $password): void
    {
        $user = User::where('phone', $phone)->first();

        abort_if(!$user || !Hash::check($password, $user->password), 401, __('auth.failed'));
        abort_unless($user->is_verified, 403, __('auth.not_verified'));

        $this->otpService->generateAndSend($phone);
    }

    public function loginVerify(string $phone, string $otp): array
    {
        abort_unless($this->otpService->verify($phone, $otp), 422, __('auth.otp_invalid'));

        $user = User::where('phone', $phone)->firstOrFail();

        return $this->buildTokenResponse($user);
    }

    public function refreshToken(string $refreshToken): array
    {
        $decoded = json_decode(base64_decode($refreshToken), true);

        abort_if(!$decoded || !isset($decoded['user_id'], $decoded['token']), 401, __('auth.refresh_failed'));

        $stored = Cache::get("refresh_token_{$decoded['user_id']}");

        abort_if($stored !== $refreshToken, 401, __('auth.refresh_failed'));

        Cache::forget("refresh_token_{$decoded['user_id']}");

        $user = User::findOrFail($decoded['user_id']);

        return $this->buildTokenResponse($user);
    }

    private function buildTokenResponse(User $user): array
    {
        $user->tokens()->where('revoked', false)->each(function ($token) {
            $token->revoke();
        });

        $tokenResult = $user->createToken('Personal Access Token');

        $refreshToken = base64_encode(json_encode([
            'user_id' => $user->id,
            'token'   => Str::random(40),
        ]));

        Cache::put("refresh_token_{$user->id}", $refreshToken, now()->addDays(30));

        return [
            'access_token'  => $tokenResult->accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'Bearer',
            'user'          => $user,
        ];
    }
}
