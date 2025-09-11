<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use App\Models\SeoSetting;

class ContactController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page','contact')->first();

        $countries = [
            ['code' => 'sa', 'name_ar' => 'السعودية', 'dial_code' => '+966'],
            ['code' => 'ae', 'name_ar' => 'الإمارات', 'dial_code' => '+971'],
            ['code' => 'kw', 'name_ar' => 'الكويت', 'dial_code' => '+965'],
            ['code' => 'qa', 'name_ar' => 'قطر', 'dial_code' => '+974'],
            ['code' => 'bh', 'name_ar' => 'البحرين', 'dial_code' => '+973'],
            ['code' => 'om', 'name_ar' => 'عمان', 'dial_code' => '+968'],
            ['code' => 'jo', 'name_ar' => 'الأردن', 'dial_code' => '+962'],
            ['code' => 'lb', 'name_ar' => 'لبنان', 'dial_code' => '+961'],
            ['code' => 'eg', 'name_ar' => 'مصر', 'dial_code' => '+20'],
            ['code' => 'ma', 'name_ar' => 'المغرب', 'dial_code' => '+212'],
            ['code' => 'dz', 'name_ar' => 'الجزائر', 'dial_code' => '+213'],
            ['code' => 'tn', 'name_ar' => 'تونس', 'dial_code' => '+216'],
            ['code' => 'ly', 'name_ar' => 'ليبيا', 'dial_code' => '+218'],
            ['code' => 'sd', 'name_ar' => 'السودان', 'dial_code' => '+249'],
            ['code' => 'iq', 'name_ar' => 'العراق', 'dial_code' => '+964'],
            ['code' => 'sy', 'name_ar' => 'سوريا', 'dial_code' => '+963'],
            ['code' => 'ye', 'name_ar' => 'اليمن', 'dial_code' => '+967'],
            ['code' => 'ps', 'name_ar' => 'فلسطين', 'dial_code' => '+970'],
            ['code' => 'us', 'name_ar' => 'الولايات المتحدة', 'dial_code' => '+1'],
            ['code' => 'gb', 'name_ar' => 'المملكة المتحدة', 'dial_code' => '+44'],
            ['code' => 'de', 'name_ar' => 'ألمانيا', 'dial_code' => '+49'],
            ['code' => 'fr', 'name_ar' => 'فرنسا', 'dial_code' => '+33'],
            ['code' => 'it', 'name_ar' => 'إيطاليا', 'dial_code' => '+39'],
            ['code' => 'es', 'name_ar' => 'إسبانيا', 'dial_code' => '+34'],
            ['code' => 'ca', 'name_ar' => 'كندا', 'dial_code' => '+1'],
            ['code' => 'au', 'name_ar' => 'أستراليا', 'dial_code' => '+61'],
            ['code' => 'in', 'name_ar' => 'الهند', 'dial_code' => '+91'],
            ['code' => 'pk', 'name_ar' => 'باكستان', 'dial_code' => '+92'],
            ['code' => 'tr', 'name_ar' => 'تركيا', 'dial_code' => '+90'],
            ['code' => 'ir', 'name_ar' => 'إيران', 'dial_code' => '+98']
        ];

        return view('frontend.pages.contact', compact('seo','countries'));
    }

    public function submit(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'country_code' => 'required',
            'message' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى ملء جميع الحقول المطلوبة بشكل صحيح',
                'errors' => $validator->errors()
            ], 422);
        }
        //dd($validator);

        try {
            Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'message' => $request->message,
            ]);


            // مثال: إرسال بريد إلكتروني
            /*
            Mail::to('support@sofa.com')->send(new ContactFormSubmitted($request->all()));
            */

            return redirect()->back()->with('success', __('site.message_sent_success'));


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }
}

