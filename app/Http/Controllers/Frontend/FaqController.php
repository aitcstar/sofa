<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\SeoSetting;
use App\Models\PageContent;
class FaqController extends Controller
{
    public function index()
{
    $seo = SeoSetting::where('page','faq')->first();

    // هات كل الأقسام المميزة من جدول الأسئلة
    $categories = Faq::select('category_ar', 'category_en')
        ->distinct()
        ->get();

    $faqCategories = [];

    foreach ($categories as $index => $category) {
        $faqs = Faq::where('category_ar', $category->category_ar)
                    ->orderBy('sort', 'asc')
                    ->get();

        $faqCategories[] = [
            'name' => app()->getLocale() == 'ar' ? $category->category_ar : $category->category_en,
            'active' => $index === 0, // أول قسم يكون Active
            'faqs' => $faqs->map(function ($faq, $faqIndex) use ($index) {
                return [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'active' => $faqIndex === 0 // أول سؤال Active
                ];
            })->toArray()
        ];
    }

    $content = PageContent::where('page','faq')->first();


    return view('frontend.pages.faq', compact('seo','faqCategories','content'));
}

}
