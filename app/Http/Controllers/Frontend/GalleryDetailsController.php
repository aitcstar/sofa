<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;

class GalleryDetailsController extends Controller
{
    /**
     * عرض صفحة تفاصيل المعرض
     */
    public function show($id)
    {
        $seo = SeoSetting::where('page','gallery')->first();

        // بيانات المشروع (يمكن استبدالها ببيانات حقيقية من قاعدة البيانات)
        $project = $this->getProjectDetails($id);

        $pageData = [
            'title' => $project['title'] . ' - SOFA Experience',
            'description' => $project['description'],
            'keywords' => 'تفاصيل مشروع, تأثيث فندقي, SOFA, ' . $project['title'],
            'project' => $project
        ];

        return view('frontend.pages.gallery-details', compact('seo','pageData'));
    }

    /**
     * الحصول على تفاصيل المشروع (بيانات وهمية للتوضيح)
     */
    private function getProjectDetails($id)
    {
        // في التطبيق الحقيقي، سيتم جلب البيانات من قاعدة البيانات
        $projects = [
            1 => [
                'title' => 'فندق المها – الرياض',
                'description' => 'تم تنفيذ المشروع في فندق المها بمدينة الرياض باستخدام باكج جاهز لغرفة نوم واحدة من SOFA، مع تخصيصات بسيطة تناسب الطابع الفندقي.',
                'type' => 'غرفة نوم رئيسية',
                'area' => 'منطقة معيشة',
                'kitchen' => 'مطبخ صغير',
                'delivery_date' => 'مارس 2025',
                'pieces_count' => '60 قطعة',
                'colors' => 'بيج، أبيض، بني (دافئة)',
                'living_room' => 'فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة',
                'kitchen_details' => 'فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة',
                'tv_design' => 'تصميم خشبي كلاسيكي',
                'cabinets' => 'تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)'
            ],
            2 => [
                'title' => 'فندق النخيل – جدة',
                'description' => 'مشروع تأثيث فندقي فاخر في جدة مع تصميم عصري وأنيق.',
                'type' => 'غرفتي نوم',
                'area' => 'منطقة معيشة كبيرة',
                'kitchen' => 'مطبخ كامل',
                'delivery_date' => 'أبريل 2025',
                'pieces_count' => '85 قطعة',
                'colors' => 'رمادي، أبيض، أزرق',
                'living_room' => 'فاخرة – كنبة 4 مقاعد + طاولة قهوة',
                'kitchen_details' => 'فاخر مع جزيرة مركزية',
                'tv_design' => 'تصميم حديث',
                'cabinets' => 'تصميم مودرن'
            ],
            // يمكن إضافة المزيد من المشاريع هنا
        ];

        return $projects[$id] ?? $projects[1]; // إرجاع المشروع الأول إذا لم يتم العثور على ID
    }
}
