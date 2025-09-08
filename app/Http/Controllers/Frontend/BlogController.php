<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Faq;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::query();

        // تحديد العمود حسب اللغة الحالية
        $categoryColumn = app()->getLocale() === 'ar' ? 'category_ar' : 'category_en';

        if ($request->filled('category')) {
            $query->where($categoryColumn, $request->category);
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('frontend.pages.blog', compact('blogs'));
    }


    public function show($slug)
{
    $locale = app()->getLocale();
    $slugColumn = $locale === 'ar' ? 'slug_ar' : 'slug_en';
    $categoryColumn = $locale === 'ar' ? 'category_ar' : 'category_en';

    $post = Blog::where($slugColumn, $slug)->firstOrFail();

    // المقالات ذات صلة بنفس التصنيف
    $relatedPosts = Blog::where($categoryColumn, $post->category)
                        ->where($slugColumn, '!=', $slug)
                        ->latest()
                        ->take(3)
                        ->get();
    $faqs = Faq::latest()->take(10)->get();

    return view('frontend.pages.blog-details', compact('post', 'relatedPosts','faqs'));
}

}
