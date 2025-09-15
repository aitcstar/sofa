<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactSection;

class ContactSectionSeeder extends Seeder
{
    public function run(): void
    {
        ContactSection::create([
            'title_ar' => 'اتصل بنا',
            'title_en' => 'Contact Us',
            'desc_ar' => 'يسعدنا تواصلك معنا عبر النموذج أو وسائل التواصل.',
            'desc_en' => 'We are happy to hear from you through the form or social media.',
            'main_showroom_ar' => 'المعرض الرئيسي - الرياض',
            'main_showroom_en' => 'Main Showroom - Riyadh',
            'work_hours_ar' => 'السبت - الخميس: 9 ص - 10 م',
            'work_hours_en' => 'Saturday - Thursday: 9 AM - 10 PM',
            'cta_heading_ar' => 'ابدأ تواصلك الآن',
            'cta_heading_en' => 'Start Your Connection Now',
            'cta_text_ar' => 'فريقنا جاهز للرد عليك في أي وقت.',
            'cta_text_en' => 'Our team is ready to respond anytime.',
            'city_ar' => 'الرياض',
            'city_en' => 'Riyadh',
            'address_ar' => '123 شارع الملك فهد، الرياض، السعودية',
            'address_en' => '123 King Fahd Street, Riyadh, Saudi Arabia',
        ]);
    }
}
