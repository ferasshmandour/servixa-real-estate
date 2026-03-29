<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\LoginVerifyRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Requests\API\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function __construct(
        private OtpService $otpService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $this->otpService->generateAndSend($user->phone);

        return $this->success(null, __('auth.registered'), 201);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        if (!$this->otpService->verify($request->phone, $request->otp)) {
            return $this->error(__('auth.otp_invalid'), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        $user->update(['is_verified' => true]);

        $tokens = $this->issueTokens($user->phone, $request->password ?? null);

        if (!$tokens) {
            // Fallback to personal access token if password grant fails
            $tokenResult = $user->createToken('Personal Access Token');
            return $this->success([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'user' => $user,
            ], __('auth.otp_verified'));
        }

        $tokens['user'] = $user;

        return $this->success($tokens, __('auth.otp_verified'));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error(__('auth.failed'), 401);
        }

        if (!$user->is_verified) {
            return $this->error(__('auth.not_verified'), 403);
        }

        $this->otpService->generateAndSend($user->phone);

        return $this->success(null, __('auth.otp_sent'));
    }

    public function loginVerify(LoginVerifyRequest $request): JsonResponse
    {
        if (!$this->otpService->verify($request->phone, $request->otp)) {
            return $this->error(__('auth.otp_invalid'), 422);
        }

        $user = User::where('phone', $request->phone)->first();

        $tokens = $this->issueTokens($user->phone, $request->password ?? null);

        if (!$tokens) {
            $tokenResult = $user->createToken('Personal Access Token');
            return $this->success([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'user' => $user,
            ], __('auth.otp_verified'));
        }

        $tokens['user'] = $user;

        return $this->success($tokens, __('auth.otp_verified'));
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'scope' => '',
        ]);

        if ($response->failed()) {
            return $this->error(__('auth.refresh_failed'), 401);
        }

        return $this->success($response->json(), __('auth.token_refreshed'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return $this->success(null, __('auth.logged_out'));
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }

    private function issueTokens(string $phone, ?string $password = null): ?array
    {
        $clientId = config('passport.password_grant_client.id');
        $clientSecret = config('passport.password_grant_client.secret');

        if (!$clientId || !$clientSecret || !$password) {
            return null;
        }

        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $phone,
            'password' => $password,
            'scope' => '',
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
