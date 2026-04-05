<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => ['ar' => 'دمشق',   'en' => 'Damascus']],
            ['name' => ['ar' => 'حلب',    'en' => 'Aleppo']],
            ['name' => ['ar' => 'حمص',    'en' => 'Homs']],
            ['name' => ['ar' => 'حماة',   'en' => 'Hama']],
            ['name' => ['ar' => 'اللاذقية', 'en' => 'Latakia']],
            ['name' => ['ar' => 'طرطوس',  'en' => 'Tartus']],
            ['name' => ['ar' => 'دير الزور', 'en' => 'Deir ez-Zor']],
            ['name' => ['ar' => 'الرقة',  'en' => 'Raqqa']],
            ['name' => ['ar' => 'السويداء', 'en' => 'As-Suwayda']],
            ['name' => ['ar' => 'درعا',   'en' => 'Daraa']],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(
                ['name->ar' => $city['name']['ar']],
                ['name' => $city['name']]
            );
        }
    }
}
