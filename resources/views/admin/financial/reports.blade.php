@extends('admin.layouts.app')

@section('title', 'لوحة التحكم المالية')

@section('content')

    <style>



        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card {
            flex: 1;
            min-width: 200px;
            border-radius: 10px;
            padding: 15px;
            color: #fff;
        }

        .bg-primary { background-color: #4e73df; }
        .bg-success { background-color: #1cc88a; }
        .bg-warning { background-color: #f6c23e; }
        .bg-info { background-color: #36b9cc; }

        .card h6 {
            margin: 0;
            font-size: 14px;
        }

        .card h3 {
            margin: 5px 0 0;
            font-size: 20px;
        }

        .section {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #f8f9fc;
            color: #333;
        }

        footer {
            position: fixed;
            bottom: 0;
            text-align: center;
            width: 100%;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<div class="container-fluid">

    <h1>التقارير المالية</h1>

    <p><strong>الفترة:</strong> من {{ $dateFrom }} إلى {{ $dateTo }}</p>

    <div class="cards">
        <div class="card bg-primary">
            <h6>إجمالي الإيرادات</h6>
            <h3>{{ number_format($reports['revenue']['total'] ?? 0, 2) }} ج.م</h3>
        </div>

        <div class="card bg-warning">
            <h6>الفواتير المصدرة</h6>
            <h3>{{ $reports['invoices']['total_issued'] ?? 0 }}</h3>
        </div>

        <div class="card bg-success">
            <h6>الفواتير المدفوعة</h6>
            <h3>{{ $reports['invoices']['total_paid'] ?? 0 }}</h3>
        </div>

        <div class="card bg-info">
            <h6>المدفوعات</h6>
            <h3>{{ number_format($reports['payments']['total'] ?? 0, 2) }} ج.م</h3>
        </div>
    </div>

    <div class="section">
        <h3>تفاصيل الفواتير</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الفاتورة</th>
                    <th>العميل</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>تاريخ الإنشاء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $invoice->invoice_number ?? '-' }}</td>
                    <td>{{ $invoice->customer->name ?? '-' }}</td>
                    <td>{{ number_format($invoice->total_amount ?? 0, 2) }} ج.م</td>
                    <td>
                        @if($invoice->status === 'paid')
                            مدفوع
                        @elseif($invoice->status === 'pending')
                            قيد الانتظار
                        @else
                            غير مدفوع
                        @endif
                    </td>
                    <td>{{ $invoice->created_at?->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;">لا توجد فواتير متاحة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    @endsection
