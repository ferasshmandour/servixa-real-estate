<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AdminAuthService $adminAuthService,
    ) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(AdminLoginRequest $request)
    {
        if (!$this->adminAuthService->login($request->email, $request->password)) {
            return back()->withErrors(['email' => __('auth.failed')]);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $this->adminAuthService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
