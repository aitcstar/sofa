@extends('frontend.layouts.pages')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">{{ __('site.confirm_order') }}</a>
</div>

<!-- ===== CONFIRM ORDER SECTION ===== -->
<section class="confirm-order">
    <div class="container d-flex flex-column gap-sm">
        <h2 class="heading-h7 text-heading">{{ __('site.confirm_furniture_order') }}</h2>

        <div class="confirm-order-container">
            <!-- Form -->
            <div class="confirm-order-form">
                <div class="tab-options">
                    <div class="tab-option active">
                        <p class="body-2 text-body mb-0">{{ __('site.individual') }}</p>
                    </div>
                    <div class="tab-option">
                        <p class="body-2 text-body mb-0">{{ __('site.company') }}</p>
                    </div>
                </div>

                <form action="{{ app()->getLocale() == 'ar' ? route('order.store', $package->id) : route('order.store.en', $package->id) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-sm-3 mt-4">
                    @csrf

                    <!-- نوع العميل (مخفي، يُحدَّث تلقائيًا) -->
                    <input type="hidden" name="client_type" id="clientType" value="individual">

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="body-2 text-body mb-0">{{ __('site.phone_number') }}</label>
                        <div class="input-phone">
                            <div class="country-select dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="flag fi fi-sa selected-flag"></span>
                                <span class="code selected-code">+966</span>
                            </div>
                            <ul class="dropdown-menu">
                                <!-- نفس قائمة الدول -->
                                @foreach($countries as $country)
                                    <li>
                                        <a class="dropdown-item country-item-login d-flex justify-content-between align-items-center" href="#"
                                        data-flag="{{ $country['code'] }}" data-code="{{ $country['dial_code'] }}">
                                            <span class="d-flex align-items-center gap-sm-3">
                                                <span class="flag fi fi-{{ $country['code'] }}"></span>
                                                <span>{{ app()->getLocale() == 'ar' ? $country['name_ar'] : $country['name_en'] }}</span>
                                            </span>
                                            <span>{{ $country['dial_code'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="country_code" class="country_code" value="+966" />
                            <input type="tel" name="phone" class="phone-number form-control" placeholder="{{ __('site.example_number') }}" required />
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="body-2 text-body mb-0">{{ __('site.email') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="{{ __('site.email') }}" />
                    </div>

                    <!-- الحقول الإضافية للشركة (مخفية افتراضيًا) -->
                    <div id="companyFields" class="d-none">
                        <div class="form-item-mobile d-flex gap-sm-3">
                            <div class="form-group" style="flex: 0.5;">
                                <label class="body-2 text-body mb-0">{{ __('site.commercial_register') }}</label>
                                <input type="text" name="commercial_register" class="form-control" />
                            </div>
                            <div class="form-group" style="flex: 0.5;">
                                <label class="body-2 text-body mb-0">{{ __('site.tax_number') }}</label>
                                <input type="text" name="tax_number" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="form-item-mobile d-flex gap-sm-3">
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.units_count') }}</label>
                            <input type="number" name="units_count" class="form-control" value="1" min="1" required />
                        </div>
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.project_type') }}</label>
                            <select name="project_type" class="form-select" required>
                                <option value="large">{{ __('site.large_project') }}</option>
                                <option value="medium">{{ __('site.medium_project') }}</option>
                                <option value="small">{{ __('site.small_project') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-item-mobile d-flex gap-sm-3">
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.current_stage') }}</label>
                            <select name="current_stage" class="form-select" required>
                                <option value="design">{{ __('site.design_stage') }}</option>
                                <option value="execution">{{ __('site.execution_stage') }}</option>
                                <option value="operation">{{ __('site.operation_stage') }}</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.has_interior_design') }}</label>
                            <select name="has_interior_design" class="form-select" required>
                                <option value="1">{{ __('site.yes') }}</option>
                                <option value="0">{{ __('site.no') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-item-mobile d-flex gap-sm-3">
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.needs_finishing_help') }}</label>
                            <select name="needs_finishing_help" class="form-select" required>
                                <option value="1">{{ __('site.yes') }}</option>
                                <option value="0">{{ __('site.no') }}</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.needs_color_help') }}</label>
                            <select name="needs_color_help" class="form-select" required>
                                <option value="1">{{ __('site.yes') }}</option>
                                <option value="0">{{ __('site.no') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-item-mobile d-flex gap-sm-3">
                        <div class="form-group" style="flex: 0.5;">
                            <label class="body-2 text-body mb-0">{{ __('site.upload_diagrams') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="diagramsToggle" name="has_diagrams" value="1">
                                <label class="form-check-label" for="diagramsToggle">{{ __('site.yes_upload') }}</label>
                            </div>
                        </div>
                        <div class="form-group" style="flex: 0.5;">
                            <div id="diagramsUpload" class="d-none">
                                <label class="body-2 text-body mb-0">{{ __('site.diagrams_file') }}</label>
                                <input type="file" name="diagrams_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">{{ __('site.allowed_formats') }}: PDF, JPG, PNG</small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom-primary">{{ __('site.send_order') }}</button>
                </form>
            </div>

           <!-- Order Details -->
            <div class="confirm-order-details">
                <h3 class="sub-heading-3">{{ __('site.order_summary') }}</h3>

                <div class="d-flex align-items-center gap-sm-3">
                    <div class="confirm-order-details-img">
                        <img src="{{ $package->image ? asset('storage/'.$package->image) : asset('assets/images/category/category-01.jpg') }}" alt="img" />
                    </div>
                    <div class="d-flex flex-column gap-sm-7">
                        <h4 class="sub-heading-4">{{ $package->{'name_'.app()->getLocale()} }}</h4>
                        <div class="d-flex align-items-center gap-sm-6">
                            <p class="heading-h7 mb-0">{{ number_format($package->price * 1.15) }}</p>
                            <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                        </div>
                    </div>
                </div>

                @php
                    // جمع الألوان من الجدول الوسيط
                    $colors = $package->packageUnitItems
                        ->pluck('item.background_color')
                        ->filter()
                        ->unique()
                        ->take(4);
                @endphp

                <ul class="pr-4 m-0">
                    <li class="body-2">{{ __('site.colors') }}:
                        @foreach($colors as $color)
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: {{ $color }}; margin: 0 2px; border-radius: 50%;"></span>
                        @endforeach
                    </li>

                    <!-- عرض الوحدات والقطع -->
                    @php
                        $groupedItems = $package->packageUnitItems->groupBy('unit_id');
                    @endphp

                    @foreach($groupedItems as $unitId => $items)
                        @php
                            $unit = $items->first()->unit;
                        @endphp
                        <li class="body-2">
                            <strong>{{ $unit->{'name_'.app()->getLocale()} }}:</strong>
                            {{ $items->pluck('item.' . (app()->getLocale() === 'ar' ? 'item_name_ar' : 'item_name_en'))->implode(', ') }}
                        </li>
                    @endforeach
                </ul>

                <!-- Pricing -->
                <div class="confirm-order-details-pricing d-flex flex-column gap-sm-7">
                    <div class="confirm-order-details-pricing-item">
                        <p class="body-2 text-body mb-0">{{ __('site.base_price') }}</p>
                        <div class="d-flex align-items-center gap-sm-6">
                            <p class="body-2 text-body mb-0">{{ number_format($package->price) }}</p>
                            <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                        </div>
                    </div>
                    <div class="confirm-order-details-pricing-item">
                        <p class="body-2 text-body mb-0">{{ __('site.tax') }} (15%)</p>
                        <div class="d-flex align-items-center gap-sm-6">
                            <p class="body-2 text-body mb-0">{{ number_format($package->price * 0.15) }}</p>
                            <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                        </div>
                    </div>
                    <div class="confirm-order-details-pricing-item">
                        <p class="body-2 text-body mb-0">{{ __('site.final_price') }}</p>
                        <div class="d-flex align-items-center gap-sm-6">
                            <p class="heading-h7 mb-0">{{ number_format($package->price * 1.15) }}</p>
                            <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/confirm-order.css') }}">
@endpush

@push('scripts')
<script>
// نفس كود اختيار الدولة من ملفك
document.addEventListener('DOMContentLoaded', function() {

       // تفعيل التبويبات
       document.querySelectorAll('.tab-option').forEach(tab => {
        tab.addEventListener('click', function() {
            // إزالة active من جميع التبويبات
            document.querySelectorAll('.tab-option').forEach(t => t.classList.remove('active'));
            // إضافة active للتب المختار
            this.classList.add('active');

            // تحديث نوع العميل
            const isCompany = this.textContent.trim() === '{{ __('site.company') }}';
            document.getElementById('clientType').value = isCompany ? 'company' : 'individual';

            // إظهار/إخفاء حقول الشركة
            const companyFields = document.getElementById('companyFields');
            if (isCompany) {
                companyFields.classList.remove('d-none');
            } else {
                companyFields.classList.add('d-none');
            }
        });
    });

    // اختيار الدولة للتسجيل
    document.querySelectorAll('.country-item-login').forEach(item => {
        item.addEventListener('click', function(e){
            e.preventDefault();
            const code = this.dataset.code;
            const flag = this.dataset.flag;
            document.querySelector('.country_code').value = code;
            document.querySelector('.selected-code').textContent = code;
            document.querySelector('.selected-flag').className = 'flag fi fi-' + flag + ' selected-flag';
        });
    });
});

document.getElementById('diagramsToggle').addEventListener('change', function() {
    const uploadField = document.getElementById('diagramsUpload');
    if (this.checked) {
        uploadField.classList.remove('d-none');
    } else {
        uploadField.classList.add('d-none');
    }
});

</script>
@endpush
