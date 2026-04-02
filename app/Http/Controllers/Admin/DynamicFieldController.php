<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreDynamicFieldRequest;
use App\Http\Requests\Admin\Category\UpdateDynamicFieldRequest;
use App\Models\Category;
use App\Models\DynamicField;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DynamicFieldController extends Controller
{
    public function __construct(private CategoryService $service) {}

    public function index(Category $category): View
    {
        $fields = $category->dynamicFields()->orderBy('sort_order')->orderBy('id')->get();

        return view('categories.dynamic-fields.index', compact('category', 'fields'));
    }

    public function create(Category $category): View
    {
        return view('categories.dynamic-fields.create', compact('category'));
    }

    public function store(StoreDynamicFieldRequest $request, Category $category): RedirectResponse
    {
        $this->service->createField($category, $request->validated());

        return redirect()
            ->route('admin.categories.fields.index', $category)
            ->with('success', 'Field added successfully.');
    }

    public function edit(Category $category, DynamicField $field): View
    {
        return view('categories.dynamic-fields.edit', compact('category', 'field'));
    }

    public function update(UpdateDynamicFieldRequest $request, Category $category, DynamicField $field): RedirectResponse
    {
        $this->service->updateField($field, $request->validated());

        return redirect()
            ->route('admin.categories.fields.index', $category)
            ->with('success', 'Field updated successfully.');
    }

    public function destroy(Category $category, DynamicField $field): RedirectResponse
    {
        $this->service->deleteField($field);

        return redirect()
            ->route('admin.categories.fields.index', $category)
            ->with('success', 'Field deleted successfully.');
    }
}
