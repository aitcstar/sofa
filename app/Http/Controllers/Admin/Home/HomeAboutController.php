<?php
namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use App\Models\HomeAboutSection;
use App\Models\HomeAboutIcon;
use Illuminate\Http\Request;

class HomeAboutController extends Controller
{
    public function edit()
    {
        $about = HomeAboutSection::first();
        $icons = HomeAboutIcon::orderBy('order')->get();
        return view('admin.home.about.edit', compact('about', 'icons'));
    }

    /*public function update(Request $request)
    {
        $request->validate([
            'sub_title_en' => 'required|string',
            'sub_title_ar' => 'required|string',
            'title_en'     => 'required|string',
            'title_ar'     => 'required|string',
            'desc_en'      => 'required|string',
            'desc_ar'      => 'required|string',
            'button_text_en' => 'nullable|string',
            'button_text_ar' => 'nullable|string',
            'button_link'  => 'nullable|url',
            'image'        => 'nullable|image|max:2048',
        ]);

        $about = HomeAboutSection::firstOrNew([]);
        $data = $request->only([
            'sub_title_en', 'sub_title_ar',
            'title_en', 'title_ar',
            'desc_en', 'desc_ar',
            'button_text_en', 'button_text_ar',
            'button_link'
        ]);

        // صورة
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('about', 'public');
            $data['image'] = $path;
        }

        $about->fill($data)->save();

        // تحديث أو إضافة الأيقونات
        if ($request->has('icons')) {
            foreach ($request->icons as $iconData) {
                // تحقق مما إذا كان 'id' موجودًا وصالحًا
                $icon = isset($iconData['id']) && !empty($iconData['id'])
                    ? HomeAboutIcon::find($iconData['id'])
                    : new HomeAboutIcon();

                // رفع الأيقونة لو كانت ملفًا جديدًا
                if (isset($iconData['icon']) && $iconData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = $iconData['icon']->store('about_icons', 'public');
                    $icon->icon = $path;
                }

                $icon->title_ar = $iconData['title_ar'] ?? '';
                $icon->title_en = $iconData['title_en'] ?? '';
                $icon->order    = $iconData['order'] ?? 1;

                $icon->save();
            }
        }


        return redirect()->back()->with('success', 'تم تحديث البيانات بنجاح');
    }*/

    public function update(Request $request)
{
    $request->validate([
        'sub_title_en' => 'required|string',
        'sub_title_ar' => 'required|string',
        'title_en'     => 'required|string',
        'title_ar'     => 'required|string',
        'desc_en'      => 'required|string',
        'desc_ar'      => 'required|string',
        'button_text_en' => 'nullable|string',
        'button_text_ar' => 'nullable|string',
        'button_link'  => 'nullable|url',
        'image'        => 'nullable|image|max:2048',
    ]);

    // احفظ بيانات القسم الرئيسي أولاً
    $about = HomeAboutSection::firstOrNew([]);
    $data = $request->only([
        'sub_title_en', 'sub_title_ar',
        'title_en', 'title_ar',
        'desc_en', 'desc_ar',
        'button_text_en', 'button_text_ar',
        'button_link'
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('about', 'public');
        $data['image'] = $path;
    }

    $about->fill($data)->save(); // تأكد من أن $about->id متوفر الآن


// جمع معرفات الأيقونات المرسلة (الموجودة في الطلب)
$submittedIds = collect($request->input('icons', []))
    ->pluck('id')
    ->filter()
    ->map(fn($id) => (int) $id)
    ->all();

// حذف جميع الأيقونات المرتبطة بهذا القسم والتي لم تُرسَل
HomeAboutIcon::where('home_about_section_id', $about->id)
    ->when(!empty($submittedIds), function ($query) use ($submittedIds) {
        $query->whereNotIn('id', $submittedIds);
    })
    ->delete();

// الآن احفظ/حدّث الأيقونات المرسلة
if ($request->has('icons')) {
    foreach ($request->icons as $iconData) {
        $icon = isset($iconData['id']) && !empty($iconData['id'])
            ? HomeAboutIcon::find($iconData['id'])
            : new HomeAboutIcon();

        $icon->home_about_section_id = $about->id;

        if (isset($iconData['icon']) && $iconData['icon'] instanceof \Illuminate\Http\UploadedFile) {
            $path = $iconData['icon']->store('about_icons', 'public');
            $icon->icon = $path;
        }

        $icon->title_ar = $iconData['title_ar'] ?? '';
        $icon->title_en = $iconData['title_en'] ?? '';
        $icon->order    = $iconData['order'] ?? 1;

        $icon->save();
    }
 }
    return redirect()->back()->with('success', 'تم تحديث البيانات بنجاح');
}
}
