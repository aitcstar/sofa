@extends('frontend.layouts.pages')

@section('content')
    <div class="breadcrumb-container container">
        <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}"
            class="body-2 text-body">{{ __('site.home') }}</a>
        <span class="body-2 text-body">/</span>
        <a href="/cart" class="body-2 text-body">{{ app()->getLocale() == 'ar' ? 'السلة' : 'Cart' }}</a>
        <span class="body-2 text-body">/</span>
        <a href="#" class="body-2 text-primary">{{ app()->getLocale() == 'ar' ? 'تأكيد الطلب' : 'Confirm Order' }}</a>
    </div>

    <section class="checkout-section">
        <div class="container">
            <h2 class="heading-h4 mb-4">{{ app()->getLocale() == 'ar' ? 'تأكيد الطلب' : 'Confirm Order' }}</h2>

            <form action="{{ app()->getLocale() == 'ar' ? route('cart.placeOrder') : route('cart.placeOrder.en') }}"
                method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="cart_data" id="cart-data-input">
                <input type="hidden" name="coupon_code" id="coupon-code-input">
                <input type="hidden" name="discount_amount" id="discount-amount-input">

                <div class="row">
                    <!-- Customer Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="sub-heading-4 mb-4">
                                    {{ app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}</h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label
                                            class="form-label">{{ app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name' }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ auth()->check() ? auth()->user()->name : old('name') }}" required>
                                        @error('name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label
                                            class="form-label">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                                            <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email"
                                            value="{{ auth()->check() ? auth()->user()->email : old('email') }}" required>
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label
                                            class="form-label">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}
                                            <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-select" name="country_code" style="max-width: 100px;"
                                                required>
                                                <option value="+966" selected>+966</option>
                                                <option value="+971">+971</option>
                                                <option value="+965">+965</option>
                                                <option value="+973">+973</option>
                                                <option value="+974">+974</option>
                                            </select>
                                            <input type="text" class="form-control" name="phone"
                                                value="{{ old('phone') }}" required>
                                        </div>
                                        @error('phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @php
                                        $cart = json_decode(session()->get('shoppingCart', '[]'), true);
                                        $unitsCount =
                                            $cart[0]['quantity'] ?? (\App\Models\Setting::first()?->min_units ?? 1);
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <label
                                            class="form-label">{{ app()->getLocale() == 'ar' ? 'عدد الوحدات' : 'Number of Units' }}
                                            <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="units_count"
                                            value="{{ $unitsCount }}" readonly>

                                        @error('units_count')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label
                                        class="form-label">{{ app()->getLocale() == 'ar' ? 'نوع العميل' : 'Client Type' }}
                                        <span class="text-danger">*</span></label>
                                    <select class="form-select" name="client_type" required>
                                        <option value="">
                                            {{ app()->getLocale() == 'ar' ? 'اختر نوع العميل' : 'Select Client Type' }}
                                        </option>
                                        <option value="individual"
                                            {{ old('client_type') == 'individual' ? 'selected' : '' }}>
                                            {{ app()->getLocale() == 'ar' ? 'فرد' : 'Individual' }}
                                        </option>
                                        <option value="commercial"
                                            {{ old('client_type') == 'commercial' ? 'selected' : '' }}>
                                            {{ app()->getLocale() == 'ar' ? 'شركة' : 'Commercial' }}
                                        </option>
                                    </select>

                                    @error('client_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label
                                        class="form-label">{{ app()->getLocale() == 'ar' ? 'ملاحظات إضافية' : 'Additional Notes' }}</label>
                                    <textarea class="form-control" name="internal_notes" rows="3">{{ old('internal_notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h3 class="sub-heading-4 mb-0">
                                {{ app()->getLocale() == 'ar' ? 'ملخص الطلب' : 'Order Summary' }}</h3>

                            <!-- Items -->
                            <div class="mt-3" id="checkout-items-list">
                                <!-- Populated by JavaScript -->
                            </div>

                            <!-- Financial Summary -->
                            <div class="d-flex flex-column gap-sm-5 mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between">
                                    <span
                                        class="body-2">{{ app()->getLocale() == 'ar' ? 'المجموع الفرعي:' : 'Subtotal:' }}</span>
                                    <div class="d-flex align-items-center gap-sm-6">
                                        <p class="body-2 mb-0" id="checkout-subtotal">0</p>
                                        <span class="body-2">{{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between" id="discount-row"
                                    style="display: none !important;">
                                    <span class="body-2">{{ app()->getLocale() == 'ar' ? 'الخصم:' : 'Discount:' }}</span>
                                    <div class="d-flex align-items-center gap-sm-6">
                                        <p class="body-2 text-success mb-0" id="checkout-discount">0</p>
                                        <span class="body-2">{{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span
                                        class="body-2">{{ app()->getLocale() == 'ar' ? 'الضريبة (15%):' : 'Tax (15%):' }}</span>
                                    <div class="d-flex align-items-center gap-sm-6">
                                        <p class="body-2 mb-0" id="checkout-tax">0</p>
                                        <span class="body-2">{{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Grand Total -->
                            <div class="total-row d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <span
                                    class="sub-heading-4 mb-0">{{ app()->getLocale() == 'ar' ? 'المجموع النهائي:' : 'Grand Total:' }}</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="sub-heading-2 mb-0" id="checkout-total">0</p>
                                    <span class="sub-heading-4">{{ app()->getLocale() == 'ar' ? 'ريال' : 'SAR' }}</span>
                                </div>
                            </div>

                            <!-- Confirm Order Button -->
                            <div class="checkout-section d-flex flex-column gap-sm-4">
                                <button type="submit" class="btn btn-custom-primary w-100">
                                    <i class="fas fa-check-circle"></i>
                                    {{ app()->getLocale() == 'ar' ? 'تأكيد الطلب' : 'Confirm Order' }}
                                </button>
                                <a href="/cart" class="btn btn-custom-outline w-100">
                                    <i class="fas fa-arrow-right"></i>
                                    {{ app()->getLocale() == 'ar' ? 'العودة للسلة' : 'Back to Cart' }}
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
        const locale = '{{ app()->getLocale() }}';
        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
        const couponData = JSON.parse(sessionStorage.getItem('appliedCoupon')) || null;

        if (cart.length === 0) {
            window.location.href = '/cart';
            return;
        }

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

        let itemsHtml = '';
        cart.forEach(item => {
            itemsHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        <div>
                            <p class="mb-0 body-4">${item.name}</p>
                            <small class="text-muted">${locale == 'ar' ? 'الكمية:' : 'Quantity:'} ${item.quantity}</small>
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

        document.getElementById('cart-data-input').value = JSON.stringify(cart);

        // ✅ معالج الإرسال المعدل
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

            if (!isLoggedIn) {
                e.preventDefault();

                // إنشاء نموذج جديد لحفظ البيانات
                const saveForm = document.createElement('form');
                saveForm.method = 'POST';
                saveForm.action = '{{ route("cart.saveCheckoutData") }}';

                // إضافة CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                saveForm.appendChild(csrfInput);

                // نسخ جميع حقول النموذج الأصلي
                const formData = new FormData(this);
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    saveForm.appendChild(input);
                }

                // إضافة بيانات السلة
                const cartInput = document.createElement('input');
                cartInput.type = 'hidden';
                cartInput.name = 'cart_data';
                cartInput.value = document.getElementById('cart-data-input').value;
                saveForm.appendChild(cartInput);

                const couponInput = document.createElement('input');
                couponInput.type = 'hidden';
                couponInput.name = 'coupon_code';
                couponInput.value = document.getElementById('coupon-code-input').value;
                saveForm.appendChild(couponInput);

                const discountInput = document.createElement('input');
                discountInput.type = 'hidden';
                discountInput.name = 'discount_amount';
                discountInput.value = document.getElementById('discount-amount-input').value;
                saveForm.appendChild(discountInput);

                // إضافة النموذج للصفحة وإرساله
                document.body.appendChild(saveForm);
                saveForm.submit();

                return;
            }

            localStorage.removeItem('shoppingCart');
            sessionStorage.removeItem('appliedCoupon');
        });

        function formatPrice(price) {
            return new Intl.NumberFormat('en-US', {minimumFractionDigits: 0}).format(price);
        }
    });
    </script>
    @endpush
@endsection
