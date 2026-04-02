<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityType\StoreActivityTypeRequest;
use App\Http\Requests\Admin\ActivityType\UpdateActivityTypeRequest;
use App\Models\ActivityType;
use App\Services\ActivityTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityTypeController extends Controller
{
    public function __construct(private ActivityTypeService $service) {}

    public function index(Request $request): View
    {
        $activityTypes = $this->service->list($request->get('search'));

        return view('activity-types.index', compact('activityTypes'));
    }

    public function create(): View
    {
        return view('activity-types.create');
    }

    public function store(StoreActivityTypeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Activity type created successfully.');
    }

    public function edit(ActivityType $activityType): View
    {
        return view('activity-types.edit', compact('activityType'));
    }

    public function update(UpdateActivityTypeRequest $request, ActivityType $activityType): RedirectResponse
    {
        $this->service->update($activityType, $request->validated());

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Activity type updated successfully.');
    }

    public function destroy(ActivityType $activityType): RedirectResponse
    {
        $this->service->delete($activityType);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Activity type deleted successfully.');
    }
}
