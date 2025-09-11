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
        $seo = SeoSetting::where('page','blog')->first();
        $locale = app()->getLocale(); // الحصول على اللغة من الـ middleware

        $slugColumn = $locale === 'ar' ? 'slug_ar' : 'slug_en';
        $categoryColumn = $locale === 'ar' ? 'category_ar' : 'category_en';

        $post = Blog::where($slugColumn, $slug)->firstOrFail();

        $relatedPosts = Blog::where($categoryColumn, $post->category)
                            ->where($slugColumn, '!=', $slug)
                            ->latest()
                            ->take(3)
                            ->get();

        $faqs = Faq::where('page','blog')->latest()->take(10)->get();

        return view('frontend.pages.blog-details', compact('seo','post', 'relatedPosts','faqs'));
    }


}
