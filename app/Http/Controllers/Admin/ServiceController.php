<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\ApproveServiceRequest;
use App\Http\Requests\Admin\Service\RejectServiceRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private ServiceService $service) {}

    public function index(Request $request): View
    {
        $services = $this->service->listForAdmin(
            status: $request->get('status'),
            search: $request->get('search')
        );

        return view('services.index', compact('services'));
    }

    public function show(Service $service): View
    {
        $service->load(['businessAccount.user', 'category', 'subcategory', 'images', 'dynamicValues.dynamicField']);

        return view('services.show', compact('service'));
    }

    public function approve(ApproveServiceRequest $request, Service $service): RedirectResponse
    {
        $this->service->approve($service);

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service approved successfully.');
    }

    public function reject(RejectServiceRequest $request, Service $service): RedirectResponse
    {
        $this->service->reject($service, $request->validated()['rejection_reason']);

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service rejected.');
    }
}
