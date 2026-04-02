<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = Cache::remember('admin.dashboard_stats', 300, fn() => [
            'pending_accounts'  => BusinessAccount::where('status', 'pending')->count(),
            'pending_services'  => Service::where('status', 'pending')->count(),
            'total_users'       => User::count(),
            'approved_services' => Service::where('status', 'approved')->count(),
        ]);

        $recentPendingAccounts = BusinessAccount::with(['user', 'city', 'activityType'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $recentPendingServices = Service::with(['businessAccount', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentPendingAccounts', 'recentPendingServices'));
    }
}
