@extends('frontend.layouts.pages')

@section('content')
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="/cart" class="body-2 text-body">السلة</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">تأكيد الطلب</a>
</div>

<section class="checkout-section">
    <div class="container">
        <h2 class="heading-h4 mb-4">تأكيد الطلب</h2>

        <form action="{{ app()->getLocale() == 'ar' ? route('cart.placeOrder') : route('cart.placeOrder.en') }}" method="POST" id="checkout-form">
            @csrf
            <input type="hidden" name="cart_data" id="cart-data-input">
            <input type="hidden" name="coupon_code" id="coupon-code-input">
            <input type="hidden" name="discount_amount" id="discount-amount-input">

            <div class="row">
                <!-- معلومات العميل -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="sub-heading-4 mb-4">معلومات الاتصال</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ auth()->check() ? auth()->user()->name : old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" value="{{ auth()->check() ? auth()->user()->email : old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select" name="country_code" style="max-width: 100px;" required>
                                            <option value="+966" selected>+966</option>
                                            <option value="+971">+971</option>
                                            <option value="+965">+965</option>
                                            <option value="+973">+973</option>
                                            <option value="+974">+974</option>
                                        </select>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                    </div>
                                    @error('phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عدد الوحدات <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="units_count" value="{{ old('units_count', 1) }}" min="1" required>
                                    @error('units_count')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h5 class="sub-heading-4 mb-3 mt-4">تفاصيل المشروع</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نوع المشروع <span class="text-danger">*</span></label>
                                    <select class="form-select" name="project_type" required>
                                        <option value="">اختر نوع المشروع</option>
                                        <option value="small" {{ old('project_type') == 'small' ? 'selected' : '' }}>صغير</option>
                                        <option value="medium" {{ old('project_type') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                        <option value="large" {{ old('project_type') == 'large' ? 'selected' : '' }}>كبير</option>
                                    </select>
                                    @error('project_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المرحلة الحالية <span class="text-danger">*</span></label>
                                    <select class="form-select" name="current_stage" required>
                                        <option value="">اختر المرحلة</option>
                                        <option value="design" {{ old('current_stage') == 'design' ? 'selected' : '' }}>تصميم</option>
                                        <option value="execution" {{ old('current_stage') == 'execution' ? 'selected' : '' }}>تنفيذ</option>
                                        <option value="operation" {{ old('current_stage') == 'operation' ? 'selected' : '' }}>تشغيل</option>
                                    </select>
                                    @error('current_stage')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="has_interior_design" id="has_interior_design" value="1" {{ old('has_interior_design') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_interior_design">
                                            لديك تصميم داخلي
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="needs_finishing_help" id="needs_finishing_help" value="1" {{ old('needs_finishing_help') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="needs_finishing_help">
                                            تحتاج مساعدة في التشطيب
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="needs_color_help" id="needs_color_help" value="1" {{ old('needs_color_help') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="needs_color_help">
                                            تحتاج مساعدة في اختيار الألوان
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ملاحظات إضافية</label>
                                <textarea class="form-control" name="internal_notes" rows="3">{{ old('internal_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ملخص الطلب -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="sub-heading-4 mb-0">ملخص الطلب</h3>

                        <!-- العناصر -->
                        <div class="mt-3" id="checkout-items-list">
                            <!-- سيتم ملؤها بـ JavaScript -->
                        </div>

                        <!-- الملخص المالي -->
                        <div class="d-flex flex-column gap-sm-5 mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <span class="body-2">المجموع الفرعي:</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 mb-0" id="checkout-subtotal">0</p>
                                    <span class="body-2">ريال</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between" id="discount-row" style="display: none !important;">
                                <span class="body-2">الخصم:</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 text-success mb-0" id="checkout-discount">0</p>
                                    <span class="body-2">ريال</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="body-2">الضريبة (15%):</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 mb-0" id="checkout-tax">0</p>
                                    <span class="body-2">ريال</span>
                                </div>
                            </div>
                        </div>

                        <!-- المجموع النهائي -->
                        <div class="total-row d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <span class="sub-heading-4 mb-0">المجموع النهائي:</span>
                            <div class="d-flex align-items-center gap-sm-6">
                                <p class="sub-heading-2 mb-0" id="checkout-total">0</p>
                                <span class="sub-heading-4">ريال</span>
                            </div>
                        </div>

                        <!-- زر تأكيد الطلب -->
                        <div class="checkout-section d-flex flex-column gap-sm-4">
                            <button type="submit" class="btn btn-custom-primary w-100">
                                <i class="fas fa-check-circle"></i>
                                تأكيد الطلب
                            </button>
                            <a href="/cart" class="btn btn-custom-outline w-100">
                                <i class="fas fa-arrow-right"></i>
                                العودة للسلة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
    const couponData = JSON.parse(sessionStorage.getItem('appliedCoupon')) || null;

    if (cart.length === 0) {
        window.location.href = '/cart';
        return;
    }

    // حساب المبالغ
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });

    let discount = 0;
    if (couponData) {
        discount = couponData.discount_amount || 0;
        document.getElementById('discount-row').style.display = 'flex';
        document.getElementById('coupon-code-input').value = couponData.code;
        document.getElementById('discount-amount-input').value = discount;
    }

    const afterDiscount = subtotal - discount;
    const tax = afterDiscount * 0.15;
    const total = afterDiscount + tax;

    // عرض العناصر
    let itemsHtml = '';
    cart.forEach(item => {
        itemsHtml += `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                    <div>
                        <p class="mb-0 body-4">${item.name}</p>
                        <small class="text-muted">الكمية: ${item.quantity}</small>
                    </div>
                </div>
                <p class="mb-0 body-4">${formatPrice(item.price * item.quantity)}</p>
            </div>
        `;
    });

    document.getElementById('checkout-items-list').innerHTML = itemsHtml;
    document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal);
    document.getElementById('checkout-discount').textContent = formatPrice(discount);
    document.getElementById('checkout-tax').textContent = formatPrice(tax);
    document.getElementById('checkout-total').textContent = formatPrice(total);

    // إرسال بيانات السلة مع النموذج
    document.getElementById('cart-data-input').value = JSON.stringify(cart);

    // عند إرسال النموذج

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    if (!isLoggedIn) {
        e.preventDefault(); // منع الإرسال
        const authModal = new bootstrap.Modal(document.getElementById('authModal'));
        authModal.show();
        const loginTab = document.querySelector('#home-tab');
        if (loginTab) {
            const tab = new bootstrap.Tab(loginTab);
            tab.show();
        }
        return;
    }

    // مسح السلة بعد الإرسال
    localStorage.removeItem('shoppingCart');
    sessionStorage.removeItem('appliedCoupon');
});


    function formatPrice(price) {
        return new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 0}).format(price);
    }
});
</script>
@endpush
@endsection
