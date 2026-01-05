<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\HelpRequest;
use App\Models\PageContent;

class HelpController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page','help')->first();
        $content = PageContent::where('page','help')->first();

        $countries = [
            ['code' => 'sa', 'name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'dial_code' => '+966'],
            ['code' => 'ae', 'name_ar' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'dial_code' => '+971'],
            ['code' => 'kw', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'dial_code' => '+965'],
            ['code' => 'qa', 'name_ar' => 'قطر', 'name_en' => 'Qatar', 'dial_code' => '+974'],
            ['code' => 'bh', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'dial_code' => '+973'],
            ['code' => 'om', 'name_ar' => 'عمان', 'name_en' => 'Oman', 'dial_code' => '+968'],
            ['code' => 'jo', 'name_ar' => 'الأردن', 'name_en' => 'Jordan', 'dial_code' => '+962'],
            ['code' => 'lb', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'dial_code' => '+961'],
            ['code' => 'eg', 'name_ar' => 'مصر', 'name_en' => 'Egypt', 'dial_code' => '+20'],
            ['code' => 'ma', 'name_ar' => 'المغرب', 'name_en' => 'Morocco', 'dial_code' => '+212'],
            ['code' => 'dz', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'dial_code' => '+213'],
            ['code' => 'tn', 'name_ar' => 'تونس', 'name_en' => 'Tunisia', 'dial_code' => '+216'],
            ['code' => 'sd', 'name_ar' => 'السودان', 'name_en' => 'Sudan', 'dial_code' => '+249'],
            ['code' => 'iq', 'name_ar' => 'العراق', 'name_en' => 'Iraq', 'dial_code' => '+964'],
            ['code' => 'sy', 'name_ar' => 'سوريا', 'name_en' => 'Syria', 'dial_code' => '+963'],
            ['code' => 'ye', 'name_ar' => 'اليمن', 'name_en' => 'Yemen', 'dial_code' => '+967'],
            ['code' => 'ps', 'name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'dial_code' => '+970'],
            ['code' => 'us', 'name_ar' => 'الولايات المتحدة', 'name_en' => 'United States', 'dial_code' => '+1'],
            ['code' => 'gb', 'name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'dial_code' => '+44'],
            ['code' => 'de', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'dial_code' => '+49'],
            ['code' => 'fr', 'name_ar' => 'فرنسا', 'name_en' => 'France', 'dial_code' => '+33'],
            ['code' => 'it', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'dial_code' => '+39'],
            ['code' => 'es', 'name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'dial_code' => '+34'],
            ['code' => 'ca', 'name_ar' => 'كندا', 'name_en' => 'Canada', 'dial_code' => '+1'],
            ['code' => 'au', 'name_ar' => 'أستراليا', 'name_en' => 'Australia', 'dial_code' => '+61'],
            ['code' => 'in', 'name_ar' => 'الهند', 'name_en' => 'India', 'dial_code' => '+91'],
            ['code' => 'pk', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'dial_code' => '+92'],
            ['code' => 'tr', 'name_ar' => 'تركيا', 'name_en' => 'Turkey', 'dial_code' => '+90'],
            ['code' => 'ir', 'name_ar' => 'إيران', 'name_en' => 'Iran', 'dial_code' => '+98'],
        ];


        return view('frontend.pages.help', compact('seo','content','countries'));
    }

/*
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'units' => 'nullable|integer',
            'message' => 'nullable|string',
        ]);

        HelpRequest::create($validated);

        $message = __('help.helper_text');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);



    }
*/

public function submit(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'company' => 'nullable|string|max:255',
        'email' => 'required|email',
        'country_code' => 'required|string|max:10',
        'phone' => 'required|string|max:20',
        'units' => 'nullable|integer|min:1',
        'message' => 'nullable|string',
        // --- new fields ---
        'project_size' => 'required|string|in:small,medium,large',
        'client_type' => 'required|string|in:individual,company',
        'has_interior_plan' => 'nullable|boolean',
        'needs_finishing_help' => 'nullable|boolean',
        'needs_color_help' => 'nullable|boolean',
    ]);

    // تحويل القيم الفارغة للـ checkboxes إلى false
    $validated['has_interior_plan'] = (bool) ($request->has_interior_plan ?? false);
    $validated['needs_finishing_help'] = (bool) ($request->needs_finishing_help ?? false);
    $validated['needs_color_help'] = (bool) ($request->needs_color_help ?? false);

    HelpRequest::create($validated);

    $message = __('help.request_submitted_successfully');

    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'message' => $message]);
    }

    return back()->with('success', $message);
}
}
