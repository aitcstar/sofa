<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactSection;
use App\Models\SeoSetting;

class ContactSectionController extends Controller
{
    public function edit()
    {
        $page = 'contact';
        $seoSettings = SeoSetting::all()->keyBy('page');

        $section = ContactSection::first();
        return view('admin.contact.edit', compact('section','page','seoSettings'));
    }

    public function update(Request $request)
    {
        $data = $request->only([
            'title_ar','title_en','desc_ar','desc_en',
            'main_showroom_ar','main_showroom_en',
            'work_hours_ar','work_hours_en',
            'cta_heading_ar','cta_heading_en',
            'cta_text_ar','cta_text_en',
            'city_ar','city_en','address_ar','address_en','lat','lng','maptitle_ar','maptitle_en','maptitle_en','mapaddress_ar','mapaddress_en'
        ]);

        $section = ContactSection::first();
        if ($section) {
            $section->update($data);
        } else {
            ContactSection::create($data);
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات صفحة اتصل بنا بنجاح');
    }
}
