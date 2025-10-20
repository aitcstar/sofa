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
            --padding-box-small-3: 12px;
            --padding-box-small-4: 16px;
            --padding-box-small-5: 20px;
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
        <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary ms-2">
            <i class="fas fa-arrow-right me-2"></i>رجوع
        </a>
    </div>

    <!-- ===== PRICING CONTAINER ===== -->
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
                    <p class="body-2 mb-0">SOFA Furnishing Co</p>
                    <p class="body-2 mb-0">الرياض، المملكة العربية السعودية</p>
                    <p class="body-2 mb-0">+966500000000</p>
                    <p class="body-2 mb-0">CR: 1010000000</p>
                </div>

                <!-- Quote Data -->
                <div class="d-flex flex-column gap-sm-6">
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">رقم العرض:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">#{{ $quote->quote_number }}</p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">تاريخ الإصدار:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">{{ $quote->created_at->format('d F Y') }}</p>
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
                    <p class="mb-0 body-2 text-heading">{{ $quote->customer->name ?? 'غير محدد' }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $quote->customer->phone ?? 'غير محدد' }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $quote->customer->address ?? 'غير محدد' }}</p>
                </div>
            </div>
        </div>

        <!-- ===== Order Details ===== -->
        <div class="order-details d-flex flex-column gap-sm-4 mb-4">
            <h3 class="heading-h8 mb-3">تفاصيل العرض</h3>

            <!-- Table -->
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
                        @foreach($quote->items as $item)
                        <tr>
                            <td class="body-2 py-3 text-center">{{ $item->unit_type ?? 'غير محدد' }}</td>
                            <td class="body-2 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="body-2 py-3 text-center">{{ $item->package->name ?? 'غير محدد' }}</td>
                            <td class="body-2 py-3 text-center">
                                <strong>{{ number_format($item->unit_price, 0) }}</strong> ريال
                            </td>
                            <td class="body-2 py-3 text-center">
                                <strong>{{ number_format($item->total_price, 0) }}</strong> ريال
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== Package Details ===== -->
        @foreach($quote->items as $item)
        @if($item->package)
        <div class="studio-package-details mb-4">
            <h3 class="heading-h8 mb-3">تفاصيل {{ $item->package->name }}</h3>

            <!-- Options -->
            @if($item->package_options)
            <div class="mb-3">
                <h4 class="sub-heading-3 mb-2">الاختيارات</h4>
                <div class="row g-3">
                    @foreach(json_decode($item->package_options, true) ?? [] as $key => $value)
                    <div class="col-md-6">
                        <div class="p-3 border rounded">
                            <p class="body-3 mb-1 fw-bold">{{ $key }}</p>
                            <p class="body-3 text-subheading mb-0">{{ $value }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Components -->
            @if($item->package->units)
            <div class="mb-3">
                <h4 class="sub-heading-3 mb-2">المكونات</h4>

                @foreach($item->package->units as $unit)
                <div class="mb-3">
                    <div class="p-2 text-center text-white" style="background-color: var(--primary);">
                        <h5 class="mb-0">{{ $unit->name }}</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="body-2 text-center">القطعة</th>
                                    <th class="body-2 text-center">السعر</th>
                                    <th class="body-2 text-center">المقاس</th>
                                    <th class="body-2 text-center">الخامة</th>
                                    <th class="body-2 text-center">اللون</th>
                                    <th class="body-2 text-center">الصورة</th>
                                    <th class="body-2 text-center">الكمية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unit->items as $unitItem)
                                <tr>
                                    <td class="body-2 text-center">{{ $unitItem->name }}</td>
                                    <td class="body-2 text-center">{{ number_format($unitItem->price, 0) }} ريال</td>
                                    <td class="body-2 text-center">{{ $unitItem->dimensions ?? '-' }}</td>
                                    <td class="body-2 text-center">{{ $unitItem->material ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: {{ $unitItem->color_code ?? '#262626' }}; color: white;">
                                            {{ $unitItem->color ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($unitItem->image)
                                        <img src="{{ asset('storage/' . $unitItem->image) }}" alt="{{ $unitItem->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;" />
                                        @else
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                        @endif
                                    </td>
                                    <td class="body-2 text-center">{{ $unitItem->quantity ?? 1 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if(!$loop->last)
        <hr class="my-4" />
        @endif
        @endif
        @endforeach

        <!-- ===== Total Summary ===== -->
        <div class="total-summary border rounded mb-4">
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">المجموع الفرعي:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($quote->subtotal, 0) }}</strong> ريال</p>
            </div>
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">الضريبة ({{ $quote->tax_rate ?? 15 }}%):</p>
                <p class="body-2 mb-0"><strong>{{ number_format($quote->tax_amount, 0) }}</strong> ريال</p>
            </div>
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">الشحن:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($quote->shipping_cost ?? 0, 0) }}</strong> ريال</p>
            </div>
            <div class="d-flex justify-content-between p-3 text-white" style="background-color: var(--primary);">
                <p class="body-2 mb-0 fw-bold">المجموع النهائي:</p>
                <p class="heading-h7 mb-0">{{ number_format($quote->total_amount, 0) }} ريال</p>
            </div>
        </div>

        <!-- ===== Payment Methods ===== -->
        <div class="payment-methods p-3 bg-light rounded mb-4">
            <div class="d-flex justify-content-between">
                <p class="body-2 mb-0">طريقة الدفع المستخدمة:</p>
                <p class="body-2 mb-0 fw-bold">{{ $quote->payment_method ?? 'تحويل بنكي' }}</p>
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

        <!-- ===== Bank Transfer ===== -->
        <div class="bank-transfer border rounded p-3 mb-4">
            <h4 class="heading-h8 mb-3">تحويل بنكي</h4>
            <div class="row g-2">
                <div class="col-6">
                    <p class="body-2 mb-0">اسم الحساب:</p>
                </div>
                <div class="col-6 text-end">
                    <p class="body-2 mb-0 fw-bold">SOFA Furnishing Co</p>
                </div>
                <div class="col-6">
                    <p class="body-2 mb-0">البنك:</p>
                </div>
                <div class="col-6 text-end">
                    <p class="body-2 mb-0 fw-bold">البنك السعودي الأول</p>
                </div>
                <div class="col-6">
                    <p class="body-2 mb-0">رقم الحساب:</p>
                </div>
                <div class="col-6 text-end">
                    <p class="body-2 mb-0 fw-bold">SA0000000000000000000000</p>
                </div>
                <div class="col-6">
                    <p class="body-2 mb-0">التحويل بمرجع:</p>
                </div>
                <div class="col-6 text-end">
                    <p class="body-2 mb-0 fw-bold">#{{ $quote->quote_number }}</p>
                </div>
            </div>
        </div>

        <!-- ===== Signature ===== -->
        <div class="signature text-end mb-4">
            <p class="fw-bold">توقيع ممثل SOFA</p>
            <div style="height: 60px; border-bottom: 1px solid #ccc; width: 200px; margin-left: auto;"></div>
        </div>

        <hr class="my-4" />

        <!-- ===== Footer ===== -->
        <div class="invoice-footer text-center">
            <p class="body-2 mb-0">شكراً لاختياركم SOFA. لمتابعة طلبك أو التواصل معنا: info@sofa.com | www.sofa.com</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

