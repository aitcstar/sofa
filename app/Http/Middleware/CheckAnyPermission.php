<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\PermissionController;

class CheckAnyPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$permissions
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has any of the required permissions
        if (!PermissionController::hasAnyPermission($user, $permissions)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'ليس لديك صلاحية للوصول إلى هذا المورد',
                    'permissions_required' => $permissions
                ], 403);
            }

            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
