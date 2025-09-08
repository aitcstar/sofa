<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

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
            'category_ar'=>'required|string|max:255',
            'category_en'=>'required|string|max:255',
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

        return redirect()->route('admin.blogs.index')->with('success','تم إنشاء المقال بنجاح');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title_ar'=>'required|string|max:255',
            'title_en'=>'required|string|max:255',
            'slug_ar' => 'nullable|string|unique:blogs,slug_ar,'.$blog->id,
            'slug_en' => 'nullable|string|unique:blogs,slug_en,'.$blog->id,
            'excerpt_ar'=>'required|string',
            'excerpt_en'=>'required|string',
            'content_ar'=>'required|string',
            'content_en'=>'required|string',
            'image'=>'nullable|image|max:2048',
            'category_ar'=>'required|string|max:255',
            'category_en'=>'required|string|max:255',
            'author_ar'=>'nullable|string|max:255',
            'author_en'=>'nullable|string|max:255',
        ]);

        $data = $request->all();

        if($request->hasFile('image')){
            if($blog->image && \Storage::disk('public')->exists($blog->image)){
                \Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs','public');
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success','تم تحديث المقال بنجاح');
    }


    public function destroy(Blog $blog)
    {
        if($blog->image && \Storage::disk('public')->exists($blog->image)){
            \Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success','تم حذف المقال بنجاح');
    }
}
