@extends('frontend.layouts.pages')

@section('title', 'تم إنشاء الطلب بنجاح')

@section('content')
<section class="order-success-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        
                        <h2 class="heading-h4 mb-3">تم إنشاء طلبك بنجاح!</h2>
                        <p class="body-2 text-muted mb-4">شكراً لك على طلبك. سنتواصل معك قريباً.</p>
                        
                        <div class="order-details bg-light p-4 rounded mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">رقم الطلب</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->order_number }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">تاريخ الطلب</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->created_at->format('Y-m-d') }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">الاسم</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->name }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">البريد الإلكتروني</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->email }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-items mb-4">
                            <h5 class="sub-heading-4 mb-3">تفاصيل الطلب</h5>
                            @foreach($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->package->image)
                                    <img src="{{ asset('storage/' . $item->package->image) }}" alt="{{ $item->package->{'name_'.app()->getLocale()} }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    @endif
                                    <div class="text-start">
                                        <p class="mb-0 body-4">{{ $item->package->{'name_'.app()->getLocale()} }}</p>
                                        <small class="text-muted">الكمية: {{ $item->quantity }}</small>
                                    </div>
                                </div>
                                <p class="mb-0 body-4">{{ number_format($item->price * $item->quantity, 0) }} ريال</p>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="order-summary bg-light p-4 rounded mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="body-2">المجموع الفرعي:</span>
                                <span class="body-2">{{ number_format($order->base_amount, 0) }} ريال</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="body-2">الخصم:</span>
                                <span class="body-2">-{{ number_format($order->discount_amount, 0) }} ريال</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span class="body-2">الضريبة (15%):</span>
                                <span class="body-2">{{ number_format($order->tax_amount, 0) }} ريال</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <strong class="sub-heading-4">المجموع النهائي:</strong>
                                <strong class="sub-heading-4">{{ number_format($order->total_amount, 0) }} ريال</strong>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="btn btn-custom-primary">
                                <i class="fas fa-home"></i>
                                العودة للرئيسية
                            </a>
                            <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="btn btn-custom-outline">
                                <i class="fas fa-shopping-bag"></i>
                                تصفح المنتجات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
