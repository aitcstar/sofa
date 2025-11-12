<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::orderBy('sort')->paginate(10);
        return view('admin.faq_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.faq_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'sort'    => 'nullable|integer',
        ]);

        FaqCategory::create($request->only('name_ar', 'name_en', 'sort'));

        return redirect()->route('admin.faq-categories.index')->with('success', 'تمت إضافة القسم بنجاح');
    }

    public function edit(FaqCategory $faqCategory)
    {
        return view('admin.faq_categories.edit', compact('faqCategory'));
    }

    public function update(Request $request, FaqCategory $faqCategory)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'sort'    => 'nullable|integer',
        ]);

        $faqCategory->update($request->only('name_ar', 'name_en', 'sort'));

        return redirect()->route('admin.faq-categories.index')->with('success', 'تم تحديث القسم بنجاح');
    }

    public function destroy(FaqCategory $faqCategory)
    {
        $faqCategory->delete();
        return redirect()->route('admin.faq-categories.index')->with('success', 'تم حذف القسم بنجاح');
    }
}
