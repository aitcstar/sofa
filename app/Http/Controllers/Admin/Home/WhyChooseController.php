<?php

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use App\Models\WhyChooseSection;
use App\Models\WhyChooseItem;
use Illuminate\Http\Request;

class WhyChooseController extends Controller
{
    public function edit()
    {
        $section = WhyChooseSection::with('items')->first();

        if (!$section) {
            $section = WhyChooseSection::create([
                'title_en' => '',
                'title_ar' => '',
                'desc_en'  => '',
                'desc_ar'  => '',
            ]);
        }

        return view('admin.home.why-choose.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'desc_en'  => 'required|string',
            'desc_ar'  => 'required|string',
            'items.*.title_en' => 'nullable|string|max:255',
            'items.*.title_ar' => 'nullable|string|max:255',
            'items.*.desc_en'  => 'nullable|string',
            'items.*.desc_ar'  => 'nullable|string',
            'items.*.icon'     => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $section = WhyChooseSection::first();
        if (!$section) {
            $section = WhyChooseSection::create($request->only(['title_en','title_ar','desc_en','desc_ar']));
        } else {
            $section->update($request->only(['title_en','title_ar','desc_en','desc_ar']));
        }

        // حفظ IDs العناصر اللي جاية من الفورم
        $incomingIds = collect($request->items)->pluck('id')->filter()->toArray();

        // حذف العناصر اللي مش موجودة في الفورم
        $section->items()->whereNotIn('id', $incomingIds)->delete();

        // تحديث أو إضافة العناصر
        if ($request->has('items')) {
            foreach ($request->items as $itemData) {
                if (!empty($itemData['id'])) {
                    // تحديث عنصر موجود
                    $item = WhyChooseItem::find($itemData['id']);
                    if ($item) {
                        if (isset($itemData['icon']) && $itemData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                            $itemData['icon'] = $itemData['icon']->store('why-choose', 'public');
                        } else {
                            unset($itemData['icon']);
                        }
                        $item->update($itemData);
                    }
                } else {
                    // إضافة عنصر جديد
                    if (!empty($itemData['title_en']) || !empty($itemData['title_ar'])) {
                        if (isset($itemData['icon']) && $itemData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                            $itemData['icon'] = $itemData['icon']->store('why-choose', 'public');
                        }
                        $section->items()->create($itemData);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'تم تحديث القسم والعناصر بنجاح');
    }

}
