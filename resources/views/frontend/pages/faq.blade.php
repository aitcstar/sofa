@extends('frontend.layouts.pages')

@section('title', 'الأسئلة الشائعة - SOFA Experience')
@section('description', 'كل ما تحتاج معرفته قبل الطلب، جمعناه لك هنا باختصار وشفافية')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('faq') : route('faq.en') }}" class="body-2 text-primary">الأسئلة الشائعة</a>
</div>

<!-- ===== FAQ SECTION ===== -->
<section class="faq">
    <div class="container">
        <!-- Heading -->
        <div class="d-flex flex-column gap-sm-5 mx-auto text-center mb-5">
            <h1 class="heading-h6 mb-0">أسئلتك… نُجيب عليها بكل وضوح</h1>
            <p class="body-2 mb-0">
                كل ما تحتاج معرفته قبل الطلب، جمعناه لك هنا باختصار وشفافية
            </p>
        </div>

        <!-- Content -->
        <div class="faq-content">
            <!-- List -->
            <div class="faq-list">
                @foreach($faqCategories as $index => $category)
                <div class="faq-item {{ $category['active'] ?? false ? 'active' : '' }}" data-category="{{ $index }}">
                    <p class="sub-heading-3 text-body mb-0">{{ $category['name'] }}</p>
                </div>
                @endforeach
            </div>

            <!-- Accordion -->
            <div class="accordion" id="faqAccordion">
                @foreach($faqCategories as $categoryIndex => $category)
                    @foreach($category['faqs'] as $faqIndex => $faq)
                    <div class="accordion-item" data-category="{{ $categoryIndex }}">
                        <div class="accordion-header">
                            <button class="accordion-button {{ ($faq['active'] ?? false) && ($categoryIndex === 0) ? '' : 'collapsed' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $categoryIndex }}{{ $faqIndex }}"
                                    aria-expanded="{{ ($faq['active'] ?? false) && ($categoryIndex === 0) ? 'true' : 'false' }}"
                                    aria-controls="collapse{{ $categoryIndex }}{{ $faqIndex }}">
                                <p class="sub-heading-3 mb-0">{{ $faq['question'] }}</p>
                            </button>
                        </div>
                        <div id="collapse{{ $categoryIndex }}{{ $faqIndex }}"
                             class="accordion-collapse collapse {{ ($faq['active'] ?? false) && ($categoryIndex === 0) ? 'show' : '' }}"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="body-2 text-body mb-0">
                                    {{ $faq['answer'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/faq.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // فلترة الأسئلة حسب التصنيف
    const faqItems = document.querySelectorAll('.faq-item');
    const accordionItems = document.querySelectorAll('.accordion-item');

    faqItems.forEach(item => {
        item.addEventListener('click', function() {
            const category = this.getAttribute('data-category');

            // إزالة النشاط من جميع العناصر
            faqItems.forEach(i => i.classList.remove('active'));
            // إضافة النشاط للعنصر المحدد
            this.classList.add('active');

            // إظهار/إخفاء الأسئلة حسب التصنيف
            accordionItems.forEach(accordionItem => {
                if (accordionItem.getAttribute('data-category') === category) {
                    accordionItem.style.display = 'block';
                } else {
                    accordionItem.style.display = 'none';
                }
            });
        });
    });

    // عرض التصنيف الأول افتراضيًا
    if (faqItems.length > 0) {
        faqItems[0].click();
    }
});
</script>
@endpush
