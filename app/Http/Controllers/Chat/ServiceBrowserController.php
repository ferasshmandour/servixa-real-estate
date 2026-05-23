<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Service\ListServicesRequest;
use App\Services\ServiceService;
use Illuminate\View\View;

class ServiceBrowserController extends Controller
{
    public function __construct(private ServiceService $serviceService) {}

    public function index(ListServicesRequest $request): View
    {
        $user = $request->user();

        $services = $this->serviceService->listPublic($request->validated());

        // Approved accounts power the "act as" picker; all account ids let the
        // view hide the Chat button on the user's own services.
        $myBusinessAccounts   = $user->businessAccounts()->where('status', 'approved')->get();
        $myBusinessAccountIds = $user->businessAccounts()->pluck('id');

        return view('chat.services.index', [
            'services'             => $services,
            'myBusinessAccounts'   => $myBusinessAccounts,
            'myBusinessAccountIds' => $myBusinessAccountIds,
            'filters'              => $request->validated(),
        ]);
    }
}
