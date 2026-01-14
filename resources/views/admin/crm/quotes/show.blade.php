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
            <i class="fas fa-print me-2"></i>
            <span data-translate="طباعة">طباعة</span>
        </button>
        <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary ms-2">
            <i class="fas fa-arrow-right me-2"></i>
            <span data-translate="رجوع">رجوع</span>
        </a>
        <button id="toggle-lang" class="btn btn-secondary ms-2">EN/AR</button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="text-primary-dark mb-1" data-translate="عرض سعر">عرض سعر</h2>
                <p class="text-muted mb-0">#{{ $quote->quote_number }}</p>
            </div>
            <div class="text-end">
                <p class="mb-1"><strong data-translate="تاريخ الإصدار:">تاريخ الإصدار:</strong> {{ \Carbon\Carbon::parse($quote->issue_date)->format('d F Y') }}</p>
                <p class="mb-1"><strong data-translate="صالح حتى:">صالح حتى:</strong> {{ \Carbon\Carbon::parse($quote->valid_until)->format('d F Y') }}</p>
            </div>
        </div>

        <hr class="my-4" />

        <!-- Customer Info -->
        <div class="mb-5">
            <h5 class="text-primary-dark mb-3" data-translate="بيانات العميل">بيانات العميل</h5>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong data-translate="الاسم:">الاسم:</strong> {{ $quote->customer_name }}</p>
                    <p class="mb-1"><strong data-translate="البريد الإلكتروني:">البريد الإلكتروني:</strong> {{ $quote->customer_email ?? '-' }}</p>
                    <p class="mb-1"><strong data-translate="الهاتف:">الهاتف:</strong> {{ $quote->customer_phone ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong data-translate="الشركة:">الشركة:</strong> {{ $quote->customer_company ?? '-' }}</p>
                    @if($quote->lead)
                        <p class="mb-1"><strong data-translate="العميل المحتمل:">العميل المحتمل:</strong> {{ $quote->lead->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Packages & Items -->
        <div class="mb-5">
            <h5 class="text-primary-dark mb-3" data-translate="العناصر">العناصر</h5>

            @if($quote->items->isEmpty())
                <div class="alert alert-warning" data-translate="لا توجد عناصر في هذا العرض.">لا توجد عناصر في هذا العرض.</div>
            @else
                @foreach($quote->items->groupBy('package_id') as $packageId => $items)
                    @php
                        $firstItem = $items->first();
                        $packageName = $firstItem->package?->name_ar ?? '-';
                    @endphp

                    <h6 class="mt-4 mb-2 border-bottom pb-2">
                        <strong data-translate="الباكج:">الباكج:</strong> {{ $packageName }}
                    </h6>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th data-translate="القطعة">القطعة</th>
                                <th data-translate="الوصف">الوصف</th>
                                <th data-translate="الكمية">الكمية</th>
                                <th data-translate="السعر">السعر</th>
                                <th data-translate="الإجمالي">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td data-key="{{ $item->item_name }}">{{ $item->item_name }}</td>
                                    <td data-key="{{ $item->description ?? '-' }}">{{ $item->description ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        <span class="currency" data-ar="{{ number_format($item->unit_price,2) }} ريال" data-en="{{ number_format($item->unit_price,2) }} SAR">
                                            {{ number_format($item->unit_price,2) }} ريال
                                        </span>
                                    </td>
                                    <td>
                                        <span class="currency" data-ar="{{ number_format($item->total_price,2) }} ريال" data-en="{{ number_format($item->total_price,2) }} SAR">
                                            {{ number_format($item->total_price,2) }} ريال
                                        </span>
                                    </td>
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
                <span data-translate="المجموع الفرعي:">المجموع الفرعي:</span>
                <span>{{ number_format($quote->subtotal, 2) }} <span class="currency" data-ar="ريال" data-en="SAR">ريال</span></span>
            </div>
            @if($quote->discount_amount > 0)
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span data-translate="الخصم:">الخصم:</span>
                <span class="text-danger">-{{ number_format($quote->discount_amount, 2) }} <span class="currency" data-ar="ريال" data-en="SAR">ريال</span></span>
            </div>
            @endif
            <div class="d-flex justify-content-between p-3 border-bottom">
                <span data-translate="الضريبة">الضريبة</span> ({{ $quote->tax_rate }}%):
                <span>{{ number_format($quote->tax_amount, 2) }} <span class="currency" data-ar="ريال" data-en="SAR">ريال</span></span>
            </div>
            <div class="d-flex justify-content-between p-3 bg-primary-dark text-white">
                <span><strong data-translate="المجموع النهائي:">المجموع النهائي:</strong></span>
                <span><strong>{{ number_format($quote->total_amount, 2) }} <span class="currency" data-ar="ريال" data-en="SAR">ريال</span></strong></span>
            </div>
        </div>

        <!-- Notes & Terms -->
        @if($quote->terms_conditions)
            <div class="mb-3">
                <h6 class="text-primary-dark mb-2" data-translate="الشروط والأحكام">الشروط والأحكام</h6>
                <p class="border p-3 rounded bg-light">{{ $quote->terms_conditions }}</p>
            </div>
        @endif

        @if($quote->notes)
            <div class="mb-3">
                <h6 class="text-primary-dark mb-2" data-translate="ملاحظات">ملاحظات</h6>
                <p class="border p-3 rounded bg-light">{{ $quote->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-5 pt-3 border-top">
            <p class="text-muted mb-0" data-translate="شكراً لاختيارك SOFA Experience. للاستفسار: info@sofa.com">
                شكراً لاختيارك SOFA Experience. للاستفسار: info@sofa.com
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let lang = 'ar';
        const translations = {
            "عرض سعر": "Quotation",
            "تاريخ الإصدار:": "Issue Date:",
            "صالح حتى:": "Valid Until:",
            "بيانات العميل": "Customer Info",
            "الاسم:": "Name:",
            "البريد الإلكتروني:": "Email:",
            "الهاتف:": "Phone:",
            "الشركة:": "Company:",
            "العميل المحتمل:": "Lead:",
            "العناصر": "Items",
            "الباكج:": "Package:",
            "القطعة": "Item",
            "الوصف": "Description",
            "الكمية": "Quantity",
            "السعر": "Price",
            "الإجمالي": "Total",
            "المجموع الفرعي:": "Subtotal:",
            "الخصم:": "Discount:",
            "الضريبة": "Tax:",
            "المجموع النهائي:": "Total Amount:",
            "الشروط والأحكام": "Terms & Conditions",
            "ملاحظات": "Notes",
            "شكراً لاختيارك SOFA Experience. للاستفسار: info@sofa.com": "Thanks for choosing SOFA Experience. For inquiries: info@sofa.com",
            "طباعة": "Print",
            "رجوع": "Back"
        };

        document.getElementById('toggle-lang').addEventListener('click', function() {
            lang = lang === 'ar' ? 'en' : 'ar';

            // ترجمة عناصر الجدول item_name و description
            document.querySelectorAll('td[data-key]').forEach(el => {
                el.innerText = el.dataset.key;
            });

            // ترجمة العملات
            document.querySelectorAll('.currency').forEach(el => {
                el.innerText = el.dataset[lang];
            });

            // ترجمة النصوص الثابتة
            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.dataset.translate;
                el.innerText = lang === 'ar' ? key : (translations[key] || key);
            });
        });
    </script>
</body>
</html>
