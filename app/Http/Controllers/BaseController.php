<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as ControllerBase;
use Illuminate\Support\Facades\View;

class BaseController extends ControllerBase
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth('employee')->check()) {
                $employee = auth('employee')->user();

                // جلب الصلاحيات الخاصة بالموظف
                $permissions = $employee->permissions()->pluck('name')->toArray();

                // مشاركة الصلاحيات مع كل views
                View::share('employeePermissions', $permissions);

                // جلب الصفحات المسموح له برؤيتها
                $visiblePages = [];
                foreach ($permissions as $perm) {
                    if ($perm === 'orders.*') {
                        $visiblePages['orders'] = ['view','create','edit','delete','assign'];
                    } else {
                        [$module, $action] = explode('.', $perm);
                        $visiblePages[$module][] = $action;
                    }
                }
                View::share('visiblePages', $visiblePages);
            }

            return $next($request);
        });
    }
}
