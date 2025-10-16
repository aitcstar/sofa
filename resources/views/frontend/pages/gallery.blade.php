@extends('frontend.layouts.pages')

@section('title', 'المعرض - SOFA Experience')
@section('description', 'تصفح معرض مشاريع SOFA Experience في تأثيث الوحدات الفندقية والسكنية')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('gallery.index') : route('gallery.index.en') }}" class="body-2 text-primary">
        {{ app()->getLocale() == 'ar' ? 'المعرض' : 'Gallery' }}
    </a>
</div>

<!-- ===== GALLERY SECTION ===== -->
<section class="gallery-section">
    <div class="container d-flex flex-column gap-md">
        <!-- Heading -->
        <div class="gallery-heading d-flex flex-column gap-sm-5 align-items-center">
            <h2 class="heading-h7 text-heading">
                {{ app()->getLocale() == 'ar' ? $content->title_ar : $content->title_en }}
            </h2>
            <p class="body-2 text-caption mb-0">
                {{ app()->getLocale() == 'ar' ? $content->text_ar : $content->text_en }}
            </p>
        </div>

       <!-- Filter Tabs -->
        <div class="gallery-filter">
            <!-- جميع المشاريع -->
            <div class="gallery-filter-item {{ request('category') ? '' : 'active' }}">
                <a href="{{ route('gallery.index') }}">
                    <p class="sub-heading-5 text-body mb-0">جميع المشاريع</p>
                </a>
            </div>

            <!-- باقي التصنيفات -->
            @foreach($categories as $cat)
                <div class="gallery-filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
                    <a href="{{ route('gallery.index', ['category' => $cat->id]) }}">
                        <p class="sub-heading-5 text-body mb-0">
                            {{ app()->getLocale() == 'ar' ? $cat->name_ar : $cat->name_en }}
                        </p>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Gallery
        <div class="gallery-grid">
            <div class="gallery-item">
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <div class="d-flex flex-column gap-sm-6">
                        <a class="sub-heading-4" href="{{ app()->getLocale() == 'ar' ? route('gallery.details', [1]) : route('gallery.details.en', [1]) }}">فندق المها – الرياض</a>
                        <div class="d-flex gap-sm-5 align-items-center">
                            <p class="body-3 text-subheading mb-0">باكج غرفة نوم واحدة</p>

                            <div class="d-flex gap-sm-6 align-items-center"
                                style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Bed" />
                                <p class="body-4 text-subheading mb-0">60 قطعة</p>
                            </div>
                        </div>
                    </div>

                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->

        <div class="gallery-grid">
            @foreach($exhibitions as $exhibition)
                <div class="gallery-item">

                    {{-- الصورة الرئيسية للمعرض --}}
                    <div class="gallery-item-image">
                        <div class="gallery-item-widget">
                            <p class="body-4 mb-0 text-white">
                                تم التسليم: {{ $exhibition->delivery_date ? \Carbon\Carbon::parse($exhibition->delivery_date)->format('F Y') : '—' }}
                            </p>
                        </div>
                        <a href="{{ app()->getLocale() === 'ar'
                            ? route('gallery.details', $exhibition->id)
                            : route('gallery.details.en', $exhibition->id) }}">
                            {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                            <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}" alt="Exhibition Image" />
                        </a>

                    </div>

                    <div class="gallery-item-content d-flex flex-column gap-sm-5" style="margin: 23px;">
                        @if($exhibition->package)
                            <!-- اسم المعرض واسم الباكج -->
                            <div class="d-flex flex-column gap-sm-6">
                                <a class="sub-heading-4" href="{{ app()->getLocale() === 'ar'
                                    ? route('gallery.details', $exhibition->id)
                                    : route('gallery.details.en', $exhibition->id) }}">
                                    {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                </a>
                                <a class="sub-heading-4" href="{{ app()->getLocale() === 'ar'
                                    ? route('packages.show', $exhibition->package->id)
                                    : route('packages.show.en', $exhibition->package->id) }}">
                                    {{ app()->getLocale() === 'ar' ? $exhibition->package->name_ar : $exhibition->package->name_en }}
                                </a>

                                <!-- الوصف وعدد القطع -->
                                <div class="d-flex gap-sm-5 align-items-center">
                                    <p class="body-3 text-subheading mb-0">
                                        {{ app()->getLocale() === 'ar'
                                            ? ($exhibition->package->description_ar ?: 'وصف الباكج غير متوفر')
                                            : ($exhibition->package->description_en ?: 'Package description not available') }}
                                    </p>
                                    <div class="d-flex gap-sm-6 align-items-center"
                                         style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                        <p class="body-4 text-subheading mb-0">
                                            {{ $exhibition->package->packageUnitItems->count() }}
                                            {{ __('site.piece') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- عرض أول 6 قطع من الباكج -->
                            @php
                                $itemsToShow = $exhibition->package->packageUnitItems->take(6);
                            @endphp

                            @foreach($itemsToShow as $pui)
                                @if($pui->item)
                                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                                        <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Item" />
                                        <div class="d-flex flex-column">
                                            <p class="body-3 mb-0">
                                                {{ app()->getLocale() === 'ar'
                                                    ? $pui->item->item_name_ar
                                                    : $pui->item->item_name_en }}
                                            </p>
                                            <p class="body-4 text-subheading mb-0">
                                                {{ app()->getLocale() === 'ar'
                                                    ? ($pui->item->color_ar . ' - ' . $pui->item->material_ar . ' - ' . $pui->item->dimensions)
                                                    : ($pui->item->color_en . ' - ' . $pui->item->material_en . ' - ' . $pui->item->dimensions) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $exhibitions->appends(request()->query())->links() }}
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/gallery.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Gallery page loaded');
});
</script>

@push('scripts')
<!--<script src="{{ asset('assets/js/pages/gallery.js') }}"></script>-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // كود JavaScript المخصص لصفحة المعرض
    console.log('Gallery page loaded');

    // فلترة المعرض حسب التصنيف
    const filterItems = document.querySelectorAll('.gallery-filter-item');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterItems.forEach(item => {
        item.addEventListener('click', function() {
            // إزالة النشاط من جميع العناصر
            filterItems.forEach(i => i.classList.remove('active'));
            // إضافة النشاط للعنصر الحالي
            this.classList.add('active');

            // هنا يمكن إضافة منطق الفلترة حسب الحاجة
            const filterValue = this.querySelector('p').textContent.trim();
            console.log('Filter by:', filterValue);

            // في الواقع، سيتم إرسال طلب AJAX أو تصفية العناصر
        });
    });

    // يمكن إضافة المزيد من التفاعلات هنا
});
</script>
@endpush
