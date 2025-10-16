<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['order.user', 'order.package', 'payments'])
                       ->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%")
                                 ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // الفواتير المتأخرة
        if ($request->filled('overdue') && $request->overdue == '1') {
            $query->where('status', 'sent')
                  ->where('due_date', '<', now()->toDateString());
        }

        $invoices = $query->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Invoice::count(),
            'draft' => Invoice::where('status', 'draft')->count(),
            'sent' => Invoice::where('status', 'sent')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'sent')
                               ->where('due_date', '<', now()->toDateString())
                               ->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::where('status', 'paid')->sum('total_amount'),
            'outstanding_amount' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount')
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create(Request $request)
    {
        $orderId = $request->get('order_id');
        $order = null;

        if ($orderId) {
            $order = Order::with(['user', 'package'])->findOrFail($orderId);
        }

        $orders = Order::whereDoesntHave('invoices')
                      ->orWhereHas('invoices', function($query) {
                          $query->where('status', 'draft');
                      })
                      ->with(['user', 'package'])
                      ->get();

        return view('admin.invoices.create', compact('order', 'orders'));
    }

    /**
     * Store a newly created invoice
     */
    /*/public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'order_id' => $request->order_id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'subtotal' => $request->subtotal,
                'tax_rate' => $request->tax_rate,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'status' => 'draft'
            ]);

            // حساب الضريبة والمجموع
            $invoice->calculateTax();
            $invoice->save();

            // تحديث المبلغ الإجمالي في الطلب
            $order = $invoice->order;
            $order->total_amount = $invoice->total_amount;
            $order->save();

            // تسجيل النشاط
            $order->logActivity('invoice_created', "تم إنشاء فاتورة رقم {$invoice->invoice_number}", auth()->id());

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage())
                           ->withInput();
        }
    }
*/

public function store(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'notes' => 'nullable|string'
    ]);

   // DB::beginTransaction();

    try {
        $order = Order::findOrFail($request->order_id);

        // إنشاء الفاتورة
        $invoice = Invoice::createFromOrder($order, auth()->user());
        $invoice->notes = $request->notes;

        // حساب المبلغ الأساسي والضريبة كما في Seeder
        $baseAmount = $order->total_amount * 0.87;
        $taxAmount = $baseAmount * 0.15;

        $invoice->subtotal = $baseAmount;
        $invoice->tax_amount = $taxAmount;
        $invoice->total_amount = $baseAmount + $taxAmount;

        $invoice->save();

        // تحديث الطلب
        $order->total_amount = $invoice->total_amount;
        $order->save();

        // إنشاء مدفوعات لو فيه مدفوعات سابقة
        if($order->paid_amount > 0){
            $invoice->payments()->create([
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'amount' => $order->paid_amount,
                'payment_method' => 'cash', // أو حسب اختيارك
                'payment_date' => now(),
                'status' => 'completed'
            ]);
        }

        DB::commit();

        return redirect()->route('admin.invoices.show', $invoice)
                         ->with('success', 'تم إنشاء الفاتورة بنجاح');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
                         ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage())
                         ->withInput();
    }
}

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.package', 'payments']);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('error', 'لا يمكن تعديل فاتورة مرسلة أو مدفوعة');
        }

        return view('admin.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('admin.invoices.show', $invoice)
                           ->with('error', 'لا يمكن تعديل فاتورة مرسلة أو مدفوعة');
        }

        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $invoice->update([
            'subtotal' => $request->subtotal,
            'tax_rate' => $request->tax_rate,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes
        ]);

        // إعادة حساب الضريبة والمجموع
        $invoice->calculateTax();
        $invoice->save();

        // تحديث المبلغ الإجمالي في الطلب
        $order = $invoice->order;
        $order->total_amount = $invoice->total_amount;
        $order->save();

        // تسجيل النشاط
        $order->logActivity('invoice_updated', "تم تحديث فاتورة رقم {$invoice->invoice_number}", auth()->id());

        return redirect()->route('admin.invoices.show', $invoice)
                       ->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    /**
     * Send invoice to customer
     */
    public function send(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إرسال فاتورة مرسلة مسبقاً'
            ]);
        }

        try {
            // إرسال الفاتورة بالبريد الإلكتروني
            $this->sendInvoiceEmail($invoice);

            // تحديث حالة الفاتورة
            $invoice->status = 'sent';
            $invoice->sent_at = now();
            $invoice->save();

            // تسجيل النشاط
            $invoice->order->logActivity('invoice_sent', "تم إرسال فاتورة رقم {$invoice->invoice_number}", auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الفاتورة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الفاتورة: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,online',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            // إنشاء دفعة جديدة
            $payment = Payment::create([
                'order_id' => $invoice->order_id,
                'invoice_id' => $invoice->id,
                'payment_number' => $this->generatePaymentNumber(),
                'amount' => $request->payment_amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'payment_date' => $request->payment_date
            ]);

            // تحديث حالة الفاتورة
            if ($request->payment_amount >= $invoice->total_amount) {
                $invoice->markAsPaid();
            }

            // تحديث حالة الدفع في الطلب
            $this->updateOrderPaymentStatus($invoice->order);

            // تسجيل النشاط
            $invoice->order->logActivity('payment_received',
                "تم استلام دفعة بقيمة {$request->payment_amount} ريال للفاتورة رقم {$invoice->invoice_number}",
                auth()->id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدفعة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدفعة: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate PDF invoice
     */
    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.package', 'payments']);

        // تنفيذ إنشاء PDF للفاتورة
        // يمكن استخدام مكتبة مثل DomPDF أو wkhtmltopdf

        return response()->download($pdfPath);
    }

    /**
     * Print invoice
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.package', 'payments']);

        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * Duplicate invoice
     */
    public function duplicate(Invoice $invoice)
    {
        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = $this->generateInvoiceNumber();
        $newInvoice->status = 'draft';
        $newInvoice->sent_at = null;
        $newInvoice->paid_at = null;
        $newInvoice->issue_date = now()->toDateString();
        $newInvoice->due_date = now()->addDays(30)->toDateString();
        $newInvoice->save();

        return redirect()->route('admin.invoices.edit', $newInvoice)
                       ->with('success', 'تم نسخ الفاتورة بنجاح');
    }

    /**
     * Delete invoice
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->route('admin.invoices.index')
                           ->with('error', 'لا يمكن حذف فاتورة مرسلة أو مدفوعة');
        }

        // تسجيل النشاط قبل الحذف
        $invoice->order->logActivity('invoice_deleted', "تم حذف فاتورة رقم {$invoice->invoice_number}", auth()->id());

        $invoice->delete();

        return redirect()->route('admin.invoices.index')
                       ->with('success', 'تم حذف الفاتورة بنجاح');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,send,mark_overdue',
            'invoices' => 'required|array',
            'invoices.*' => 'exists:invoices,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $invoices = Invoice::whereIn('id', $request->invoices)->get();
        $count = 0;

        foreach ($invoices as $invoice) {
            switch ($request->action) {
                case 'delete':
                    if ($invoice->status === 'draft') {
                        $invoice->order->logActivity('invoice_deleted',
                            "تم حذف فاتورة رقم {$invoice->invoice_number} (عملية جماعية)",
                            auth()->id());
                        $invoice->delete();
                        $count++;
                    }
                    break;

                case 'send':
                    if ($invoice->status === 'draft') {
                        try {
                            $this->sendInvoiceEmail($invoice);
                            $invoice->status = 'sent';
                            $invoice->sent_at = now();
                            $invoice->save();
                            $count++;
                        } catch (\Exception $e) {
                            // تسجيل الخطأ
                        }
                    }
                    break;

                case 'mark_overdue':
                    if ($invoice->status === 'sent' && $invoice->due_date < now()->toDateString()) {
                        $invoice->markAsOverdue();
                        $count++;
                    }
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم تنفيذ العملية على {$count} فاتورة بنجاح"
        ]);
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices()
    {
        $overdueInvoices = Invoice::where('status', 'sent')
                                 ->where('due_date', '<', now()->toDateString())
                                 ->with(['order.user'])
                                 ->get();

        foreach ($overdueInvoices as $invoice) {
            $invoice->markAsOverdue();
        }

        return response()->json([
            'count' => $overdueInvoices->count(),
            'invoices' => $overdueInvoices
        ]);
    }

    // Helper Methods

    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = Invoice::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? ($lastInvoice->id + 1) : 1;

        return 'INV-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function generatePaymentNumber()
    {
        $year = date('Y');
        $lastPayment = Payment::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastPayment ? ($lastPayment->id + 1) : 1;

        return 'PAY-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function sendInvoiceEmail(Invoice $invoice)
    {
        $customer = $invoice->order->user;

        if (!$customer || !$customer->email) {
            throw new \Exception('لا يوجد بريد إلكتروني للعميل');
        }

        Mail::send('emails.invoice', ['invoice' => $invoice], function ($message) use ($customer, $invoice) {
            $message->to($customer->email)
                   ->subject("فاتورة رقم {$invoice->invoice_number}");
        });
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
}
