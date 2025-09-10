<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Step;

class StepsTableSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'icon' => 'fas fa-pencil-ruler',
                'title_en' => 'Choose Design',
                'title_ar' => 'اختيار التصميم',
                'desc_en' => 'Select the suitable design for your unit.',
                'desc_ar' => 'اختيار التصميم المناسب لوحدتك.',
                'order' => 1,
            ],
            [
                'icon' => 'fas fa-boxes',
                'title_en' => 'Prepare Materials',
                'title_ar' => 'تجهيز المواد',
                'desc_en' => 'Prepare all necessary materials for execution.',
                'desc_ar' => 'تجهيز المواد اللازمة للتنفيذ.',
                'order' => 2,
            ],
            [
                'icon' => 'fas fa-tools',
                'title_en' => 'Execution',
                'title_ar' => 'تنفيذ العمل',
                'desc_en' => 'Start executing the unit according to the design.',
                'desc_ar' => 'بدء تنفيذ الوحدة حسب التصميم.',
                'order' => 3,
            ],
            [
                'icon' => 'fas fa-check-circle',
                'title_en' => 'Final Delivery',
                'title_ar' => 'التسليم النهائي',
                'desc_en' => 'Deliver the unit after completing all steps.',
                'desc_ar' => 'تسليم الوحدة بعد الانتهاء من جميع المراحل.',
                'order' => 4,
            ],
        ];

        foreach ($steps as $step) {
            Step::create($step);
        }
    }
}
