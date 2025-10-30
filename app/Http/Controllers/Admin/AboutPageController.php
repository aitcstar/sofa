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

   /* public function update(Request $request, $id)
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
    */

    public function update(Request $request, $id)
    {
        $section = AboutPage::findOrFail($id);
        $data = $request->validate([
            'title_ar' => 'nullable|string',
            'title_en' => 'nullable|string',
            'text_ar'  => 'nullable|string',
            'text_en'  => 'nullable|string',
            'items_ar' => 'nullable|array',
            'items_en' => 'nullable|array',
            //'item_icons' => 'nullable|array',
            //'item_icons.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // رفع صورة القسم الرئيسية
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('about', 'public');
        }

        // معالجة صور العناصر
        $newIcons = [];
        $currentIcons = $section->item_icons ?? [];

        if ($request->hasFile('item_icons')) {
            $uploadedIcons = $request->file('item_icons');
            $itemsCount = count($request->items_ar ?? []);

            for ($i = 0; $i < $itemsCount; $i++) {
                if (isset($uploadedIcons[$i]) && !empty($uploadedIcons[$i])) {
                    // رفع الصورة الجديدة
                    $newIcons[] = $uploadedIcons[$i]->store('about_item_icons', 'public');
                } else {
                    // الاحتفاظ بالصورة الحالية إن وجدت
                    $newIcons[] = $currentIcons[$i] ?? null;
                }
            }
        } else {
            // لم يتم رفع أي صور جديدة → الاحتفاظ بالحالية (لكن فقط بعدد العناصر المرسلة)
            $itemsCount = count($request->items_ar ?? []);
            $newIcons = array_slice($currentIcons, 0, $itemsCount);
        }

        // تحديث البيانات
        $section->fill($data);
        $section->item_icons = $newIcons;
        $section->save();

        return redirect()->route('admin.about.index')->with('success', 'تم تحديث البيانات بنجاح');
    }
}
