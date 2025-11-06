<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::first(); // عندك سجل واحد فقط
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'       => 'nullable|string|max:255',
            'email'           => 'nullable|email',
            'worktime'         => 'nullable|string',
            'phone'           => 'nullable|string|max:50',
            'whatsapp'        => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'snapchat'        => 'nullable|url',
            'tiktok'         => 'nullable|url',
            'instagram'       => 'nullable|url',
            'linkedin'        => 'nullable|url',
            'youtube'         => 'nullable|url',
            'seo_title'      => 'nullable|string|max:255',
            'seo_description'=> 'nullable|string',
            'seo_title_en'      => 'nullable|string|max:255',
            'seo_description_en'=> 'nullable|string',
            'seo_keywords'   => 'nullable|string',
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $setting->fill($request->all());
        $setting->save();

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
