<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4e73df;
        }
        .header h1 {
            color: #4e73df;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .invoice-info-section h3 {
            color: #4e73df;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e3e6f0;
            padding-bottom: 5px;
        }
        .invoice-info-section p {
            margin: 5px 0;
            color: #5a5c69;
        }
        .invoice-info-section strong {
            color: #2e2f37;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th {
            background-color: #4e73df;
            color: white;
            padding: 12px;
            text-align: right;
            font-weight: bold;
        }
        .invoice-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
        }
        .invoice-table tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .totals {
            width: 50%;
            margin-right: auto;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px 12px;
            border-bottom: 1px solid #e3e6f0;
        }
        .totals td:first-child {
            font-weight: bold;
            color: #2e2f37;
        }
        .totals td:last-child {
            text-align: left;
            color: #5a5c69;
        }
        .totals .total-row {
            background-color: #4e73df;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        .totals .total-row td {
            border: none;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-paid {
            background-color: #1cc88a;
            color: white;
        }
        .status-pending {
            background-color: #f6c23e;
            color: white;
        }
        .status-partial {
            background-color: #36b9cc;
            color: white;
        }
        .status-overdue {
            background-color: #e74a3b;
            color: white;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e3e6f0;
            text-align: center;
            color: #858796;
            font-size: 12px;
        }
        .payments-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fc;
            border-radius: 5px;
        }
        .payments-section h3 {
            color: #4e73df;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاتورة ضريبية</h1>
        <p>رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong></p>
        <p>تاريخ الإصدار: <strong>{{ $invoice->created_at->format('Y-m-d') }}</strong></p>
        @if($invoice->due_date)
        <p>تاريخ الاستحقاق: <strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}</strong></p>
        @endif
    </div>

    <div class="invoice-info">
        <div class="invoice-info-section">
            <h3>معلومات الشركة</h3>
            <p><strong>اسم الشركة:</strong> {{ config('app.name', 'شركة الأثاث') }}</p>
            <p><strong>العنوان:</strong> المملكة العربية السعودية</p>
            <p><strong>الهاتف:</strong> +966 XX XXX XXXX</p>
            <p><strong>البريد الإلكتروني:</strong> info@company.com</p>
            @if(isset($invoice->order) && $invoice->order->tax_number)
            <p><strong>الرقم الضريبي:</strong> {{ $invoice->order->tax_number }}</p>
            @endif
        </div>

        <div class="invoice-info-section">
            <h3>معلومات العميل</h3>
            @if($invoice->order && $invoice->order->user)
            <p><strong>الاسم:</strong> {{ $invoice->order->name ?? $invoice->order->user->name }}</p>
            <p><strong>البريد الإلكتروني:</strong> {{ $invoice->order->email ?? $invoice->order->user->email }}</p>
            <p><strong>الهاتف:</strong> {{ $invoice->order->phone ?? $invoice->order->user->phone }}</p>
            @if($invoice->order->client_type === 'commercial' && $invoice->order->commercial_register)
            <p><strong>السجل التجاري:</strong> {{ $invoice->order->commercial_register }}</p>
            @endif
            @endif
            <p><strong>حالة الفاتورة:</strong>
                <span class="status-badge status-{{ $invoice->status }}">
                    @if($invoice->status === 'paid')
                        مدفوعة
                    @elseif($invoice->status === 'pending')
                        معلقة
                    @elseif($invoice->status === 'partial')
                        مدفوعة جزئياً
                    @else
                        متأخرة
                    @endif
                </span>
            </p>
        </div>
    </div>

    <h3 style="color: #4e73df; margin-bottom: 15px;">تفاصيل الفاتورة</h3>
    <table class="invoice-table">
        <thead>
            <tr>
                <th>البيان</th>
                <th>الوصف</th>
                <th>المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->order)
            <tr>
                <td>طلب رقم</td>
                <td>{{ $invoice->order->order_number }}</td>
                <td>-</td>
            </tr>
            @if($invoice->order->package)
            <tr>
                <td>الباقة</td>
                <td>{{ $invoice->order->package->name }}</td>
                <td>{{ number_format($invoice->order->base_amount ?? 0, 2) }} ريال</td>
            </tr>
            @endif
            @endif
            <tr>
                <td>المبلغ الأساسي</td>
                <td>-</td>
                <td>{{ number_format($invoice->subtotal ?? 0, 2) }} ريال</td>
            </tr>
            <tr>
                <td>الضريبة ({{ $invoice->tax_rate ?? 15 }}%)</td>
                <td>-</td>
                <td>{{ number_format($invoice->tax_amount ?? 0, 2) }} ريال</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td>الخصم</td>
                <td>-</td>
                <td>-{{ number_format($invoice->discount_amount, 2) }} ريال</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>المجموع الفرعي:</td>
                <td>{{ number_format($invoice->subtotal ?? 0, 2) }} ريال</td>
            </tr>
            <tr>
                <td>الضريبة:</td>
                <td>{{ number_format($invoice->tax_amount ?? 0, 2) }} ريال</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td>الخصم:</td>
                <td>-{{ number_format($invoice->discount_amount, 2) }} ريال</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>الإجمالي:</td>
                <td>{{ number_format($invoice->total_amount, 2) }} ريال</td>
            </tr>
            <tr>
                <td>المدفوع:</td>
                <td>{{ number_format($invoice->paid_amount ?? 0, 2) }} ريال</td>
            </tr>
            <tr>
                <td>المتبقي:</td>
                <td>{{ number_format($invoice->total_amount - ($invoice->paid_amount ?? 0), 2) }} ريال</td>
            </tr>
        </table>
    </div>

    @if($invoice->payments && $invoice->payments->count() > 0)
    <div class="payments-section">
        <h3>سجل المدفوعات</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                    <td>{{ number_format($payment->amount, 2) }} ريال</td>
                    <td>{{ $payment->payment_method ?? 'غير محدد' }}</td>
                    <td>
                        <span class="status-badge status-{{ $payment->status }}">
                            {{ $payment->status === 'completed' ? 'مكتمل' : 'معلق' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($invoice->notes)
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fc; border-radius: 5px;">
        <h3 style="color: #4e73df; font-size: 14px; margin-bottom: 10px;">ملاحظات:</h3>
        <p style="color: #5a5c69;">{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>هذه فاتورة إلكترونية تم إنشاؤها بواسطة النظام</p>
        <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i A') }}</p>
    </div>
</body>
</html>

