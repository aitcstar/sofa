<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeAboutSection;
use App\Models\HomeAboutIcon;

class HomeAboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة قسم "نبذة من نحن"
        $about = HomeAboutSection::create([
            'image' => 'uploads/about/about.jpg',
            'sub_title_en' => 'Who We Are',
            'sub_title_ar' => 'من نحن',
            'title_en' => 'About Our Company',
            'title_ar' => 'نبذة عن شركتنا',
            'desc_en' => 'We are a company specialized in providing the best services to our clients with high professionalism.',
            'desc_ar' => 'نحن شركة متخصصة في تقديم أفضل الخدمات لعملائنا باحترافية عالية.',
            'button_text_en' => 'Learn More',
            'button_text_ar' => 'اعرف المزيد',
            'button_link' => '/about',
        ]);

        // إضافة الأيقونات الخاصة بالقسم
        $icons = [
            [
                'icon' => 'fas fa-home',
                'title_en' => 'Professional Work',
                'title_ar' => 'عمل احترافي',
                'order' => 1,
            ],
            [
                'icon' => 'fas fa-users',
                'title_en' => 'Expert Team',
                'title_ar' => 'فريق خبير',
                'order' => 2,
            ],
            [
                'icon' => 'fas fa-cogs',
                'title_en' => 'Best Solutions',
                'title_ar' => 'أفضل الحلول',
                'order' => 3,
            ],
        ];

        foreach ($icons as $icon) {
            HomeAboutIcon::create($icon);
        }
    }
}
