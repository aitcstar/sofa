@extends('frontend.layouts.pages')

@section('title', 'من نحن - SOFA Experience')
@section('description', 'تعرف على رؤية وقيم SOFA Experience في تقديم حلول التأثيث الفندقي الذكية')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home') }}" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('about') }}" class="body-2 text-primary">من نحن</a>
</div>

<!-- ===== OUR VISION SECTION ===== -->
<section class="our-vision">
    <div class="container position-relative">
        <!-- Pattern -->
        <img class="our-vision-pattern" src="{{ asset('assets/images/about/pattern.svg') }}" alt="Pattern" />

        <!-- Content -->
        <div class="our-vision-content d-flex flex-column gap-md">
            <!-- Heading -->
            <div class="d-flex flex-column gap-sm-5">
                <h6 class="heading-h6">رؤيتنا</h6>
                <p class="caption-4 mb-0">
                     نطمح إلى أن نكون المنصة الأذكى والأكثر احترافًا في تأثيث الوحدات السكنية والفندقية في السوق السعودي والعربي
                </p>
            </div>

            <!-- List -->
            <ul class="d-flex flex-column gap-sm-4">
                <li class="sub-heading-4">
                    تقديم حلول جاهزة وسريعة
                </li>
                <li class="sub-heading-4">
                    ضمان الجودة والشفافية
                </li>
                <li class="sub-heading-4">
                    تمكين العميل من التحكم الكامل بتجربة التأثيث
                </li>
            </ul>
        </div>

        <!-- Image -->
        <div class="our-vision-image">
            <img src="{{ asset('assets/images/about/about-01.jpg') }}" alt="رؤيتنا" />
        </div>
    </div>
</section>

<!-- ===== OUR VALUES SECTION ===== -->
<section class="our-values">
    <div class="container position-relative">
        <!-- Pattern -->
        <img class="our-values-pattern" src="{{ asset('assets/images/about/pattern.svg') }}" alt="Pattern" />

        <!-- Image -->
        <div class="our-values-image">
            <img src="{{ asset('assets/images/about/about-02.jpg') }}" alt="قيمنا" />
        </div>

        <!-- Content -->
        <div class="our-values-content d-flex flex-column gap-md">
            <!-- Heading -->
            <div class="d-flex flex-column gap-sm-5">
                <h6 class="heading-h6">قيمنا</h6>
                <p class="caption-4 mb-0" style="max-width: 274px;">
                    قيمنا ليست شعارات… بل التزام حقيقي ينعكس في جودة التنفيذ ورضا العميل.
                </p>
            </div>

            <!-- List -->
            <ul class="list-unstyled d-flex flex-column gap-sm-4">
                <li class="sub-heading-4">
                    تنفيذ احترافي بجودة عالية في كل مرة
                </li>
                <li class="sub-heading-4">
                    الالتزام بالجودة والوقت
                </li>
                <li class="sub-heading-4">
                    الشفافية في التسعير والتعامل
                </li>
                <li class="sub-heading-4">
                    راحة العميل أولويتنا
                </li>
                <li class="sub-heading-4">
                    التكامل بين التصميم والتنفيذ
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- ===== WHY SOFA SECTION ===== -->
<section class="why-sofa bg-primary">
    <div class="d-flex">
        <!-- Content -->
        <div class="why-sofa-content container d-flex flex-column justify-content-center gap-md position-relative">
            <!-- pattern -->
            <img class="why-sofa-pattern" src="{{ asset('assets/images/about/pattern.svg') }}" alt="Pattern" />

            <!-- Heading -->
            <div class="d-flex flex-column gap-sm-5">
                <h2 class="heading-h6 text-white">لماذا SOFA؟</h2>
                <p class="caption-4 mb-0"> SOFA ليست مجرد منصة بيع أثاث… بل شريك تأثيث فندقي ذكي</p>
            </div>

            <!-- List -->
            <ul class="list-unstyled d-flex flex-column gap-sm-4">
                <li class="body-3 mb-0">
                    باكجات تصميم جاهزة
                </li>
                <li class="body-3 mb-0">
                    تجربة طلب ذكية بالكامل
                </li>
                <li class="body-3 mb-0">
                    متابعة الطلب من البداية حتى التركيب
                </li>
                <li class="body-3 mb-0">
                    خيارات تشطيب تناسب كل وحدة
                </li>
            </ul>
        </div>

        <!-- Image -->
        <div class="why-sofa-image flex-1">
            <img src="{{ asset('assets/images/about/about-03.jpg') }}" alt="لماذا SOFA" />
        </div>
    </div>
</section>

<!-- ===== OUR SMART STEPS SECTION ===== -->
<section class="our-smart-steps">
    <div class="container">
        <!-- info -->
        <div class="our-smart-steps-info position-relative">
            <!-- pattern -->
            <img class="pattern" src="{{ asset('assets/images/about/pattern.svg') }}" alt="Pattern" />

            <!-- info item -->
            <div class="our-smart-steps-info-item">
                <!-- avatar -->
                <div class="our-smart-steps-info-avatar">
                    <img src="{{ asset('assets/images/about/avatar.jpg') }}" alt="Avatar" />
                </div>

                <!-- content -->
                <div class="our-smart-steps-info-content">
                    <p class="sub-heading-5 text-white mb-0">م. أحمد ابراهيم</p>
                    <p class="body-4 text-white mb-0">2 وحدات</p>
                </div>
            </div>

            <!-- info item -->
            <div class="our-smart-steps-info-item">
                <!-- content -->
                <div class="our-smart-steps-info-content d-flex flex-column gap-sm-4">
                    <p class="body-4 text-white mb-0" style="opacity: 0.8">حالة الطلب الحالية</p>
                    <p class="body-4 text-white mb-0">جاري التركيب 80%</p>
                    <div class="our-smart-steps-info-progress">
                        <div class="our-smart-steps-info-progress-bar" style="width: 80%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- steps -->
        <div class="our-smart-steps-steps d-flex flex-column gap-md">
            <!-- heading -->
            <div class="our-smart-steps-heading">
                <h2 class="heading-h6">خطواتنا الذكية</h2>
                <p class="caption-3 mb-0">رحلة تأثيث تبدأ بفهم مشروعك وتنتهي بوحدة جاهزة</p>
            </div>

            <!-- list -->
            <ul class="our-smart-list list-unstyled d-flex flex-column gap-sm-4 p-0">
                <!-- step 1 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-01.svg') }}" alt="Step 1" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">المشروع غير مؤثث</p>
                    </div>
                </li>

                <!-- step 2 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-02.svg') }}" alt="Step 2" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">تحديد نوع الوحدة</p>
                    </div>
                </li>

                <!-- step 3 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-03.svg') }}" alt="Step 3" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">مراجعة التصميم والتشطيب</p>
                    </div>
                </li>

                <!-- step 4 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-04.svg') }}" alt="Step 4" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">اختيار الباكج</p>
                    </div>
                </li>

                <!-- step 5 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-05.svg') }}" alt="Step 5" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">تنفيذ الطلب (تصميم – تصنيع – شحن)</p>
                    </div>
                </li>

                <!-- step 6 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-06.svg') }}" alt="Step 6" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">التركيب</p>
                    </div>
                </li>

                <!-- step 7 -->
                <li class="d-flex gap-sm-3 align-items-center">
                    <div class="our-smart-steps-step-icon">
                        <img src="{{ asset('assets/images/about/step-07.svg') }}" alt="Step 7" />
                    </div>

                    <!-- content -->
                    <div class="our-smart-steps-step-content">
                        <p class="sub-heading-5 mb-0">المشروع مؤثث وجاهز للسكن</p>
                    </div>
                </li>
            </ul>

            <!-- start button -->
            <div class="our-smart-steps-start-button">
                <a href="{{ route('categories.index') }}" class="btn btn-custom-secondary w-100">
                    ابدأ الآن و استلم عرض السعر
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ===== READY TO FURNISH YOUR UNIT SECTION ===== -->
<section class="ready-to-furnish-your-unit">
    <div class="ready-to-furnish-your-unit-container container position-relative bg-primary">
        <!-- pattern -->
        <img class="ready-to-furnish-your-unit-pattern" src="{{ asset('assets/images/about/pattern.svg') }}" alt="Pattern" />

        <!-- content -->
        <div class="ready-to-furnish-your-unit-content d-flex flex-column gap-sm">
            <div class="d-flex flex-column gap-sm-5">
                <h2 class="heading-h6 text-white">جاهز لتأثيث وحدتك؟</h2>
                <p class="caption-4 mb-0">
                    ابدأ خطوتك الأولى الآن… باكجاتنا جاهزة للتنفيذ والتسليم في وقت قياسي
                </p>
            </div>

            <!-- buttons -->
            <div class="ready-to-furnish-your-unit-buttons d-flex gap-sm-3">
                <a href="https://wa.me/{{$siteSettings->whatsapp}}" target="_blank" class="btn btn-custom-secondary d-flex align-items-center gap-2">
                    <p class="text-nowrap mb-0">تحدث معنا عبر واتساب</p>
                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                </a>
                <a href="{{ route('contact.index') }}" class="btn border-btn">
                    <p class="text-nowrap mb-0">اطلب الان</p>
                    <i class="fa-solid fa-arrow-left" style="font-size: 18px;"></i>
                </a>
            </div>
        </div>

        <!-- image -->
        <div class="ready-to-furnish-your-unit-image-container">
            <div class="ready-to-furnish-your-unit-image">
                <img src="{{ asset('assets/images/about/about-05.jpg') }}" alt="جاهز لتأثيث وحدتك" />
            </div>
        </div>
    </div>
</section>

<!-- ===== WHY DO WE SERVE SECTION ===== -->
<section class="why-do-we-serve">
    <div class="container">
        <!-- Content -->
        <div class="why-do-we-serve-content d-flex flex-column gap-md">
            <!-- Heading -->
            <div class="d-flex flex-column gap-sm-5">
                <h6 class="heading-h6">نخدم من؟</h6>
                <p class="caption-4 mb-0">حلول تأثيث مرنة وسريعة لأصحاب الشقق، المطورين، والمشغّلين في قطاع السكن والضيافة.
                </p>
            </div>

            <!-- List -->
            <ul class="d-flex flex-column gap-sm-4">
                <li class="sub-heading-4">
                    أصحاب الشقق
                </li>
                <li class="sub-heading-4">
                    المطورين العقاريين
                </li>
                <li class="sub-heading-4">
                    مشغلي الشقق الفندقية
                </li>
                <li class="sub-heading-4">المستثمرين العقاريين</li>
            </ul>
        </div>

        <!-- images -->
        <div class="why-do-we-serve-images d-flex gap-sm-3">
            <div class="d-flex flex-column gap-sm-3 mt-4">
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-06.jpg') }}" alt="Why Do We Serve" />
                </div>
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-07.jpg') }}" alt="Why Do We Serve" />
                </div>
            </div>
            <div class="d-flex flex-column gap-sm-3">
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-08.jpg') }}" alt="Why Do We Serve" />
                </div>
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-09.jpg') }}" alt="Why Do We Serve" />
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/about.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/pages/about.js') }}"></script>
@endpush
