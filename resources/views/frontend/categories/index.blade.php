@extends('frontend.layouts.pages')

@section('title', 'التصنيفات - SOFA Experience')
@section('description', 'تصفح جميع تصنيفات الأثاث الفندقي المتاحة في SOFA Experience')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home') }}" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">التصنيفات</a>
</div>

<!-- ===== FILTER & ROOMS SECTION ===== -->
<section class="filter-rooms-container">
    <div class="container">
        <!-- ===== MOBILE FILTER TOGGLE ===== -->
        <div class="mobile-filter-toggle padding-box-small-4 d-flex justify-content-between align-items-center"
            id="mobileFilterToggle">
            <!-- Title -->
            <div class="d-flex gap-sm-3 align-items-center">
                <p class="body-1 text-subheading mb-0">فرز وتصنيف</p>
                <img src="{{ asset('assets/images/icons/filter.svg') }}" alt="Filter" />
            </div>

            <!-- Quantity -->
            <span class="body-4 text-caption mb-0">{{ $categories->count() }} باكجات</span>
        </div>

        <!-- ===== FILTER SECTION ===== -->
        <div class="filter-section-container">
            <!-- Filter -->
            <div class="filter-section">
                <h3 class="filter-title heading-h8 mb-0">فلتر</h3>

                <!-- Unit Type -->
                <div class="filter-group">
                    <h4 class="sub-heading-4 text-subheading mb-0">نوع الوحدة</h4>

                    <div class="filter-options">
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="unit-type" data-value="studio"></div>
                            <span class="body-2 text-body">استوديو</span>
                        </div>
                        <div class="filter-option">
                            <div class="filter-checkbox checked" data-filter="unit-type" data-value="three-rooms"></div>
                            <span class="body-2 text-body">ثلاث غرف</span>
                        </div>
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="unit-type" data-value="two-rooms"></div>
                            <span class="body-2 text-body">غرفتين</span>
                        </div>
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="unit-type" data-value="one-room"></div>
                            <span class="body-2 text-body">غرفة</span>
                        </div>
                    </div>
                </div>

                <!-- Finishing Style -->
                <div class="filter-group">
                    <h4 class="sub-heading-4 text-subheading mb-0">نمط التشطيب</h4>

                    <div class="filter-options">
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="finishing-style" data-value="luxury-plus"></div>
                            <span class="body-2 text-body">فاخر بلس</span>
                        </div>
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="finishing-style" data-value="luxury"></div>
                            <span class="body-2 text-body">فاخر</span>
                        </div>
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="finishing-style" data-value="standard"></div>
                            <span class="body-2 text-body">قياسي</span>
                        </div>
                    </div>
                </div>

                <!-- Color Style -->
                <div class="filter-group">
                    <h4 class="sub-heading-4 text-subheading mb-0">ستايل الألوان</h4>

                    <div class="filter-options">
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="color-style" data-value="beige"></div>
                            <div class="filter-option-content">
                                <span class="body-2 text-body">درجات البيج</span>
                                <div class="color-swatch beige"></div>
                            </div>
                        </div>
                        <div class="filter-option">
                            <div class="filter-checkbox" data-filter="color-style" data-value="gray"></div>
                            <div class="filter-option-content">
                                <span class="body-2 text-body">درجات الرمادي</span>
                                <div class="color-swatch gray"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms -->
            <div class="rooms-container">
                <div class="row">
                    @foreach($categories as $category)
                    <!-- Item {{ $loop->iteration }} -->
                    <div class="col-sm-12 col-md-6 mb-sm-4">
                        <div class="room-item">
                            <!-- image & widget -->
                            <div class="image">
                                <div class="widget text-center">
                                    <span class="body-4 text-white">جاهز للتسليم السريع</span>
                                </div>
                                <img src="{{ asset($category->image ?? 'assets/images/category/category-01.jpg') }}" class="w-100 h-100" alt="{{ $category->name }}" />
                            </div>

                            <!-- Content -->
                            <div class="content d-flex flex-column gap-sm-3">

                                <!-- Title & Quantity & Description -->
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex flex-column gap-sm-6">
                                        <h5 class="sub-heading-3">
                                            {{ $category->name }}
                                        </h5>
                                        <p class="body-3 mb-0">
                                            {{ $category->description ?? 'مثالي للمساحات الصغيرة، يوفر الراحة والأناقة' }}
                                        </p>
                                    </div>
                                    <p class="body-2" style="color: var(--secondary);">{{ $category->items_count }} قطعة</p>
                                </div>

                                <!-- Price -->
                                <div class="d-flex align-items-center gap-sm-5 mb-2">
                                    <p class="body-2 text-caption mb-0">ابتداءً من</p>
                                    <h4 class="heading-h6 mb-0">
                                        {{ number_format($category->price) }}
                                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                                    </h4>
                                </div>

                                <!-- Options -->
                                <div class="d-flex flex-column gap-sm-4">
                                    <!-- Including -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                      <p class="body-2 text-caption mb-0" style="width: 90px;">يشمل:</p>
                                      <div class="d-flex gap-sm-5">
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                          <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                          <span class="body-4">غرفة نوم</span>
                                        </div>
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                          <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="" />
                                          <span class="body-4">مجلس</span>
                                        </div>
                                        <span class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                          <img src="{{ asset('assets/images/icons/foot.png') }}" alt="" />
                                          <span class="body-4">طاولة طعام</span>
                                        </span>
                                      </div>
                                    </div>

                                    <!-- Colors -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                      <p class="body-2 text-caption mb-0" style="width: 90px;">الألوان المتاحة:</p>
                                      <div class="d-flex gap-sm-5">
                                        <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #f5f1e6"></span>
                                        <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #aaaaaa"></span>
                                        <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #a1866f"></span>
                                        <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #8b5e3c"></span>
                                      </div>
                                    </div>

                                    <!-- Time implementation -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                      <p class="body-2 text-caption mb-0" style="width: 90px;">مدة التنفيذ:</p>
                                      <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                        <img src="{{ asset('assets/images/icons/clock-watch.png') }}" alt="" />
                                        <span class="body-4">15–20 يوم عمل</span>
                                      </div>
                                    </div>

                                    <!-- Service -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                      <p class="body-2 text-caption mb-0" style="width: 90px;">الخدمة</p>
                                      <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                        <img src="{{ asset('assets/images/icons/tools-wench-ruler.png') }}" alt="" />
                                        <span class="body-4">يشمل التركيب والتوصيل الكامل</span>
                                      </div>
                                    </div>

                                    <!-- Payment Plan -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                      <p class="body-2 text-caption mb-0" style="width: 90px;"> خطة الدفع :</p>
                                      <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                        <img src="{{ asset('assets/images/icons/wallet-2.png') }}" alt="" />
                                        <span class="body-4">50٪ مقدم – 50٪ قبل التسليم</span>
                                      </div>
                                    </div>

                                    <!-- Decoration -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                      <p class="body-2 text-caption mb-0" style="width: 90px;"> الديكور :</p>
                                      <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                                        <img src="{{ asset('assets/images/icons/brush-ruler.png') }}" alt="" />
                                        <span class="body-4"> ديكور أساسي بسيط</span>
                                      </div>
                                    </div>
                                  </div>
                            </div>

                            <!-- Actions Buttons -->
                            <div class="actions d-flex gap-sm-2">
                                <a href="https://wa.me/{{$siteSettings->whatsapp}}" target="_blank" class="btn btn-custom-primary w-100">
                                    <p class="text-nowrap mb-0">أرسل لي عرض السعر</p>
                                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                                 </a>
                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-custom-secondary">
                                    <span style="white-space: nowrap;">عرض التفاصيل</span>
                                    <i class="fa-solid fa-arrow-left action-icon"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== MOBILE FILTER OVERLAY ===== -->
<div class="mobile-filter-overlay" id="mobileFilterOverlay"></div>

<!-- ===== MOBILE FILTER DRAWER ===== -->
<div class="mobile-filter-drawer" id="mobileFilterDrawer">
    <!-- Close Button -->
    <div class="mobile-filter-header" id="mobileFilterClose">
        <i class="fas fa-times" style="font-size: 18px;"></i>
        <p class="body-1 text-subheading mb-0">اغلاق</p>
    </div>

    <!-- Content -->
    <div class="mobile-filter-content">
        <!-- Title -->
        <h3 class="heading-h8 mb-0">فلتر</h3>

        <!-- Unit Type -->
        <div class="mobile-filter-group">
            <h4 class="sub-heading-4 mb-0">نوع الوحدة</h4>
            <div class="mobile-filter-options">
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="unit-type" data-value="studio"></div>
                    <span class="body-2 text-body">استوديو</span>
                </div>
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox checked" data-filter="unit-type" data-value="three-rooms"></div>
                    <span class="body-2 text-body">ثلاث غرف</span>
                </div>
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="unit-type" data-value="two-rooms"></div>
                    <span class="body-2 text-body">غرفتين</span>
                </div>
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="unit-type" data-value="one-room"></div>
                    <span class="body-2 text-body">غرفة</span>
                </div>
            </div>
        </div>

        <!-- Finishing Style -->
        <div class="mobile-filter-group">
            <h4 class="sub-heading-4 mb-0">نمط التشطيب</h4>
            <div class="mobile-filter-options">
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="finishing-style" data-value="luxury-plus"></div>
                    <span class="body-2 text-body">فاخر بلس</span>
                </div>
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="finishing-style" data-value="luxury"></div>
                    <span class="body-2 text-body">فاخر</span>
                </div>
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="finishing-style" data-value="standard"></div>
                    <span class="body-2 text-body">قياسي</span>
                </div>
            </div>
        </div>

        <!-- Color Style -->
        <div class="mobile-filter-group">
            <h4 class="sub-heading-4 mb-0">ستايل الألوان</h4>
            <div class="mobile-filter-options">
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="color-style" data-value="beige"></div>
                    <div class="mobile-filter-option-content">
                        <span class="body-2 text-body">درجات البيج</span>
                        <div class="mobile-color-swatch beige"></div>
                    </div>
                </div>
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox" data-filter="color-style" data-value="gray"></div>
                    <div class="mobile-filter-option-content">
                        <span class="body-2 text-body">درجات الرمادي</span>
                        <div class="mobile-color-swatch gray"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Button -->
    <div class="flex-grow-1 d-flex">
        <button class="btn btn-custom-primary mt-auto" id="applyFilters">
            تطبيق
        </button>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/categories.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Filter Elements
    const mobileFilterToggle = document.getElementById('mobileFilterToggle');
    const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
    const mobileFilterDrawer = document.getElementById('mobileFilterDrawer');
    const mobileFilterClose = document.getElementById('mobileFilterClose');
    const applyFilters = document.getElementById('applyFilters');

    // Mobile Filter Checkboxes
    const mobileFilterCheckboxes = document.querySelectorAll('.mobile-filter-checkbox');

    // Open Mobile Filter
    function openMobileFilter() {
        mobileFilterOverlay.style.display = 'block';
        mobileFilterDrawer.style.display = 'flex';

        // Trigger animation after display is set
        setTimeout(() => {
            mobileFilterOverlay.classList.add('active');
            mobileFilterDrawer.classList.add('active');
        }, 10);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    // Close Mobile Filter
    function closeMobileFilter() {
        mobileFilterOverlay.classList.remove('active');
        mobileFilterDrawer.classList.remove('active');

        // Hide elements after animation
        setTimeout(() => {
            mobileFilterOverlay.style.display = 'none';
            mobileFilterDrawer.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    // Toggle Checkbox
    function toggleCheckbox(checkbox) {
        checkbox.classList.toggle('checked');
    }

    // Mobile Filter Event Listeners
    mobileFilterToggle.addEventListener('click', openMobileFilter);
    mobileFilterClose.addEventListener('click', closeMobileFilter);
    mobileFilterOverlay.addEventListener('click', closeMobileFilter);
    applyFilters.addEventListener('click', closeMobileFilter);

    // Checkbox click events
    mobileFilterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('click', () => toggleCheckbox(checkbox));
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (mobileFilterDrawer.classList.contains('active')) {
                closeMobileFilter();
            }
        }
    });

    // Prevent drawer close when clicking inside it
    mobileFilterDrawer.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // إرسال رسالة واتساب
    window.sendWhatsApp = function(categoryName) {
        const phoneNumber = "966500000000";
        const message = `مرحباً، أنا مهتم بـ ${categoryName} وأريد معرفة المزيد عن العروض والأسعار.`;
        const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
    }

    // فلترة التصنيفات
    function filterCategories() {
        const selectedFilters = {
            'unit-type': [],
            'finishing-style': [],
            'color-style': []
        };

        // جمع الفلاتر المحددة
        document.querySelectorAll('.filter-checkbox.checked, .mobile-filter-checkbox.checked').forEach(checkbox => {
            const filterType = checkbox.getAttribute('data-filter');
            const filterValue = checkbox.getAttribute('data-value');
            if (selectedFilters[filterType]) {
                selectedFilters[filterType].push(filterValue);
            }
        });

        console.log('الفلاتر المحددة:', selectedFilters);
        // هنا يمكنك إضافة منطق الفلترة حسب الحاجة
    }

    // إضافة event listeners للفلاتر
    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            this.classList.toggle('checked');
            filterCategories();
        });
    });

    // تطبيق الفلاتر عند النقر على زر التطبيق
    applyFilters.addEventListener('click', filterCategories);
});
</script>
@endpush
