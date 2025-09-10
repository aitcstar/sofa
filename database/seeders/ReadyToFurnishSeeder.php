<?php

namespace Database\Seeders;
use App\Models\ReadyToFurnishSection;
use Illuminate\Database\Seeder;

class ReadyToFurnishSeeder extends Seeder
{
    public function run()
    {
        ReadyToFurnishSection::create([
            'title_en' => 'Ready to furnish your unit?',
            'title_ar' => 'هل أنت جاهز لتأثيث وحدتك؟',
            'desc_en'  => 'Contact us via WhatsApp or start your order now.',
            'desc_ar'  => 'تواصل معنا عبر واتساب أو ابدأ طلبك الآن.',
            'whatsapp' => '201234567890',
            'start_order_link' => '/help',
            'image' => 'assets/images/about/about-05.jpg',
        ]);
    }
}
