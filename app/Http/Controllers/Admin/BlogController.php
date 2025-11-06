<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use App\Models\PageContent;
use App\Models\Faq;
class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(10);
        $page = 'blog';
        $seoSettings = SeoSetting::all()->keyBy('page');
        $content = PageContent::where('page', 'blog')->first();

        return view('admin.blogs.index', compact('blogs','page','seoSettings','content'));
    }

    public function create()
    {
        $categories = BlogCategory::all();

        return view('admin.blogs.create', compact('categories'));
    }

    /*public function store(Request $request)
    {
        $request->validate([
            'title_ar'=>'required|string|max:255',
            'title_en'=>'required|string|max:255',
            'excerpt_ar'=>'required|string',
            'excerpt_en'=>'required|string',
            'content_ar'=>'required|string',
            'content_en'=>'required|string',
            'image'=>'nullable|image|max:2048',
            'category_id' => 'required|exists:blog_categories,id',
            'author_ar'=>'required|string|max:255',
            'author_en'=>'required|string|max:255',
        ]);

        $data = $request->all();

        // slug العربي
        $slugAr = Str::slug($request->title_ar, '-');
        $originalSlugAr = $slugAr;
        $count = 1;
        while (Blog::where('slug_ar', $slugAr)->exists()) {
            $slugAr = $originalSlugAr . '-' . $count++;
        }
        $data['slug_ar'] = $slugAr;

        // slug الإنجليزي
        $slugEn = Str::slug($request->title_en, '-');
        $originalSlugEn = $slugEn;
        $count = 1;
        while (Blog::where('slug_en', $slugEn)->exists()) {
            $slugEn = $originalSlugEn . '-' . $count++;
        }
        $data['slug_en'] = $slugEn;

        // رفع الصورة لو موجودة
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('blogs','public');
        }

        Blog::create($data);

        if ($request->has('faqs')) {
            foreach ($request->faqs as $faq) {
                $blog->faqs()->create($faq);
            }
        }

        return redirect()->route('admin.blogs.index')->with('success','تم إنشاء المقال بنجاح');
    }*/
    public function store(Request $request)
{
    $request->validate([
        'title_ar'=>'required|string|max:255',
        'title_en'=>'required|string|max:255',
        'excerpt_ar'=>'required|string',
        'excerpt_en'=>'required|string',
        'content_ar'=>'required|string',
        'content_en'=>'required|string',
        'image'=>'nullable|image|max:2048',
        'category_id' => 'required|exists:blog_categories,id',
        'author_ar'=>'required|string|max:255',
        'author_en'=>'required|string|max:255',

        'meta_title_en' => 'nullable|string',
        'meta_title_ar' => 'nullable|string',
        'meta_description_en' => 'nullable|string',
        'meta_description_ar' => 'nullable|string',
        'slug_en' => 'nullable|string',
        'slug_ar' => 'nullable|string',
    ]);

    // استبعاد الـ faqs
    $data = $request->except('faqs');

    // slug العربي
    /*$slugAr = Str::slug($request->title_ar, '-');
    $originalSlugAr = $slugAr;
    $count = 1;
    while (Blog::where('slug_ar', $slugAr)->exists()) {
        $slugAr = $originalSlugAr . '-' . $count++;
    }
    $data['slug_ar'] = $slugAr;

    // slug الإنجليزي
    $slugEn = Str::slug($request->title_en, '-');
    $originalSlugEn = $slugEn;
    $count = 1;
    while (Blog::where('slug_en', $slugEn)->exists()) {
        $slugEn = $originalSlugEn . '-' . $count++;
    }
    $data['slug_en'] = $slugEn;*/

    // رفع الصورة لو موجودة
    if($request->hasFile('image')){
        $data['image'] = $request->file('image')->store('blogs','public');
    }

    // إنشاء المدونة
    $blog = Blog::create($data);

    // حفظ الأسئلة لو موجودة
    if ($request->has('faqs')) {
        foreach ($request->faqs as $faq) {
            $blog->faqs()->create($faq);
        }
    }

    return redirect()->route('admin.blogs.index')->with('success','تم إنشاء المقال بنجاح');
}


    public function edit(Blog $blog)
    {
        $blog = Blog::with('faqs')->findOrFail($blog->id);
        $categories = BlogCategory::all();

        return view('admin.blogs.edit', compact('blog','categories','blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title_ar'=>'required|string|max:255',
            'title_en'=>'required|string|max:255',

            'excerpt_ar'=>'required|string',
            'excerpt_en'=>'required|string',
            'content_ar'=>'required|string',
            'content_en'=>'required|string',
            'image'=>'nullable|image|max:2048',
            'category_id' => 'required|exists:blog_categories,id',
            'author_ar'=>'nullable|string|max:255',
            'author_en'=>'nullable|string|max:255',

            'meta_title_en' => 'nullable|string',
            'meta_title_ar' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'slug_en' => 'nullable|string',
            'slug_ar' => 'nullable|string',
        ]);

        // استبعاد faqs من البيانات المحدثة للمدونة
        $data = $request->except('faqs');

        if ($request->hasFile('image')) {
            if ($blog->image && \Storage::disk('public')->exists($blog->image)) {
                \Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);

        // تحديث الأسئلة المرتبطة
        $faqs = $request->input('faqs', []);

        foreach ($faqs as $faqData) {
            if (isset($faqData['id'])) {
                // تحديث موجود
                $faq = Faq::find($faqData['id']);
                if ($faq) {
                    $faq->update($faqData);
                }
            } else {
                // إضافة جديد
                $blog->faqs()->create($faqData);
            }
        }

        return redirect()->route('admin.blogs.index')->with('success', 'تم تحديث المقال بنجاح');
    }


    public function destroy(Blog $blog)
    {
        if($blog->image && \Storage::disk('public')->exists($blog->image)){
            \Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success','تم حذف المقال بنجاح');
    }


    public function updateBlog(Request $request)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'text_ar'  => 'required',
            'text_en'  => 'required',
        ]);

        $content = PageContent::where('page', 'blog')->first();
        $content->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات صفحة المدونة بنجاح');
    }
}
