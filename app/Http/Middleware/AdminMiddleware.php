<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // لو المستخدم داخل بجارد الأدمن
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // لو مش أدمن → يرجعه لصفحة تسجيل الدخول
        return redirect()->route('admin.login')->withErrors(['email' => 'يجب تسجيل الدخول كأدمن للوصول.']);
    }
}
