<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // ← لا تنسَ هذا في الأعلى
use App\Models\SeoSetting;
use App\Models\AboutPage;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;

class AuthController extends Controller
{


    public function checkPhone(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => __('site.phone_not_registered')])->with('open_login_tab', true);
        }

        $otp = rand(10000, 99999);
        $user->otpcode = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        session(['otp_user_id' => $user->id]);
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return back()->with('show_otp_modal', true);
    }

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('site.email_not_registered')])->with('open_login_tab', true);
        }

        $otp = rand(10000, 99999);
        $user->otpcode = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        session(['otp_user_id' => $user->id]);
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return back()->with('show_otp_modal', true);
    }


    /*
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

        // ✅ التحقق من صحة الكود وصلاحية OTP
        if (!$user || $user->otpcode != $fullCode || ($user->otp_expires_at && \Carbon\Carbon::parse($user->otp_expires_at)->isPast())    ) {
            return redirect()->back()
                ->with('otp_error', __('site.invalid_or_expired_otp'))
                ->with('show_otp_modal', true);
        }

        // تسجيل الدخول
        Auth::login($user);

        // ✅ حذف OTP بعد الاستخدام
        $user->otpcode = null;
        $user->otp_expires_at = null;
        $user->save();

        // تنظيف الجلسة
        $fromRegistration = session()->has('otp_from_registration');
        session()->forget(['otp_user_id', 'otp_phone', 'otp_from_registration']);

        if ($fromRegistration) {
            $seo = SeoSetting::where('page', 'about')->first();
            $sections = AboutPage::all();
            $minUnits = Setting::value('min_units') ?? 1; // ⚡ هنا نضيف المتغير

            return view('frontend.pages.welcome', compact('seo', 'sections','minUnits'));
        }

        // 🔁 لو في طلب كان متخزن قبل تسجيل الدخول → نرجع نكمله
        if (session()->has('redirect_after_login')) {
            return redirect()->to(session()->pull('redirect_after_login'));
        }

        return redirect()->intended('/');


        //return redirect()->intended('/');
    }
*/

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

    if (
        !$user ||
        $user->otpcode != $fullCode ||
        ($user->otp_expires_at && \Carbon\Carbon::parse($user->otp_expires_at)->isPast())
    ) {
        return redirect()->back()
            ->with('otp_error', __('site.invalid_or_expired_otp'))
            ->with('show_otp_modal', true);
    }

    Auth::login($user);

    $user->otpcode = null;
    $user->otp_expires_at = null;
    $user->save();

    $fromRegistration = session()->has('otp_from_registration');
    $redirectAfterLogin = session()->pull('redirect_after_login'); // 👈 هنا
    session()->forget(['otp_user_id', 'otp_phone', 'otp_from_registration']);

    if ($fromRegistration) {
        $seo = SeoSetting::where('page', 'about')->first();
        $sections = AboutPage::all();
        $minUnits = Setting::value('min_units') ?? 1;

        return view('frontend.pages.welcome', compact('seo', 'sections','minUnits'));
    }

    // ✅ لو فيه صفحة مطلوبة قبل تسجيل الدخول
    if ($redirectAfterLogin) {
        return redirect()->to($redirectAfterLogin);
    }

    return redirect()->route('home');
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

        $otp = rand(10000, 99999);
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'code'      => $request->country_code,
            'otpcode'   => $otp,//rand(10000, 99999), // أو استخدم OTP حقيقي
            'role'      => 'customer'
        ]);

        // حفظ بيانات المستخدم في الجلسة لاستخدامها في التحقق
        session([
            'otp_user_id' => $user->id,
            'otp_phone'   => $user->phone,
            'otp_from_registration' => true,
        ]);

        try {
            Mail::to($user->email)->send(new SendOtpMail($otp));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'email' => 'حدث خطأ أثناء إرسال رمز التحقق. حاول مرة أخرى.'
            ]);
        }


        // إعادة التوجيه لنفس الصفحة مع فلش يطلب فتح مودال OTP
        return redirect()->back()->with('show_otp_modal', true);
    }

}
