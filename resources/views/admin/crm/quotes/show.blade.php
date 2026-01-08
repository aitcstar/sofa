<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>عرض السعر #{{ $quote->quote_number }} - SOFA Experience</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f9f9f9;
        }

        .print-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        @media print {
            .print-button, .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
        }

        .invoice-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .text-primary-dark { color: #08203E; }
        .bg-primary-dark { background-color: #08203E !important; }
    </style>
</head>

<body>
    <!-- Print & Back Buttons -->
    <div class="print-button no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-2"></i>طباعة
        </button>
        <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary ms-2">
            <i class="fas fa-arrow-right me-2"></i>رجوع
        </a>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="text-primary-dark mb-1">عرض سعر</h2>
                <p class="text-muted mb-0">#{{ $quote->quote_number }}</p>
            </div>
            <div class="text-end">
                <p class="mb-1"><strong>تاريخ الإصدار:</strong> {{ \Carbon\Carbon::parse($quote->issue_date)->format('d F Y') }}</p>
                <p class="mb-1"><strong>صالح حتى:</strong> {{ \Carbon\Carbon::parse($quote->valid_until)->format('d F Y') }}</p>
            </div>
        </div>

        <hr class="my-4" />

        <!-- Customer Info -->
        <div class="mb-5">
            <h5 class="text-primary-dark mb-3">بيانات العميل</h5>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>الاسم:</strong> {{ $quote->customer_name }}</p>
                    <p class="mb-1"><strong>البريد الإلكتروني:</strong> {{ $quote->customer_email ?? 'غير محدد' }}</p>
                    <p class="mb-1"><strong>الهاتف:</strong> {{ $quote->customer_phone ?? 'غير محدد' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>الشركة:</strong> {{ $quote->customer_company ?? 'غير محدد' }}</p>
                    @if($quote->lead)
                        <p class="mb-1"><strong>العميل المحتمل:</strong> {{ $quote->lead->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Packages & Items -->
        <div class="mb-5">
            <h5 class="text-primary-dark mb-3">العناصر</h5>

            @if($quote->items->isEmpty())
                <div class="alert alert-warning">لا توجد عناصر في هذا العرض.</div>
            @else
                @foreach($quote->items->groupBy('package_id') as $packageId => $items)
                    @php
                        $firstItem = $items->first();
                        $packageName = $firstItem->package?->name_ar ?? 'غير معروف';
                    @endphp

                    <h6 class="mt-4 mb-2 border-bottom pb-2"><strong>الباكج: {{ $packageName }}</strong></h6>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>القطعة</th>
                                <th>الوصف</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }} ريال</td>
                                    <td>{{ number_format($item->total_price, 2) }} ريال</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>

        <!-- Totals -->
        <div class="border rounded mb-4">
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($quote->subtotal, 2) }} ريال</span>
            </div>
            @if($quote->discount_amount > 0)
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span>الخصم:</span>
                <span class="text-danger">-{{ number_format($quote->discount_amount, 2) }} ريال</span>
            </div>
            @endif
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span>الضريبة ({{ $quote->tax_rate }}%):</span>
                <span>{{ number_format($quote->tax_amount, 2) }} ريال</span>
            </div>
            <div class="d-flex justify-content-between p-3 bg-primary-dark text-white">
                <span><strong>المجموع النهائي:</strong></span>
                <span><strong>{{ number_format($quote->total_amount, 2) }} ريال</strong></span>
            </div>
        </div>

        <!-- Notes & Terms -->
        @if($quote->terms_conditions)
            <div class="mb-3">
                <h6 class="text-primary-dark mb-2">الشروط والأحكام</h6>
                <p class="border p-3 rounded bg-light">{{ $quote->terms_conditions }}</p>
            </div>
        @endif

        @if($quote->notes)
            <div class="mb-3">
                <h6 class="text-primary-dark mb-2">ملاحظات</h6>
                <p class="border p-3 rounded bg-light">{{ $quote->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-5 pt-3 border-top">
            <p class="text-muted mb-0">شكراً لاختيارك SOFA Experience. للاستفسار: info@sofa.com</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
