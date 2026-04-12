<?php

namespace App\Services;

use App\Models\Slider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SliderService
{
    public function list(): LengthAwarePaginator
    {
        return Slider::orderBy('sort_order')->paginate(15);
    }

    public function create(array $data): Slider
    {
        $slider = Slider::create([
            'link'       => $data['link'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => $data['is_active'] ?? true,
        ]);

        $slider->addMedia($data['image'])
               ->toMediaCollection('image');

        return $slider;
    }

    public function update(Slider $slider, array $data): Slider
    {
        $slider->update([
            'link'       => $data['link'] ?? $slider->link,
            'sort_order' => $data['sort_order'] ?? $slider->sort_order,
            'is_active'  => $data['is_active'] ?? $slider->is_active,
        ]);

        // singleFile collection auto-deletes the old image
        if (isset($data['image'])) {
            $slider->addMedia($data['image'])
                   ->toMediaCollection('image');
        }

        return $slider;
    }

    public function delete(Slider $slider): void
    {
        // Media Library automatically deletes the image file
        $slider->delete();
    }
}
