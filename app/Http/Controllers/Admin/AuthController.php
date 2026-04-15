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

        // Carry _tab through the redirect so the dashboard request also has it,
        // letting the TabAwareSessionGuard find the freshly-stored session key.
        $tab = substr(preg_replace('/[^a-zA-Z0-9\-]/', '', $request->input('_tab', '')), 0, 40);

        return redirect()->route('admin.dashboard', $tab ? ['_tab' => $tab] : []);
    }

    public function logout(Request $request)
    {
        // Logs out only the current tab's session key — other tabs stay logged in.
        $this->adminAuthService->logout();

        // Do NOT call session()->invalidate() here — that would wipe every tab's
        // session data. Regenerating the CSRF token is sufficient.
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
