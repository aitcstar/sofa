<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderTimeline;
use App\Models\OrderAssignment;
use App\Models\User;
use App\Models\Package;
use App\Models\Employee;
use App\Models\OrderStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OrdersExport;

class EnhancedOrderController extends Controller
{
    /**
     * Display enhanced orders listing.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'package', 'assignedEmployee', 'activeAssignments.user']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Check for overdue orders
        if ($request->filled('overdue') && $request->overdue == '1') {
            $query->where('expected_delivery_date', '<', now())
                  ->whereNotIn('status', ['completed', 'cancelled', 'delivered']);
        }

        // Check for duplicates
        if ($request->filled('duplicates') && $request->duplicates == '1') {
            $query->where('is_duplicate', true);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $employees = User::where('role', 'employee')->orWhere('role', 'admin')->get();
        $packages = Package::all();

        // Get statistics
        $stats = $this->getOrderStatistics();

        return view('admin.orders.enhanced.index', compact('orders', 'employees', 'packages', 'stats'));
    }

    /**
     * Show enhanced order details.
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'package',
            'assignedEmployee',
            'activeAssignments.user',
            'timeline',
            'logs.user',
            'invoices',
            'payments',
            'paymentSchedules'
        ]);

        // Get available employees for assignment
        $employees = Employee::where('is_active',1)->get();//User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.orders.enhanced.show', compact('order', 'employees'));
    }

    /**
     * Show create order form.
     */
    public function create()
    {
        $packages = Package::all();
        $customers = User::where('role', 'customer')->get();
        $employees = Employee::where('is_active',1)->get();//User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.orders.enhanced.create', compact('packages', 'customers', 'employees'));
    }

    /**
     * Store new order.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'units_count' => 'required|integer|min:1',
            'project_type' => 'required|string',
            'current_stage' => 'required|string',
            'client_type' => 'required',
            'priority' => 'nullable',
            'base_amount' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'paid_amount' => 'nullable|numeric|min:0',
            'expected_delivery_date' => 'nullable|date',
            'diagrams' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        //DB::beginTransaction();

        try {
            // Find or create customer
            $customer = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'country_code' => $request->country_code,
                    'role' => 'customer',
                    //'password' => bcrypt(str_random(16)),
                ]
            );

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

            // Handle file upload
            $diagramsPath = null;
            if ($request->hasFile('diagrams')) {
                $diagramsPath = $request->file('diagrams')->store('orders/diagrams', 'public');
            }

            // Create order
            $order = Order::create([
                'user_id' => $customer->id,
                'package_id' => $request->package_id,
                'order_number' => $orderNumber,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'units_count' => $request->units_count,
                'project_type' => $request->project_type,
                'current_stage' => $request->current_stage,
                'client_type' => $request->client_type,
                'commercial_register' => $request->commercial_register,
                'tax_number' => $request->tax_number,
                'has_interior_design' => $request->has('has_interior_design'),
                'needs_finishing_help' => $request->has('needs_finishing_help'),
                'needs_color_help' => $request->has('needs_color_help'),
                'diagrams_path' => $diagramsPath,
                'priority' => $request->priority ?? 'normal',
                'base_amount' => $request->base_amount,
                'tax_amount' => $request->tax_amount,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount ?? 0,
                'payment_status' => $request->payment_status,
                'payment_schedule' => $request->payment_schedule,
                'expected_delivery_date' => $request->expected_delivery_date,
                'internal_notes' => $request->internal_notes,
                'status' => 'pending',
            ]);

            // Create default timeline
            //$this->createDefaultTimeline($order);
            // ربط المراحل بالطلب الجديد تلقائيًا
            $stages = OrderStage::orderBy('order_number')->get();

            foreach ($stages as $stage) {
                $order->stageStatuses()->create([
                    'order_stage_id' => $stage->id,
                    'status' => 'not_started',
                ]);
            }
            // Log order creation
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'action' => 'created',
                'description' => 'تم إنشاء الطلب من لوحة التحكم',
            ]);


            // حفظ جدول الدفعات
            if ($request->has('payment_schedule')) {
                foreach ($request->payment_schedule as $schedule) {
                    $order->paymentSchedules()->create([
                        'amount' => $schedule['amount'] ?? 0,
                        'due_date' => $schedule['due_date'] ?? null,
                        'status' => $schedule['status'] ?? 'unpaid',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.enhanced.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح');

        } catch (\Exception $e) {
            //DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show edit order form.
     */
    public function edit(Order $order)
    {
        $packages = Package::all();
        $employees = Employee::where('is_active',1)->get();//User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.orders.enhanced.edit', compact('order', 'packages', 'employees'));
    }

    /**
     * Update order.
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'package_id' => 'required|exists:packages,id',
            'units_count' => 'required|integer|min:1',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:normal,high,urgent',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $oldStatus = $order->status;

            $order->update($request->except(['diagrams']));

            // Handle file upload
            if ($request->hasFile('diagrams')) {
                // Delete old file
                if ($order->diagrams_path) {
                    Storage::disk('public')->delete($order->diagrams_path);
                }
                $order->diagrams_path = $request->file('diagrams')->store('orders/diagrams', 'public');
                $order->save();
            }

            // Log status change if changed
            if ($oldStatus !== $order->status) {
                OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'action' => 'status_changed',
                    'old_data' => $oldStatus,
                    'new_data' => $order->status,
                    'description' => "تم تغيير الحالة من {$oldStatus} إلى {$order->status}",
                ]);
            }

            DB::commit();

            return redirect()->route('admin.orders.enhanced.show', $order)
                ->with('success', 'تم تحديث الطلب بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Log status change
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'old_data' => $oldStatus,
            'new_data' => $request->status,
            'description' => $request->notes ?? "تم تغيير الحالة من {$oldStatus} إلى {$request->status}",
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    /**
     * Assign employee to order.
     */
    public function assignEmployee(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:employees,id',
            'role' => 'required',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        //DB::beginTransaction();

        try {
            // Create assignment
            OrderAssignment::create([
                'order_id' => $order->id,
                'user_id' => $request->user_id,
                'assigned_by' => $order->user_id,
                'assigned_at' => now(),
                'role' => $request->role,
                'notes' => $request->notes,
                'is_active' => true,
            ]);

            // Update order assigned_to if not set
            if (!$order->assigned_to) {
                $order->assigned_to = $request->user_id;
                $order->save();
            }

            // Log assignment
            $user =  Employee::find($request->user_id);//User::find($request->user_id);
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'action' => 'employee_assigned',
                'description' => "تم تعيين {$user->name} كـ {$request->role}",
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'تم تعيين الموظف بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Remove employee assignment.
     */
    public function removeAssignment($assignmentId)
    {
        //dd($assignmentId);
        $assignment = OrderAssignment::findOrFail($assignmentId);

        $assignment->is_active = false;
        $assignment->save();

        // Log removal
        OrderLog::create([
            'order_id' => $assignment->order_id,
            'user_id' => $assignment->user_id,
            'action' => 'employee_unassigned',
            'description' => "تم إلغاء تعيين {$assignment->user->name}",
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Add note to order.
     */
    public function addNote(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $order->internal_notes = ($order->internal_notes ? $order->internal_notes . "\n\n" : '') .
                                 '[' . now()->format('Y-m-d H:i') . '] ' . auth()->user()->name . ":\n" .
                                 $request->note;
        $order->save();

        // Log note
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'action' => 'note_added',
            'description' => 'تم إضافة ملاحظة جديدة',
        ]);

        return redirect()->back()->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    /**
     * Update order timeline.
     */
    /*
    public function updateTimeline(Request $request, Order $order)
    {
        DB::beginTransaction();

        try {
            // Delete existing timeline
            OrderTimeline::where('order_id', $order->id)->delete();

            // Create new timeline stages
            if ($request->has('stages')) {
                foreach ($request->stages as $index => $stage) {
                    OrderTimeline::create([
                        'order_id' => $order->id,
                        'stage_name' => $stage['name'],
                        'stage_order' => $index + 1,
                        'is_completed' => isset($stage['completed']),
                        'completed_at' => isset($stage['completed']) ? now() : null,
                    ]);
                }
            }

            // Log timeline update
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'action' => 'timeline_updated',
                'description' => 'تم تحديث مراحل الطلب',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'تم تحديث المراحل بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }*/

    public function updateTimeline(Request $request, Order $order)
    {
        // كل الـ stageStatuses الحالية للطلب
        $allStatuses = $order->stageStatuses()->get()->keyBy('order_stage_id');

        // كل مراحل الطلب من الجدول
        $allStages = \App\Models\OrderStage::pluck('id');

        foreach ($allStages as $stageId) {
            $completed = isset($request->stages[$stageId]['completed']);

            if ($allStatuses->has($stageId)) {
                // تحديث الحالة الموجودة
                $allStatuses[$stageId]->update([
                    'status' => $completed ? 'completed' : 'not_started', // أو 'in_progress' حسب اللي تحبه
                    'completed_at' => $completed ? now() : null,
                ]);
            } else {
                // إنشاء سجل جديد لو مش موجود
                $order->stageStatuses()->create([
                    'order_stage_id' => $stageId,
                    'status' => $completed ? 'completed' : 'not_started', // أو 'in_progress'
                    'completed_at' => $completed ? now() : null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'تم تحديث مراحل الطلب بنجاح');
    }


    public function detailsHtml(Order $order)
{
    return view('admin.financial.enhanced.partials.order-details', compact('order'));
}




    /**
     * Show reports page.
     */
    public function reports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $query = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->with(['user', 'package'])->get();

        // Calculate statistics
        $stats = [
            'total_orders' => $orders->count(),
            'completed_orders' => $orders->where('status', 'completed')->count(),
            'in_progress_orders' => $orders->where('status', 'in_progress')->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'avg_completion_time' => $this->calculateAvgCompletionTime($orders),
            'completion_rate' => $orders->count() > 0 ? ($orders->where('status', 'completed')->count() / $orders->count()) * 100 : 0,
            'avg_order_value' => $orders->count() > 0 ? $orders->avg('total_amount') : 0,
        ];

        // Prepare chart data
        $chartData = $this->prepareChartData($orders);

        return view('admin.orders.enhanced.reports', compact('orders', 'stats', 'chartData'));
    }

    /**
     * Export orders.
     */
    public function export(Request $request)
    {
        $format = $request->input('export', 'excel');

        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $query = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->with(['user', 'package'])->get();

        if ($format === 'excel') {
            return Excel::download(new OrdersExport($orders), 'orders_' . date('Y-m-d') . '.xlsx');
        } elseif ($format === 'pdf') {
            $pdf = PDF::loadView('admin.orders.export-pdf', compact('orders'));
            return $pdf->download('orders_' . date('Y-m-d') . '.pdf');
        }

        return redirect()->back()->with('error', 'صيغة التصدير غير مدعومة');
    }

    /**
     * Import orders.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            Excel::import(new OrdersImport, $request->file('file'));

            return redirect()->back()->with('success', 'تم استيراد الطلبات بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics.
     */
    private function getOrderStatistics()
    {
        return [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'overdue' => Order::where('expected_delivery_date', '<', now())
                              ->whereNotIn('status', ['completed', 'cancelled'])
                              ->count(),
            'total_revenue' => Order::whereIn('payment_status', ['paid', 'partial'])->sum('paid_amount'),
            'pending_revenue' => Order::where('payment_status', 'pending')->sum('total_amount'),
        ];
    }

    /**
     * Create default timeline for order.
     */
    /*
    private function createDefaultTimeline($order)
    {
        $stages = [
            'استلام الطلب',
            'مراجعة المتطلبات',
            'التصميم الأولي',
            'الموافقة على التصميم',
            'بدء التنفيذ',
            'المراجعة النهائية',
            'التسليم',
        ];

        foreach ($stages as $index => $stage) {
            OrderTimeline::create([
                'order_id' => $order->id,
                'stage_name' => $stage,
                'stage_order' => $index + 1,
                'is_completed' => $index === 0, // First stage is completed by default
                'completed_at' => $index === 0 ? now() : null,
            ]);
        }
    }*/

    /**
     * Calculate average completion time.
     */
    private function calculateAvgCompletionTime($orders)
    {
        $completedOrders = $orders->where('status', 'completed')->where('delivered_at', '!=', null);

        if ($completedOrders->count() === 0) {
            return 0;
        }

        $totalDays = 0;
        foreach ($completedOrders as $order) {
            $totalDays += $order->created_at->diffInDays($order->delivered_at);
        }

        return round($totalDays / $completedOrders->count());
    }

    /**
     * Prepare chart data.
     */
    private function prepareChartData($orders)
    {
        // Status distribution
        $statusData = [
            'pending' => $orders->where('status', 'pending')->count(),
            'in_progress' => $orders->where('status', 'in_progress')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        // Payment status distribution
        $paymentStatusData = [
            'unpaid' => $orders->where('payment_status', 'unpaid')->count(),
            'partial' => $orders->where('payment_status', 'partial')->count(),
            'paid' => $orders->where('payment_status', 'paid')->count(),
        ];

        // Monthly data
        $monthlyLabels = [];
        $monthlyOrders = [];
        $monthlyRevenue = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');

            $monthOrders = $orders->filter(function($order) use ($date) {
                return $order->created_at->format('Y-m') === $date->format('Y-m');
            });

            $monthlyOrders[] = $monthOrders->count();
            $monthlyRevenue[] = $monthOrders->sum('total_amount');
        }

        // Project types
        $projectTypes = $orders->groupBy('project_type');
        $projectTypeLabels = [];
        $projectTypeData = [];

        foreach ($projectTypes as $type => $typeOrders) {
            $projectTypeLabels[] = $this->getProjectTypeText($type);
            $projectTypeData[] = $typeOrders->count();
        }

        // Top packages
        $packageGroups = $orders->groupBy('package_id');
        $topPackageLabels = [];
        $topPackageData = [];

        foreach ($packageGroups->take(5) as $packageId => $packageOrders) {
            $package = Package::find($packageId);
            $topPackageLabels[] = $package ? $package->name : 'غير محدد';
            $topPackageData[] = $packageOrders->count();
        }

        return [
            'status' => $statusData,
            'payment_status' => $paymentStatusData,
            'monthly' => [
                'labels' => $monthlyLabels,
                'orders' => $monthlyOrders,
                'revenue' => $monthlyRevenue,
            ],
            'project_types' => [
                'labels' => $projectTypeLabels,
                'data' => $projectTypeData,
            ],
            'top_packages' => [
                'labels' => $topPackageLabels,
                'data' => $topPackageData,
            ],
        ];
    }

    /**
     * Get project type text.
     */
    private function getProjectTypeText($type)
    {
        $types = [
            'villa' => 'فيلا',
            'apartment' => 'شقة',
            'office' => 'مكتب',
            'commercial' => 'تجاري',
            'other' => 'أخرى',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Add payment schedule to order.
     */
    public function addPaymentSchedule(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,paid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $schedule = $order->paymentSchedules()->create([
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status ?? 'unpaid',
        ]);

        // Log activity
        $order->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'payment_schedule_added',
            'description' => 'تم إضافة دفعة جديدة بقيمة ' . number_format($request->amount, 2) . ' ريال',
            'old_data' => null,
            'new_data' => json_encode($schedule),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الدفعة بنجاح',
            'schedule' => $schedule
        ]);
    }

    /**
     * Update payment schedule status.
     */
    public function updatePaymentSchedule(Request $request, Order $order, $scheduleId)
    {
        $schedule = $order->paymentSchedules()->findOrFail($scheduleId);

        $oldStatus = $schedule->status;
        $schedule->update([
            'status' => $request->status
        ]);

        // Update order paid amount if status changed to paid
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            $order->increment('paid_amount', $schedule->amount);

            // Update payment status
            if ($order->paid_amount >= $order->total_amount) {
                $order->update(['payment_status' => 'paid']);
            } else {
                $order->update(['payment_status' => 'partial']);
            }
        }

        // Log activity
        $order->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'payment_schedule_updated',
            'description' => 'تم تحديث حالة الدفعة إلى ' . ($request->status === 'paid' ? 'مدفوعة' : 'غير مدفوعة'),
            'old_data' => json_encode($oldStatus),
            'new_data' => json_encode($request->status),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الدفعة بنجاح'
        ]);
    }

    /**
     * Delete payment schedule.
     */
    public function deletePaymentSchedule(Order $order, $scheduleId)
    {
        $schedule = $order->paymentSchedules()->findOrFail($scheduleId);

        $amount = $schedule->amount;
        $schedule->delete();

        // Log activity
        $order->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'payment_schedule_deleted',
            'description' => 'تم حذف دفعة بقيمة ' . number_format($amount, 2) . ' ريال',
            'old_data' => json_encode($schedule),
            'new_data' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدفعة بنجاح'
        ]);
    }
}

