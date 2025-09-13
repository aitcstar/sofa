<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogCategory;

class BlogCategorySeeder extends Seeder
{

    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'تقنية',
                'name_en' => 'Technology',
                'slug_ar' => 'تقنية',
                'slug_en' => 'technology',
            ],
            [
                'name_ar' => 'تصميم',
                'name_en' => 'Design',
                'slug_ar' => 'تصميم',
                'slug_en' => 'design',
            ],
            [
                'name_ar' => 'أعمال',
                'name_en' => 'Business',
                'slug_ar' => 'أعمال',
                'slug_en' => 'business',
            ],
            [
                'name_ar' => 'نصائح',
                'name_en' => 'Tips',
                'slug_ar' => 'نصائح',
                'slug_en' => 'tips',
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }
    }
}
