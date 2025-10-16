<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use App\Models\OrderLog;
use App\Models\OrderAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EnhancedEmployeeController extends Controller
{
    /**
     * Display employees list.
     */
    public function index(Request $request)
    {
        $query = Employee::with('role')
                ->withCount([
                    'assignedOrders',
                    'assignedOrders as active_orders_count' => function ($q) {
                        $q->whereHas('order', function ($sub) {
                            $sub->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                        });
                    },
                    'assignedOrders as completed_orders_count' => function ($q) {
                        $q->whereHas('order', function ($sub) {
                            $sub->where('status', 'delivered');
                        });
                    }
                ]);


        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(20);
        $roles = Role::where('is_active', true)->get();

        return view('admin.employees.enhanced-index', compact('employees', 'roles'));
    }

    /**
     * Show employee details.
     */
    public function show(Employee $employee)
{
    $employee->load('role.permissions', 'assignedOrders.order');

    /*$stats = [
        'total_orders' => $employee->assignedOrders()->count(),

        'active_orders' => $employee->assignedOrders()
            ->whereHas('order', function ($q) {
                $q->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
            })
            ->count(),

        'completed_orders' => $employee->assignedOrders()
            ->whereHas('order', function ($q) {
                $q->where('status', 'delivered');
            })
            ->count(),

        'pending_orders' => $employee->assignedOrders()
            ->whereHas('order', function ($q) {
                $q->where('status', 'pending');
            })
            ->count(),
    ];*/

    $recentOrders = $employee->assignedOrders()
        ->with('order')
        ->latest()
        ->limit(10)
        ->get();



        /*$employee->load([
            'assignedOrders' => function ($query) {
                $query->with(['package', 'user'])
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);*/

        $stats = $this->getEmployeeStats($employee);
        $performance = $this->getEmployeePerformance($employee);
        $recentActivities = $this->getEmployeeRecentActivities($employee);



    return view('admin.employees.show', compact('employee', 'stats', 'recentOrders','performance','recentActivities'));
}


    /**
     * Show create employee form.
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.employees.enhanced-create', compact('roles'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'job_title' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            //'is_active' => 'boolean',
        ]);

        $employee = Employee::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'job_title' => $validated['job_title'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    /**
     * Show edit employee form.
     */
    public function edit(Employee $employee)
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.employees.enhanced-edit', compact('employee', 'roles'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'job_title' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'job_title' => $validated['job_title'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        return redirect()->route('admin.employees.enhanced.index')
            ->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        // Check if employee has active orders
        $activeOrders = $employee->assignedOrders()
            ->whereNotIn('status', ['delivered', 'cancelled', 'archived'])
            ->count();

        if ($activeOrders > 0) {
            return back()->with('error', 'لا يمكن حذف الموظف لأنه مرتبط بطلبات نشطة');
        }

        $employee->delete();

        return redirect()->route('admin.employees.enhanced.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }

    /**
     * Toggle employee active status.
     */
    public function toggleStatus(Employee $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);

        return back()->with('success', 'تم تحديث حالة الموظف بنجاح');
    }

    /**
     * Show employee performance.
     */
    public function performance(Employee $employee)
    {
        $stats = [
            'total_orders' => $employee->assignedOrders()->count(),
            'completed_orders' => $employee->assignedOrders()
                ->where('status', 'delivered')
                ->count(),
            'pending_orders' => $employee->assignedOrders()
                ->whereNotIn('status', ['delivered', 'cancelled', 'archived'])
                ->count(),
            'cancelled_orders' => $employee->assignedOrders()
                ->where('status', 'cancelled')
                ->count(),
        ];

        // Calculate completion rate
        $stats['completion_rate'] = $stats['total_orders'] > 0
            ? round(($stats['completed_orders'] / $stats['total_orders']) * 100, 2)
            : 0;

        // Get monthly performance
        $monthlyPerformance = $employee->assignedOrders()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total,
                         SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as completed')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        return view('admin.employees.performance', compact('employee', 'stats', 'monthlyPerformance'));
    }

    /**
     * Assign orders to employee.
     */
    public function assignOrders(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        foreach ($validated['order_ids'] as $orderId) {
            OrderAssignment::updateOrCreate(
                ['order_id' => $orderId],
                [
                    'employee_id' => $employee->id,
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'تم تعيين الطلبات للموظف بنجاح');
    }

 /**
     * Unassign orders from employee.
     */
    public function unassignOrders(User $employee, Request $request)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        try {
            DB::beginTransaction();

            $unassignedCount = 0;
            foreach ($request->order_ids as $orderId) {
                $order = Order::find($orderId);

                if ($order && $order->assigned_to == $employee->id) {
                    $order->update(['assigned_to' => null]);

                    // Log the unassignment
                    OrderLog::createLog(
                        $order->id,
                        'unassigned',
                        $employee->name,
                        null,
                        "تم إلغاء تعيين الطلب من الموظف {$employee->name}"
                    );

                    $unassignedCount++;
                }
            }

            DB::commit();

            return back()->with('success', "تم إلغاء تعيين {$unassignedCount} طلب من الموظف");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إلغاء تعيين الطلبات: ' . $e->getMessage());
        }
    }

     /**
     * Build permissions array.
     */
    private function buildPermissions(Request $request): array
    {
        $permissions = [];

        if ($request->boolean('can_manage_orders')) {
            $permissions[] = 'manage_orders';
        }

        if ($request->boolean('can_view_financial')) {
            $permissions[] = 'view_financial';
        }

        if ($request->boolean('can_manage_customers')) {
            $permissions[] = 'manage_customers';
        }

        if ($request->boolean('can_generate_reports')) {
            $permissions[] = 'generate_reports';
        }

        return $permissions;
    }

    /**
     * Get employee statistics.
     */
    private function getEmployeeStats(Employee $employee): array
    {
        return [
            'total_orders' => $employee->assignedOrders()->count(),

            'active_orders' => $employee->assignedOrders()
                ->whereHas('order', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                })
                ->count(),

            'completed_orders' => $employee->assignedOrders()
                ->whereHas('order', function ($q) {
                    $q->where('status', 'delivered');
                })
                ->count(),

            'overdue_orders' => $employee->assignedOrders()
                ->whereHas('order', function ($q) {
                    $q->where('expected_delivery_date', '<', now())
                      ->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                })
                ->count(),

            'this_month_completed' => $employee->assignedOrders()
                ->whereHas('order', function ($q) {
                    $q->where('status', 'delivered')
                      ->whereMonth('delivered_at', now()->month);
                })
                ->count(),
        ];
    }


    /**
     * Get employee performance metrics.
     */
    private function getEmployeePerformance(Employee $employee): array
    {$completedOrders = $employee->assignedOrders()
        ->whereHas('order', function ($query) {
            $query->where('status', 'delivered')
                  ->whereNotNull('delivered_at');
        })
        ->get();

    $totalOrders = $employee->assignedOrders()->count();

    $completionRate = $totalOrders > 0
        ? round(($completedOrders->count() / $totalOrders) * 100, 2)
        : 0;

    $onTimeDeliveries = $completedOrders->filter(function ($assignment) {
        return optional($assignment->order)->delivered_at <= optional($assignment->order)->expected_delivery_date;
    });

    $onTimeRate = $completedOrders->count() > 0
        ? round(($onTimeDeliveries->count() / $completedOrders->count()) * 100, 2)
        : 0;

    return [
        'completion_rate' => $completionRate,
        'on_time_delivery_rate' => $onTimeRate,
        'average_completion_days' => $this->getAverageCompletionTime($employee),
        'customer_satisfaction' => 95,
    ];

    }

    /**
     * Get employee recent activities.
     */
    private function getEmployeeRecentActivities(Employee $employee): array
    {
        return OrderLog::where('user_id', $employee->id)
            ->orWhereHas('order', function ($query) use ($employee) {
                $query->where('assigned_to', $employee->id);
            })
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get average completion time for employee.
     */
    private function getAverageCompletionTime(Employee $employee, $startDate = null, $endDate = null): float
{
    $query = $employee->assignedOrders()
        ->whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->where('status', 'delivered')
              ->whereNotNull('delivered_at');

            if ($startDate && $endDate) {
                $q->whereBetween('delivered_at', [$startDate, $endDate]);
            }
        })
        ->with('order');

    $assignments = $query->get();

    if ($assignments->isEmpty()) {
        return 0;
    }

    $totalDays = $assignments->sum(function ($assignment) {
        $order = $assignment->order;
        return $order ? $order->created_at->diffInDays($order->delivered_at) : 0;
    });

    return round($totalDays / $assignments->count(), 1);
}


    /**
     * Get on-time delivery rate for employee.
     */
    private function getOnTimeDeliveryRate(Employee $employee, $startDate = null, $endDate = null): float
    {
        $query = $employee->assignedOrders()
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->whereNotNull('expected_delivery_date');

        if ($startDate && $endDate) {
            $query->whereBetween('delivered_at', [$startDate, $endDate]);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            return 0;
        }

        $onTimeOrders = $orders->filter(function ($order) {
            return $order->delivered_at <= $order->expected_delivery_date;
        });

        return round(($onTimeOrders->count() / $orders->count()) * 100, 2);
    }

    /**
     * Get daily performance data.
     */
    private function getDailyPerformance(Employee $employee, $startDate, $endDate): array
    {
        $performance = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $completed = $employee->assignedOrders()
                ->where('status', 'delivered')
                ->whereDate('delivered_at', $current)
                ->count();

            $performance[] = [
                'date' => $current->format('Y-m-d'),
                'completed_orders' => $completed,
            ];

            $current->addDay();
        }

        return $performance;
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate(string $period): \Carbon\Carbon
    {
        return match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }

    /**
     * Display permissions matrix.
     */
    /*
    public function permissionsMatrix()
    {
        $roles = Role::with('permissions')->where('is_active', true)->get();

        // Define all available permissions grouped by module
        $permissionGroups = [
            'إدارة المحتوى' => [
                'content.view',
                'content.create',
                'content.edit',
                'content.delete',
            ],
            'إدارة أسئلة الاستبيان' => [
                'survey.view',
                'survey.create',
                'survey.edit',
                'survey.delete',
            ],
            'إدارة الباكجات' => [
                'packages.view',
                'packages.create',
                'packages.edit',
                'packages.delete',
            ],
            'إدارة المدونات' => [
                'blog.view',
                'blog.create',
                'blog.edit',
                'blog.delete',
            ],
            'إدارة المعارض' => [
                'gallery.view',
                'gallery.create',
                'gallery.edit',
                'gallery.delete',
            ],
            'الأسئلة الشائعة' => [
                'faq.view',
                'faq.create',
                'faq.edit',
                'faq.delete',
            ],
            'طلب مساعدة' => [
                'support.view',
                'support.respond',
            ],
            'رسائل اتصل بنا' => [
                'contact.view',
                'contact.respond',
            ],
            'الرئيسية' => [
                'dashboard.view',
                'reports.view',
            ],
        ];

        return view('admin.employees.permissions-matrix', compact('roles', 'permissionGroups'));
    }
*/

public function permissionsMatrix()
{
    $roles = Role::with('permissions')->where('is_active', true)->get();

    // جلب كل الموديولات (المجموعات)
    $modules = \App\Models\Permission::select('module')
        ->distinct()
        ->orderBy('module')
        ->pluck('module');

    // تجهيز المصفوفة بحيث تحتوي على الصلاحيات لكل مجموعة (module)
    $permissionGroups = [];

    foreach ($modules as $module) {
        foreach ($modules as $module) {
            $translatedModule = __("modules.$module"); // 👈 ترجمة الموديول من ملف modules.php
            $permissionGroups[$translatedModule] = \App\Models\Permission::where('module', $module)
                ->orderBy('id')
                ->get(['name', 'display_name']);
        }
    }

    return view('admin.employees.permissions-matrix', compact('roles', 'permissionGroups'));
}



}

