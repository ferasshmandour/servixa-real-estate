<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['ar' => 'عقارات', 'en' => 'Real Estate'],
                'children' => [
                    ['name' => ['ar' => 'شقق سكنية',  'en' => 'Apartments']],
                    ['name' => ['ar' => 'فلل ومنازل', 'en' => 'Villas & Houses']],
                    ['name' => ['ar' => 'أراضي',       'en' => 'Land']],
                    ['name' => ['ar' => 'محلات تجارية', 'en' => 'Commercial Shops']],
                    ['name' => ['ar' => 'مكاتب',       'en' => 'Offices']],
                ],
            ],
            [
                'name' => ['ar' => 'خدمات البناء', 'en' => 'Construction Services'],
                'children' => [
                    ['name' => ['ar' => 'مقاولات عامة',    'en' => 'General Contracting']],
                    ['name' => ['ar' => 'تصميم معماري',    'en' => 'Architectural Design']],
                    ['name' => ['ar' => 'تصميم داخلي',     'en' => 'Interior Design']],
                    ['name' => ['ar' => 'أعمال حديد',      'en' => 'Ironwork']],
                    ['name' => ['ar' => 'أعمال كهربائية',  'en' => 'Electrical Works']],
                    ['name' => ['ar' => 'أعمال سباكة',     'en' => 'Plumbing Works']],
                ],
            ],
            [
                'name' => ['ar' => 'مواد البناء', 'en' => 'Building Materials'],
                'children' => [
                    ['name' => ['ar' => 'حديد ومعادن',     'en' => 'Iron & Metals']],
                    ['name' => ['ar' => 'خرسانة وإسمنت',   'en' => 'Concrete & Cement']],
                    ['name' => ['ar' => 'طابوق وبلوك',     'en' => 'Bricks & Blocks']],
                    ['name' => ['ar' => 'رخام وسيراميك',   'en' => 'Marble & Ceramics']],
                    ['name' => ['ar' => 'دهانات وعوازل',   'en' => 'Paints & Insulation']],
                ],
            ],
            [
                'name' => ['ar' => 'تجهيزات ومعدات', 'en' => 'Equipment & Machinery'],
                'children' => [
                    ['name' => ['ar' => 'معدات ثقيلة',     'en' => 'Heavy Equipment']],
                    ['name' => ['ar' => 'أدوات يدوية',     'en' => 'Hand Tools']],
                    ['name' => ['ar' => 'معدات السقالات',  'en' => 'Scaffolding Equipment']],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $parent = Category::firstOrCreate(
                ['name->ar' => $categoryData['name']['ar'], 'parent_id' => null],
                ['name' => $categoryData['name']]
            );

            foreach ($categoryData['children'] as $child) {
                Category::firstOrCreate(
                    ['name->ar' => $child['name']['ar'], 'parent_id' => $parent->id],
                    ['name' => $child['name'], 'parent_id' => $parent->id]
                );
            }
        }
    }
}
