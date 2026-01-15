<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>الفاتورة #{{ $invoice->order_number }} - SOFA Experience</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/invoice-pricing.css') }}" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
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
        }

        :root {
            --primary: #08203E;
            --input-text: #333;
        }

        .gap-sm-5 { gap: 8px; }
        .gap-sm-6 { gap: 12px; }
        .body-2 { font-size: 14px; }
        .heading-h7 { font-size: 18px; font-weight: 700; }
        .heading-h8 { font-size: 20px; font-weight: 700; }
        .sub-heading-3 { font-size: 16px; font-weight: 600; }
        .text-heading { color: #08203E; }
        .text-subheading { color: #666; }
    </style>
</head>

<body>
    <!-- Print Button -->
    <div class="print-button no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-2"></i>طباعة
        </button>

    </div>

    <!-- ===== INVOICE CONTAINER ===== -->
    <div class="container invoice-pricing-container" style="max-width: 900px; margin: 40px auto; padding: 40px; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        <!-- ===== Logo & Details ===== -->
        <div class="logo-details" style="display: flex; justify-content: space-between; margin-bottom: 30px;">
            <!-- Logo -->
            <div class="logo">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" style="max-width: 150px;" />
            </div>

            <!-- Details -->
            <div class="details" style="display: flex; gap: 40px;">
                <!-- Provider Company Data -->
                <div class="d-flex flex-column gap-sm-6">
                    <p class="body-2 mb-0">{{ $siteSettings->site_name }}</p>
                    <p class="body-2 mb-0">{{ $siteSettings->address }}</p>
                    <p class="body-2 mb-0">{{ $siteSettings->phone }}</p>
                    <p class="body-2 mb-0">CR: 1010000000</p>
                </div>

                <!-- Invoice Data -->
                <div class="d-flex flex-column gap-sm-6">
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">رقم الفاتورة:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;"># {{ $invoice->order_number }}</p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">تاريخ الإصدار:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">{{ $invoice->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">تاريخ الاستحقاق:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">{{ $invoice->due_date ? $invoice->due_date->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">الحالة:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">
                            @if($invoice->status == 'paid')
                                مدفوعة
                            @elseif($invoice->status == 'partial')
                                مدفوعة جزئياً
                            @else
                                غير مدفوعة
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4" />

        <!-- ===== Customer Data ===== -->
        <div class="customer-data d-flex flex-column gap-sm-4 mb-4">
            <h3 class="heading-h8 mb-3">بيانات العميل</h3>
            <div class="customer-data-item-details" style="display: flex; justify-content: space-between;">
                <div class="d-flex flex-column gap-sm-5">
                    <p class="mb-0 body-2">اسم العميل:</p>
                    <p class="mb-0 body-2">رقم التواصل:</p>
                    <p class="mb-0 body-2">العنوان:</p>
                </div>
                <div class="d-flex flex-column gap-sm-5" style="text-align: left;">
                    <p class="mb-0 body-2 text-heading">{{ $invoice->user->name ?? 'غير محدد' }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $invoice->user->phone ?? 'غير محدد' }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $invoice->user->address ?? 'غير محدد' }}</p>
                </div>
            </div>
        </div>

       <!-- ===== Order Details ===== -->
<div class="order-details d-flex flex-column gap-sm-4 mb-4">
    <h3 class="heading-h8 mb-3">تفاصيل الطلب</h3>

    <!-- Table
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th class="body-2 py-3 text-center">نوع الوحدة</th>
                    <th class="body-2 py-3 text-center">عدد الوحدات</th>
                    <th class="body-2 py-3 text-center">الباكج</th>
                    <th class="body-2 py-3 text-center">السعر/الوحدة</th>
                    <th class="body-2 py-3 text-center">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->package?->packageUnitItems ?? [] as $item)
                <tr>
                    <td class="body-2 py-3 text-center">{{ $item->unit->name_ar ?? 'غير محدد' }}</td>
                    <td class="body-2 py-3 text-center">{{ $item->quantity ?? 1 }}</td>
                    <td class="body-2 py-3 text-center">{{ $item->package->name_ar ?? 'غير محدد' }}</td>
                    <td class="body-2 py-3 text-center">
                        <strong>{{ number_format($item->package->price ?? 0, 0) }}</strong> ريال
                    </td>
                    <td class="body-2 py-3 text-center">
                        <strong>{{ number_format(($item->package->price ?? 0) * ($item->quantity ?? 1) * 1.15, 0) }}</strong> ريال
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> -->
</div>

<!-- ===== Package Details (Expanded) ===== -->
@if($invoice->package && $invoice->package->packageUnitItems)
<div class="studio-package-details mb-4">

<div class="mb-3">
    <h4 class="sub-heading-3 mb-2">المكونات</h4>

    @php
    $units = $invoice->package->packageUnitItems->groupBy(fn($item) => $item->unit->name_ar);
    @endphp

    @foreach($units as $unitName => $unitItems)
        <div class="mb-3">
            <!-- اسم الوحدة -->
            <div class="p-2 text-center text-white" style="background-color: var(--primary);">
                <h5 class="mb-0">{{ $unitName }}</h5>
            </div>

            <!-- جدول القطع التابعة للوحدة -->
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="body-2 text-center">القطعة</th>
                            <th class="body-2 text-center">المقاس</th>
                            <th class="body-2 text-center">الخامة</th>
                            <th class="body-2 text-center">اللون</th>
                            <th class="body-2 text-center">الصورة</th>
                            <th class="body-2 text-center">الكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unitItems as $unitItem)
                        <tr>
                            <td class="text-center">{{ $unitItem->item->item_name_ar ?? '-' }}</td>
                            <td class="text-center">{{ $unitItem->item->dimensions ?? '-' }}</td>
                            <td class="text-center">{{ $unitItem->item->material_ar ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge" style="background-color: {{ $unitItem->item->background_color ?? '#262626' }}; color: white;">
                                    {{ $unitItem->item->color_ar ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($unitItem->item->image_path)
                                    <img src="{{ asset('storage/' . $unitItem->item->image_path) }}"
                                         alt="{{ $unitItem->item->name }}"
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                @endif
                            </td>
                            <td class="text-center">{{ $unitItem->quantity ?? 1 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    </div>
</div>
@endif


        <!-- ===== Total Summary ===== -->
        <div class="total-summary border rounded mb-4">
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">المجموع الفرعي:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($invoice->subtotal, 0) }}</strong> ريال</p>
            </div>
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">الضريبة ({{ $invoice->tax_rate ?? 15 }}%):</p>
                <p class="body-2 mb-0"><strong>{{ number_format($invoice->tax_amount, 0) }}</strong> ريال</p>
            </div>
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">الشحن:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($invoice->shipping_cost ?? 0, 0) }}</strong> ريال</p>
            </div>
            <div class="d-flex justify-content-between p-3 text-white" style="background-color: var(--primary);">
                <p class="body-2 mb-0 fw-bold">المجموع النهائي:</p>
                <p class="heading-h7 mb-0">{{ number_format($invoice->total_amount, 0) }} ريال</p>
            </div>
        </div>

        <!-- ===== Payment Methods ===== -->
        <div class="payment-methods p-3 bg-light rounded mb-4">
            <div class="d-flex justify-content-between">
                <p class="body-2 mb-0"> المبلغ المدفوع:</p>
                <p class="body-2 mb-0 fw-bold">{{ number_format($invoice->paid_amount ?? 0) }}  ريال</p>
            </div>
        </div>

        <!-- ===== Policies ===== -->
        <div class="policies border rounded p-3 mb-4">
            <div class="mb-3">
                <p class="fw-bold mb-1">شروط الضمان:</p>
                <p class="body-2 mb-0">يشمل الضمان عيوب التصنيع لمدة سنة واحدة من تاريخ التسليم</p>
            </div>
            <div>
                <p class="fw-bold mb-1">سياسة الإرجاع:</p>
                <p class="body-2 mb-0">لا يمكن إرجاع المنتجات بعد التنفيذ إلا في حال وجود عيوب واضحة في التصنيع</p>
            </div>
        </div>

        <hr class="my-4" />

        <!-- ===== Footer ===== -->
        <div class="invoice-footer text-center">
            <p class="body-2 mb-0">شكراً لاختياركم SOFA. لمتابعة طلبك أو التواصل معنا: info@sofa4.com | www.sofa4.com</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
