<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\SeoSetting;
use App\Models\PageContent;
use App\Models\FaqCategory;


class FaqController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page', 'faq')->first();

        // هات الأقسام مع الأسئلة المرتبطة بها بترتيب الفرز
        $categories = FaqCategory::with(['faqs' => function($q) {
            $q->orderBy('sort', 'asc');
        }])
        ->orderBy('sort', 'asc')
        ->get();

        $faqCategories = [];

        foreach ($categories as $index => $category) {
            $faqCategories[] = [
                'name' => app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en,
                'active' => $index === 0, // أول قسم Active
                'faqs' => $category->faqs->map(function ($faq, $faqIndex) use ($index) {
                    return [
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                        'active' => $faqIndex === 0 // أول سؤال Active
                    ];
                })->toArray()
            ];
        }

        $content = PageContent::where('page', 'faq')->first();

        return view('frontend.pages.faq', compact('seo', 'faqCategories', 'content'));
    }


}
