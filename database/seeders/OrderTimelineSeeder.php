<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderTimelineSection;
use App\Models\OrderTimelineItem;

class OrderTimelineSeeder extends Seeder
{
    public function run(): void
    {
        // القسم الرئيسي
        $section = OrderTimelineSection::create([
            'title_ar' => 'تايم لاين الطلب',
            'title_en' => 'Order Timeline',
            'desc_ar'  => 'مراحل الطلب من البداية للنهاية',
            'desc_en'  => 'The order journey from start to finish',
        ]);

        // العناصر
        $items = [
            [
                'title_ar' => 'تم إنشاء الطلب',
                'title_en' => 'Order Placed',
                'desc_ar'  => 'تم استلام طلبك بنجاح',
                'desc_en'  => 'Your order has been successfully received',
                'color'    => '#08203E',
            ],
            [
                'title_ar' => 'التصميم',
                'title_en' => 'Design',
                'desc_ar'  => 'إعداد التصميم وفق المواصفات',
                'desc_en'  => 'Preparing the design according to specifications',
                'color'    => '#AD996F',
            ],
            [
                'title_ar' => 'التصنيع',
                'title_en' => 'Manufacturing',
                'desc_ar'  => 'بدء عملية التصنيع',
                'desc_en'  => 'Start of the manufacturing process',
                'color'    => '#979DAC',
            ],
            [
                'title_ar' => 'الشحن',
                'title_en' => 'Shipping',
                'desc_ar'  => 'شحن الطلب إلى العميل',
                'desc_en'  => 'Shipping the order to the customer',
                'color'    => '#33415C',
            ],
            [
                'title_ar' => 'الدفعة الثانية',
                'title_en' => 'Second Payment',
                'desc_ar'  => 'تأكيد الدفع الثاني',
                'desc_en'  => 'Confirming the second payment',
                'color'    => '#32B828',
            ],
            [
                'title_ar' => 'التركيب',
                'title_en' => 'Installation',
                'desc_ar'  => 'تركيب المنتج في الموقع',
                'desc_en'  => 'Installation of the product on site',
                'color'    => '#C1B41C',
            ],
        ];

        foreach ($items as $item) {
            $section->items()->create($item);
        }
    }
}
