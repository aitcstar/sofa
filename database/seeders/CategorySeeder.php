<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'غرف المعيشة',
                'name_en' => 'Living Rooms',
                'description_ar' => 'أثاث غرف المعيشة الفاخر والعصري',
                'description_en' => 'Luxury and modern living room furniture',
                'slug' => 'living-rooms',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'غرف النوم',
                'name_en' => 'Bedrooms',
                'description_ar' => 'أثاث غرف النوم المريح والأنيق',
                'description_en' => 'Comfortable and elegant bedroom furniture',
                'slug' => 'bedrooms',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'غرف الطعام',
                'name_en' => 'Dining Rooms',
                'description_ar' => 'طاولات وكراسي غرف الطعام الفاخرة',
                'description_en' => 'Luxury dining room tables and chairs',
                'slug' => 'dining-rooms',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'المكاتب',
                'name_en' => 'Offices',
                'description_ar' => 'أثاث المكاتب العملي والأنيق',
                'description_en' => 'Practical and elegant office furniture',
                'slug' => 'offices',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
