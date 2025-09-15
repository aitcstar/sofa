@extends('frontend.layouts.pages')

@section('title', __('site.contact_us') . ' - SOFA Experience')
@section('description', 'يسعدنا تواصلك معنا! نحن في SOFA نهتم بتقديم تجربة متكاملة لعملائنا، ونسعد بالإجابة على استفساراتكم وتقديم الدعم')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
        <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">{{ __('site.contact_us') }}</a>
</div>


<!-- ===== CONTACT FORM SECTION ===== -->
<section class="contact-form">
    <div class="container">
        <!-- Form -->
        <div class="contact-form">
            <!-- heading -->
            <div class="contact-form-heading">
                <h2 class="heading-h7">
                    {{ app()->getLocale() == 'ar' ? $section->title_ar : $section->title_en }}
                </h2>
                <p class="caption-5">
                    {{ app()->getLocale() == 'ar' ? $section->desc_ar : $section->desc_en }}
                </p>

            </div>

            <!-- form -->
            <div class="contact-form-form">
                <form action="{{ app()->getLocale() == 'ar' ? route('contact.submit') : route('contact.submit.en') }}" method="POST" class="d-flex flex-column gap-sm-3" id="contactForm">                    @csrf
                    <!-- Name Input -->
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('site.name') }}" required>
                    </div>

                    <!-- Email Input -->
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('site.email') }}" required>
                    </div>

                    <!-- Phone Input -->
                    <div class="form-group">
                        <div class="input-phone">
                            <!-- Country Select -->
                            <div class="country-select" data-bs-toggle="dropdown" aria-expanded="false" style="height: 50px;">
                                <span class="flag fi fi-sa" id="selected-flag"></span>
                                <span class="code" id="selected-code">+966</span>
                                <i class="fas fa-chevron-down dropdown-icon"></i>
                            </div>

                            <!-- Dropdown Menu -->
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="input-with-icon" style="min-height: 45px;">
                                        <input type="text" class="form-control" placeholder="{{ __('site.search_here') }}" id="countrySearch" />
                                        <i class="input-icon">
                                            <img src="{{ asset('assets/images/icons/search-normal.png') }}" alt="{{ __('site.search') }}" />
                                        </i>
                                    </div>
                                </li>
                                @foreach($countries as $country)
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="{{ $country['code'] }}"
                                        data-code="{{ $country['dial_code'] }}">
                                        <span class="d-flex align-items-center gap-sm-3">
                                            <span class="flag fi fi-{{ $country['code'] }}"></span>
                                            <span class="body-2">{{ $country['name_ar'] }}</span>
                                        </span>
                                        <span class="body-2">{{ $country['dial_code'] }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>

                            <!-- Phone Number Input -->
                            <input type="tel" class="phone-number" name="phone" dir="rtl" placeholder="{{ __('site.phone_example') }}" style="height: 50px" required />
                            <input type="hidden" name="country_code" id="countryCode" value="+966">
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="form-group">
                        <textarea rows="3" class="form-control form-textarea" id="message" name="message"
                            placeholder="{{ __('site.write_message_here') }}" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-custom-primary">
                        {{ __('site.send_message') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="contact-info">
            <!-- Item 1 -->
            <div class="contact-info-item d-flex flex-column gap-sm-5">
                <h6 class="heading-h7 mb-0">{{ __('site.contact_info') }}</h6>
                <p class="caption-4 mb-0">{{ __('site.contact_info_description') }}</p>
            </div>

            <!-- Item 2 -->
            <div class="contact-info-item d-flex flex-column gap-sm-5">
                <h6 class="sub-heading-4 mb-0">{{ __('site.main_showroom') }}</h6>
                <p class="caption-4 mb-0">
                    {{ app()->getLocale() == 'ar' ? $section->main_showroom_ar : $section->main_showroom_en }}
                </p>
            </div>

            <div class="contact-info-item d-flex flex-column gap-sm-5">
                <h6 class="sub-heading-4 mb-0">{{ __('site.work_hours') }}</h6>
                <p class="caption-4 mb-0">
                    {{ app()->getLocale() == 'ar' ? $section->work_hours_ar : $section->work_hours_en }}
                </p>
            </div>



            <!-- Item 4 -->
            <div class="contact-info-item d-flex flex-column gap-sm-5">
                <h6 class="sub-heading-4 mb-0">{{ __('site.contact_number') }}</h6>
                <p class="caption-4 mb-0">{{$siteSettings->phone}}</p>
            </div>

            <!-- Item 5 -->
            <div class="contact-info-item d-flex flex-column gap-sm-5">
                <h6 class="sub-heading-4 mb-0">{{ __('site.email') }}</h6>
                <p class="caption-4 mb-0">{{$siteSettings->email}}</p>
            </div>

            <!-- Item 6 -->
            <div class="contact-info-item d-flex flex-column gap-sm-5">
                <h6 class="sub-heading-4 mb-0">{{ __('site.social_accounts') }}</h6>
                <div class="d-flex gap-sm-4">
                    <a href="{{$siteSettings->snapchat}}" class="contact-info-social-icon">
                        <i class="fa-brands fa-snapchat"></i>
                    </a>
                    <a href="{{$siteSettings->youtube}}" class="contact-info-social-icon">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="{{$siteSettings->tiktok}}" class="contact-info-social-icon">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>
                    <a href="{{$siteSettings->instagram}}" class="contact-info-social-icon">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA BUTTON SECTION ===== -->
<section class="cta-button">
    <div class="container">
        <div class="cta-button-container d-flex flex-column align-items-center gap-md">
            <!-- overlay -->
            <div class="cta-button-overlay"></div>

            <!-- heading -->
            <div class="cta-button-heading d-flex flex-column gap-sm-3">
                <h2 class="heading-h7 mb-0 text-white">
                    {{ app()->getLocale() == 'ar' ? $section->cta_heading_ar : $section->cta_heading_en }}
                </h2>
                <p class="caption-5 mb-0 text-white" style="max-width: 592px; opacity: 0.8;">
                    {{ app()->getLocale() == 'ar' ? $section->cta_text_ar : $section->cta_text_en }}
                </p>
            </div>

            <!-- buttons -->
            <div class="cta-button-buttons d-flex gap-sm-3">


                <a href="https://wa.me/{{ $siteSettings->whatsapp }}" target="_blank" class="btn btn-custom-secondary">
                    <p class="text-nowrap mb-0">{{ __('site.cta_whatsapp') }}</p>
                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                </a>

                <a href="{{ app()->getLocale() == 'ar' ? route('help.index') : route('help.index.en') }}" class="btn btn-custom-outline">                    <p class="mb-0">{{ __('site.cta_order_now') }}</p>
                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" style="font-size: 18px;"></i>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ===== CONTACT MAP SECTION ===== -->
<section class="contact-map">
    <div class="container d-flex flex-column gap-sm">
        <!-- heading -->
        <div class="contact-map-heading d-flex flex-column gap-sm-5">
            <h2 class="heading-h7 mb-0">
                {{ app()->getLocale() == 'ar' ? $section->city_ar : $section->city_en }}
            </h2>
            <p class="caption-5 mb-0">
                {{ app()->getLocale() == 'ar' ? $section->address_ar : $section->address_en }}
            </p>
        </div>


        <!-- map -->
        <div class="contact-map-map">
            <div id="map"></div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/contact.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.0/css/flag-icons.min.css" />
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // بيانات الدول (يمكن نقلها إلى الـ Controller لاحقًا)
    const countries = [
        {code: 'sa', name_ar: 'السعودية', dial_code: '+966'},
        {code: 'ae', name_ar: 'الإمارات', dial_code: '+971'},
        {code: 'kw', name_ar: 'الكويت', dial_code: '+965'},
        {code: 'qa', name_ar: 'قطر', dial_code: '+974'},
        {code: 'bh', name_ar: 'البحرين', dial_code: '+973'},
        {code: 'om', name_ar: 'عمان', dial_code: '+968'},
        {code: 'jo', name_ar: 'الأردن', dial_code: '+962'},
        {code: 'lb', name_ar: 'لبنان', dial_code: '+961'},
        {code: 'eg', name_ar: 'مصر', dial_code: '+20'},
        {code: 'ma', name_ar: 'المغرب', dial_code: '+212'},
        {code: 'dz', name_ar: 'الجزائر', dial_code: '+213'},
        {code: 'tn', name_ar: 'تونس', dial_code: '+216'},
        {code: 'ly', name_ar: 'ليبيا', dial_code: '+218'},
        {code: 'sd', name_ar: 'السودان', dial_code: '+249'},
        {code: 'iq', name_ar: 'العراق', dial_code: '+964'},
        {code: 'sy', name_ar: 'سوريا', dial_code: '+963'},
        {code: 'ye', name_ar: 'اليمن', dial_code: '+967'},
        {code: 'ps', name_ar: 'فلسطين', dial_code: '+970'},
        {code: 'us', name_ar: 'الولايات المتحدة', dial_code: '+1'},
        {code: 'gb', name_ar: 'المملكة المتحدة', dial_code: '+44'},
        {code: 'de', name_ar: 'ألمانيا', dial_code: '+49'},
        {code: 'fr', name_ar: 'فرنسا', dial_code: '+33'},
        {code: 'it', name_ar: 'إيطاليا', dial_code: '+39'},
        {code: 'es', name_ar: 'إسبانيا', dial_code: '+34'},
        {code: 'ca', name_ar: 'كندا', dial_code: '+1'},
        {code: 'au', name_ar: 'أستراليا', dial_code: '+61'},
        {code: 'in', name_ar: 'الهند', dial_code: '+91'},
        {code: 'pk', name_ar: 'باكستان', dial_code: '+92'},
        {code: 'tr', name_ar: 'تركيا', dial_code: '+90'},
        {code: 'ir', name_ar: 'إيران', dial_code: '+98'}
    ];

    // تحديد رمز الدولة الافتراضي
    let selectedCountry = countries.find(country => country.code === 'sa');

    // تحديث واجهة اختيار الدولة
    function updateSelectedCountry() {
        document.getElementById('selected-flag').className = `flag fi fi-${selectedCountry.code}`;
        document.getElementById('selected-code').textContent = selectedCountry.dial_code;
        document.getElementById('countryCode').value = selectedCountry.dial_code;
    }

    // إضافة event listeners لعناصر القائمة المنسدلة
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const flag = this.getAttribute('data-flag');
            const code = this.getAttribute('data-code');

            selectedCountry = countries.find(country => country.code === flag);
            updateSelectedCountry();
        });
    });

    // البحث في قائمة الدول
    const countrySearch = document.getElementById('countrySearch');
    if (countrySearch) {
        countrySearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const dropdownItems = document.querySelectorAll('.dropdown-item');

            dropdownItems.forEach(item => {
                const countryName = item.querySelector('.body-2').textContent.toLowerCase();
                if (countryName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // تهيئة الخريطة
    function initMap() {
        var map = L.map('map').setView([24.7136, 46.6753], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 14
        }).addTo(map);

        // إضافة علامة للموقع
        L.marker([24.7136, 46.6753])
            .addTo(map)
            .bindPopup('مركز SOFA لتجربة التأثيث<br>الرياض – طريق الملك عبدالعزيز، حي الياسمين')
            .openPopup();

        // Remove controls
        map.removeControl(map.zoomControl);
        map.removeControl(map.attributionControl);

        // Disable zoom and other interactions
        map.scrollWheelZoom.disable();
        map.doubleClickZoom.disable();
        map.boxZoom.disable();
        map.keyboard.disable();
        map.dragging.disable();
    }

    // تهيئة الخريطة عند تحميل الصفحة
    initMap();


});

// تحديث الدولة الافتراضية عند التحميل
document.addEventListener('DOMContentLoaded', function() {
    const selectedFlag = document.getElementById('selected-flag');
    const selectedCode = document.getElementById('selected-code');
    const countryCode = document.getElementById('countryCode');

    if (selectedFlag && selectedCode && countryCode) {
        selectedFlag.className = 'flag fi fi-sa';
        selectedCode.textContent = '+966';
        countryCode.value = '+966';
    }
});
</script>
@endpush
