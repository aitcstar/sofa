<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcessSection;
use App\Models\ProcessStep;

class ProcessSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء السيكشن الأساسي
        $section = ProcessSection::create([
            'title_en' => 'How the process works',
            'title_ar' => 'كيف تتم العملية',
            'desc_en'  => 'Follow the simple steps to complete your request easily.',
            'desc_ar'  => 'اتبع الخطوات البسيطة لإكمال طلبك بسهولة.',
            'button_text_en' => 'Start Now',
            'button_text_ar' => 'ابدأ الآن',
        ]);

        // إنشاء الخطوات (steps)
        $steps = [
            [
                'icon' => 'assets/images/hero/Platform Text Container.png',
                'title_en' => 'Choose your package color',
                'title_ar' => 'اختر لون الباقة',
                'desc_en' => 'Select the right package and color that suits you.',
                'desc_ar' => 'اختر الباقة واللون المناسب لك.',
                'order' => 1,
            ],
            [
                'icon' => 'assets/images/hero/icone9.png',
                'title_en' => 'Fill the smart form',
                'title_ar' => 'املأ النموذج الذكي',
                'desc_en' => 'Provide us with the necessary details.',
                'desc_ar' => 'زودنا بالتفاصيل اللازمة.',
                'order' => 2,
            ],
            [
                'icon' => 'assets/images/hero/icone10.png',
                'title_en' => 'Receive a custom quote',
                'title_ar' => 'احصل على عرض سعر مخصص',
                'desc_en' => 'We will send you a detailed quote.',
                'desc_ar' => 'سوف نرسل لك عرض سعر مفصل.',
                'order' => 3,
            ],
            [
                'icon' => 'assets/images/hero/icone11.png',
                'title_en' => 'Receive your furnished unit',
                'title_ar' => 'استلم وحدتك المفروشة',
                'desc_en' => 'Your unit will be delivered ready.',
                'desc_ar' => 'سيتم تسليم وحدتك جاهزة.',
                'order' => 4,
            ],
        ];

        foreach ($steps as $step) {
            $section->steps()->create($step);
        }
    }
}
