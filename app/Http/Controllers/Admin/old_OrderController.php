<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class old_OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'package', 'assignedEmployee'])
                     ->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب الموظف المعين
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // الطلبات المتأخرة
        if ($request->filled('overdue') && $request->overdue == '1') {
            $query->overdue();
        }

        // الطلبات عالية الأولوية
        if ($request->filled('high_priority') && $request->high_priority == '1') {
            $query->highPriority();
        }

        $orders = $query->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Order::count(),
            'pending' => Order::pending()->count(),
            'confirmed' => Order::confirmed()->count(),
            'in_progress' => Order::inProgress()->count(),
            'shipped' => Order::shipped()->count(),
            'delivered' => Order::delivered()->count(),
            'overdue' => Order::overdue()->count(),
            'high_priority' => Order::highPriority()->count()
        ];

        $employees = User::where('role', 'employee')->get();
        $packages = Package::all();

        return view('admin.orders.index', compact('orders', 'stats', 'employees', 'packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = Package::all();
        $employees = User::where('role', 'employee')->get();
        $customers = User::where('role', 'customer')->get();

        return view('admin.orders.create', compact('packages', 'employees', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'email' => 'nullable|email|max:255',
            'units_count' => 'required|integer|min:1',
            'project_type' => 'required|in:large,medium,small',
            'current_stage' => 'required|in:design,execution,operation',
            'colors' => 'nullable|array',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|integer|between:1,5',
            'expected_delivery_date' => 'nullable|date|after:today',
            'total_amount' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            // إنشاء الطلب
            $order = Order::create([
                'user_id' => $request->user_id,
                'package_id' => $request->package_id,
                'order_number' => $this->generateOrderNumber(),
                'name' => $request->name,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'email' => $request->email,
                'units_count' => $request->units_count,
                'project_type' => $request->project_type,
                'current_stage' => $request->current_stage,
                'has_interior_design' => $request->has('has_interior_design'),
                'needs_finishing_help' => $request->has('needs_finishing_help'),
                'needs_color_help' => $request->has('needs_color_help'),
                'colors' => $request->colors,
                'assigned_to' => $request->assigned_to,
                'status' => 'pending',
                'priority' => $request->priority,
                'expected_delivery_date' => $request->expected_delivery_date,
                'total_amount' => $request->total_amount,
                'internal_notes' => $request->internal_notes,
                'last_activity_at' => now()
            ]);

            // فحص الطلبات المكررة
            if ($order->checkForDuplicates()) {
                $original = Order::where('phone', $order->phone)
                                ->where('email', $order->email)
                                ->where('package_id', $order->package_id)
                                ->where('id', '!=', $order->id)
                                ->where('created_at', '>=', now()->subDays(30))
                                ->first();

                if ($original) {
                    $order->markAsDuplicate($original->id);
                }
            }

            // تسجيل النشاط
            $order->logActivity('created', 'تم إنشاء الطلب من لوحة التحكم', auth()->id());

            // إنشاء فاتورة إذا تم تحديد المبلغ
            if ($request->total_amount) {
                $invoice = $this->createInvoiceForOrder($order, $request->total_amount);
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                           ->with('success', 'تم إنشاء الطلب بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'package', 'assignedEmployee', 'logs.user', 'invoices', 'payments']);

        $timeline_steps = $order->getTimelineSteps();
        $employees = User::where('role', 'employee')->get();

        return view('admin.orders.show', compact('order', 'timeline_steps', 'employees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $packages = Package::all();
        $employees = User::where('role', 'employee')->get();
        $customers = User::where('role', 'customer')->get();

        return view('admin.orders.edit', compact('order', 'packages', 'employees', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'email' => 'nullable|email|max:255',
            'units_count' => 'required|integer|min:1',
            'project_type' => 'required|in:large,medium,small',
            'current_stage' => 'required|in:design,execution,operation',
            'colors' => 'nullable|array',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|integer|between:1,5',
            'expected_delivery_date' => 'nullable|date',
            'total_amount' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $old_data = $order->toArray();

        $order->update([
            'package_id' => $request->package_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'email' => $request->email,
            'units_count' => $request->units_count,
            'project_type' => $request->project_type,
            'current_stage' => $request->current_stage,
            'has_interior_design' => $request->has('has_interior_design'),
            'needs_finishing_help' => $request->has('needs_finishing_help'),
            'needs_color_help' => $request->has('needs_color_help'),
            'colors' => $request->colors,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'expected_delivery_date' => $request->expected_delivery_date,
            'total_amount' => $request->total_amount,
            'internal_notes' => $request->internal_notes,
            'last_activity_at' => now()
        ]);

        // تسجيل النشاط
        $order->logActivity('updated', 'تم تحديث بيانات الطلب', auth()->id(), $old_data, $order->toArray());

        return redirect()->route('admin.orders.show', $order)
                       ->with('success', 'تم تحديث الطلب بنجاح');
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,archived,cancelled',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $old_status = $order->status;
        $order->status = $request->status;
        $order->last_activity_at = now();

        if ($request->status == 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        // تسجيل النشاط
        $description = "تم تغيير حالة الطلب من {$old_status} إلى {$request->status}";
        if ($request->notes) {
            $description .= " - ملاحظات: {$request->notes}";
        }

        $order->logActivity('status_changed', $description, auth()->id(),
                          ['status' => $old_status],
                          ['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'status_text' => $order->status_text,
            'status_color' => $order->status_color
        ]);
    }

    /**
     * Update timeline step
     */
    public function updateTimeline(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'step' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $order->updateTimeline($request->step, $request->status, now(), $request->notes);

        // تسجيل النشاط
        $step_name = $order->getTimelineSteps()[$request->step]['name'] ?? $request->step;
        $order->logActivity('timeline_updated', "تم تحديث مرحلة {$step_name} إلى {$request->status}", auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الجدول الزمني بنجاح'
        ]);
    }

    /**
     * Assign employee to order
     */
    public function assignEmployee(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $old_employee = $order->assignedEmployee;
        $order->assigned_to = $request->employee_id;
        $order->last_activity_at = now();
        $order->save();

        $new_employee = User::find($request->employee_id);

        // تسجيل النشاط
        $description = "تم تعيين الموظف {$new_employee->name} للطلب";
        if ($old_employee) {
            $description = "تم تغيير الموظف المعين من {$old_employee->name} إلى {$new_employee->name}";
        }

        $order->logActivity('assigned', $description, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين الموظف بنجاح',
            'employee_name' => $new_employee->name
        ]);
    }

    /**
     * Add internal note
     */
    public function addNote(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $current_notes = $order->internal_notes ?? '';
        $new_note = "[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "] " . $request->note;

        $order->internal_notes = $current_notes . "\n" . $new_note;
        $order->last_activity_at = now();
        $order->save();

        // تسجيل النشاط
        $order->logActivity('note_added', 'تم إضافة ملاحظة داخلية', auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الملاحظة بنجاح'
        ]);
    }

    /**
     * Export orders
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');

        // تطبيق نفس الفلاتر المستخدمة في الفهرس
        $query = Order::with(['user', 'package', 'assignedEmployee']);

        // ... تطبيق الفلاتر ...

        $orders = $query->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($orders);
        } else {
            return $this->exportToExcel($orders);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // تسجيل النشاط قبل الحذف
        $order->logActivity('deleted', 'تم حذف الطلب', auth()->id());

        $order->delete();

        return redirect()->route('admin.orders.index')
                       ->with('success', 'تم حذف الطلب بنجاح');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,update_status,assign_employee,export',
            'orders' => 'required|array',
            'orders.*' => 'exists:orders,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $orders = Order::whereIn('id', $request->orders)->get();
        $count = 0;

        foreach ($orders as $order) {
            switch ($request->action) {
                case 'delete':
                    $order->logActivity('deleted', 'تم حذف الطلب (عملية جماعية)', auth()->id());
                    $order->delete();
                    $count++;
                    break;

                case 'update_status':
                    if ($request->filled('status')) {
                        $old_status = $order->status;
                        $order->status = $request->status;
                        $order->last_activity_at = now();
                        $order->save();

                        $order->logActivity('status_changed',
                                          "تم تغيير حالة الطلب من {$old_status} إلى {$request->status} (عملية جماعية)",
                                          auth()->id());
                        $count++;
                    }
                    break;

                case 'assign_employee':
                    if ($request->filled('employee_id')) {
                        $order->assigned_to = $request->employee_id;
                        $order->last_activity_at = now();
                        $order->save();

                        $employee = User::find($request->employee_id);
                        $order->logActivity('assigned',
                                          "تم تعيين الموظف {$employee->name} للطلب (عملية جماعية)",
                                          auth()->id());
                        $count++;
                    }
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم تنفيذ العملية على {$count} طلب بنجاح"
        ]);
    }

    // Helper Methods
    private function generateOrderNumber()
    {
        $year = date('Y');
        $lastOrder = Order::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ($lastOrder->id + 1) : 1;

        return 'ORD-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function createInvoiceForOrder(Order $order, $amount)
    {
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'subtotal' => $amount,
            'tax_rate' => 15.00,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'status' => 'draft'
        ]);

        $invoice->calculateTax();
        $invoice->save();

        return $invoice;
    }

    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = Invoice::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? ($lastInvoice->id + 1) : 1;

        return 'INV-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function exportToPdf($orders)
    {
        // تنفيذ تصدير PDF
        // يمكن استخدام مكتبة مثل DomPDF أو wkhtmltopdf
    }

    private function exportToExcel($orders)
    {
        // تنفيذ تصدير Excel
        // يمكن استخدام مكتبة مثل Laravel Excel
    }
}
