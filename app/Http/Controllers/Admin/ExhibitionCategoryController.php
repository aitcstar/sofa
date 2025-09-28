<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionCategory;
use Illuminate\Http\Request;

class ExhibitionCategoryController extends Controller
{
    public function index()
    {
        $categories = ExhibitionCategory::latest()->paginate(10);
        return view('admin.exhibition_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.exhibition_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug_ar' => 'required|string|max:255|unique:exhibition_categories,slug_ar',
            'slug_en' => 'required|string|max:255|unique:exhibition_categories,slug_en',
        ]);

        ExhibitionCategory::create($request->all());

        return redirect()->route('admin.exhibition-categories.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(ExhibitionCategory $exhibitionCategory)
    {
        return view('admin.exhibition_categories.edit', compact('exhibitionCategory'));
    }

    public function update(Request $request, ExhibitionCategory $exhibitionCategory)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug_ar' => 'required|string|max:255|unique:exhibition_categories,slug_ar,' . $exhibitionCategory->id,
            'slug_en' => 'required|string|max:255|unique:exhibition_categories,slug_en,' . $exhibitionCategory->id,
        ]);

        $exhibitionCategory->update($request->all());

        return redirect()->route('admin.exhibition-categories.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(ExhibitionCategory $exhibitionCategory)
    {
        $exhibitionCategory->delete();

        return redirect()->route('admin.exhibition-categories.index')->with('success', 'تم الحذف بنجاح');
    }
}
