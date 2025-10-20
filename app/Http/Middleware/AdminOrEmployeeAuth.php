<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrEmployeeAuth
{
    public function handle(Request $request, Closure $next)
    {
        // لو المستخدم مسجل دخول كـ admin
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // أو مسجل دخول كـ employee
        if (Auth::guard('employee')->check()) {
            return $next($request);
        }

        // لو مش أي واحد منهم → رجّعه لصفحة الدخول
        return redirect()->route('admin.login');
    }
}
