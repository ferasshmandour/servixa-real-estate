<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CityService
{
    public function list(?string $search = null): LengthAwarePaginator
    {
        return City::query()
            ->when($search, fn($q) => $q->where(fn($q) => $q
                ->where('name_ar', 'LIKE', "%{$search}%")
                ->orWhere('name_en', 'LIKE', "%{$search}%")
            ))
            ->orderBy('id')
            ->paginate(15);
    }

    public function allForApi(): Collection
    {
        return City::orderBy('id')->get();
    }

    public function create(array $data): City
    {
        $city = new City();
        $city->setTranslation('name', 'ar', $data['name_ar'])
             ->setTranslation('name', 'en', $data['name_en']);
        $city->save();

        return $city;
    }

    public function update(City $city, array $data): City
    {
        $city->setTranslation('name', 'ar', $data['name_ar'])
             ->setTranslation('name', 'en', $data['name_en']);
        $city->save();

        return $city;
    }

    public function delete(City $city): void
    {
        abort_if(
            $city->businessAccounts()->exists(),
            422,
            'Cannot delete a city that has associated business accounts.'
        );

        $city->delete();
    }
}
