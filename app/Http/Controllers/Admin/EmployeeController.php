<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display employees list.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'employee')
            ->withCount([
                'assignedOrders',
                'assignedOrders as active_orders_count' => function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                },
                'assignedOrders as completed_orders_count' => function ($q) {
                    $q->where('status', 'delivered');
                }
            ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(20);

        $departments = User::where('role', 'employee')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show employee details.
     */
    public function show(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $employee->load([
            'assignedOrders' => function ($query) {
                $query->with(['package', 'user'])
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);

        $stats = $this->getEmployeeStats($employee);
        $performance = $this->getEmployeePerformance($employee);
        $recentActivities = $this->getEmployeeRecentActivities($employee);

        return view('admin.employees.show', compact(
            'employee',
            'stats',
            'performance',
            'recentActivities'
        ));
    }

    /**
     * Show create employee form.
     */
    public function create()
    {
        $departments = User::where('role', 'employee')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.employees.create', compact('departments'));
    }

    /**
     * Store new employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'password' => 'required|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
            'can_manage_orders' => 'boolean',
            'can_view_financial' => 'boolean',
            'can_manage_customers' => 'boolean',
            'can_generate_reports' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $employee = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'employee_id' => $request->employee_id ?: $this->generateEmployeeId(),
                'department' => $request->department,
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'password' => Hash::make($request->password),
                'role' => 'employee',
                'is_active' => $request->boolean('is_active', true),
                'permissions' => $this->buildPermissions($request),
                'metadata' => [
                    'can_manage_orders' => $request->boolean('can_manage_orders'),
                    'can_view_financial' => $request->boolean('can_view_financial'),
                    'can_manage_customers' => $request->boolean('can_manage_customers'),
                    'can_generate_reports' => $request->boolean('can_generate_reports'),
                    'created_by' => auth()->id(),
                ]
            ]);

            DB::commit();

            return redirect()
                ->route('admin.employees.show', $employee)
                ->with('success', 'تم إضافة الموظف بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Show edit employee form.
     */
    public function edit(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $departments = User::where('role', 'employee')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update employee.
     */
    public function update(User $employee, Request $request)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'phone' => 'nullable|string|max:20',
            'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($employee->id)],
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'password' => 'nullable|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
            'can_manage_orders' => 'boolean',
            'can_view_financial' => 'boolean',
            'can_manage_customers' => 'boolean',
            'can_generate_reports' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'employee_id' => $request->employee_id ?: $employee->employee_id,
                'department' => $request->department,
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'is_active' => $request->boolean('is_active', true),
                'permissions' => $this->buildPermissions($request),
                'metadata' => array_merge($employee->metadata ?? [], [
                    'can_manage_orders' => $request->boolean('can_manage_orders'),
                    'can_view_financial' => $request->boolean('can_view_financial'),
                    'can_manage_customers' => $request->boolean('can_manage_customers'),
                    'can_generate_reports' => $request->boolean('can_generate_reports'),
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ])
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $employee->update($updateData);

            DB::commit();

            return redirect()
                ->route('admin.employees.show', $employee)
                ->with('success', 'تم تحديث بيانات الموظف بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Delete employee.
     */
    public function destroy(User $employee)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        try {
            DB::beginTransaction();

            // Check if employee has active orders
            $activeOrdersCount = $employee->assignedOrders()
                ->whereNotIn('status', ['delivered', 'cancelled', 'archived'])
                ->count();

            if ($activeOrdersCount > 0) {
                return back()->with('error', 'لا يمكن حذف الموظف لأن لديه طلبات نشطة');
            }

            // Soft delete the employee
            $employee->update(['is_active' => false]);
            $employee->delete();

            DB::commit();

            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'تم حذف الموظف بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Assign orders to employee.
     */
    public function assignOrders(User $employee, Request $request)
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

            $assignedCount = 0;
            foreach ($request->order_ids as $orderId) {
                $order = Order::find($orderId);
                
                if ($order && !$order->assigned_to) {
                    $order->update(['assigned_to' => $employee->id]);
                    
                    // Log the assignment
                    OrderLog::createLog(
                        $order->id,
                        'assigned',
                        null,
                        $employee->name,
                        "تم تعيين الطلب للموظف {$employee->name}"
                    );

                    $assignedCount++;
                }
            }

            DB::commit();

            return back()->with('success', "تم تعيين {$assignedCount} طلب للموظف");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تعيين الطلبات: ' . $e->getMessage());
        }
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
     * Get employee workload.
     */
    public function workload(Request $request)
    {
        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->withCount([
                'assignedOrders as total_orders',
                'assignedOrders as active_orders' => function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                },
                'assignedOrders as overdue_orders' => function ($q) {
                    $q->where('expected_delivery_date', '<', now())
                      ->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                }
            ])
            ->orderBy('active_orders', 'desc')
            ->get();

        return view('admin.employees.workload', compact('employees'));
    }

    /**
     * Get employee performance report.
     */
    public function performance(User $employee, Request $request)
    {
        if ($employee->role !== 'employee') {
            abort(404);
        }

        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $performance = [
            'orders_completed' => $employee->assignedOrders()
                ->where('status', 'delivered')
                ->whereBetween('delivered_at', [$startDate, $endDate])
                ->count(),
            'orders_assigned' => $employee->assignedOrders()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'average_completion_time' => $this->getAverageCompletionTime($employee, $startDate, $endDate),
            'on_time_delivery_rate' => $this->getOnTimeDeliveryRate($employee, $startDate, $endDate),
            'customer_satisfaction' => 95, // Placeholder
            'daily_performance' => $this->getDailyPerformance($employee, $startDate, $endDate),
        ];

        return response()->json($performance);
    }

    /**
     * Generate employee ID.
     */
    private function generateEmployeeId(): string
    {
        $year = now()->year;
        $lastEmployee = User::where('role', 'employee')
            ->whereNotNull('employee_id')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastEmployee && $lastEmployee->employee_id) {
            $lastSequence = (int) substr($lastEmployee->employee_id, -3);
            $sequence = $lastSequence + 1;
        }

        return "EMP{$year}" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
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
    private function getEmployeeStats(User $employee): array
    {
        return [
            'total_orders' => $employee->assignedOrders()->count(),
            'active_orders' => $employee->assignedOrders()
                ->whereNotIn('status', ['delivered', 'cancelled', 'archived'])
                ->count(),
            'completed_orders' => $employee->assignedOrders()
                ->where('status', 'delivered')
                ->count(),
            'overdue_orders' => $employee->assignedOrders()
                ->where('expected_delivery_date', '<', now())
                ->whereNotIn('status', ['delivered', 'cancelled', 'archived'])
                ->count(),
            'this_month_completed' => $employee->assignedOrders()
                ->where('status', 'delivered')
                ->whereMonth('delivered_at', now()->month)
                ->count(),
        ];
    }

    /**
     * Get employee performance metrics.
     */
    private function getEmployeePerformance(User $employee): array
    {
        $completedOrders = $employee->assignedOrders()
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->get();

        $totalOrders = $employee->assignedOrders()->count();
        $completionRate = $totalOrders > 0 ? 
            round(($completedOrders->count() / $totalOrders) * 100, 2) : 0;

        $onTimeDeliveries = $completedOrders->filter(function ($order) {
            return $order->delivered_at <= $order->expected_delivery_date;
        });

        $onTimeRate = $completedOrders->count() > 0 ? 
            round(($onTimeDeliveries->count() / $completedOrders->count()) * 100, 2) : 0;

        return [
            'completion_rate' => $completionRate,
            'on_time_delivery_rate' => $onTimeRate,
            'average_completion_days' => $this->getAverageCompletionTime($employee),
            'customer_satisfaction' => 95, // Placeholder
        ];
    }

    /**
     * Get employee recent activities.
     */
    private function getEmployeeRecentActivities(User $employee): array
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
    private function getAverageCompletionTime(User $employee, $startDate = null, $endDate = null): float
    {
        $query = $employee->assignedOrders()
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at');

        if ($startDate && $endDate) {
            $query->whereBetween('delivered_at', [$startDate, $endDate]);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            return 0;
        }

        $totalDays = $orders->sum(function ($order) {
            return $order->created_at->diffInDays($order->delivered_at);
        });

        return round($totalDays / $orders->count(), 1);
    }

    /**
     * Get on-time delivery rate for employee.
     */
    private function getOnTimeDeliveryRate(User $employee, $startDate = null, $endDate = null): float
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
    private function getDailyPerformance(User $employee, $startDate, $endDate): array
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
}
