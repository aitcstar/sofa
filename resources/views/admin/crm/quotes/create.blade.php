@extends('admin.layouts.app')

@section('title', 'إنشاء عرض سعر جديد')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">إنشاء عرض سعر جديد</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.crm.quotes.index') }}">عروض الأسعار</a></li>
                        <li class="breadcrumb-item active">إنشء جديد</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i>رجوع
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>معلومات عرض السعر</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.crm.quotes.store') }}" method="POST" id="quoteForm">
                    @csrf

                    <!-- معلومات العميل -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">معلومات العميل</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lead_id" class="form-label">العميل المحتمل</label>
                                <select class="form-select @error('lead_id') is-invalid @enderror" id="lead_id"
                                    name="lead_id">
                                    <option value="">اختر عميل محتمل</option>
                                    @if (isset($leads))
                                        @foreach ($leads as $lead)
                                            <option value="{{ $lead->id }}" data-name="{{ $lead->name }}"
                                                data-email="{{ $lead->email }}" data-phone="{{ $lead->phone }}"
                                                data-company="{{ $lead->company }}"
                                                {{ old('lead_id', request('lead_id')) == $lead->id ? 'selected' : '' }}>
                                                {{ $lead->name }} - {{ $lead->email }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('lead_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">اسم العميل <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                    id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                    id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                    id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="customer_company" class="form-label">الشركة</label>
                                <input type="text" class="form-control @error('customer_company') is-invalid @enderror"
                                    id="customer_company" name="customer_company" value="{{ old('customer_company') }}">
                                @error('customer_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- معلومات العرض -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">معلومات العرض</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="issue_date" class="form-label">تاريخ الإصدار <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                                    id="issue_date" name="issue_date"
                                    value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valid_until" class="form-label">صالح حتى <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                                    id="valid_until" name="valid_until"
                                    value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}" required>
                                @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- عناصر العرض (الباكجات) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">عناصر العرض (الباكجات)</h5>
                            <button type="button" class="btn btn-success btn-sm mb-3" id="addItemBtn">
                                <i class="fas fa-plus me-2"></i>إضافة باكج
                            </button>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 30%;">الباكج</th>
                                            <th style="width: 20%;">الوصف</th>
                                            <th style="width: 15%;">الكمية</th>
                                            <th style="width: 15%;">السعر</th>
                                            <th style="width: 15%;">الإجمالي</th>
                                            <th style="width: 5%;">حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        <!-- سيتم إضافة الصفوف هنا ديناميكياً -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- الإجماليات -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="terms_conditions" class="form-label">الشروط والأحكام</label>
                                <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3">{{ old('terms_conditions') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="discount_amount" class="form-label">الخصم (ريال)</label>
                                        <input type="number" class="form-control" id="discount_amount"
                                            name="discount_amount" value="{{ old('discount_amount', 0) }}"
                                            step="0.01" min="0">
                                    </div>

                                    <div class="mb-3">
                                        <label for="tax_rate" class="form-label">نسبة الضريبة (%)</label>
                                        <input type="number" class="form-control" id="tax_rate" name="tax_rate"
                                            value="{{ old('tax_rate', 15) }}" step="0.01" min="0" required>
                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>المجموع الفرعي:</strong>
                                        <span id="subtotalDisplay">0.00 ريال</span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>الخصم:</strong>
                                        <span id="discountDisplay">0.00 ريال</span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>الضريبة:</strong>
                                        <span id="taxDisplay">0.00 ريال</span>
                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between">
                                        <strong class="text-primary">الإجمالي:</strong>
                                        <strong class="text-primary" id="totalDisplay">0.00 ريال</strong>
                                    </div>

                                    <input type="hidden" name="subtotal" id="subtotal" value="0">
                                    <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ كمسودة
                            </button>
                            <!--<button type="submit" name="action" value="send" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>حفظ وإرسال
                            </button>-->
                            <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log("✅ السكريبت يعمل — بدء التحميل...");

            const packages = @js($packages ?? []);
            let itemCounter = 1000;

            // Auto-fill customer data
            document.getElementById('lead_id')?.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt.value) {
                    document.getElementById('customer_name').value = opt.dataset.name || '';
                    document.getElementById('customer_email').value = opt.dataset.email || '';
                    document.getElementById('customer_phone').value = opt.dataset.phone || '';
                    document.getElementById('customer_company').value = opt.dataset.company || '';
                }
            });

            // Add new package row
            document.getElementById('addItemBtn').addEventListener('click', function () {
                addItemRow();
            });

            function addItemRow(data = null) {
                const rowId = itemCounter++;

                const row = document.createElement('tr');
                row.id = `item-row-${rowId}`;
                row.className = 'package-row';

                let packageOptions = '<option value="">اختر باكج</option>';
                packages.forEach(pkg => {
                    const selected = data && data.package_id == pkg.id ? 'selected' : '';
                    const itemsJson = JSON.stringify(pkg.items).replace(/'/g, "\\'").replace(/"/g, '&quot;');
                    packageOptions += `<option value="${pkg.id}" data-items='${itemsJson}' data-package-name="${pkg.name_ar}" ${selected}>${pkg.name_ar}</option>`;
                });

                row.innerHTML = `
                    <td colspan="6">
                        <div class="d-flex align-items-center mb-2">
                            <select class="form-select package-select me-2" data-row="${rowId}">
                                ${packageOptions}
                            </select>
                            <button type="button" class="btn btn-danger btn-sm remove-package" data-row="${rowId}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="package-items-container" id="items-container-${rowId}">
                            <!-- سيتم ملء القطع هنا -->
                        </div>
                    </td>
                `;

                document.getElementById('itemsTableBody').appendChild(row);

                // Event: عند تغيير الباكج
                const select = row.querySelector('.package-select');
                select.addEventListener('change', function () {
                    const option = this.options[this.selectedIndex];
                    const pkgId = option.value;
                    const pkgName = option.dataset.packageName || '';
                    const items = pkgId ? JSON.parse(option.dataset.items.replace(/&quot;/g, '"')) : [];

                    // ✅ أضف حقول package_id و package_name كـ hidden inputs
                    const container = row.querySelector('.package-items-container');
                    container.innerHTML = '';

                    if (pkgId) {
                        const hiddenId = document.createElement('input');
                        hiddenId.type = 'hidden';
                        hiddenId.name = `packages[${rowId}][package_id]`;
                        hiddenId.value = pkgId;
                        container.before(hiddenId);

                        const hiddenName = document.createElement('input');
                        hiddenName.type = 'hidden';
                        hiddenName.name = `packages[${rowId}][package_name]`;
                        hiddenName.value = pkgName;
                        container.before(hiddenName);
                    }

                    renderPackageItems(rowId, items, data?.items || null);
                    calculateTotals();
                });

                // Load existing data if editing
                if (data && data.package_id) {
                    select.value = data.package_id;
                    select.dispatchEvent(new Event('change'));
                }
            }

            function renderPackageItems(rowId, items, savedItems = null) {
                const container = document.getElementById(`items-container-${rowId}`);
                if (!container) return;

                if (items.length === 0) {
                    container.innerHTML = '<p class="text-muted">لا توجد قطع لهذا الباكج</p>';
                    return;
                }

                let tableHTML = '<table class="table table-sm w-100"><thead><tr><th>القطعة</th><th>الوصف</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr></thead><tbody>';

                items.forEach((item, index) => {
                    const saved = savedItems?.find(i => i.name === item.name) || {};
                    const qty = saved.quantity || item.default_quantity || 1;
                    const price = saved.price || item.default_price || 0;
                    const total = qty * price;

                    tableHTML += `
                        <tr>
                            <td>${item.name}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm description-input"
                                       name="packages[${rowId}][items][${index}][description]"
                                       value="${saved.description || item.description || ''}">
                                <input type="hidden" name="packages[${rowId}][items][${index}][name]" value="${item.name}">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm quantity-input"
                                       name="packages[${rowId}][items][${index}][quantity]"
                                       value="${qty}" min="1" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm price-input"
                                       name="packages[${rowId}][items][${index}][unit_price]"
                                       value="${price}" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm total-input" readonly value="${total.toFixed(2)}">
                            </td>
                        </tr>
                    `;
                });

                tableHTML += '</tbody></table>';
                container.innerHTML = tableHTML;

                calculateTotals();
            }

            // ✅ Event Delegation for dynamic calculation
            document.addEventListener('input', function(e) {
                if (e.target.matches('.quantity-input, .price-input')) {
                    const row = e.target.closest('tr');
                    if (!row) return;
                    const qty = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
                    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                    const totalInput = row.querySelector('.total-input');
                    if (totalInput) {
                        totalInput.value = (qty * price).toFixed(2);
                        calculateTotals();
                    }
                }
            });

            function removePackageRow(rowId) {
                const row = document.getElementById(`item-row-${rowId}`);
                if (row) row.remove();
                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.total-input').forEach(input => {
                    subtotal += parseFloat(input.value) || 0;
                });

                const discount = parseFloat(document.getElementById('discount_amount')?.value) || 0;
                const taxRate = parseFloat(document.getElementById('tax_rate')?.value) || 0;
                const afterDiscount = subtotal - discount;
                const taxAmount = afterDiscount * (taxRate / 100);
                const total = afterDiscount + taxAmount;

                document.getElementById('subtotal').value = subtotal.toFixed(2);
                document.getElementById('tax_amount').value = taxAmount.toFixed(2);
                document.getElementById('total_amount').value = total.toFixed(2);

                document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2) + ' ريال';
                document.getElementById('discountDisplay').textContent = discount.toFixed(2) + ' ريال';
                document.getElementById('taxDisplay').textContent = taxAmount.toFixed(2) + ' ريال';
                document.getElementById('totalDisplay').textContent = total.toFixed(2) + ' ريال';
            }

            document.getElementById('discount_amount')?.addEventListener('input', calculateTotals);
            document.getElementById('tax_rate')?.addEventListener('input', calculateTotals);

            // Delete package
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-package')) {
                    const btn = e.target.closest('.remove-package');
                    const rowId = btn.dataset.row;
                    removePackageRow(rowId);
                }
            });

            // Add first row
            addItemRow();

            // Form validation
            document.getElementById('quoteForm').addEventListener('submit', function(e) {
                let hasItems = false;
                document.querySelectorAll('.total-input').forEach(input => {
                    if (parseFloat(input.value) > 0) hasItems = true;
                });
                if (!hasItems) {
                    e.preventDefault();
                    alert('يجب إضافة باكج يحتوي على قطع');
                    return false;
                }
            });
        });
    </script>
    @endpush
@endsection
