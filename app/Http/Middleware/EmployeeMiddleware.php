<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EmployeeMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('employee')->check()) {
            return $next($request);
        }

        return redirect()->route('employee.login'); // لو مش مسجل دخول
    }
}
