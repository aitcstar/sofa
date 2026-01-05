@extends('frontend.layouts.pages')

@section('title', app()->getLocale() == 'ar' ? 'تم إنشاء الطلب بنجاح' : 'Order Created Successfully')

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

                        <h2 class="heading-h4 mb-3">{{ app()->getLocale() == 'ar' ? 'تم إنشاء طلبك بنجاح!' : 'Your Order Has Been Placed Successfully!' }}</h2>
                        <p class="body-2 text-muted mb-4">{{ app()->getLocale() == 'ar' ? 'شكراً لك على طلبك. سنتواصل معك قريباً.' : 'Thank you for your order. We will contact you shortly.' }}</p>

                        <div class="order-details bg-light p-4 rounded mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">{{ app()->getLocale() == 'ar' ? 'رقم الطلب' : 'Order Number' }}</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->order_number }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">{{ app()->getLocale() == 'ar' ? 'تاريخ الطلب' : 'Order Date' }}</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->created_at->format('Y-m-d') }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">{{ app()->getLocale() == 'ar' ? 'الاسم' : 'Name' }}</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->name }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="body-4 text-muted mb-1">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</p>
                                    <h5 class="sub-heading-4 mb-0">{{ $order->email }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="order-items mb-4">
                            <h5 class="sub-heading-4 mb-3">{{ app()->getLocale() == 'ar' ? 'تفاصيل الطلب' : 'Order Details' }}</h5>
                            @foreach($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->package->image)
                                    <img src="{{ asset('storage/' . $item->package->image) }}" alt="{{ $item->package->{'name_'.app()->getLocale()} }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    @endif
                                    <div class="text-start">
                                        <p class="mb-0 body-4">{{ $item->package->{'name_'.app()->getLocale()} }}</p>
                                        <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'الكمية:' : 'Quantity:' }} {{ $item->quantity }}</small>
                                    </div>
                                </div>
                                <p class="mb-0 body-4">{{ number_format($item->price * $item->quantity, 0) }} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</p>
                            </div>
                            @endforeach
                        </div>

                        <div class="order-summary bg-light p-4 rounded mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="body-2">{{ app()->getLocale() == 'ar' ? 'المجموع الفرعي:' : 'Subtotal:' }}</span>
                                <span class="body-2">{{ number_format($order->base_amount, 0) }} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="body-2">{{ app()->getLocale() == 'ar' ? 'الخصم:' : 'Discount:' }}</span>
                                <span class="body-2">-{{ number_format($order->discount_amount, 0) }} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span class="body-2">{{ app()->getLocale() == 'ar' ? 'الضريبة (15%):' : 'Tax (15%):' }}</span>
                                <span class="body-2">{{ number_format($order->tax_amount, 0) }} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <strong class="sub-heading-4">{{ app()->getLocale() == 'ar' ? 'المجموع النهائي:' : 'Grand Total:' }}</strong>
                                <strong class="sub-heading-4">{{ number_format($order->total_amount, 0) }} {{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</strong>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="btn btn-custom-primary">
                                <i class="fas fa-home"></i>
                                {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
                            </a>
                            <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="btn btn-custom-outline">
                                <i class="fas fa-shopping-bag"></i>
                                {{ app()->getLocale() == 'ar' ? 'تصفح المنتجات' : 'Browse Products' }}
                            </a>


                            <a href="{{ url('/order/' . $order->id) }}" class="btn btn-custom-outline">
                                <i class="fas fa-shopping-bag"></i>
                                {{ app()->getLocale() == 'ar' ? 'تفاصيل الطلب' : 'Order details' }}
                            </a>




                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
