<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Http\Controllers\Admin\PermissionController;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Directive to check if user has permission
        Blade::if('permission', function (string $permission) {
            return PermissionController::hasPermission(auth()->user(), $permission);
        });

        // Directive to check if user has any of the permissions
        Blade::if('anypermission', function (...$permissions) {
            return PermissionController::hasAnyPermission(auth()->user(), $permissions);
        });

        // Directive to check if user has all permissions
        Blade::if('allpermissions', function (...$permissions) {
            return PermissionController::hasAllPermissions(auth()->user(), $permissions);
        });

        // Directive to check if user is admin
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->role === 'admin';
        });

        // Directive to check if user is employee
        Blade::if('employee', function () {
            return auth()->check() && auth()->user()->role === 'employee';
        });

        // Directive to check if user is customer
        Blade::if('customer', function () {
            return auth()->check() && auth()->user()->role === 'customer';
        });

        // Directive to check user role
        Blade::if('role', function (string $role) {
            return auth()->check() && auth()->user()->role === $role;
        });
    }
}
