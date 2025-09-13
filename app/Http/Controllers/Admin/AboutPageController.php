<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use App\Models\SeoSetting;

class AboutPageController extends Controller
{
    public function index()
    {
        $page = 'about';
        $seoSettings = SeoSetting::all()->keyBy('page');

        $sections = AboutPage::all()->groupBy('section');
        return view('admin.about.index', compact('sections','page','seoSettings'));
    }

    public function edit($id)
    {
        $section = AboutPage::findOrFail($id);
        return view('admin.about.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $section = AboutPage::findOrFail($id);

        $data = $request->validate([
            'title_ar' => 'nullable|string',
            'title_en' => 'nullable|string',
            'text_ar' => 'nullable|string',
            'text_en' => 'nullable|string',
            'items_ar' => 'nullable|array',
            'items_en' => 'nullable|array',
            'image'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('about', 'public');
        }

        $section->update($data);

        return redirect()->route('admin.about.index')->with('success', 'تم تحديث البيانات بنجاح');
    }
}
