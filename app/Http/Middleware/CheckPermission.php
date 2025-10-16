<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Check if user is authenticated as employee
        if (auth()->guard('employee')->check()) {
            $employee = auth()->guard('employee')->user();

            // Check if employee is active
            if (!$employee->is_active) {
                auth()->guard('employee')->logout();
                return redirect()->route('employee.login')->with('error', 'حسابك غير مفعل');
            }

            // If no permissions specified, just check authentication
            if (empty($permissions)) {
                return $next($request);
            }

            // Check if employee has any of the required permissions
            foreach ($permissions as $permission) {
                if ($employee->hasPermission($permission)) {
                    return $next($request);
                }
            }

            // Employee doesn't have required permission
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'ليس لديك صلاحية للوصول إلى هذا المورد',
                    'permissions_required' => $permissions
                ], 403);
            }

            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        // Check if user is authenticated as admin
        if (auth()->guard('admin')->check()) {
            // Admins have all permissions
            return $next($request);
        }

        // Not authenticated
        return redirect()->route('employee.login')->with('error', 'يجب تسجيل الدخول أولاً');
    }
}

