<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoSetting;

class SeoSettingSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            'home'     => ['ar' => 'الرئيسية', 'en' => 'Home'],
            'about'    => ['ar' => 'من نحن', 'en' => 'About Us'],
            'contact'  => ['ar' => 'اتصل بنا', 'en' => 'Contact Us'],
            'blog'     => ['ar' => 'المدونة', 'en' => 'Blog'],
            'category' => ['ar' => 'التصنيفات', 'en' => 'Categories'],
            'gallery'  => ['ar' => 'معرض الصور', 'en' => 'Gallery'],
            'help'     => ['ar' => 'المساعدة', 'en' => 'Help'],
            'faq'      => ['ar' => 'الأسئلة الشائعة', 'en' => 'Faq'],


        ];

        foreach ($pages as $page => $names) {
            SeoSetting::updateOrCreate(
                ['page' => $page],
                [
                    'meta_title_ar'       => $names['ar'] . ' | موقعنا',
                    'meta_description_ar' => 'صفحة ' . $names['ar'] . ' الخاصة بموقعنا.',
                    'slug_ar'             => $page,
                    'canonical_ar'        => url('ar/' . $page),

                    'meta_title_en'       => $names['en'] . ' | Our Website',
                    'meta_description_en' => 'This is the ' . $names['en'] . ' page of our website.',
                    'slug_en'             => $page,
                    'canonical_en'        => url('en/' . $page),

                    'index_status'        => 'index',
                ]
            );
        }
    }
}

