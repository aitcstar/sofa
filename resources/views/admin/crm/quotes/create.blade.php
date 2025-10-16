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
                    <li class="breadcrumb-item active">إنشاء جديد</li>
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
                            <select class="form-select @error('lead_id') is-invalid @enderror" 
                                    id="lead_id" name="lead_id">
                                <option value="">اختر عميل محتمل</option>
                                @if(isset($leads))
                                    @foreach($leads as $lead)
                                        <option value="{{ $lead->id }}" 
                                                data-name="{{ $lead->name }}"
                                                data-email="{{ $lead->email }}"
                                                data-phone="{{ $lead->phone }}"
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
                            <label for="customer_name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
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
                            <label for="issue_date" class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                   id="issue_date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="valid_until" class="form-label">صالح حتى <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}" required>
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
                                    <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                           value="{{ old('discount_amount', 0) }}" step="0.01" min="0">
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
                        <button type="submit" name="action" value="send" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>حفظ وإرسال
                        </button>
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
    // Packages data
    const packages = @json($packages ?? []);
    let itemCounter = 0;

    // Auto-fill customer data when lead is selected
    document.getElementById('lead_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('customer_name').value = selectedOption.dataset.name || '';
            document.getElementById('customer_email').value = selectedOption.dataset.email || '';
            document.getElementById('customer_phone').value = selectedOption.dataset.phone || '';
            document.getElementById('customer_company').value = selectedOption.dataset.company || '';
        }
    });

    // Add item row
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItemRow();
    });

    function addItemRow(data = null) {
        itemCounter++;
        const row = document.createElement('tr');
        row.id = `item-row-${itemCounter}`;
        
        let packageOptions = '<option value="">اختر باكج</option>';
        packages.forEach(pkg => {
            const selected = data && data.package_id == pkg.id ? 'selected' : '';
            packageOptions += `<option value="${pkg.id}" data-price="${pkg.price}" ${selected}>${pkg.name_ar}</option>`;
        });

        row.innerHTML = `
            <td>
                <select class="form-select package-select" name="items[${itemCounter}][package_id]" data-row="${itemCounter}">
                    ${packageOptions}
                </select>
                <input type="hidden" name="items[${itemCounter}][item_name]" class="item-name" value="${data?.item_name || ''}">
            </td>
            <td>
                <input type="text" class="form-control" name="items[${itemCounter}][description]" 
                       placeholder="وصف الباكج" value="${data?.description || ''}">
            </td>
            <td>
                <input type="number" class="form-control quantity-input" name="items[${itemCounter}][quantity]" 
                       value="${data?.quantity || 1}" min="1" data-row="${itemCounter}" required>
            </td>
            <td>
                <input type="number" class="form-control price-input" name="items[${itemCounter}][unit_price]" 
                       value="${data?.unit_price || 0}" step="0.01" min="0" data-row="${itemCounter}" required>
            </td>
            <td>
                <input type="text" class="form-control total-input" readonly value="0.00">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item" data-row="${itemCounter}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        document.getElementById('itemsTableBody').appendChild(row);

        // Add event listeners
        row.querySelector('.package-select').addEventListener('change', function() {
            updatePackageData(this.dataset.row);
        });
        
        row.querySelector('.quantity-input').addEventListener('input', function() {
            updateRowTotal(this.dataset.row);
        });
        
        row.querySelector('.price-input').addEventListener('input', function() {
            updateRowTotal(this.dataset.row);
        });
        
        row.querySelector('.remove-item').addEventListener('click', function() {
            removeItemRow(this.dataset.row);
        });

        // Update if data provided
        if (data) {
            updateRowTotal(itemCounter);
        }
    }

    function updatePackageData(rowId) {
        const row = document.getElementById(`item-row-${rowId}`);
        const packageSelect = row.querySelector('.package-select');
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        
        if (selectedOption.value) {
            const packageName = selectedOption.text;
            const packagePrice = selectedOption.dataset.price || 0;
            
            row.querySelector('.item-name').value = packageName;
            row.querySelector('.price-input').value = packagePrice;
            
            updateRowTotal(rowId);
        }
    }

    function updateRowTotal(rowId) {
        const row = document.getElementById(`item-row-${rowId}`);
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = quantity * price;
        
        row.querySelector('.total-input').value = total.toFixed(2);
        
        calculateTotals();
    }

    function removeItemRow(rowId) {
        const row = document.getElementById(`item-row-${rowId}`);
        row.remove();
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('.total-input').forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });
        
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        
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

    // Event listeners for discount and tax
    document.getElementById('discount_amount').addEventListener('input', calculateTotals);
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);

    // Add initial row
    addItemRow();

    // Form validation
    document.getElementById('quoteForm').addEventListener('submit', function(e) {
        const itemsCount = document.querySelectorAll('#itemsTableBody tr').length;
        if (itemsCount === 0) {
            e.preventDefault();
            alert('يجب إضافة باكج واحد على الأقل');
            return false;
        }
    });
</script>
@endpush
@endsection

