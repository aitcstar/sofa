@extends('frontend.layouts.pages')

@section('title', 'طلب بمساعدة - SOFA Experience')
@section('description', 'تواصل معنا اليوم للحصول على باكجاتنا الفندقية الجاهزة باحترافية وسرعة')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>

    <a href="#" class="body-1 text-primary">{{ __('site.help') }}</a>
</div>

<!-- ===== HELP SECTION ===== -->
<section class="help">
    <div class="container">


        <!-- heading-->
        <div class="d-flex flex-column gap-sm-4">
            <h1 class="heading-h7 mb-0"> {{ app()->getLocale() == 'ar' ? $content->title_ar : $content->title_en }}</h1>
            <p class="body-2 mb-0">  {{ app()->getLocale() == 'ar' ? $content->text_ar : $content->text_en }}</p>
        </div>

        <!-- form -->
        <div class="help-form">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ app()->getLocale() == 'ar' ? route('help.submit') : route('help.submit.en') }}"
            method="POST"
            class="d-flex flex-column gap-sm-3"
            id="helpForm">
          @csrf

          <!-- Name -->
          <div class="form-group">
              <input type="text" class="form-control" id="name" name="name"
                     placeholder="{{ __('help.name') }}" required />
          </div>

          <!-- Company Name -->
          <div class="form-group">
              <input type="text" class="form-control" id="company-name" name="company"
                     placeholder="{{ __('help.company') }}" />
          </div>

          <!-- Email -->
          <div class="form-group">
              <input type="email" class="form-control" id="email" name="email"
                     placeholder="{{ __('help.email') }}" required />
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
                              <input type="text" class="form-control" placeholder="{{ __('help.search') }}" id="countrySearch" />
                              <i class="input-icon">
                                  <img src="{{ asset('assets/images/icons/search-normal.png') }}" alt="{{ __('help.search') }}" />
                              </i>
                          </div>
                      </li>
                      @foreach($countries as $country)
                      <li>
                          <a class="dropdown-item d-flex justify-content-between align-items-center" href="#"
                              data-flag="{{ $country['code'] }}" data-code="{{ $country['dial_code'] }}">
                              <span class="d-flex align-items-center gap-sm-3">
                                  <span class="flag fi fi-{{ $country['code'] }}"></span>
                                  <span class="body-2">
                                      {{ app()->getLocale() == 'ar' ? $country['name_ar'] : $country['name_en'] }}
                                  </span>
                              </span>
                              <span class="body-2">{{ $country['dial_code'] }}</span>
                          </a>
                      </li>
                      @endforeach
                  </ul>

                  <!-- Phone Number Input -->
                  <input type="tel" class="phone-number" name="phone" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
                         placeholder="{{ __('help.phone_placeholder') }}" style="height: 50px" required />
                  <input type="hidden" name="country_code" id="countryCode" value="+966">
              </div>
          </div>

          <!-- Number of units -->
          <div class="form-group">
              <input type="number" class="form-control" id="units" name="units"
                     placeholder="{{ __('help.units') }}" required />
          </div>

          <!-- Message -->
          <div class="form-group">
              <textarea class="form-control" id="message" name="message"
                        placeholder="{{ __('help.message') }}" rows="3"></textarea>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-custom-primary">
              {{ __('help.submit') }}
          </button>

          <!-- Helper text -->
          <p class="body-3 mb-0 text-secondary">
              {{ __('help.helper_text') }}
          </p>
      </form>


        <meta name="csrf-token" content="{{ csrf_token() }}">

        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/help.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.0/css/flag-icons.min.css" />
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // بيانات الدول
    const countries = [
    {code: 'sa', name_ar: 'السعودية', name_en: 'Saudi Arabia', dial_code: '+966'},
    {code: 'ae', name_ar: 'الإمارات', name_en: 'United Arab Emirates', dial_code: '+971'},
    {code: 'kw', name_ar: 'الكويت', name_en: 'Kuwait', dial_code: '+965'},
    {code: 'qa', name_ar: 'قطر', name_en: 'Qatar', dial_code: '+974'},
    {code: 'bh', name_ar: 'البحرين', name_en: 'Bahrain', dial_code: '+973'},
    {code: 'om', name_ar: 'عمان', name_en: 'Oman', dial_code: '+968'},
    {code: 'jo', name_ar: 'الأردن', name_en: 'Jordan', dial_code: '+962'},
    {code: 'lb', name_ar: 'لبنان', name_en: 'Lebanon', dial_code: '+961'},
    {code: 'eg', name_ar: 'مصر', name_en: 'Egypt', dial_code: '+20'},
    {code: 'ma', name_ar: 'المغرب', name_en: 'Morocco', dial_code: '+212'},
    {code: 'dz', name_ar: 'الجزائر', name_en: 'Algeria', dial_code: '+213'},
    {code: 'tn', name_ar: 'تونس', name_en: 'Tunisia', dial_code: '+216'},
    {code: 'ly', name_ar: 'ليبيا', name_en: 'Libya', dial_code: '+218'},
    {code: 'sd', name_ar: 'السودان', name_en: 'Sudan', dial_code: '+249'},
    {code: 'iq', name_ar: 'العراق', name_en: 'Iraq', dial_code: '+964'},
    {code: 'sy', name_ar: 'سوريا', name_en: 'Syria', dial_code: '+963'},
    {code: 'ye', name_ar: 'اليمن', name_en: 'Yemen', dial_code: '+967'},
    {code: 'ps', name_ar: 'فلسطين', name_en: 'Palestine', dial_code: '+970'},
    {code: 'us', name_ar: 'الولايات المتحدة', name_en: 'United States', dial_code: '+1'},
    {code: 'gb', name_ar: 'المملكة المتحدة', name_en: 'United Kingdom', dial_code: '+44'},
    {code: 'de', name_ar: 'ألمانيا', name_en: 'Germany', dial_code: '+49'},
    {code: 'fr', name_ar: 'فرنسا', name_en: 'France', dial_code: '+33'},
    {code: 'it', name_ar: 'إيطاليا', name_en: 'Italy', dial_code: '+39'},
    {code: 'es', name_ar: 'إسبانيا', name_en: 'Spain', dial_code: '+34'},
    {code: 'ca', name_ar: 'كندا', name_en: 'Canada', dial_code: '+1'},
    {code: 'au', name_ar: 'أستراليا', name_en: 'Australia', dial_code: '+61'},
    {code: 'in', name_ar: 'الهند', name_en: 'India', dial_code: '+91'},
    {code: 'pk', name_ar: 'باكستان', name_en: 'Pakistan', dial_code: '+92'},
    {code: 'tr', name_ar: 'تركيا', name_en: 'Turkey', dial_code: '+90'},
    {code: 'ir', name_ar: 'إيران', name_en: 'Iran', dial_code: '+98'}
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

            // إغلاق القائمة المنسدلة
            const dropdown = new bootstrap.Dropdown(document.querySelector('.country-select'));
            dropdown.hide();
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

    // إرسال نموذج المساعدة
    const helpForm = document.getElementById('helpForm');
    if (helpForm) {
        helpForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.reset();
                } else {
                    alert('حدث خطأ أثناء إرسال الطلب');
                }
            })
            .catch(err => {
                console.error(err);
                alert('حدث خطأ أثناء إرسال الطلب');
            });
        });
    }

    // تهيئة الدولة الافتراضية
    updateSelectedCountry();
});
</script>
@endpush
