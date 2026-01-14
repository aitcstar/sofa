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

        #toggle-lang {
            margin-left: 10px;
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
            <i class="fas fa-print me-2"></i>
            <span class="translate" data-ar="طباعة" data-en="Print">طباعة</span>
        </button>
        <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary ms-2">
            <i class="fas fa-arrow-right me-2"></i>
            <span class="translate" data-ar="رجوع" data-en="Back">رجوع</span>
        </a>
        <button id="toggle-lang" class="btn btn-info">EN</button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="text-primary-dark mb-1 translate" data-ar="عرض سعر" data-en="Quotation">عرض سعر</h2>
                <p class="text-muted mb-0">#{{ $quote->quote_number }}</p>
            </div>
            <div class="text-end">
                <p class="mb-1">
                    <strong class="translate" data-ar="تاريخ الإصدار:" data-en="Issue Date:">تاريخ الإصدار:</strong>
                    {{ \Carbon\Carbon::parse($quote->issue_date)->format('d F Y') }}
                </p>
                <p class="mb-1">
                    <strong class="translate" data-ar="صالح حتى:" data-en="Valid Until:">صالح حتى:</strong>
                    {{ \Carbon\Carbon::parse($quote->valid_until)->format('d F Y') }}
                </p>
            </div>
        </div>

        <hr class="my-4" />

        <!-- Customer Info -->
        <div class="mb-5">
            <h5 class="text-primary-dark mb-3 translate" data-ar="بيانات العميل" data-en="Customer Info">بيانات العميل</h5>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong class="translate" data-ar="الاسم:" data-en="Name:">الاسم:</strong> {{ $quote->customer_name }}
                    </p>
                    <p class="mb-1">
                        <strong class="translate" data-ar="البريد الإلكتروني:" data-en="Email:">البريد الإلكتروني:</strong> {{ $quote->customer_email ?? 'غير محدد' }}
                    </p>
                    <p class="mb-1">
                        <strong class="translate" data-ar="الهاتف:" data-en="Phone:">الهاتف:</strong> {{ $quote->customer_phone ?? 'غير محدد' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong class="translate" data-ar="الشركة:" data-en="Company:">الشركة:</strong> {{ $quote->customer_company ?? 'غير محدد' }}
                    </p>
                    @if($quote->lead)
                        <p class="mb-1">
                            <strong class="translate" data-ar="العميل المحتمل:" data-en="Lead:">العميل المحتمل:</strong> {{ $quote->lead->name }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Packages & Items -->
        <div class="mb-5">
            <h5 class="text-primary-dark mb-3 translate" data-ar="العناصر" data-en="Items">العناصر</h5>

            @if($quote->items->isEmpty())
                <div class="alert alert-warning translate" data-ar="لا توجد عناصر في هذا العرض." data-en="No items in this quotation.">
                    لا توجد عناصر في هذا العرض.
                </div>
            @else
                @foreach($quote->items->groupBy('package_id') as $packageId => $items)
                    @php
                        $firstItem = $items->first();
                        $packageName = $firstItem->package?->name_ar ?? 'غير معروف';
                    @endphp

                    <h6 class="mt-4 mb-2 border-bottom pb-2 translate" data-ar="الباكج: {{ $packageName }}" data-en="Package: {{ $packageName }}">
                        الباكج: {{ $packageName }}
                    </h6>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="translate" data-ar="القطعة" data-en="Item">القطعة</th>
                                <th class="translate" data-ar="الوصف" data-en="Description">الوصف</th>
                                <th class="translate" data-ar="الكمية" data-en="Quantity">الكمية</th>
                                <th class="translate" data-ar="السعر" data-en="Price">السعر</th>
                                <th class="translate" data-ar="الإجمالي" data-en="Total">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="translate" data-ar="{{ $item->item_name }}" data-en="{{ $item->item_name_en ?? $item->item_name }}">
                                        {{ $item->item_name }}
                                    </td>
                                    <td class="translate" data-ar="{{ $item->description ?? '-' }}" data-en="{{ $item->description_en ?? ($item->description ?? '-') }}">
                                        {{ $item->description ?? '-' }}
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }} <span class="translate" data-ar="ريال" data-en="SAR">ريال</span></td>
                                    <td>{{ number_format($item->total_price, 2) }} <span class="translate" data-ar="ريال" data-en="SAR">ريال</span></td>
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
                <span class="translate" data-ar="المجموع الفرعي:" data-en="Subtotal:">المجموع الفرعي:</span>
                <span>{{ number_format($quote->subtotal, 2) }} <span class="translate" data-ar="ريال" data-en="SAR">ريال</span></span>
            </div>
            @if($quote->discount_amount > 0)
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span class="translate" data-ar="الخصم:" data-en="Discount:">الخصم:</span>
                <span class="text-danger">-{{ number_format($quote->discount_amount, 2) }} <span class="translate" data-ar="ريال" data-en="SAR">ريال</span></span>
            </div>
            @endif
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span class="translate" data-ar="الضريبة ({{ $quote->tax_rate }}%):" data-en="Tax ({{ $quote->tax_rate }}%):">الضريبة ({{ $quote->tax_rate }}%):</span>
                <span>{{ number_format($quote->tax_amount, 2) }} <span class="translate" data-ar="ريال" data-en="SAR">ريال</span></span>
            </div>
            <div class="d-flex justify-content-between p-3 bg-primary-dark text-white">
                <span class="translate" data-ar="المجموع النهائي:" data-en="Total:"><strong>المجموع النهائي:</strong></span>
                <span><strong>{{ number_format($quote->total_amount, 2) }} <span class="translate" data-ar="ريال" data-en="SAR">ريال</span></strong></span>
            </div>
        </div>

        <!-- Notes & Terms -->
        @if($quote->terms_conditions)
            <div class="mb-3">
                <h6 class="text-primary-dark mb-2 translate" data-ar="الشروط والأحكام" data-en="Terms & Conditions">الشروط والأحكام</h6>
                <p class="border p-3 rounded bg-light">{{ $quote->terms_conditions }}</p>
            </div>
        @endif

        @if($quote->notes)
            <div class="mb-3">
                <h6 class="text-primary-dark mb-2 translate" data-ar="ملاحظات" data-en="Notes">ملاحظات</h6>
                <p class="border p-3 rounded bg-light">{{ $quote->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-5 pt-3 border-top">
            <p class="text-muted mb-0 translate" data-ar="شكراً لاختيارك SOFA Experience. للاستفسار: info@sofa.com" data-en="Thank you for choosing SOFA Experience. For inquiries: info@sofa.com">
                شكراً لاختيارك SOFA Experience. للاستفسار: info@sofa.com
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Language Toggle Script -->
    <script>
        const toggleBtn = document.getElementById('toggle-lang');
        let currentLang = 'ar';

        toggleBtn.addEventListener('click', () => {
            currentLang = currentLang === 'ar' ? 'en' : 'ar';
            toggleBtn.textContent = currentLang.toUpperCase();

            document.querySelectorAll('.translate').forEach(el => {
                el.textContent = el.getAttribute('data-' + currentLang);
            });
        });
    </script>
</body>
</html>
