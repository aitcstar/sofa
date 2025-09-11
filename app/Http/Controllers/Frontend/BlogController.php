<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\SeoSetting;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $seo = SeoSetting::where('page','blog')->first();

        $query = Blog::query();

        // تحديد العمود حسب اللغة الحالية
        $categoryColumn = app()->getLocale() === 'ar' ? 'category_ar' : 'category_en';

        if ($request->filled('category')) {
            $query->where($categoryColumn, $request->category);
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('frontend.pages.blog', compact('seo','blogs'));
    }




public function show($slug)
{
    $locale = app()->getLocale();
    $seo = SeoSetting::where('page','blog')->first();

    // البحث في كلا الحقلين (slug_en و slug_ar)
    $post = Blog::where('slug_en', $slug)
                ->orWhere('slug_ar', $slug)
                ->firstOrFail();

    // إذا كان الـ slug بالإنجليزية ولكن اللغة الحالية عربية
    if ($post->slug_en === $slug && $locale === 'ar') {
        // إعادة توجيه إلى الـ slug العربي
        return redirect()->route('blog.details', [$post->slug_ar]);
    }

    // إذا كان الـ slug بالعربية ولكن اللغة الحالية إنجليزية
    if ($post->slug_ar === $slug && $locale === 'en') {
        // إعادة توجيه إلى الـ slug الإنجليزي
        return redirect()->route('blog.details.en', [$post->slug_en]);
    }

    $categoryColumn = $locale === 'ar' ? 'category_ar' : 'category_en';

    $relatedPosts = Blog::where($categoryColumn, $post->$categoryColumn)
                        ->where(function($query) use ($slug, $locale) {
                            $query->where('slug_en', '!=', $slug)
                                  ->where('slug_ar', '!=', $slug);
                        })
                        ->latest()
                        ->take(3)
                        ->get();
                        $faqs = Faq::where('page','blog')->latest()->take(10)->get();

    return view('frontend.pages.blog-details', compact('post', 'relatedPosts', 'seo','faqs'));
}

}
