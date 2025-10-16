<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Available permissions in the system.
     */
    private array $availablePermissions = [
        'dashboard' => [
            'view_dashboard' => 'عرض لوحة التحكم',
            'view_analytics' => 'عرض التحليلات والإحصائيات',
        ],
        'orders' => [
            'view_orders' => 'عرض الطلبات',
            'create_orders' => 'إنشاء طلبات جديدة',
            'edit_orders' => 'تعديل الطلبات',
            'delete_orders' => 'حذف الطلبات',
            'assign_orders' => 'تعيين الطلبات للموظفين',
            'change_order_status' => 'تغيير حالة الطلبات',
            'view_order_timeline' => 'عرض تايم لاين الطلبات',
            'manage_order_timeline' => 'إدارة تايم لاين الطلبات',
        ],
        'customers' => [
            'view_customers' => 'عرض العملاء',
            'create_customers' => 'إضافة عملاء جدد',
            'edit_customers' => 'تعديل بيانات العملاء',
            'delete_customers' => 'حذف العملاء',
            'view_customer_orders' => 'عرض طلبات العملاء',
        ],
        'financial' => [
            'view_financial' => 'عرض البيانات المالية',
            'manage_invoices' => 'إدارة الفواتير',
            'manage_payments' => 'إدارة المدفوعات',
            'view_financial_reports' => 'عرض التقارير المالية',
            'export_financial_data' => 'تصدير البيانات المالية',
        ],
        'employees' => [
            'view_employees' => 'عرض الموظفين',
            'create_employees' => 'إضافة موظفين جدد',
            'edit_employees' => 'تعديل بيانات الموظفين',
            'delete_employees' => 'حذف الموظفين',
            'manage_permissions' => 'إدارة صلاحيات الموظفين',
            'view_employee_performance' => 'عرض أداء الموظفين',
        ],
        'crm' => [
            'view_leads' => 'عرض العملاء المحتملين',
            'manage_leads' => 'إدارة العملاء المحتملين',
            'view_quotes' => 'عرض العروض',
            'manage_quotes' => 'إدارة العروض',
            'view_crm_reports' => 'عرض تقارير CRM',
        ],
        'marketing' => [
            'view_campaigns' => 'عرض الحملات التسويقية',
            'manage_campaigns' => 'إدارة الحملات التسويقية',
            'manage_coupons' => 'إدارة الكوبونات',
            'view_marketing_analytics' => 'عرض تحليلات التسويق',
        ],
        'content' => [
            'manage_gallery' => 'إدارة المعرض',
            'manage_packages' => 'إدارة الباكجات',
            'manage_questionnaire' => 'إدارة الاستبيان',
            'manage_website_content' => 'إدارة محتوى الموقع',
        ],
        'system' => [
            'view_system_logs' => 'عرض سجلات النظام',
            'manage_notifications' => 'إدارة الإشعارات',
            'manage_automation' => 'إدارة الأتمتة',
            'system_backup' => 'نسخ احتياطي للنظام',
            'system_settings' => 'إعدادات النظام',
        ],
        'reports' => [
            'view_all_reports' => 'عرض جميع التقارير',
            'generate_reports' => 'إنشاء التقارير',
            'export_reports' => 'تصدير التقارير',
            'schedule_reports' => 'جدولة التقارير',
        ]
    ];

    /**
     * Display permissions management.
     */
    public function index()
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        $permissions = $this->availablePermissions;

        return view('admin.permissions.index', compact('employees', 'permissions'));
    }

    /**
     * Show employee permissions.
     */
    public function show(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $employeePermissions = $employee->permissions ?? [];
        $permissions = $this->availablePermissions;

        return view('admin.permissions.show', compact('employee', 'employeePermissions', 'permissions'));
    }

    /**
     * Update employee permissions.
     */
    public function update(User $employee, Request $request)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        try {
            DB::beginTransaction();

            // Validate that all requested permissions exist
            $requestedPermissions = $request->permissions ?? [];
            $validPermissions = $this->getValidPermissions($requestedPermissions);

            $employee->update([
                'permissions' => $validPermissions,
                'metadata' => array_merge($employee->metadata ?? [], [
                    'permissions_updated_by' => auth()->id(),
                    'permissions_updated_at' => now(),
                ])
            ]);

            DB::commit();

            return redirect()
                ->route('admin.permissions.show', $employee)
                ->with('success', 'تم تحديث صلاحيات الموظف بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update permissions for multiple employees.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'action' => 'required|in:add,remove,replace',
        ]);

        try {
            DB::beginTransaction();

            $employees = User::whereIn('id', $request->employee_ids)
                ->where('role', 'employee')
                ->get();

            $requestedPermissions = $request->permissions ?? [];
            $validPermissions = $this->getValidPermissions($requestedPermissions);

            $updatedCount = 0;

            foreach ($employees as $employee) {
                $currentPermissions = $employee->permissions ?? [];

                $newPermissions = match($request->action) {
                    'add' => array_unique(array_merge($currentPermissions, $validPermissions)),
                    'remove' => array_diff($currentPermissions, $validPermissions),
                    'replace' => $validPermissions,
                };

                $employee->update([
                    'permissions' => array_values($newPermissions),
                    'metadata' => array_merge($employee->metadata ?? [], [
                        'permissions_bulk_updated_by' => auth()->id(),
                        'permissions_bulk_updated_at' => now(),
                    ])
                ]);

                $updatedCount++;
            }

            DB::commit();

            return back()->with('success', "تم تحديث صلاحيات {$updatedCount} موظف بنجاح");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage());
        }
    }

    /**
     * Create permission template.
     */
    public function createTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
        ]);

        try {
            $validPermissions = $this->getValidPermissions($request->permissions);

            $template = [
                'name' => $request->name,
                'description' => $request->description,
                'permissions' => $validPermissions,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ];

            // Store template in cache or database
            $templates = cache()->get('permission_templates', []);
            $templates[] = $template;
            cache()->put('permission_templates', $templates, now()->addYear());

            return back()->with('success', 'تم إنشاء قالب الصلاحيات بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إنشاء القالب: ' . $e->getMessage());
        }
    }

    /**
     * Apply permission template to employees.
     */
    public function applyTemplate(Request $request)
    {
        $request->validate([
            'template_index' => 'required|integer',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $templates = cache()->get('permission_templates', []);
            
            if (!isset($templates[$request->template_index])) {
                return back()->with('error', 'القالب المحدد غير موجود');
            }

            $template = $templates[$request->template_index];
            $employees = User::whereIn('id', $request->employee_ids)
                ->where('role', 'employee')
                ->get();

            $updatedCount = 0;

            foreach ($employees as $employee) {
                $employee->update([
                    'permissions' => $template['permissions'],
                    'metadata' => array_merge($employee->metadata ?? [], [
                        'template_applied' => $template['name'],
                        'template_applied_by' => auth()->id(),
                        'template_applied_at' => now(),
                    ])
                ]);

                $updatedCount++;
            }

            DB::commit();

            return back()->with('success', "تم تطبيق القالب على {$updatedCount} موظف بنجاح");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تطبيق القالب: ' . $e->getMessage());
        }
    }

    /**
     * Get permission templates.
     */
    public function getTemplates()
    {
        $templates = cache()->get('permission_templates', []);
        return response()->json($templates);
    }

    /**
     * Delete permission template.
     */
    public function deleteTemplate(Request $request)
    {
        $request->validate([
            'template_index' => 'required|integer',
        ]);

        try {
            $templates = cache()->get('permission_templates', []);
            
            if (isset($templates[$request->template_index])) {
                unset($templates[$request->template_index]);
                $templates = array_values($templates); // Re-index array
                cache()->put('permission_templates', $templates, now()->addYear());
                
                return back()->with('success', 'تم حذف القالب بنجاح');
            }

            return back()->with('error', 'القالب المحدد غير موجود');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف القالب: ' . $e->getMessage());
        }
    }

    /**
     * Get permissions report.
     */
    public function report()
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        $permissionStats = [];
        $allPermissions = $this->getAllPermissionKeys();

        foreach ($allPermissions as $permission) {
            $count = $employees->filter(function ($employee) use ($permission) {
                return in_array($permission, $employee->permissions ?? []);
            })->count();

            $permissionStats[$permission] = [
                'name' => $this->getPermissionName($permission),
                'count' => $count,
                'percentage' => $employees->count() > 0 ? round(($count / $employees->count()) * 100, 1) : 0,
            ];
        }

        return view('admin.permissions.report', compact('employees', 'permissionStats'));
    }

    /**
     * Check if user has permission.
     */
    public static function hasPermission(User $user, string $permission): bool
    {
        if ($user->role === 'admin') {
            return true; // Admins have all permissions
        }

        if ($user->role !== 'employee') {
            return false;
        }

        return in_array($permission, $user->permissions ?? []);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public static function hasAnyPermission(User $user, array $permissions): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'employee') {
            return false;
        }

        $userPermissions = $user->permissions ?? [];
        return !empty(array_intersect($permissions, $userPermissions));
    }

    /**
     * Check if user has all of the given permissions.
     */
    public static function hasAllPermissions(User $user, array $permissions): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'employee') {
            return false;
        }

        $userPermissions = $user->permissions ?? [];
        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Validate and filter permissions.
     */
    private function getValidPermissions(array $permissions): array
    {
        $allPermissions = $this->getAllPermissionKeys();
        return array_values(array_intersect($permissions, $allPermissions));
    }

    /**
     * Get all permission keys.
     */
    private function getAllPermissionKeys(): array
    {
        $keys = [];
        foreach ($this->availablePermissions as $category => $permissions) {
            $keys = array_merge($keys, array_keys($permissions));
        }
        return $keys;
    }

    /**
     * Get permission name by key.
     */
    private function getPermissionName(string $key): string
    {
        foreach ($this->availablePermissions as $category => $permissions) {
            if (isset($permissions[$key])) {
                return $permissions[$key];
            }
        }
        return $key;
    }

    /**
     * Get default permission templates.
     */
    public function getDefaultTemplates(): array
    {
        return [
            [
                'name' => 'موظف مبيعات',
                'description' => 'صلاحيات أساسية لموظف المبيعات',
                'permissions' => [
                    'view_dashboard',
                    'view_orders',
                    'create_orders',
                    'edit_orders',
                    'view_customers',
                    'create_customers',
                    'edit_customers',
                    'view_customer_orders',
                    'view_leads',
                    'manage_leads',
                    'view_quotes',
                    'manage_quotes',
                ]
            ],
            [
                'name' => 'مدير العمليات',
                'description' => 'صلاحيات إدارة العمليات والطلبات',
                'permissions' => [
                    'view_dashboard',
                    'view_analytics',
                    'view_orders',
                    'create_orders',
                    'edit_orders',
                    'assign_orders',
                    'change_order_status',
                    'view_order_timeline',
                    'manage_order_timeline',
                    'view_customers',
                    'view_employees',
                    'view_employee_performance',
                    'view_all_reports',
                    'generate_reports',
                ]
            ],
            [
                'name' => 'محاسب',
                'description' => 'صلاحيات الشؤون المالية والمحاسبة',
                'permissions' => [
                    'view_dashboard',
                    'view_financial',
                    'manage_invoices',
                    'manage_payments',
                    'view_financial_reports',
                    'export_financial_data',
                    'view_orders',
                    'view_customers',
                ]
            ],
            [
                'name' => 'مسؤول تسويق',
                'description' => 'صلاحيات التسويق وإدارة المحتوى',
                'permissions' => [
                    'view_dashboard',
                    'view_campaigns',
                    'manage_campaigns',
                    'manage_coupons',
                    'view_marketing_analytics',
                    'manage_gallery',
                    'manage_packages',
                    'manage_questionnaire',
                    'manage_website_content',
                    'view_leads',
                    'manage_leads',
                ]
            ]
        ];
    }
}
