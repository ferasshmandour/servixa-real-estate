<?php

namespace App\Services;

use App\Models\ActivityType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ActivityTypeService
{
    public function list(?string $search = null): LengthAwarePaginator
    {
        return ActivityType::query()
            ->when($search, fn($q) => $q->where(fn($q) => $q
                ->whereRaw("name->>'$.ar' LIKE ?", ["%{$search}%"])
                ->orWhereRaw("name->>'$.en' LIKE ?", ["%{$search}%"])
            ))
            ->orderBy('id')
            ->paginate(15);
    }

    public function allForApi(): Collection
    {
        return ActivityType::orderBy('id')->get();
    }

    public function create(array $data): ActivityType
    {
        $type = new ActivityType();
        $type->setTranslation('name', 'ar', $data['name_ar'])
             ->setTranslation('name', 'en', $data['name_en']);
        $type->save();

        return $type;
    }

    public function update(ActivityType $activityType, array $data): ActivityType
    {
        $activityType->setTranslation('name', 'ar', $data['name_ar'])
                     ->setTranslation('name', 'en', $data['name_en']);
        $activityType->save();

        return $activityType;
    }

    public function delete(ActivityType $activityType): void
    {
        abort_if(
            $activityType->businessAccounts()->exists(),
            422,
            'Cannot delete an activity type that has associated business accounts.'
        );

        $activityType->delete();
    }
}
