<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SecurityLog;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip for certain routes
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Check if 2FA is required for this user
        if ($this->requiresTwoFactor($user)) {
            // Check if 2FA is already verified for this session
            if (!session('two_factor_verified')) {
                SecurityLog::logSuspiciousActivity(
                    'محاولة الوصول بدون تفعيل المصادقة الثنائية',
                    $user->id,
                    [
                        'ip' => $request->ip(),
                        'route' => $request->route()->getName(),
                        'url' => $request->fullUrl()
                    ]
                );

                return $this->redirectToTwoFactor($request);
            }
        }

        return $next($request);
    }

    /**
     * Check if the route should skip 2FA verification.
     */
    private function shouldSkip(Request $request): bool
    {
        $skipRoutes = [
            'two-factor.show',
            'two-factor.verify',
            'two-factor.resend',
            'logout',
            'password.request',
            'password.reset',
        ];

        $routeName = $request->route()->getName();
        
        return in_array($routeName, $skipRoutes) || 
               str_starts_with($routeName, 'two-factor.');
    }

    /**
     * Check if user requires two-factor authentication.
     */
    private function requiresTwoFactor($user): bool
    {
        // Check if 2FA is globally required
        if (config('security.two_factor_required', false)) {
            return true;
        }

        // Check if user has 2FA enabled
        if ($user->two_factor_enabled ?? false) {
            return true;
        }

        // Check if user role requires 2FA
        $rolesRequiring2FA = ['admin', 'super_admin', 'manager'];
        if ($user->hasAnyRole($rolesRequiring2FA)) {
            return true;
        }

        // Check if accessing sensitive areas
        $request = request();
        $sensitiveAreas = [
            'admin/users',
            'admin/security',
            'admin/settings',
            'admin/reports',
            'admin/financial',
        ];

        foreach ($sensitiveAreas as $area) {
            if (str_starts_with($request->path(), $area)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Redirect to two-factor authentication page.
     */
    private function redirectToTwoFactor(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'المصادقة الثنائية مطلوبة',
                'code' => 'TWO_FACTOR_REQUIRED',
                'redirect_url' => route('two-factor.show')
            ], 403);
        }

        return redirect()->route('two-factor.show')
            ->with('intended_url', $request->fullUrl())
            ->with('warning', 'المصادقة الثنائية مطلوبة للوصول إلى هذه الصفحة');
    }
}
