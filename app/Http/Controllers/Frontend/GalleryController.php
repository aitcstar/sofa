<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * عرض صفحة المعرض
     */
    public function index()
    {
        $pageData = [
            'title' => 'المعرض - SOFA Experience',
            'description' => 'تصفح معرض مشاريع SOFA Experience في تأثيث الوحدات الفندقية والسكنية',
            'keywords' => 'معرض, مشاريع, تأثيث فندقي, تصميم داخلي, SOFA',
        ];

        return view('frontend.pages.gallery', compact('pageData'));
    }
}
