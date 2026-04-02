<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessAccount\ApproveRequest;
use App\Http\Requests\Admin\BusinessAccount\RejectRequest;
use App\Models\BusinessAccount;
use App\Services\BusinessAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessAccountController extends Controller
{
    public function __construct(private BusinessAccountService $service) {}

    public function index(Request $request): View
    {
        $accounts = $this->service->listForAdmin(
            status: $request->get('status'),
            search: $request->get('search')
        );

        return view('business-accounts.index', compact('accounts'));
    }

    public function show(BusinessAccount $businessAccount): View
    {
        $businessAccount->load(['user', 'city', 'activityType', 'files']);

        return view('business-accounts.show', compact('businessAccount'));
    }

    public function approve(ApproveRequest $request, BusinessAccount $businessAccount): RedirectResponse
    {
        $this->service->approve($businessAccount);

        return redirect()
            ->route('admin.business-accounts.show', $businessAccount)
            ->with('success', 'Business account approved successfully.');
    }

    public function reject(RejectRequest $request, BusinessAccount $businessAccount): RedirectResponse
    {
        $this->service->reject($businessAccount, $request->validated()['rejection_reason']);

        return redirect()
            ->route('admin.business-accounts.show', $businessAccount)
            ->with('success', 'Business account rejected.');
    }
}
