<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => ['ar' => 'مورد مواد بناء',  'en' => 'Building Materials Supplier']],
            ['name' => ['ar' => 'مقاول',            'en' => 'Contractor']],
            ['name' => ['ar' => 'وسيط عقاري',       'en' => 'Real Estate Broker']],
            ['name' => ['ar' => 'مصمم داخلي',       'en' => 'Interior Designer']],
            ['name' => ['ar' => 'شركة تشييد',       'en' => 'Construction Company']],
            ['name' => ['ar' => 'مستشار هندسي',     'en' => 'Engineering Consultant']],
            ['name' => ['ar' => 'شركة صيانة',       'en' => 'Maintenance Company']],
            ['name' => ['ar' => 'مطور عقاري',       'en' => 'Real Estate Developer']],
        ];

        foreach ($types as $type) {
            ActivityType::firstOrCreate(
                ['name->ar' => $type['name']['ar']],
                ['name' => $type['name']]
            );
        }
    }
}
