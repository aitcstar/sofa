@extends('frontend.layouts.pages')


@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">{{ __('site.categories') }}</a>
</div>

<!-- ===== FILTER & ROOMS SECTION ===== -->
<section class="filter-rooms-container">
    <div class="container">
        <!-- ===== MOBILE FILTER TOGGLE ===== -->
        <div class="mobile-filter-toggle padding-box-small-4 d-flex justify-content-between align-items-center"
            id="mobileFilterToggle">
            <!-- Title -->
            <div class="d-flex gap-sm-3 align-items-center">
                <p class="body-1 text-subheading mb-0">{{ __('site.Sort and classify') }}</p>
                <img src="{{ asset('assets/images/icons/filter.svg') }}" alt="Filter" />
            </div>

            <!-- Quantity -->
            <span class="body-4 text-caption mb-0">{{ $packages->count() }} {{ __('site.categories') }}</span>
        </div>

        <!-- ===== FILTER SECTION ===== -->
        <div class="filter-section-container">
            <!-- Filter -->
            <div class="filter-section">
                <h3 class="filter-title heading-h8 mb-0">{{ __('site.filter') }}</h3>

                <div class="filter-group">
                    <h4 class="sub-heading-4 text-subheading mb-0">{{ __('site.unit_type') }} </h4>
                    <div class="filter-options">
                        @foreach($unitTypes as $type)
    <div class="filter-option">
        <div class="filter-checkbox"
             data-filter="unit-type"
             data-value="{{ $type['name_'.app()->getLocale()] }}">
        </div>
        <span class="body-2 text-body">{{ $type['name_'.app()->getLocale()] }}</span>
    </div>
@endforeach



                    </div>
                </div>

                <div class="filter-group">
                    <h4 class="sub-heading-4 text-subheading mb-0" >{{ __('site.Color_style') }}</h4>
                    <div class="filter-options">
                        @foreach($colors as $color)
                            <div class="filter-option">
                                <div class="filter-checkbox"
                                     data-filter="color-style"
                                     data-value="{{ $color['background_color'] }}"
                                     data-label="{{ app()->getLocale() === 'ar' ? $color['color_ar'] : $color['color_en'] }}">
                                </div>
                                <div class="filter-option-content">
                                    <span class="body-2 text-body">
                                        {{ app()->getLocale() === 'ar' ? $color['color_ar'] : $color['color_en'] }}
                                    </span>
                                    <div class="color-swatch" style="background-color: {{ $color['background_color'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Rooms -->
            <div class="rooms-container" style="width: 100%;s">
                <div class="row">
                    @foreach($packages as $package)
                    <!-- Package Item -->
                    <div class="col-sm-12 col-md-6 mb-sm-4 package-cards"
                        data-package-name="{{ $package->{'name_'.app()->getLocale()} }}"
                        data-colors="{{ $package->units->flatMap->items->pluck('background_color')->filter()->unique()->implode(',') }}"
                        data-unit-types="{{ $package->units->pluck('name_'.app()->getLocale())->implode(',') }}">

                        <div class="room-item">
                            <!-- image & widget -->
                            <div class="image">
                                <!--<div class="widget text-center">
                                    <span class="body-4 text-white">جاهز للتسليم السريع</span>
                                </div>-->
                                @if($package->image)
                                    <img src="{{ asset('storage/' . $package->image) }}" class="w-100 h-100" alt="{{ $package->{'name_'.app()->getLocale()} }}" />
                                @else
                                    <img src="{{ asset('assets/images/category/category-01.jpg') }}" class="w-100 h-100" alt="{{ $package->{'name_'.app()->getLocale()} }}" />
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="content d-flex flex-column gap-sm-3">
                                <!-- Title & Quantity & Description -->
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex flex-column gap-sm-6">
                                        <h5 class="sub-heading-3">  {{ __('site.package') }} {{ $package->{'name_'.app()->getLocale()} }}</h5>
                                        <p class="body-3 mb-0">
                                            {{ $package->{'description_'.app()->getLocale()}  ?: 'مثالي للمساحات الصغيرة، يوفر الراحة والأناقة' }}
                                        </p>
                                    </div>
                                    <p class="body-2" style="color: var(--secondary);">
                                        {{ $package->units->sum(fn($u) => $u->items->count()) }} {{ __('site.piece') }}
                                    </p>
                                </div>

                                <!-- Price -->
                                <div class="d-flex align-items-center gap-sm-5 mb-2">
                                    <p class="body-2 text-caption mb-0">  {{ __('site.Starting_from') }}</p>
                                    <h4 class="heading-h6 mb-0">
                                        {{ number_format($package->price)  }}
                                        <img src="{{ asset('assets/images/hero/Platform Subtitle.png') }}" alt="" />
                                    </h4>
                                </div>

                                <!-- Options -->
                                <div class="d-flex flex-column gap-sm-4">
                                    <!-- Including -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Includes') }}</p>
                                        <div class="d-flex flex-wrap gap-sm-3">
                                            @foreach($package->units as $unit)
                                                <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                                    @if($unit->type == "bedroom")
                                                        <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                                    @elseif($unit->type == "living_room")
                                                        <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="" />
                                                    @elseif($unit->type == "kitchen")
                                                        <img src="{{ asset('assets/images/icons/foot.png') }}" alt="" />
                                                    @elseif($unit->type == "external")
                                                        <img src="{{ asset('assets/images/icons/Group.png') }}" alt="" />
                                                    @else
                                                        <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                                    @endif
                                                    <span class="body-4">{{ $unit->{'name_'.app()->getLocale()} }}</span>

                                                </div>

                                            @endforeach
                                        </div>
                                    </div>

                                    @php
                                        $colors = $package->units
                                            ->flatMap->items
                                            ->pluck('background_color')
                                            ->filter()
                                            ->unique()
                                            ->take(4);
                                    @endphp

                                    <!-- Colors -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Available_colors') }}</p>
                                        <div class="d-flex gap-sm-5">
                                            @forelse($colors as $color)
                                            <span class="rounded-pill" style="width: 34px; height: 16px; background-color: {{ $color }}"></span>
                                        @empty
                                            <span>لا توجد ألوان</span>
                                        @endforelse
                                        </div>
                                    </div>

                                    <!-- Time implementation -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Duration') }}</p>
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            <img src="{{ asset('assets/images/icons/clock-watch.png') }}" alt="" />
                                            <span class="body-4">{{ $package->{'period_'.app()->getLocale()} }}</span>
                                        </div>
                                    </div>

                                    <!-- Service -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Service') }}</p>
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            <img src="{{ asset('assets/images/icons/tools-wench-ruler.png') }}" alt="" />
                                            <span class="body-4">{{ $package->{'service_includes_'.app()->getLocale()} }}</span>
                                        </div>
                                    </div>

                                    <!-- Payment Plan -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Payment_plan') }}</p>
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            <img src="{{ asset('assets/images/icons/wallet-2.png') }}" alt="" />
                                            <span class="body-4">{{ $package->{'payment_plan_'.app()->getLocale()} }}</span>
                                        </div>
                                    </div>

                                    <!-- Decoration -->
                                    <div class="d-flex gap-sm-3 align-items-center">
                                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Decoration') }}</p>
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            <img src="{{ asset('assets/images/icons/brush-ruler.png') }}" alt="" />
                                            <span class="body-4">{{ $package->{'decoration_'.app()->getLocale()} }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Buttons -->
                            <div class="actions d-flex gap-sm-2">
                                <a href="https://wa.me/{{ $siteSettings->whatsapp }}" target="_blank" class="btn btn-custom-primary w-100">
                                    <p class="text-nowrap mb-0">{{ __('site.send_whatsapp_quote') }}</p>
                                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                                </a>
                                <a href="{{ app()->getLocale() == 'ar' ? route('packages.show', ['id' => $package->id]) : route('packages.show.en', ['id' => $package->id]) }}" class="btn btn-custom-secondary w-100">
                                    <span style="white-space: nowrap;">{{ __('site.view_details') }}</span>
                                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-icon"></i>
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
        <p class="body-1 text-subheading mb-0">{{ __('site.close') }}</p>
    </div>

    <!-- Content -->
    <div class="mobile-filter-content">
        <!-- Title -->
        <h3 class="heading-h8 mb-0">{{ __('site.filter') }}</h3>

        <!-- Unit Type -->
        <div class="mobile-filter-group">
            <h4 class="sub-heading-4 mb-0"> {{ __('site.unit_type') }} </h4>
            <div class="mobile-filter-options">
                @foreach($unitTypes as $type)
                <div class="mobile-filter-option">
                    <div class="mobile-filter-checkbox"
                         data-filter="unit-type"
                         data-value="{{ $type['name_'.app()->getLocale()] }}">
                    </div>
                    <span class="body-2 text-body">{{ $type['name_'.app()->getLocale()] }}</span>
                </div>
            @endforeach


            </div>
        </div>


        <!-- Color Style -->
        <div class="mobile-filter-group">
            <h4 class="sub-heading-4 mb-0"> {{ __('site.Color_style') }}</h4>
            <div class="mobile-filter-options">



                @foreach($mobileColors as $color)
                @if(is_array($color) && isset($color['background_color']))
                    <div class="mobile-filter-option">
                        <div class="mobile-filter-checkbox"
                             data-filter="color-style"
                             data-value="{{ $color['background_color'] }}"
                             data-label="{{ app()->getLocale() === 'ar' ? $color['color_ar'] : $color['color_en'] }}">

                        </div>
                        <div class="mobile-filter-option-content">
                            <span class="body-2 text-body">
                                {{ app()->getLocale() === 'ar' ? $color['color_ar'] : $color['color_en'] }}
                            </span>

                            <div class="mobile-color-swatch" style="background-color: {{ $color['background_color'] }};"></div>
                        </div>
                    </div>
                @endif
            @endforeach


            </div>
        </div>
    </div>

    <!-- Apply Button -->
    <div class="flex-grow-1 d-flex">
        <button class="btn btn-custom-primary mt-auto" id="applyFiltersMobile">
            {{ __('site.apply') }}
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
    /*function filterCategories() {
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
    applyFilters.addEventListener('click', filterCategories);*/
});


document.addEventListener('DOMContentLoaded', function () {
    // SELECTORS
    const desktopCheckboxes = Array.from(document.querySelectorAll('.filter-checkbox'));
    const mobileCheckboxes  = Array.from(document.querySelectorAll('.mobile-filter-checkbox'));
    const cards             = Array.from(document.querySelectorAll('.package-cards'));
    const applyBtn          = document.getElementById('applyFiltersMobile');
    const mobileDrawer      = document.getElementById('mobileFilterDrawer');
    const mobileOverlay     = document.getElementById('mobileFilterOverlay'); // لو موجود

    // debug quick info
    if (!cards.length) {
        console.warn('No package cards found. تأكد أن العناصر تحمل class="package-cards" وأنها موجودة في الصفحة.');
    }

    // helper: collect active filters from a NodeList (desktop or mobile)
    function getActiveFiltersFrom(nodeList) {
    const active = { "package-name": [], "color-style": [], "unit-type": [] }; // ✅ ضفت unit-type
    nodeList.forEach(cb => {
        if (cb.classList && cb.classList.contains('checked')) {
            const f = cb.dataset.filter;
            const v = (cb.dataset.value || '').toString().trim();
            if (f && v) {
                active[f] = active[f] || [];
                active[f].push(v);
            }
        }
    });
    return active;
}


    // helper: normalize colors from data-colors attribute into array (lowercase, trimmed)
    function normalizeCardColors(str) {
        if (!str) return [];
        return str.split(',')
                  .map(s => s.trim().toLowerCase())
                  .filter(Boolean);
    }

    // apply filters to DOM cards
    function applyFilters(activeFilters) {
    cards.forEach(card => {
        let show = true;

        const pkgName = (card.dataset.packageName || '').toString().trim();

        // package-name filter
        if (activeFilters["package-name"] && activeFilters["package-name"].length > 0) {
            const selectedNames = activeFilters["package-name"].map(v => v.toString().trim());
            if (!selectedNames.includes(pkgName)) show = false;
        }

        // color filter
        if (activeFilters["color-style"] && activeFilters["color-style"].length > 0) {
            const cardColors = normalizeCardColors(card.dataset.colors || '');
            const selectedColors = activeFilters["color-style"].map(v => v.toString().trim().toLowerCase());
            const intersection = selectedColors.some(sc => cardColors.includes(sc));
            if (!intersection) show = false;
        }

        // ✅ unit-type filter
        if (activeFilters["unit-type"] && activeFilters["unit-type"].length > 0) {
            const cardUnits = (card.dataset.unitTypes || '').split(',').map(u => u.trim());
            const selectedUnits = activeFilters["unit-type"].map(v => v.toString().trim());
            const match = selectedUnits.some(u => cardUnits.includes(u));
            if (!match) show = false;
        }

        card.style.display = show ? '' : 'none';
    });
}

    // DESKTOP: toggle immediately
    desktopCheckboxes.forEach(cb => {
        cb.addEventListener('click', function () {
            cb.classList.toggle('checked');
            const active = getActiveFiltersFrom(desktopCheckboxes);
            applyFilters(active);
        });
    });

    // MOBILE: only toggle UI on clicks, apply on button press
    mobileCheckboxes.forEach(cb => {
        cb.addEventListener('click', function () {
            cb.classList.toggle('checked');
        });
    });

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            const active = getActiveFiltersFrom(mobileCheckboxes);
            applyFilters(active);

            // اختياري: اغلاق الدروير بعد التطبيق (تأكد أن لديك overlay)
            if (mobileDrawer) {
                mobileDrawer.classList.remove('active');
                mobileDrawer.style.display = 'none';
            }
            if (mobileOverlay) {
                mobileOverlay.classList.remove('active');
                mobileOverlay.style.display = 'none';
            }
            document.body.style.overflow = '';
        });
    } else {
        console.warn('applyFilters button not found (id="applyFilters").');
    }

    // initial show all
    applyFilters({ "package-name": [], "color-style": [] });
});




</script>
@endpush
