<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroSlider;

class HeroSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'image' => 'sliders/slider1.jpg',
                'title_ar' => 'عنوان السلايدر الأول',
                'title_en' => 'First Slider Title',
                'description_ar' => 'وصف السلايدر الأول بالعربي',
                'description_en' => 'First slider description in English',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'image' => 'sliders/slider2.jpg',
                'title_ar' => 'عنوان السلايدر الثاني',
                'title_en' => 'Second Slider Title',
                'description_ar' => 'وصف السلايدر الثاني بالعربي',
                'description_en' => 'Second slider description in English',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'image' => 'sliders/slider3.jpg',
                'title_ar' => 'عنوان السلايدر الثالث',
                'title_en' => 'Third Slider Title',
                'description_ar' => 'وصف السلايدر الثالث بالعربي',
                'description_en' => 'Third slider description in English',
                'order' => 3,
                'is_active' => false,
            ],
        ];

        foreach ($sliders as $slider) {
            HeroSlider::create($slider);
        }
    }
}
