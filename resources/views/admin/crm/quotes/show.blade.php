@extends('admin.layouts.app')

@section('title', 'عرض السعر - ' . $quote->quote_number)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/invoice-pricing.css') }}">
<style>
    .invoice-pricing-container {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        .invoice-pricing-container {
            box-shadow: none;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header with Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="mb-1">عرض السعر: {{ $quote->quote_number }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.quotes.index') }}">عروض الأسعار</a></li>
                    <li class="breadcrumb-item active">{{ $quote->quote_number }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>طباعة
            </button>
            <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Quote Content -->
    <div class="invoice-pricing-container">
        <!-- Logo & Details -->
        <div class="logo-details" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px;">
            <!-- Logo -->
            <div class="logo">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" style="max-width: 150px;" />
            </div>

            <!-- Details -->
            <div style="display: flex; gap: 40px;">
                <!-- Provider Company Data -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <p style="margin: 0; font-size: 14px;">SOFA Furnishing Co</p>
                    <p style="margin: 0; font-size: 14px;">الرياض، المملكة العربية السعودية</p>
                    <p style="margin: 0; font-size: 14px;">+966500000000</p>
                    <p style="margin: 0; font-size: 14px;">CR: 1010000000</p>
                </div>

                <!-- Quote Data -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; gap: 10px;">
                        <p style="margin: 0; font-size: 14px;">رقم العرض:</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->quote_number }}</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <p style="margin: 0; font-size: 14px;">تاريخ الإصدار:</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->issue_date->format('d/m/Y') }}</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <p style="margin: 0; font-size: 14px;">صالح حتى:</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->valid_until->format('d/m/Y') }}</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <p style="margin: 0; font-size: 14px;">الحالة:</p>
                        <span class="badge bg-{{ $quote->status_color ?? 'secondary' }}">{{ $quote->status_text ?? $quote->status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <hr style="margin: 30px 0;" />

        <!-- Customer Data -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">بيانات العميل</h3>
            <div style="display: flex; justify-content: space-between; max-width: 600px;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <p style="margin: 0; font-size: 14px;">اسم العميل:</p>
                    <p style="margin: 0; font-size: 14px;">رقم التواصل:</p>
                    @if($quote->customer_email)
                    <p style="margin: 0; font-size: 14px;">البريد الإلكتروني:</p>
                    @endif
                    @if($quote->customer_company)
                    <p style="margin: 0; font-size: 14px;">الشركة:</p>
                    @endif
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px; text-align: left;">
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->customer_name }}</p>
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->customer_phone }}</p>
                    @if($quote->customer_email)
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->customer_email }}</p>
                    @endif
                    @if($quote->customer_company)
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quote->customer_company }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">تفاصيل العرض</h3>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="padding: 12px;">الباكج</th>
                            <th style="padding: 12px;">الوصف</th>
                            <th style="padding: 12px;">الكمية</th>
                            <th style="padding: 12px;">السعر/الوحدة</th>
                            <th style="padding: 12px;">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->items as $item)
                        <tr>
                            <td style="padding: 12px;">{{ $item->item_name }}</td>
                            <td style="padding: 12px;">{{ $item->description ?? '-' }}</td>
                            <td style="padding: 12px; text-align: center;">{{ $item->quantity }}</td>
                            <td style="padding: 12px; text-align: center;">{{ number_format($item->unit_price, 2) }} ريال</td>
                            <td style="padding: 12px; text-align: center; font-weight: bold;">{{ number_format($item->total_price, 2) }} ريال</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @foreach($quote->items->where('package_id', '!=', null) as $item)
        @if($item->package)
        <!-- Package Details -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 15px;">تفاصيل {{ $item->package->name_ar }}</h3>

            @if($item->package->units && $item->package->units->count() > 0)
            @foreach($item->package->units as $unit)
            <div style="margin-bottom: 20px;">
                <div style="background-color: #08203e; color: white; padding: 10px; text-align: center; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 16px;">{{ $unit->name_ar }}</h4>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="padding: 10px;">القطعة</th>
                                <th style="padding: 10px;">السعر</th>
                                <th style="padding: 10px;">المقاس</th>
                                <th style="padding: 10px;">الخامة</th>
                                <th style="padding: 10px;">اللون</th>
                                @if($unit->items->where('image', '!=', null)->count() > 0)
                                <th style="padding: 10px;">الصورة</th>
                                @endif
                                <th style="padding: 10px;">الكمية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unit->items as $unitItem)
                            <tr>
                                <td style="padding: 10px;">{{ $unitItem->name_ar }}</td>
                                <td style="padding: 10px; text-align: center;">{{ number_format($unitItem->price, 2) }} ريال</td>
                                <td style="padding: 10px; text-align: center;">{{ $unitItem->dimensions ?? '-' }}</td>
                                <td style="padding: 10px; text-align: center;">{{ $unitItem->material ?? '-' }}</td>
                                <td style="padding: 10px; text-align: center;">{{ $unitItem->color ?? '-' }}</td>
                                @if($unit->items->where('image', '!=', null)->count() > 0)
                                <td style="padding: 10px; text-align: center;">
                                    @if($unitItem->image)
                                    <img src="{{ asset('storage/' . $unitItem->image) }}" alt="{{ $unitItem->name_ar }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" />
                                    @else
                                    <span>-</span>
                                    @endif
                                </td>
                                @endif
                                <td style="padding: 10px; text-align: center;">{{ $unitItem->quantity ?? 1 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <hr style="margin: 30px 0;" />
        @endif
        @endforeach

        <!-- Summary -->
        <div style="margin-bottom: 30px;">
            <div style="max-width: 400px; margin-left: auto;">
                <div style="display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid #dee2e6;">
                    <span>المجموع الفرعي:</span>
                    <span style="font-weight: bold;">{{ number_format($quote->subtotal, 2) }} ريال</span>
                </div>

                @if($quote->discount_amount > 0)
                <div style="display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid #dee2e6;">
                    <span>الخصم:</span>
                    <span style="font-weight: bold; color: #dc3545;">-{{ number_format($quote->discount_amount, 2) }} ريال</span>
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid #dee2e6;">
                    <span>ضريبة القيمة المضافة ({{ $quote->tax_rate }}%):</span>
                    <span style="font-weight: bold;">{{ number_format($quote->tax_amount, 2) }} ريال</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 15px; background-color: #08203e; color: white;">
                    <span style="font-weight: bold;">المجموع النهائي:</span>
                    <span style="font-weight: bold; font-size: 18px;">{{ number_format($quote->total_amount, 2) }} ريال</span>
                </div>
            </div>
        </div>

        @if($quote->terms_conditions)
        <div style="margin-bottom: 20px;">
            <p style="margin: 0; font-weight: bold;">الشروط والأحكام:</p>
            <p style="margin: 5px 0 0 0;">{{ $quote->terms_conditions }}</p>
        </div>
        @endif

        @if($quote->notes)
        <div style="margin-bottom: 20px;">
            <p style="margin: 0; font-weight: bold;">ملاحظات:</p>
            <p style="margin: 5px 0 0 0;">{{ $quote->notes }}</p>
        </div>
        @endif

        <!-- Signature -->
        <div style="text-align: left; margin-top: 50px; margin-bottom: 30px;">
            <p style="font-weight: bold;">توقيع ممثل SOFA</p>
            <div style="border-bottom: 1px solid #000; width: 200px; margin-top: 40px;"></div>
        </div>

        <hr style="margin: 30px 0;" />

        <!-- Footer -->
        <div style="text-align: center; padding: 20px;">
            <p style="margin: 0; font-size: 14px;">شكراً لاختياركم SOFA. لمتابعة طلبك أو التواصل معنا: info@sofa.com | www.sofa.com</p>
        </div>
    </div>
</div>
@endsection

