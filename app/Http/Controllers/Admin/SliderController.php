<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use App\Models\Slider;
use App\Services\SliderService;

class SliderController extends Controller
{
    public function index(SliderService $sliderService)
    {
        $sliders = $sliderService->list();

        return view('sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('sliders.create');
    }

    public function store(StoreSliderRequest $request, SliderService $sliderService)
    {
        $sliderService->create($request->validated());

        return redirect()->route('admin.sliders.index')
                         ->with('success', __('admin.slider_created'));
    }

    public function edit(Slider $slider)
    {
        return view('sliders.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request, Slider $slider, SliderService $sliderService)
    {
        $sliderService->update($slider, $request->validated());

        return redirect()->route('admin.sliders.index')
                         ->with('success', __('admin.slider_updated'));
    }

    public function destroy(Slider $slider, SliderService $sliderService)
    {
        $sliderService->delete($slider);

        return redirect()->route('admin.sliders.index')
                         ->with('success', __('admin.slider_deleted'));
    }
}
