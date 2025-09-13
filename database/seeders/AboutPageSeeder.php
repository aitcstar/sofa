<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutPage;

class AboutPageSeeder extends Seeder
{
    public function run()
    {
        // Vision
        AboutPage::create([
            'section' => 'vision',
            'title_ar' => 'رؤيتنا',
            'title_en' => 'Our Vision',
            'text_ar' => 'نص تجريبي بالعربية لرؤيتنا...',
            'text_en' => 'Sample English text for our vision...',
            'items_ar' => ['عنصر ١', 'عنصر ٢', 'عنصر ٣'],
            'items_en' => ['Item 1', 'Item 2', 'Item 3'],
            'image' => 'about/about-01.jpg',
        ]);

        // Values
        AboutPage::create([
            'section' => 'values',
            'title_ar' => 'قيمنا',
            'title_en' => 'Our Values',
            'text_ar' => 'نص بالعربية لقيمنا...',
            'text_en' => 'English text for our values...',
            'items_ar' => ['قيمة ١', 'قيمة ٢', 'قيمة ٣', 'قيمة ٤', 'قيمة ٥'],
            'items_en' => ['Value 1', 'Value 2', 'Value 3', 'Value 4', 'Value 5'],
            'image' => 'about/about-02.jpg',
        ]);

        // Why Sofa
        AboutPage::create([
            'section' => 'why_sofa',
            'title_ar' => 'لماذا صوفا؟',
            'title_en' => 'Why Sofa?',
            'text_ar' => 'نص عن لماذا تختار صوفا...',
            'text_en' => 'Text about why to choose Sofa...',
            'items_ar' => ['ميزة ١', 'ميزة ٢', 'ميزة ٣', 'ميزة ٤'],
            'items_en' => ['Advantage 1', 'Advantage 2', 'Advantage 3', 'Advantage 4'],
            'image' => 'about/about-03.jpg',
        ]);

        // Smart Steps
        AboutPage::create([
            'section' => 'smart_steps',
            'title_ar' => 'خطواتنا الذكية',
            'title_en' => 'Our Smart Steps',
            'text_ar' => 'شرح خطوات صوفا الذكية...',
            'text_en' => 'Description of Sofa smart steps...',
            'items_ar' => ['الخطوة ١', 'الخطوة ٢', 'الخطوة ٣', 'الخطوة ٤', 'الخطوة ٥', 'الخطوة ٦', 'الخطوة ٧'],
            'items_en' => ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Step 6', 'Step 7'],
            'image' => 'about/about-04.jpg',
        ]);

        // Ready to furnish
        AboutPage::create([
            'section' => 'ready_to_furnish',
            'title_ar' => 'جاهز لتأثيث وحدتك؟',
            'title_en' => 'Ready to Furnish Your Unit?',
            'text_ar' => 'نص بالعربية عن تجهيز وحدتك...',
            'text_en' => 'English text about furnishing your unit...',
            'items_ar' => null,
            'items_en' => null,
            'image' => 'about/about-05.jpg',
        ]);

        // Why do we serve
        AboutPage::create([
            'section' => 'why_do_we_serve',
            'title_ar' => 'لماذا نخدم؟',
            'title_en' => 'Why Do We Serve?',
            'text_ar' => 'نص عن لماذا نقدم الخدمة...',
            'text_en' => 'Text about why we serve...',
            'items_ar' => ['خدمة ١', 'خدمة ٢', 'خدمة ٣', 'خدمة ٤'],
            'items_en' => ['Service 1', 'Service 2', 'Service 3', 'Service 4'],
            'image' => 'about/about-06.jpg',
        ]);
    }
}
