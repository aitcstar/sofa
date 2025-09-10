@extends('frontend.layouts.pages')

@section('title', 'طلب بمساعدة - SOFA Experience')
@section('description', 'تواصل معنا اليوم للحصول على باكجاتنا الفندقية الجاهزة باحترافية وسرعة')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home',['locale' => app()->getLocale()]) }}" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-1 text-primary">طلب بمساعدة</a>
</div>

<!-- ===== HELP SECTION ===== -->
<section class="help">
    <div class="container">
        <!-- heading -->
        <div class="d-flex flex-column gap-sm-4">
            <h1 class="heading-h7 mb-0">هل أنت جاهز لبدء تأثيث مشروعك؟</h1>
            <p class="body-2 mb-0">تواصل معنا اليوم للحصول على باكجاتنا الفندقية الجاهزة باحترافية وسرعة</p>
        </div>

        <!-- form -->
        <div class="help-form">
            <form action="{{ route('help.submit',['locale' => app()->getLocale()]) }}" method="POST" class="d-flex flex-column gap-sm-3" id="helpForm">
                @csrf
                <!-- Name -->
                <div class="form-group">
                    <input type="text" class="form-control" id="name" name="name" placeholder="الاسم الكامل" required />
                </div>

                <!-- Company Name -->
                <div class="form-group">
                    <input type="text" class="form-control" id="company-name" name="company" placeholder="إسم الشركة (ان وجد)" />
                </div>

                <!-- Email -->
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="البريد الإلكتروني" required />
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
                                    <input type="text" class="form-control" placeholder="ابحث هنا" id="countrySearch" />
                                    <i class="input-icon">
                                        <img src="{{ asset('assets/images/icons/search-normal.png') }}" alt="بحث" />
                                    </i>
                                </div>
                            </li>
                            @foreach($countries as $country)
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="#"
                                    data-flag="{{ $country['code'] }}" data-code="{{ $country['dial_code'] }}">
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
                        <input type="tel" class="phone-number" name="phone" dir="rtl" placeholder="مثال 5xxxxxxx" style="height: 50px" required />
                        <input type="hidden" name="country_code" id="countryCode" value="+966">
                    </div>
                </div>

                <!-- Number of units -->
                <div class="form-group">
                    <input type="number" class="form-control" id="units" name="units" placeholder="عدد الوحدات / حجم المشروع" required />
                </div>

                <!-- Message -->
                <div class="form-group">
                    <textarea class="form-control" id="message" name="message" placeholder="ملاحظات اضافية او طلبات خاصة" rows="3"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-custom-primary">
                    أرسل الطلب
                </button>

                <!-- Helper text -->
                <p class="body-3 mb-0 text-secondary">
                    سوف يتم التواصل معكم خلال 48 ساعة
                </p>
            </form>
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

            // التحقق من صحة البيانات
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.querySelector('.phone-number').value;
            const units = document.getElementById('units').value;

            if (!name || !email || !phone || !units) {
                alert('يرجى ملء جميع الحقول الإلزامية');
                return;
            }

            // إرسال البيانات عبر AJAX
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('تم إرسال طلبك بنجاح! سنتواصل معك خلال 48 ساعة.');
                    this.reset();
                    updateSelectedCountry(); // إعادة تعيين الدولة الافتراضية
                } else {
                    alert('حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.');
            });
        });
    }

    // تهيئة الدولة الافتراضية
    updateSelectedCountry();
});
</script>
@endpush
