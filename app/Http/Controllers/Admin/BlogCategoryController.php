<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::latest()->paginate(10);
        return view('admin.blog_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        BlogCategory::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'slug_ar' => Str::slug($request->name_ar, '-'),
            'slug_en' => Str::slug($request->name_en, '-'),
        ]);

        return redirect()->route('admin.blog_categories.index')->with('success', 'تمت إضافة القسم بنجاح');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog_categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $blogCategory->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'slug_ar' => Str::slug($request->name_ar, '-'),
            'slug_en' => Str::slug($request->name_en, '-'),
        ]);

        return redirect()->route('admin.blog_categories.index')->with('success', 'تم تعديل القسم بنجاح');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();
        return redirect()->route('admin.blog_categories.index')->with('success', 'تم حذف القسم بنجاح');
    }
}
