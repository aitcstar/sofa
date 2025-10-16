<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['order.user', 'order.package', 'invoice'])
                       ->orderBy('payment_date', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب طريقة الدفع
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%")
                                 ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'refunded' => Payment::where('status', 'refunded')->count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'today_amount' => Payment::where('status', 'completed')
                                    ->whereDate('payment_date', today())
                                    ->sum('amount'),
            'month_amount' => Payment::where('status', 'completed')
                                    ->whereMonth('payment_date', now()->month)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('amount')
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request)
    {
        $orderId = $request->get('order_id');
        $invoiceId = $request->get('invoice_id');
        
        $order = null;
        $invoice = null;
        
        if ($orderId) {
            $order = Order::with(['user', 'package', 'invoices'])->findOrFail($orderId);
        }
        
        if ($invoiceId) {
            $invoice = Invoice::with(['order.user', 'order.package'])->findOrFail($invoiceId);
            $order = $invoice->order;
        }

        // الطلبات التي لها مبالغ مستحقة
        $orders = Order::where('payment_status', '!=', 'paid')
                      ->where('total_amount', '>', 0)
                      ->with(['user', 'package'])
                      ->get();

        return view('admin.payments.create', compact('order', 'invoice', 'orders'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'order_id' => $request->order_id,
                'invoice_id' => $request->invoice_id,
                'payment_number' => $this->generatePaymentNumber(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'payment_date' => $request->payment_date
            ]);

            // تحديث حالة الدفع في الطلب
            $this->updateOrderPaymentStatus($payment->order);

            // تحديث حالة الفاتورة إذا كانت موجودة
            if ($payment->invoice) {
                $this->updateInvoiceStatus($payment->invoice);
            }

            // تسجيل النشاط
            $payment->order->logActivity('payment_received', 
                "تم استلام دفعة بقيمة {$payment->amount} ريال", 
                auth()->id());

            DB::commit();

            return redirect()->route('admin.payments.show', $payment)
                           ->with('success', 'تم تسجيل الدفعة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء تسجيل الدفعة: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.package', 'invoice']);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return redirect()->route('admin.payments.show', $payment)
                           ->with('error', 'لا يمكن تعديل دفعة مكتملة');
        }

        return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment)
    {
        if ($payment->status === 'completed') {
            return redirect()->route('admin.payments.show', $payment)
                           ->with('error', 'لا يمكن تعديل دفعة مكتملة');
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $payment->update([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes
        ]);

        // تسجيل النشاط
        $payment->order->logActivity('payment_updated', 
            "تم تحديث دفعة رقم {$payment->payment_number}", 
            auth()->id());

        return redirect()->route('admin.payments.show', $payment)
                       ->with('success', 'تم تحديث الدفعة بنجاح');
    }

    /**
     * Confirm payment
     */
    public function confirm(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'الدفعة ليست في حالة انتظار'
            ]);
        }

        DB::beginTransaction();
        try {
            $payment->markAsCompleted();

            // تحديث حالة الدفع في الطلب
            $this->updateOrderPaymentStatus($payment->order);

            // تحديث حالة الفاتورة إذا كانت موجودة
            if ($payment->invoice) {
                $this->updateInvoiceStatus($payment->invoice);
            }

            // تسجيل النشاط
            $payment->order->logActivity('payment_confirmed', 
                "تم تأكيد دفعة رقم {$payment->payment_number}", 
                auth()->id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تأكيد الدفعة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تأكيد الدفعة: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Refund payment
     */
    public function refund(Request $request, Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن استرداد دفعة غير مكتملة'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $payment->refund($request->refund_amount, $request->refund_reason);

            // تحديث حالة الدفع في الطلب
            $this->updateOrderPaymentStatus($payment->order);

            // تحديث حالة الفاتورة إذا كانت موجودة
            if ($payment->invoice) {
                $this->updateInvoiceStatus($payment->invoice);
            }

            // تسجيل النشاط
            $payment->order->logActivity('payment_refunded', 
                "تم استرداد {$request->refund_amount} ريال من دفعة رقم {$payment->payment_number}", 
                auth()->id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم استرداد الدفعة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استرداد الدفعة: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate payment receipt
     */
    public function generateReceipt(Payment $payment)
    {
        $payment->load(['order.user', 'order.package', 'invoice']);
        
        // تنفيذ إنشاء إيصال الدفع
        // يمكن استخدام مكتبة مثل DomPDF أو wkhtmltopdf
        
        return response()->download($receiptPath);
    }

    /**
     * Print payment receipt
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['order.user', 'order.package', 'invoice']);
        
        return view('admin.payments.receipt', compact('payment'));
    }

    /**
     * Delete payment
     */
    public function destroy(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return redirect()->route('admin.payments.index')
                           ->with('error', 'لا يمكن حذف دفعة مكتملة');
        }

        // تسجيل النشاط قبل الحذف
        $payment->order->logActivity('payment_deleted', 
            "تم حذف دفعة رقم {$payment->payment_number}", 
            auth()->id());

        $payment->delete();

        return redirect()->route('admin.payments.index')
                       ->with('success', 'تم حذف الدفعة بنجاح');
    }

    /**
     * Get payment statistics
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $stats = [
            'total_payments' => Payment::where('status', 'completed')
                                      ->whereBetween('payment_date', [$startDate, $endDate])
                                      ->count(),
            'total_amount' => Payment::where('status', 'completed')
                                    ->whereBetween('payment_date', [$startDate, $endDate])
                                    ->sum('amount'),
            'avg_payment' => Payment::where('status', 'completed')
                                   ->whereBetween('payment_date', [$startDate, $endDate])
                                   ->avg('amount'),
            'payment_methods' => Payment::where('status', 'completed')
                                       ->whereBetween('payment_date', [$startDate, $endDate])
                                       ->groupBy('payment_method')
                                       ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                                       ->get(),
            'daily_payments' => Payment::where('status', 'completed')
                                      ->whereBetween('payment_date', [$startDate, $endDate])
                                      ->groupBy(DB::raw('DATE(payment_date)'))
                                      ->selectRaw('DATE(payment_date) as date, COUNT(*) as count, SUM(amount) as total')
                                      ->orderBy('date')
                                      ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Export payments
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $payments = Payment::with(['order.user', 'order.package', 'invoice'])
                          ->whereBetween('payment_date', [$startDate, $endDate])
                          ->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($payments, $startDate, $endDate);
        } else {
            return $this->exportToExcel($payments, $startDate, $endDate);
        }
    }

    // Helper Methods

    private function generatePaymentNumber()
    {
        $year = date('Y');
        $lastPayment = Payment::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastPayment ? ($lastPayment->id + 1) : 1;
        
        return 'PAY-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function updateOrderPaymentStatus(Order $order)
    {
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        $totalAmount = $order->total_amount;

        if ($totalPaid >= $totalAmount) {
            $order->payment_status = 'paid';
            $order->paid_amount = $totalPaid;
        } elseif ($totalPaid > 0) {
            $order->payment_status = 'partial';
            $order->paid_amount = $totalPaid;
        } else {
            $order->payment_status = 'unpaid';
            $order->paid_amount = 0;
        }

        $order->save();
    }

    private function updateInvoiceStatus(Invoice $invoice)
    {
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->markAsPaid();
        }
    }

    private function getStartDate($period)
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }

    private function exportToPdf($payments, $startDate, $endDate)
    {
        // تنفيذ تصدير PDF للمدفوعات
    }

    private function exportToExcel($payments, $startDate, $endDate)
    {
        // تنفيذ تصدير Excel للمدفوعات
    }
}
