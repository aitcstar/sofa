@extends('admin.layouts.app')

@section('title', 'إضافة طلب جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">إضافة طلب جديد</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.enhanced.index') }}">الطلبات</a></li>
                    <li class="breadcrumb-item active">إضافة طلب</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.orders.enhanced.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <form action="{{ route('admin.orders.enhanced.store') }}" method="POST" enctype="multipart/form-data" id="orderForm">
        @csrf

        <!-- Progress Steps -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="steps-progress">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">بيانات العميل</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">تفاصيل المشروع</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-title">اختيار الباكج</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-title">المعلومات المالية</div>
                    </div>
                    <div class="step" data-step="5">
                        <div class="step-number">5</div>
                        <div class="step-title">التأكيد</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Customer Information -->
        <div class="step-content" id="step-1">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>بيانات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="country_code" class="form-select" style="max-width: 120px;">
                                    <option value="+966" selected>+966</option>
                                    <option value="+971">+971</option>
                                    <option value="+965">+965</option>
                                    <option value="+973">+973</option>
                                    <option value="+974">+974</option>
                                    <option value="+968">+968</option>
                                </select>
                                <input type="tel" name="phone" class="form-control" required value="{{ old('phone') }}">
                            </div>
                            @error('phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع العميل <span class="text-danger">*</span></label>
                            <select name="client_type" class="form-select" id="clientType" required>
                                <option value="individual" {{ old('client_type') === 'individual' ? 'selected' : '' }}>فرد</option>
                                <option value="commercial" {{ old('client_type') === 'commercial' ? 'selected' : '' }}>شركة</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3" id="commercialRegisterField" style="display: none;">
                            <label class="form-label">رقم السجل التجاري</label>
                            <input type="text" name="commercial_register" class="form-control" value="{{ old('commercial_register') }}">
                        </div>

                        <div class="col-md-6 mb-3" id="taxNumberField" style="display: none;">
                            <label class="form-label">الرقم الضريبي</label>
                            <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Project Details -->
        <div class="step-content" id="step-2" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>تفاصيل المشروع</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عدد الوحدات <span class="text-danger">*</span></label>
                            <input type="number" name="units_count" class="form-control" min="1" required value="{{ old('units_count', 1) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع المشروع <span class="text-danger">*</span></label>
                            <select name="project_type" class="form-select" required>
                                <option value="">اختر نوع المشروع</option>
                                <option value="large" {{ old('project_type') === 'large' ? 'selected' : '' }}>مشروع كبير</option>
                                <option value="medium" {{ old('project_type') === 'medium' ? 'selected' : '' }}>مشروع متوسط</option>
                                <option value="small" {{ old('project_type') === 'small' ? 'selected' : '' }}>مشروع صغير</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">المرحلة الحالية <span class="text-danger">*</span></label>
                            <select name="current_stage" class="form-select" required>
                                <option value="">اختر المرحلة</option>//design', 'execution', 'operation
                                <option value="design" {{ old('current_stage') === 'design' ? 'selected' : '' }}>مرحلة التصميم</option>
                                <option value="execution" {{ old('current_stage') === 'execution' ? 'selected' : '' }}>مرحلة التنفيذ</option>
                                <option value="operation" {{ old('current_stage') === 'operation' ? 'selected' : '' }}>مرحلة التشغيل</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">الأولوية</label>
                            <select name="priority" class="form-select">
                                <option value="1" {{ old('priority') === 'normal' ? 'selected' : '' }}>عادية</option>
                                <option value="2" {{ old('priority') === 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="3" {{ old('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">الخدمات المطلوبة</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="has_interior_design" class="form-check-input" id="interiorDesign" {{ old('has_interior_design') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="interiorDesign">
                                            تصميم داخلي
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="needs_finishing_help" class="form-check-input" id="finishingHelp" {{ old('needs_finishing_help') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="finishingHelp">
                                            مساعدة في التشطيب
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="needs_color_help" class="form-check-input" id="colorHelp" {{ old('needs_color_help') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="colorHelp">
                                            مساعدة في اختيار الألوان
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">المخططات (إن وجدت)</label>
                            <input type="file" name="diagrams" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">الصيغ المدعومة: PDF, JPG, PNG</small>
                        </div>

                        <!--<div class="col-md-12 mb-3">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea name="internal_notes" class="form-control" rows="3">{{ old('internal_notes') }}</textarea>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Package Selection -->
        <div class="step-content" id="step-3" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>اختيار الباكج</h5>
                </div>
                <div class="card-body">
                    <div class="row" id="packagesContainer">
                        @foreach($packages as $package)
                        <div class="col-md-4 mb-3">
                            <div class="card package-card h-100" data-package-id="{{ $package->id }}" data-package-price="{{ $package->price }}">
                                <div class="card-body text-center">
                                    @if($package->image)
                                    <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name }}" class="img-fluid mb-3" style="max-height: 150px;">
                                    @endif
                                    <h5 class="card-title">{{ $package->name }}</h5>
                                    <p class="text-muted">{{ $package->description }}</p>
                                    <h3 class="text-success">{{ number_format($package->price, 2) }} ريال</h3>
                                    <button type="button" class="btn btn-outline-primary select-package-btn">
                                        <i class="fas fa-check me-2"></i>اختيار
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="package_id" id="selectedPackageId" required>
                    <div id="packageError" class="text-danger mt-2" style="display: none;">الرجاء اختيار الباكج</div>
                </div>
            </div>
        </div>

        <!-- Step 4: Financial Information -->
        <div class="step-content" id="step-4" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>المعلومات المالية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المبلغ الأساسي</label>
                            <input type="number" name="base_amount" id="baseAmount" class="form-control" step="0.01" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">الضريبة (15%)</label>
                            <input type="number" name="tax_amount" id="taxAmount" class="form-control" step="0.01" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">الإجمالي</label>
                            <input type="number" name="total_amount" id="totalAmount" class="form-control" step="0.01" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">حالة الدفع</label>
                            <select name="payment_status" class="form-select" id="paymentStatus">
                                <option value="unpaid">غير مدفوعه</option>
                                <option value="partial">دفعة أولى</option>
                                <option value="paid">مدفوع بالكامل</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3" id="paidAmountField" style="display: none;">
                            <label class="form-label">المبلغ المدفوع</label>
                            <input type="number" name="paid_amount" id="paidAmount" class="form-control" step="0.01" min="0">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ التسليم المتوقع</label>
                            <input type="date" name="expected_delivery_date" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">جدول الدفعات</label>
                            <div id="paymentSchedule">
                                <div class="payment-schedule-item mb-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="number" name="payment_schedule[0][amount]" class="form-control" placeholder="المبلغ" step="0.01">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="date" name="payment_schedule[0][due_date]" class="form-control" placeholder="تاريخ الاستحقاق">
                                        </div>
                                        <div class="col-md-3">
                                            <select name="payment_schedule[0][status]" class="form-select">
                                                <option value="unpaid">غير مدفوعه</option>
                                                <option value="paid">مدفوع</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removePaymentSchedule(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-success mt-2" onclick="addPaymentSchedule()">
                                <i class="fas fa-plus me-1"></i>إضافة دفعة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Confirmation -->
        <div class="step-content" id="step-5" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>مراجعة وتأكيد الطلب</h5>
                </div>
                <div class="card-body">
                    <div id="orderSummary">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-right me-2"></i>السابق
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                        التالي<i class="fas fa-arrow-left ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="fas fa-check me-2"></i>تأكيد الطلب
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .steps-progress {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .steps-progress::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #dee2e6;
        z-index: 0;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #dee2e6;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 8px;
        transition: all 0.3s;
    }

    .step.active .step-number {
        background-color: #0d6efd;
        color: white;
    }

    .step.completed .step-number {
        background-color: #198754;
        color: white;
    }

    .step-title {
        font-size: 14px;
        color: #6c757d;
        text-align: center;
    }

    .step.active .step-title {
        color: #0d6efd;
        font-weight: bold;
    }

    .package-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .package-card.selected {
        border-color: #0d6efd;
        background-color: #f0f8ff;
    }

    .package-card.selected .select-package-btn {
        background-color: #0d6efd;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 5;
    let paymentScheduleCounter = 1;

    // Client Type Toggle
    document.getElementById('clientType').addEventListener('change', function() {
        const commercialFields = document.querySelectorAll('#commercialRegisterField, #taxNumberField');
        if (this.value === 'commercial') {
            commercialFields.forEach(field => field.style.display = 'block');
        } else {
            commercialFields.forEach(field => field.style.display = 'none');
        }
    });

    // Payment Status Toggle
    document.getElementById('paymentStatus').addEventListener('change', function() {
        const paidAmountField = document.getElementById('paidAmountField');
        if (this.value === 'partial' || this.value === 'paid') {
            paidAmountField.style.display = 'block';
        } else {
            paidAmountField.style.display = 'none';
        }
    });

    // Package Selection
    document.querySelectorAll('.package-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove selection from all cards
            document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));

            // Add selection to clicked card
            this.classList.add('selected');

            // Set package ID
            const packageId = this.dataset.packageId;
            const packagePrice = parseFloat(this.dataset.packagePrice);
            document.getElementById('selectedPackageId').value = packageId;

            // Calculate financial amounts
            calculateFinancials(packagePrice);

            // Hide error
            document.getElementById('packageError').style.display = 'none';
        });
    });

    function calculateFinancials(baseAmount) {
        const taxRate = 0.15;
        const taxAmount = baseAmount * taxRate;
        const totalAmount = baseAmount + taxAmount;

        document.getElementById('baseAmount').value = baseAmount.toFixed(2);
        document.getElementById('taxAmount').value = taxAmount.toFixed(2);
        document.getElementById('totalAmount').value = totalAmount.toFixed(2);
    }

    function changeStep(direction) {
        // Validate current step
        if (direction === 1 && !validateStep(currentStep)) {
            return;
        }

        // Hide current step
        document.getElementById(`step-${currentStep}`).style.display = 'none';
        document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
        if (direction === 1) {
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('completed');
        }

        // Update current step
        currentStep += direction;

        // Show new step
        document.getElementById(`step-${currentStep}`).style.display = 'block';
        document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');

        // Update buttons
        updateButtons();

        // If last step, show summary
        if (currentStep === totalSteps) {
            showOrderSummary();
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(step) {
        let isValid = true;

        if (step === 1) {
            // Validate customer information
            const requiredFields = ['name', 'phone', 'email'];
            requiredFields.forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
        } else if (step === 2) {
            // Validate project details
            const requiredFields = ['units_count', 'project_type', 'current_stage'];
            requiredFields.forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input.value) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
        } else if (step === 3) {
            // Validate package selection
            const packageId = document.getElementById('selectedPackageId').value;
            if (!packageId) {
                document.getElementById('packageError').style.display = 'block';
                isValid = false;
            }
        }

        return isValid;
    }

    function updateButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
        nextBtn.style.display = currentStep === totalSteps ? 'none' : 'block';
        submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
    }

    function showOrderSummary() {
        const summary = document.getElementById('orderSummary');
        const formData = new FormData(document.getElementById('orderForm'));

        let html = '<div class="row">';

        // Customer Info
        html += '<div class="col-md-6"><div class="card mb-3"><div class="card-header bg-primary text-white">بيانات العميل</div><div class="card-body">';
        html += `<p><strong>الاسم:</strong> ${formData.get('name')}</p>`;
        html += `<p><strong>الهاتف:</strong> ${formData.get('phone')} ${formData.get('country_code')}</p>`;
        html += `<p><strong>البريد:</strong> ${formData.get('email')}</p>`;
        html += `<p><strong>نوع العميل:</strong> ${formData.get('client_type') === 'individual' ? 'فرد' : 'شركة'}</p>`;
        html += '</div></div></div>';

        // Project Info
        html += '<div class="col-md-6"><div class="card mb-3"><div class="card-header bg-info text-white">تفاصيل المشروع</div><div class="card-body">';
        html += `<p><strong>عدد الوحدات:</strong> ${formData.get('units_count')}</p>`;
        html += `<p><strong>نوع المشروع:</strong> ${formData.get('project_type')}</p>`;
        html += `<p><strong>المرحلة:</strong> ${formData.get('current_stage')}</p>`;
        html += '</div></div></div>';

        // Financial Info
        html += '<div class="col-md-12"><div class="card mb-3"><div class="card-header bg-success text-white">المعلومات المالية</div><div class="card-body">';
        html += `<p><strong>المبلغ الأساسي:</strong> ${parseFloat(formData.get('base_amount')).toFixed(2)} ريال</p>`;
        html += `<p><strong>الضريبة:</strong> ${parseFloat(formData.get('tax_amount')).toFixed(2)} ريال</p>`;
        html += `<p><strong>الإجمالي:</strong> ${parseFloat(formData.get('total_amount')).toFixed(2)} ريال</p>`;
        html += '</div></div></div>';

        html += '</div>';

        summary.innerHTML = html;
    }

    function addPaymentSchedule() {
        const container = document.getElementById('paymentSchedule');
        const newItem = `
            <div class="payment-schedule-item mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <input type="number" name="payment_schedule[${paymentScheduleCounter}][amount]" class="form-control" placeholder="المبلغ" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="payment_schedule[${paymentScheduleCounter}][due_date]" class="form-control" placeholder="تاريخ الاستحقاق">
                    </div>
                    <div class="col-md-3">
                        <select name="payment_schedule[${paymentScheduleCounter}][status]" class="form-select">
                            <option value="unpaid">غير مدفوعه</option>
                            <option value="paid">مدفوع</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removePaymentSchedule(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newItem);
        paymentScheduleCounter++;
    }

    function removePaymentSchedule(button) {
        button.closest('.payment-schedule-item').remove();
    }

    // Initialize
    updateButtons();
</script>
@endpush

