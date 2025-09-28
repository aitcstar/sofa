<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class ExhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('exhibitions')->insert([
        [
            'category_id' => 1,
            'package_id' => 1,
            'name_ar' => 'معرض الفن الحديث',
            'name_en' => 'Modern Art Exhibition',
            'slug_ar' => 'معرض-الفن-الحديث',
            'slug_en' => 'modern-art-exhibition',
            'summary_ar' => 'نبذة عن المعرض...',
            'summary_en' => 'Exhibition summary...',
            'delivery_date' => now()->addDays(30),
            'is_active' => true,
        ]
    ]);
}

}
