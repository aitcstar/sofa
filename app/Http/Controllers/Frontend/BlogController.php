<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Faq;
use App\Models\SeoSetting;
use App\Models\PageContent;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $seo = SeoSetting::where('page', 'blog')->first();

        // جلب الأقسام
        $categories = BlogCategory::select('id', 'name_ar', 'name_en')->get();

        $query = Blog::with('category'); // جلب العلاقة

        if ($request->filled('category')) {
            // فلترة حسب ID مش الاسم
            $query->where('category_id', $request->category);
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate(9);
        $content = PageContent::where('page','blog')->first();

        return view('frontend.pages.blog', compact('seo','blogs','categories','content'));
    }





    public function show($slug)
    {
        $locale = app()->getLocale();
        $seo = SeoSetting::where('page', 'blog')->first();

        // البحث في كلا الحقلين (slug_en و slug_ar)
        $post = Blog::with(['category','faqs'])
            ->where('slug_en', $slug)
            ->orWhere('slug_ar', $slug)
            ->firstOrFail();

        // إذا كان الـ slug بالإنجليزية ولكن اللغة الحالية عربية
        if ($post->slug_en === $slug && $locale === 'ar') {
            return redirect()->route('blog.details', [$post->slug_ar]);
        }

        // إذا كان الـ slug بالعربية ولكن اللغة الحالية إنجليزية
        if ($post->slug_ar === $slug && $locale === 'en') {
            return redirect()->route('blog.details.en', [$post->slug_en]);
        }

        // المقالات ذات الصلة حسب القسم المرتبط
        $relatedPosts = Blog::with('category')
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        $faqs = Faq::where('page', 'blog')->latest()->take(10)->get();

        return view('frontend.pages.blog-details', compact('post', 'relatedPosts', 'seo','faqs'));
    }


}
