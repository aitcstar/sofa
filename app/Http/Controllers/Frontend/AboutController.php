<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;

class AboutController extends Controller
{
    /**
     * عرض صفحة "من نحن"
     */
    public function index()
    {
        $seo = SeoSetting::where('page','about')->first();
        $pageData = [
            'title' => 'من نحن - SOFA Experience',
            'description' => 'تعرف على رؤية وقيم SOFA Experience في تقديم حلول التأثيث الفندقي الذكية',
            'keywords' => 'تأثيث فندقي, تصميم داخلي, أثاث, SOFA, من نحن',
        ];

        return view('frontend.pages.about', compact('seo','pageData'));
    }
}
