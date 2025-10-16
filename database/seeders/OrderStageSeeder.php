<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderStage;

class OrderStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['title_ar' => 'التصميم', 'title_en' => 'Design', 'order_number' => 1],
            ['title_ar' => 'التصنيع', 'title_en' => 'Manufacturing', 'order_number' => 2],
            ['title_ar' => 'الشحن', 'title_en' => 'Shipping', 'order_number' => 3],
            ['title_ar' => 'الدفعة الأولى', 'title_en' => 'First Payment', 'order_number' => 4],
            ['title_ar' => 'الدفعة الثانية', 'title_en' => 'Second Payment', 'order_number' => 5],
        ];

        foreach ($stages as $stage) {
            OrderStage::updateOrCreate(['title_ar' => $stage['title_ar']], $stage);
        }
    }
}
