<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class ExhibitionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('exhibition_categories')->insert([
            ['name_ar' => 'تصميم داخلي', 'name_en' => 'Interior Design', 'slug_ar' => 'تصميم-داخلي', 'slug_en' => 'interior-design'],
            ['name_ar' => 'معارض فنية', 'name_en' => 'Art Exhibitions', 'slug_ar' => 'معارض-فنية', 'slug_en' => 'art-exhibitions'],
        ]);
    }

}
