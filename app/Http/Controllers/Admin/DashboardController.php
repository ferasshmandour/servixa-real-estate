<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = Cache::remember('admin.dashboard_stats', 300, function () {
            $serviceCounts = Service::query()
                ->selectRaw("SUM(status = 'pending') as pending_services")
                ->selectRaw("SUM(status = 'approved') as approved_services")
                ->first();

            return [
                'pending_accounts'  => BusinessAccount::where('status', 'pending')->count(),
                'pending_services'  => (int) $serviceCounts->pending_services,
                'total_users'       => User::count(),
                'approved_services' => (int) $serviceCounts->approved_services,
            ];
        });

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
