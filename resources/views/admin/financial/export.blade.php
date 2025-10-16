<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقرير المالي</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            direction: rtl;
            text-align: right;
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #ffffff;
            padding: 20px;
            line-height: 1.6;
        }

        h2, h5, h6 {
            margin: 0;
            color: #2c3e50;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4e73df;
        }

        .header h2 {
            color: #4e73df;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header .date-range {
            color: #858796;
            font-size: 14px;
            margin-top: 10px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 10px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 20px;
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .bg-success { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
        .bg-info { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
        .bg-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); color: #000; }
        .bg-danger { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }

        .stat-card h6 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.8;
        }

        .section-title {
            background-color: #4e73df;
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            margin: 30px 0 15px 0;
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table thead {
            background-color: #4e73df;
            color: white;
        }

        th {
            padding: 12px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fc;
        }

        tbody tr:hover {
            background-color: #eaecf4;
        }

        .empty-row {
            text-align: center;
            color: #858796;
            font-style: italic;
            padding: 20px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e3e6f0;
            text-align: center;
            color: #858796;
            font-size: 12px;
        }

        .summary-box {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-box h6 {
            color: #4e73df;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e3e6f0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #5a5c69;
            font-weight: bold;
        }

        .summary-value {
            color: #2e2f37;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>التقرير المالي الشامل</h2>
            <div class="date-range">
                <strong>الفترة:</strong> من {{ $dateFrom }} إلى {{ $dateTo }}
            </div>
            <div class="date-range">
                تاريخ الطباعة: {{ now()->format('Y-m-d H:i A') }}
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-box">
            <h6>ملخص الإحصائيات المالية</h6>
            <div class="summary-item">
                <span class="summary-label">إجمالي الإيرادات:</span>
                <span class="summary-value">{{ number_format($stats['total_revenue'] ?? 0, 2) }} ريال</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">المدفوعات المستلمة:</span>
                <span class="summary-value">{{ number_format($stats['paid_amount'] ?? 0, 2) }} ريال</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">المدفوعات المؤجلة:</span>
                <span class="summary-value">{{ number_format($stats['unpaid_amount'] ?? 0, 2) }} ريال</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">المدفوعات المتأخرة:</span>
                <span class="summary-value">{{ number_format($stats['overdue_amount'] ?? 0, 2) }} ريال</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">عدد الفواتير المدفوعة:</span>
                <span class="summary-value">{{ $stats['paid_invoices'] ?? 0 }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">عدد الفواتير المعلقة:</span>
                <span class="summary-value">{{ $stats['pending_invoices'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-card bg-success">
                    <h6>إجمالي الإيرادات</h6>
                    <div class="stat-value">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                    <div class="stat-label">ريال سعودي</div>
                </div>
                <div class="stat-card bg-info">
                    <h6>المدفوعات المستلمة</h6>
                    <div class="stat-value">{{ number_format($stats['paid_amount'] ?? 0, 0) }}</div>
                    <div class="stat-label">ريال سعودي</div>
                </div>
                <div class="stat-card bg-warning">
                    <h6>المدفوعات المؤجلة</h6>
                    <div class="stat-value">{{ number_format($stats['unpaid_amount'] ?? 0, 0) }}</div>
                    <div class="stat-label">ريال سعودي</div>
                </div>
                <div class="stat-card bg-danger">
                    <h6>المدفوعات المتأخرة</h6>
                    <div class="stat-value">{{ number_format($stats['overdue_amount'] ?? 0, 0) }}</div>
                    <div class="stat-label">ريال سعودي</div>
                </div>
            </div>
        </div>

        <!-- Recent Payments Table -->
        <h5 class="section-title">آخر المدفوعات المستلمة</h5>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الفاتورة</th>
                    <th>اسم العميل</th>
                    <th>المبلغ المدفوع</th>
                    <th>تاريخ الدفع</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPayments ?? [] as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>#{{ $payment->invoice->invoice_number ?? '-' }}</td>
                        <td>{{ $payment->invoice->order->name ?? '-' }}</td>
                        <td>{{ number_format($payment->amount ?? 0, 2) }} ريال</td>
                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">لا توجد مدفوعات حديثة في هذه الفترة</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pending Invoices Table -->
        <h5 class="section-title">الفواتير المعلقة</h5>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الفاتورة</th>
                    <th>اسم العميل</th>
                    <th>المبلغ الإجمالي</th>
                    <th>تاريخ الاستحقاق</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingInvoices ?? [] as $index => $invoice)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>#{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->order->name ?? '-' }}</td>
                        <td>{{ number_format($invoice->total_amount ?? 0, 2) }} ريال</td>
                        <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">لا توجد فواتير معلقة</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p><strong>{{ config('app.name', 'نظام إدارة الأثاث') }}</strong></p>
            <p>هذا تقرير مالي تم إنشاؤه تلقائياً بواسطة النظام</p>
            <p>جميع الحقوق محفوظة © {{ now()->year }}</p>
        </div>
    </div>
</body>
</html>
