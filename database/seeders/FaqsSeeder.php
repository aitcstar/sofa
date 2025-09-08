<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaqsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $faqs = [
            [
                'category_ar' => 'التوصيل والتركيب',
                'category_en' => 'Delivery & Installation',
                'question_ar' => 'ما هو هدف الموقع؟',
                'question_en' => 'What is the purpose of the website?',
                'answer_ar' => 'هدف الموقع هو تقديم أفضل الخدمات للعملاء.',
                'answer_en' => 'The purpose of the website is to provide the best services to clients.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_ar' => 'التوصيل والتركيب',
                'category_en' => 'Delivery & Installation',
                'question_ar' => 'كيف يمكنني التواصل مع الدعم؟',
                'question_en' => 'How can I contact support?',
                'answer_ar' => 'يمكنك التواصل معنا عبر صفحة التواصل أو البريد الإلكتروني.',
                'answer_en' => 'You can contact us via the contact page or email.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // ممكن تضيف هنا أي عدد من الأسئلة
        ];

        DB::table('faqs')->insert($faqs);
    }
}
