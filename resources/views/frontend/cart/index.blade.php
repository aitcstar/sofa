@extends('frontend.layouts.pages')

@section('content')
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">السلة</a>
</div>

<!-- ===== CART MAIN SECTION ===== -->
<section class="cart-section">
    <div class="container">
        <div class="cart-content">
            <div class="row" id="cart-items-container">
                <!-- Cart Items Column -->
                <div class="col-lg-8 d-flex flex-column gap-sm-3 mb-3">
                    <div class="d-flex flex-column gap-sm-4 mb-4" id="cart-items-list">
                        <!-- Items will be inserted here by JavaScript -->
                    </div>

                    <!-- Continue Shopping -->
                    <div class="continue-shopping">
                        <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="btn btn-custom-outline">
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
                                    <p class="body-2 mb-0" id="subtotal-amount">0</p>
                                    <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="body-2">الخصم:</span>
                                <div class="d-flex align-items-center gap-sm-6">
                                    <p class="body-2 text-success mb-0" id="discount-amount">0</p>
                                    <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
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
                                <input type="text" class="form-control" style="flex: 1;" placeholder="أدخل كود الخصم" id="promo-code-input" />
                                <button class="btn btn-custom-secondary" style="min-width: fit-content;" id="apply-promo-btn">تطبيق</button>
                            </div>
                        </div>

                        <!-- Total Row -->
                        <div class="total-row d-flex justify-content-between align-items-center mt-4">
                            <span class="sub-heading-4 mb-0">المجموع النهائي:</span>
                            <div class="d-flex align-items-center gap-sm-6">
                                <p class="sub-heading-2 mb-0" id="total-amount">0</p>
                                <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <div class="checkout-section d-flex flex-column gap-sm-4">
                            <button class="btn btn-custom-primary w-100" onclick="alert('سيتم توجيهك لصفحة الدفع قريباً')">
                                <i class="fas fa-credit-card"></i>
                                إتمام الطلب
                            </button>
                            <button class="btn btn-custom-outline w-100" onclick="alert('سيتم فتح واتساب قريباً')">
                                <i class="fab fa-whatsapp"></i>
                                طلب عبر واتساب
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart State (Hidden by default) -->
            <div class="empty-cart-state" id="empty-cart-state" style="display: none;">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart empty-cart-icon mb-3"></i>
                    <h3 class="sub-heading-3 mb-3">العربة فارغة</h3>
                    <p class="body-2 text-caption mb-4">لم تقم بإضافة أي منتجات إلى العربة بعد</p>
                    <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="btn btn-custom-primary">
                        تصفح المنتجات
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== CART PAGE STYLES ===== */

/* ===== CART ITEMS ===== */
.cart-item {
    display: flex;
    gap: var(--gap-sm-4);
    padding: var(--spacing-sm);
    border: 1px solid var(--surface-border);
    border-radius: var(--radius-small-box);
}

.cart-item-image {
    width: 140px;
    height: 120px;
    overflow: hidden;
    flex-shrink: 0;
    border-radius: var(--radius-small-box);
}

.cart-item-image img {
    object-fit: cover;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-md);
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 1px solid var(--surface-border);
    border-radius: var(--radius-small-box);
    overflow: hidden;
    padding: 0;
    max-height: 35px;
}

.quantity-btn {
    border: none;
    background-color: var(surface-border);
    padding: var(--spacing-sm) var(--spacing-md);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 40px;
}

.quantity-input {
    border: none;
    text-align: center;
    width: 60px;
    padding: 0 var(--spacing-sm);
    background: var(--surface-white);
    height: 40px;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input:focus {
    outline: none;
    box-shadow: none;
}

.remove-item-btn {
    border: none;
    background: none;
    color: var(--danger);
    display: flex;
    cursor: pointer;
    align-items: center;
    gap: var(--gap-sm-7);
}

.remove-item-btn:hover {
    background-color: rgba(var(--danger-rgb), 0.1);
}

/* ===== ORDER SUMMARY ===== */
.order-summary {
    width: 400px;
    height: fit-content;
    position: sticky;
    top: 90px;
    overflow: hidden;
    border: 1px solid var(--surface-border);
    border-radius: var(--radius-small-box);
    padding: var(--padding-box-small);
    display: flex;
    flex-direction: column;
    background-color: var(--surface-gray-box);
}

.total-row {
    height: 70px;
    border-top: 1px solid var(--surface-border);
    padding: var(--spacing-sm) var(--spacing-md);
}

/* ===== CHECKOUT SECTION ===== */
.checkout-section {
    border-top: 1px solid var(--surface-border);
    ;
    padding-top: var(--spacing-md);
}

/* ===== EMPTY CART STATE ===== */
.empty-cart-state {
    text-align: center;
    padding: var(--spacing-5xl) var(--spacing-md);
}

.empty-cart-icon {
    font-size: 4rem;
    color: var(--text-caption);
    opacity: 0.5;
}

/* ===== MOBILE RESPONSIVE STYLES ===== */
@media (max-width: 767.98px) {
    .cart-section {
        padding: var(--spacing-md) 0;
    }

    .cart-item {
        padding: var(--spacing-sm);
    }

    .cart-item-image {
        height: 150px;
    }

    .checkout-section .btn {
        margin-bottom: var(--spacing-sm) !important;
    }
}
    </style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
    const cartItemsList = document.getElementById('cart-items-list');
    const cartItemsContainer = document.getElementById('cart-items-container');
    const emptyCartState = document.getElementById('empty-cart-state');

    if (cart.length === 0) {
        cartItemsContainer.style.display = 'none';
        emptyCartState.style.display = 'block';
        return;
    }

    let subtotal = 0;
    let html = '';

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        html += `
            <div class="cart-item" data-index="${index}">
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}" class="w-100 h-100" />
                </div>
                <div class="d-flex flex-column gap-sm-5 flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <h3 class="sub-heading-3 mb-0">${item.name}</h3>
                        <div class="d-flex align-items-center gap-sm-6">
                            <p class="sub-heading-2 mb-0">${formatPrice(itemTotal)}</p>
                            <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                        </div>
                    </div>
                    <div class="mb-2" style="flex: 1;">
                        ${item.description ? `<p class="body-4">${item.description}</p>` : ''}
                        ${item.pieces ? `<p class="body-4">عدد القطع: ${item.pieces}</p>` : ''}
                    </div>
                    <div class="cart-item-actions">
                        <div class="quantity-controls">
                            <button class="quantity-btn quantity-decrease" data-index="${index}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-index="${index}" readonly />
                            <button class="quantity-btn quantity-increase" data-index="${index}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <button class="remove-item-btn" data-index="${index}">
                            <i class="fas fa-trash-alt"></i>
                            <span>إزالة</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    cartItemsList.innerHTML = html;

    // Update summary
    document.getElementById('subtotal-amount').textContent = formatPrice(subtotal);
    document.getElementById('discount-amount').textContent = '0';
    document.getElementById('total-amount').textContent = formatPrice(subtotal);

    // Event listeners
    document.querySelectorAll('.quantity-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            cart[index].quantity += 1;
            updateCart();
        });
    });

    document.querySelectorAll('.quantity-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1;
                updateCart();
            }
        });
    });

    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            if (confirm('هل تريد حذف هذا العنصر من السلة؟')) {
                cart.splice(index, 1);
                updateCart();
                location.reload();
            }
        });
    });

    function updateCart() {
        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        location.reload();
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 0}).format(price);
    }
});
</script>
@endpush
@endsection
