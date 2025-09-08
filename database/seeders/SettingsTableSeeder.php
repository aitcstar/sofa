<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'site_name'       => 'SOFA Experience',
            'email'           => 'info@sofa.com',
            'worktime'           => 'من السبت إلى الخميس: 10:00 صباحًا – 9:00 مساءً الجمعة: 4:00 مساءً – 9:00 مساءً',
            'phone'           => '+966500000000',
            'whatsapp'        => '+966500000000',
            'address'         => 'الرياض – طريق الملك عبدالعزيز، حي الياسمين داخل مركز SOFA لتجربة التأثيث',
            'snapchat'        => 'https://snapchat.com/sofa',
            'tiktok'          => 'https://tiktok.com/sofa',
            'instagram'       => 'https://instagram.com/sofa',
            'linkedin'        => 'https://linkedin.com/company/sofa',
            'youtube'         => 'https://youtube.com/company/sofa',
            'seo_title'       => 'SOFA Experience',
            'seo_description' => 'أفضل منصة لإدارة موقعك بسهولة مع دعم SEO كامل.',
            'seo_keywords'    => 'SOFA, SEO, إدارة, موقع',
        ]);
    }
}
