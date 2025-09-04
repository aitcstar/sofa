<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name'     => 'سالم الحربي',
                'location' => 'الدمام',
                'message'  => 'وصل الطلب في الموعد، خدمة رائعة كما هو موضح في الموقع. شكراً لكم!',
                'rating'   => 5,
                'image'    => null,
            ],
            [
                'name'     => 'منى العتيبي',
                'location' => 'الرياض',
                'message'  => 'التغليف ممتاز والتعامل راقي جداً. بالتأكيد سأتعامل معكم مرة أخرى.',
                'rating'   => 4,
                'image'    => null,
            ],
            [
                'name'     => 'أحمد الزهراني',
                'location' => 'جدة',
                'message'  => 'سهل جداً في الطلب والتواصل. خدمة عملاء ممتازة.',
                'rating'   => 5,
                'image'    => null,
            ],
            [
                'name'     => 'فاطمة محمد',
                'location' => 'الخبر',
                'message'  => 'المنتج مطابق للوصف، لكن وقت التوصيل تأخر قليلاً.',
                'rating'   => 3,
                'image'    => null,
            ],
            [
                'name'     => 'خالد الغامدي',
                'location' => 'مكة',
                'message'  => 'تجربة أكثر من رائعة. كل شيء كان سلس وسريع.',
                'rating'   => 5,
                'image'    => null,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
