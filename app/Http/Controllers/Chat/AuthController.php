<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\LoginRequest;
use App\Services\UserAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private UserAuthService $userAuthService) {}

    public function showLogin(): View
    {
        return view('chat.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $ok = $this->userAuthService->login(
            $data['phone'],
            $data['password'],
            (bool) ($data['remember'] ?? false),
        );

        if (! $ok) {
            return back()
                ->withErrors(['phone' => __('auth.failed')])
                ->onlyInput('phone');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('chat.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->userAuthService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('chat.login');
    }
}
