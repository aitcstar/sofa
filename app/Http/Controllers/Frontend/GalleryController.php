<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;

class GalleryController extends Controller
{
    /**
     * عرض صفحة المعرض
     */
    public function index()
    {
        $seo = SeoSetting::where('page','gallery')->first();

        $pageData = [
            'title' => 'المعرض - SOFA Experience',
            'description' => 'تصفح معرض مشاريع SOFA Experience في تأثيث الوحدات الفندقية والسكنية',
            'keywords' => 'معرض, مشاريع, تأثيث فندقي, تصميم داخلي, SOFA',
        ];

        return view('frontend.pages.gallery', compact('seo','pageData'));
    }
}
