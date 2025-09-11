@extends('frontend.layouts.pages')

@section('title', $pageData['title'])
@section('description', $pageData['description'])

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('gallery.index') : route('gallery.index.en') }}" class="body-2 text-body">المعرض</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">تفاصيل المشروع</a>
</div>

<!-- ===== GALLERY HEADING SECTION ===== -->
<section class="gallery-heading-section">
    <div class="container">
        <div class="gallery-heading-container d-flex flex-column text-center gap-md">
            <!-- Heading -->
            <div class="gallery-heading-title d-flex flex-column gap-sm-5">
                <h2 class="heading-h7 text-heading">{{ $pageData['project']['title'] }}</h2>
                <p class="caption-4 text-caption mb-0 mx-auto" style="max-width: 712px;">
                    تم تنفيذ المشروع في فندق المها بمدينة الرياض باستخدام باكج جاهز لغرفة نوم واحدة من SOFA، مع تخصيصات بسيطة تناسب الطابع الفندقي. تنسيق الألوان المستخدم هو {{ $pageData['project']['colors'] }}، وتم التسليم في {{ $pageData['project']['delivery_date'] }}
                </p>

                <div class="gallery-features d-flex gap-sm-4 mx-auto">
                    <div class="gallery-feature-item d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/user.svg') }}" alt="Delivery" />
                        <p class="body-2 text-body mb-0" style="white-space: nowrap;">{{ $pageData['project']['type'] }}</p>
                    </div>
                    <div class="gallery-feature-item d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/book-mark.svg') }}" alt="Delivery" />
                        <p class="body-2 text-body mb-0" style="white-space: nowrap;">{{ $pageData['project']['area'] }}</p>
                    </div>
                    <div class="gallery-feature-item d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/user.svg') }}" alt="Delivery" />
                        <p class="body-2 text-body mb-0" style="white-space: nowrap;">{{ $pageData['project']['kitchen'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="gallery-heading-images">
                <div class="gallery-heading-image-grid">
                    <!-- item 1 -->
                    <div class="gallery-heading-image-item">
                        <div class="gallery-heading-image-sub-item gallery-large-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                        </div>
                    </div>

                    <!-- item 2 -->
                    <div class="gallery-heading-image-item d-flex flex-column gap-sm-3">
                        <div class="gallery-heading-image-sub-item gallery-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-02.jpg') }}" alt="Gallery Image" />
                        </div>
                        <div class="gallery-heading-image-sub-item gallery-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-02.jpg') }}" alt="Gallery Image" />
                        </div>
                    </div>

                    <!-- item 3 -->
                    <div class="gallery-heading-image-item d-flex flex-column gap-sm-3">
                        <div class="gallery-heading-image-sub-item gallery-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-03.jpg') }}" alt="Gallery Image" />
                        </div>
                        <div class="gallery-heading-image-sub-item gallery-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-03.jpg') }}" alt="Gallery Image" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== GALLERY DETAILS SECTION ===== -->
<section class="gallery-details-section">
    <div class="container">
        <div class="gallery-details-container d-flex flex-column text-center gap-md">
            <!-- Heading -->
            <div class="gallery-details-title d-flex flex-column gap-sm-5">
                <h2 class="heading-h7 text-heading">تفاصيل الباكج</h2>
                <p class="caption-4 text-caption mb-0 mx-auto" style="max-width: 712px;">
                    الباكج المستخدم يحتوي على {{ $pageData['project']['pieces_count'] }}، ويشمل الأثاث الكامل لغرفة نوم واحدة، المعيشة، والمطبخ. تم اختيار {{ $pageData['project']['tv_design'] }} للتلفزيون وتصميم عمراني مفتوح لطاولة الطعام
                </p>
            </div>

            <!-- images -->
            <div class="gallery-details-images">
                <div class="gallery-details-image-grid">
                    <div class="gallery-details-image-item">
                        <div class="gallery-details-image-sub-item gallery-large-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                            <div class="image-overlay">
                                <span class="sub-heading-2">غرفة النوم</span>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-details-image-item d-flex gap-sm">
                        <div class="gallery-details-image-sub-item gallery-small-img" style="flex: 0.5">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                            <div class="image-overlay">
                                <span class="sub-heading-2">المعيشة</span>
                            </div>
                        </div>
                        <div class="gallery-details-image-sub-item gallery-small-img" style="flex: 0.5">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                            <div class="image-overlay">
                                <span class="sub-heading-2">المطبخ</span>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-details-image-item">
                        <div class="gallery-details-image-sub-item gallery-large-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                            <div class="image-overlay">
                                <span class="sub-heading-2">التفاصيل</span>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-details-image-item">
                        <div class="gallery-details-image-sub-item gallery-large-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                            <div class="image-overlay">
                                <span class="sub-heading-2">الديكور</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== STEPS IMPLEMENT PROJECT SECTION ===== -->
<section class="steps-implement-project-section">
    <div class="container">
        <div class="steps-implement-project-container d-flex flex-column text-center gap-md">
            <!-- Heading -->
            <div class="gallery-details-title d-flex flex-column gap-sm-5">
                <h2 class="heading-h7 text-heading">خطوات تنفيذ المشروع</h2>
                <p class="caption-4 text-caption mb-0 mx-auto">اعتمد المشروع على باكج جاهز من SOFA، وتم تخصيصه حسب المساحة والتشطيب. شملت الخدمة</p>
            </div>

            <!-- steps -->
            <div class="steps-implement-project-steps d-flex gap-md">
                <div class="steps-implement-project-step-item d-flex flex-column align-items-center gap-sm-4">
                    <div class="steps-implement-project-step-icon">
                        <img src="{{ asset('assets/images/gallery/step-01.svg') }}" alt="Gallery Image" />
                    </div>
                    <div class="steps-implement-project-step-content">
                        <p class="body-2 text-subheading mb-0">تحديد الوحدة ونوع الاستخدام الفندقي</p>
                    </div>
                </div>
                <div class="steps-implement-project-step-item d-flex flex-column align-items-center gap-sm-4">
                    <div class="steps-implement-project-step-icon">
                        <img src="{{ asset('assets/images/gallery/step-02.svg') }}" alt="Gallery Image" />
                    </div>
                    <div class="steps-implement-project-step-content">
                        <p class="body-2 text-subheading mb-0">اختيار الألوان الدافئة المناسبة للضيوف</p>
                    </div>
                </div>
                <div class="steps-implement-project-step-item d-flex flex-column align-items-center gap-sm-4">
                    <div class="steps-implement-project-step-icon">
                        <img src="{{ asset('assets/images/gallery/step-03.svg') }}" alt="Gallery Image" />
                    </div>
                    <div class="steps-implement-project-step-content">
                        <p class="body-2 text-subheading mb-0">اعتماد الباكج بناءً على المساحة والوظيفة</p>
                    </div>
                </div>
                <div class="steps-implement-project-step-item d-flex flex-column align-items-center gap-sm-4">
                    <div class="steps-implement-project-step-icon">
                        <img src="{{ asset('assets/images/gallery/step-04.svg') }}" alt="Gallery Image" />
                    </div>
                    <div class="steps-implement-project-step-content">
                        <p class="body-2 text-subheading mb-0">التنفيذ (تصنيع – تجهيز – شحن)</p>
                    </div>
                </div>
                <div class="steps-implement-project-step-item d-flex flex-column align-items-center gap-sm-4">
                    <div class="steps-implement-project-step-icon">
                        <img src="{{ asset('assets/images/gallery/step-05.svg') }}" alt="Gallery Image" />
                    </div>
                    <div class="steps-implement-project-step-content">
                        <p class="body-2 text-subheading mb-0">التركيب والتسليم في الموقع</p>
                    </div>
                </div>
            </div>

            <!-- images -->
            <div class="steps-implement-project-images">
                <div class="steps-implement-project-image-grid">
                    <div class="steps-implement-project-image-item">
                        <div class="steps-implement-project-image-sub-item steps-implement-project-large-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                        </div>
                    </div>
                    <div class="steps-implement-project-image-item d-flex flex-column gap-sm-3">
                        <div class="steps-implement-project-image-sub-item steps-implement-project-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                        </div>
                        <div class="steps-implement-project-image-sub-item steps-implement-project-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                        </div>
                    </div>
                    <div class="steps-implement-project-image-item d-flex gap-sm-3">
                        <div class="steps-implement-project-image-sub-item steps-implement-project-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                        </div>
                        <div class="steps-implement-project-image-sub-item steps-implement-project-small-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                        </div>
                    </div>
                    <div class="steps-implement-project-image-item">
                        <div class="steps-implement-project-image-sub-item steps-implement-project-large-img">
                            <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                            <div class="image-overlay">
                                <span class="sub-heading-2">التسليم النهائي</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PACKAGE CONTAIN SECTION ===== -->
<section class="package-contain-section">
    <div class="container">
        <div class="package-contain-container d-flex flex-column gap-sm-4">
            <h2 class="heading-h7 text-heading">مكونات وصور الباكج</h2>

            <!-- details -->
            <div class="package-contain-details-grid">
                <!-- item 1 -->
                <div class="package-contain-detail-item d-flex flex-column gap-sm-4">
                    <!-- heading -->
                    <div class="d-flex align-items-center gap-sm-5">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="living-room" />
                        <p class="heading-h9 mb-0">المعيشة</p>
                    </div>

                    <!-- Table -->
                    <div class="package-contain-detail-item-table">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>القطعة</th>
                                        <th>المقاس</th>
                                        <th>الخامة</th>
                                        <th>اللون</th>
                                        <th>الصورة</th>
                                        <th>الكمية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="body-2">كنبة 3 مقاعد</td>
                                        <td class="body-2">2.2×100×70</td>
                                        <td class="body-2">قماش مخملي</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #262626;">أسود</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">كنبة 3 مقاعد</td>
                                        <td class="body-2">2.2×100×70</td>
                                        <td class="body-2">قماش مخملي</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #262626;">أسود</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">كنبة 3 مقاعد</td>
                                        <td class="body-2">2.2×100×70</td>
                                        <td class="body-2">قماش مخmلي</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #262626;">أسود</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">كنبة 3 مقاعد</td>
                                        <td class="body-2">2.2×100×70</td>
                                        <td class="body-2">قماش مخملي</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #262626;">أسود</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- item 2 -->
                <div class="package-contain-detail-item d-flex flex-column gap-sm-4">
                    <!-- heading -->
                    <div class="d-flex align-items-center gap-sm-5">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="living-room" />
                        <p class="heading-h9 mb-0">غرفة النوم الرئيسية</p>
                    </div>

                    <!-- Table -->
                    <div class="package-contain-detail-item-table">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>القطعة</th>
                                        <th>المقاس</th>
                                        <th>الخامة</th>
                                        <th>اللون</th>
                                        <th>الصورة</th>
                                        <th>الكمية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="body-2">سرير مزدوج</td>
                                        <td class="body-2">2.0×180×200</td>
                                        <td class="body-2">خشب طبيعي</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #8B4513;">بني</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">خزانة ملابس</td>
                                        <td class="body-2">2.2×120×60</td>
                                        <td class="body-2">خشب MDF</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #F5F5DC;">أبيض</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">طاولة جانبية</td>
                                        <td class="body-2">0.5×50×50</td>
                                        <td class="body-2">خشب</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #8B4513;">بني</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">2</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">مرآة</td>
                                        <td class="body-2">1.8×80×5</td>
                                        <td class="body-2">زجاج</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #C0C0C0;">فضي</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- item 3 -->
                <div class="package-contain-detail-item d-flex flex-column gap-sm-4">
                    <!-- heading -->
                    <div class="d-flex align-items-center gap-sm-5">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="kitchen" />
                        <p class="heading-h9 mb-0">المطبخ</p>
                    </div>

                    <!-- Table -->
                    <div class="package-contain-detail-item-table">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>القطعة</th>
                                        <th>المقاس</th>
                                        <th>الخامة</th>
                                        <th>اللون</th>
                                        <th>الصورة</th>
                                        <th>الكمية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="body-2">طاولة الطعام</td>
                                        <td class="body-2">0.75×120×80</td>
                                        <td class="body-2">خشب</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #8B4513;">بني</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">كراسي الطعام</td>
                                        <td class="body-2">0.9×45×45</td>
                                        <td class="body-2">خشب وقماش</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #8B4513;">بني</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">4</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">خزائن المطبخ</td>
                                        <td class="body-2">2.2×200×60</td>
                                        <td class="body-2">خشب MDF</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #F5F5DC;">أبيض</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                    <tr>
                                        <td class="body-2">جزيرة المطبخ</td>
                                        <td class="body-2">0.9×120×80</td>
                                        <td class="body-2">رخام</td>
                                        <td class="color-box">
                                            <p class="body-2 text-subheading mb-0" style="background-color: #C0C0C0;">رمادي</p>
                                        </td>
                                        <td class="image-box">
                                            <div class="img-box">
                                                <img src="{{ asset('assets/images/gallery/char.png') }}" alt="Gallery Image" />
                                            </div>
                                        </td>
                                        <td class="body-2">1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/gallery-details.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/pages/gallery-details.js') }}"></script>
@endpush
