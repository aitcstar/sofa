<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhyChooseSection;
use App\Models\WhyChooseItem;

class WhyChooseSeeder extends Seeder
{
    public function run()
    {
        $section = WhyChooseSection::create([
            'title_en' => 'Why Choose Us',
            'title_ar' => 'لماذا تختارنا',
            'desc_en'  => 'We provide the best services for our clients.',
            'desc_ar'  => 'نحن نقدم أفضل الخدمات لعملائنا.',
        ]);

        $items = [
            [
                'title_en' => 'Fast Delivery',
                'title_ar' => 'توصيل سريع',
                'desc_en'  => 'We ensure quick delivery.',
                'desc_ar'  => 'نضمن توصيل سريع.',
            ],
            [
                'title_en' => 'Clear Pricing',
                'title_ar' => 'تسعير واضح',
                'desc_en'  => 'No hidden costs.',
                'desc_ar'  => 'بدون تكاليف مخفية.',
            ],
            [
                'title_en' => 'High Quality',
                'title_ar' => 'جودة عالية',
                'desc_en'  => 'We deliver the best quality.',
                'desc_ar'  => 'نقدم أفضل جودة.',
            ],
        ];

        foreach ($items as $item) {
            $section->items()->create($item);
        }
    }
}
