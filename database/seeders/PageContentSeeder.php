<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use DB;
class PageContentSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('page_contents')->insert([
            'page' => 'blog',
            'title_ar' => 'هل ترغب بتجهيز وحدتك الفندقية بأناقة وبأسرع وقت؟',
            'title_en' => 'Do you want to prepare your hotel unit elegantly and quickly?',
            'text_ar' => 'استكشف نصائح وأفكار تصميم، وتعرف على أسرار تجهيز الوحدات السكنية بأعلى كفاءة وجودة',
            'text_en' => 'Explore design tips and ideas, and discover the secrets of equipping residential units with the highest efficiency and quality',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
