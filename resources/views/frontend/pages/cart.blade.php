@extends('frontend.layouts.pages')

@section('title', $seo->title ?? __('site.blog'))
@section('description', $seo->description ?? __('site.default_description'))

@section('content')
 <!-- ===== BREADCRUMB ===== -->
 <div class="breadcrumb-container container">
    <a href="/" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="/pages/cart.html" class="body-2 text-primary">العربة</a>
</div>

<!-- ===== CART MAIN SECTION ===== -->
<section class="cart-section">
    <div class="container">
        <div class="cart-content">
            <div class="row">
                <!-- Cart Items Column -->
                <div class="col-lg-8 d-flex flex-column gap-sm-3 mb-3">
                    <div class="d-flex flex-column gap-sm-4 mb-4">
                        <!-- Cart Item 1 -->
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="../assets/images/category/category-01.jpg" alt="باكج غرفة نوم واحدة"
                                    class="w-100 h-100" />
                            </div>
                            <div class="d-flex flex-column gap-sm-5 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h3 class="sub-heading-3 mb-0">باكج غرفة نوم واحدة</h3>
                                    <div class="d-flex align-items-center gap-sm-6">
                                        <p class="sub-heading-2 mb-0">26,000</p>
                                        <img src="../assets/images/currency/sar.svg" alt="SAR" />
                                    </div>
                                </div>
                                <div class="mb-2" style="flex: 1;">
                                    <ul class="mb-0">
                                        <li class="body-4">المعيشة: فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</li>
                                        <li class="body-4">الألوان: بيج، أبيض، بني (دافئة)</li>
                                        <li class="body-4">التلفزيون: تصميم خشبي كلاسيكي</li>
                                        <li class="body-4">المطبخ: فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</li>
                                        <li class="body-4">الخزائن: تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)
                                        </li>
                                    </ul>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn quantity-decrease">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input" value="1" min="1" />
                                        <button class="quantity-btn quantity-increase">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <button class="remove-item-btn">
                                        <i class="fas fa-trash-alt"></i>
                                        <span>إزالة</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Item 2 -->
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="../assets/images/category/category-01.jpg" alt="باكج غرفة نوم واحدة"
                                    class="w-100 h-100" />
                            </div>
                            <div class="d-flex flex-column gap-sm-5 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h3 class="sub-heading-3 mb-0">باكج غرفة نوم واحدة</h3>
                                    <div class="d-flex align-items-center gap-sm-6">
                                        <p class="sub-heading-2 mb-0">26,000</p>
                                        <img src="../assets/images/currency/sar.svg" alt="SAR" />
                                    </div>
                                </div>
                                <div class="mb-2" style="flex: 1;">
                                    <ul class="mb-0">
                                        <li class="body-4">المعيشة: فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</li>
                                        <li class="body-4">الألوان: بيج، أبيض، بني (دافئة)</li>
                                        <li class="body-4">التلفزيون: تصميم خشبي كلاسيكي</li>
                                        <li class="body-4">المطبخ: فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</li>
                                        <li class="body-4">الخزائن: تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)
                                        </li>
                                    </ul>
                                </div>
                                <div class="cart-item-actions">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn quantity-decrease">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input" value="1" min="1" />
                                        <button class="quantity-btn quantity-increase">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <button class="remove-item-btn">
                                        <i class="fas fa-trash-alt"></i>
                                        <span>إزالة</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Continue Shopping -->
                    <div class="continue-shopping">
                        <a href="/pages/categories.html" class="btn btn-custom-outline">
                            <i class="fas fa-arrow-right me-2"></i>
                            متابعة التسوق
                        </a>
                    </div>
                </div>

                <!-- Order Summary Column -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="sub-heading-4 mb-0">ملخص الطلب</h3>

                        <!-- Summary Row -->
                        <div class="d-flex flex-column gap-sm-5 mt-3">
                            <div class="d-flex justify-content-between">
                                <span class="body-2">المجموع الفرعي:</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 mb-0">20,555</p>
                                    <img src="../assets/images/currency/sar.svg" alt="SAR" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="body-2">الخصم:</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 text-success mb-0">-3,445</p>
                                    <img src="../assets/images/currency/sar.svg" alt="SAR" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="body-2">التوصيل والتركيب:</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 text-secondary mb-0">مجاني</p>
                                </div>
                            </div>
                        </div>

                        <!-- Promo Code -->
                        <div class="mt-4 d-flex flex-column gap-sm-4">
                            <h4 class="sub-heading-4 mb-0">كود الخصم</h4>
                            <div class="d-flex gap-sm-4">
                                <input type="text" class="form-control" style="flex: 1;"
                                    placeholder="أدخل كود الخصم" />
                                <button class="btn btn-custom-secondary"
                                    style="min-width: fit-content;">تطبيق</button>
                            </div>
                        </div>

                        <!-- Total Row -->
                        <div class="total-row d-flex justify-content-between align-items-center mt-4">
                            <span class="sub-heading-4 mb-0">المجموع النهائي:</span>
                            <div class="d-flex align-items-center gap-sm-6">
                                <p class="sub-heading-2 mb-0">20,555</p>
                                <img src="../assets/images/currency/sar.svg" alt="SAR" />
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <div class="checkout-section d-flex flex-column gap-sm-4">
                            <button class="btn btn-custom-primary w-100">
                                <i class="fas fa-credit-card"></i>
                                إتمام الطلب
                            </button>
                            <button class="btn btn-custom-outline w-100">
                                <i class="fab fa-whatsapp"></i>
                                طلب عبر واتساب
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart State (Hidden by default) -->
            <div class="empty-cart-state" style="display: none;">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart empty-cart-icon mb-3"></i>
                    <h3 class="sub-heading-3 mb-3">العربة فارغة</h3>
                    <p class="body-2 text-caption mb-4">لم تقم بإضافة أي منتجات إلى العربة بعد</p>
                    <a href="/pages/categories.html" class="btn btn-custom-primary">
                        تصفح المنتجات
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/cart.css') }}">
@endpush
