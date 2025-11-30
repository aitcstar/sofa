<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // ← لا تنسَ هذا في الأعلى
use App\Models\SeoSetting;
use App\Models\AboutPage;
class AuthController extends Controller
{

    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'        => 'required|string',
            'code' => 'required|string',
        ]);



        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_login_tab', true);
        }


        $phone = trim($request->phone);
        $country_code = trim($request->code);

        $user = User::where('phone', $phone)
            ->where('code', $country_code)
            ->first();
            //dd($user );

        if (!$user) {
            return redirect()->back()
                ->withErrors(['phone' => __('site.phone_not_registered')])
                ->withInput()
                ->with('open_login_tab', true);
        }

        // توليد كود OTP جديد
        $user->otpcode = '12345';//rand(10000, 99999);
        $user->save();

        // حفظ في الجلسة
        session(['otp_user_id' => $user->id]);

        return redirect()->back()->with('show_otp_modal', true);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|unique:users,phone',
            'email'        => 'required|string|unique:users,email',
            'country_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_register_tab', true); // لفتح تاب التسجيل تلقائيًا
        }


        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'code'      => $request->country_code,
            'otpcode'   => 12345,//rand(10000, 99999), // أو استخدم OTP حقيقي
        ]);

        // حفظ بيانات المستخدم في الجلسة لاستخدامها في التحقق
        session([
            'otp_user_id' => $user->id,
            'otp_phone'   => $user->phone,
            'otp_from_registration' => true,
        ]);

        // إعادة التوجيه لنفس الصفحة مع فلش يطلب فتح مودال OTP
        return redirect()->back()->with('show_otp_modal', true);
    }



    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|array|size:5',
            'code.*' => 'required|numeric|digits:1',
        ]);

        $fullCode = implode('', $request->code);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('home')->withErrors(['error' => __('site.session_expired')]);
        }

        $user = User::find($userId);
        if ($user && $user->otpcode == $fullCode) {
            Auth::login($user);

            // ✅ احفظ الحالة قبل مسح الجلسة
            $fromRegistration = session()->has('otp_from_registration');

            // الآن نسيان كل بيانات OTP من الجلسة
            session()->forget(['otp_user_id', 'otp_phone', 'otp_from_registration']);

            if ($fromRegistration) {
                $seo = SeoSetting::where('page', 'about')->first();
                $sections = AboutPage::all();
                return view('frontend.pages.welcome', compact('seo', 'sections'));
            }

            return redirect()->intended('/');
        }

        return redirect()->back()
            ->with('otp_error', __('site.invalid_otp'))
            ->with('show_otp_modal', true);
    }
}
