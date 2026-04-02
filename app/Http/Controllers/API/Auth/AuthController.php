<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\LoginVerifyRequest;
use App\Http\Requests\API\Auth\RefreshTokenRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Requests\API\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authService->register($request->validated());

        return $this->success(null, __('auth.registered'), 201);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $this->authService->verifyOtp(
            $request->phone,
            $request->otp,
        );

        return $this->success($data, __('auth.otp_verified'));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $this->authService->login($request->phone, $request->password);

        return $this->success(null, __('auth.otp_sent'));
    }

    public function loginVerify(LoginVerifyRequest $request): JsonResponse
    {
        $data = $this->authService->loginVerify(
            $request->phone,
            $request->otp,
        );

        return $this->success($data, __('auth.otp_verified'));
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $data = $this->authService->refreshToken($request->refresh_token);

        return $this->success($data, __('auth.token_refreshed'));
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
}
