<?php

namespace App\Http\Controllers\Employee\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * إظهار صفحة تسجيل الدخول للموظف
     */
    public function showLoginForm()
    {
        return view('employee.auth.login'); // اعمل بليد جديد login.blade.php
    }

    /**
     * تسجيل الدخول
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('employee')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => __('البريد الإلكتروني أو كلمة المرور غير صحيحة.'),
        ])->onlyInput('email');
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }
}
