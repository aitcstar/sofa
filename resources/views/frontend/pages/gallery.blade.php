@extends('frontend.layouts.pages')

@section('title', 'المعرض - SOFA Experience')
@section('description', 'تصفح معرض مشاريع SOFA Experience في تأثيث الوحدات الفندقية والسكنية')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('gallery.index') : route('gallery.index.en') }}" class="body-2 text-primary">المعرض</a>
</div>

<!-- ===== GALLERY SECTION ===== -->
<section class="gallery-section">
    <div class="container d-flex flex-column gap-md">
        <!-- Heading -->
        <div class="gallery-heading d-flex flex-column gap-sm-5 align-items-center">
            <h2 class="heading-h7 text-heading">معرض مشاريعنا</h2>
            <p class="body-2 text-caption mb-0">نحن لا نعرض أثاثاً فقط، بل نروي قصة كل وحدة فندقية أضفنا لها لمسة من الفخامة
                والتناغم. كل مشروع يعكس تنسيق ألوان مدروس، وتوزيع أثاث عملي، وجودة تنفيذ احترافية في أقل وقت</p>
        </div>

        <!-- Filter (Tabs) -->
        <div class="gallery-filter">
            <div class="gallery-filter-item active">
                <p class="sub-heading-5 text-body mb-0">جميع المشاريع</p>
            </div>
            <div class="gallery-filter-item">
                <p class="sub-heading-5 text-body mb-0">المعيشة</p>
            </div>
            <div class="gallery-filter-item">
                <p class="sub-heading-5 text-body mb-0">النوم</p>
            </div>
            <div class="gallery-filter-item">
                <p class="sub-heading-5 text-body mb-0">المطبخ</p>
            </div>
            <div class="gallery-filter-item">
                <p class="sub-heading-5 text-body mb-0">أحدث المشاريع</p>
            </div>
            <div class="gallery-filter-item">
                <p class="sub-heading-5 text-body mb-0">مشاريع جاهزة للسكن</p>
            </div>
        </div>

        <!-- Gallery -->
        <div class="gallery-grid">
            <!-- Item 1 -->
            <div class="gallery-item">
                <!-- image & widget -->
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <!-- content -->
                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <!-- heading -->
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

                    <!-- feature item 1 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <!-- feature item 2 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <!-- feature item 3 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <!-- feature item 4 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <!-- feature item 5 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="gallery-item">
                <!-- image & widget -->
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <!-- content -->
                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <!-- heading -->
                    <div class="d-flex flex-column gap-sm-6">
                        <a class="sub-heading-4" href="{{ app()->getLocale() == 'ar' ? route('gallery.details', [2]) : route('gallery.details.en', [2]) }}">فندق المها – الرياض</a>
                        <div class="d-flex gap-sm-5 align-items-center">
                            <p class="body-3 text-subheading mb-0">باكج غرفة نوم واحدة</p>

                            <div class="d-flex gap-sm-6 align-items-center"
                                style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Bed" />
                                <p class="body-4 text-subheading mb-0">60 قطعة</p>
                            </div>
                        </div>
                    </div>

                    <!-- feature item 1 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <!-- feature item 2 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <!-- feature item 3 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <!-- feature item 4 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <!-- feature item 5 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="gallery-item">
                <!-- image & widget -->
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <!-- content -->
                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <!-- heading -->
                    <div class="d-flex flex-column gap-sm-6">
                        <a class="sub-heading-4" href="{{ app()->getLocale() == 'ar' ? route('gallery.details', [3]) : route('gallery.details.en', [3]) }}">فندق المها – الرياض</a>
                        <div class="d-flex gap-sm-5 align-items-center">
                            <p class="body-3 text-subheading mb-0">باكج غرفة نوم واحدة</p>

                            <div class="d-flex gap-sm-6 align-items-center"
                                style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Bed" />
                                <p class="body-4 text-subheading mb-0">60 قطعة</p>
                            </div>
                        </div>
                    </div>

                    <!-- feature item 1 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <!-- feature item 2 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <!-- feature item 3 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <!-- feature item 4 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <!-- feature item 5 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 4 -->
            <div class="gallery-item">
                <!-- image & widget -->
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <!-- content -->
                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <!-- heading -->
                    <div class="d-flex flex-column gap-sm-6">
                        <a class="sub-heading-4" href="{{ app()->getLocale() == 'ar' ? route('gallery.details', [4]) : route('gallery.details.en', [4]) }}">فندق المها – الرياض</a>
                        <div class="d-flex gap-sm-5 align-items-center">
                            <p class="body-3 text-subheading mb-0">باكج غرفة نوم واحدة</p>

                            <div class="d-flex gap-sm-6 align-items-center"
                                style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Bed" />
                                <p class="body-4 text-subheading mb-0">60 قطعة</p>
                            </div>
                        </div>
                    </div>

                    <!-- feature item 1 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <!-- feature item 2 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <!-- feature item 3 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <!-- feature item 4 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <!-- feature item 5 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 5 -->
            <div class="gallery-item">
                <!-- image & widget -->
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <!-- content -->
                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <!-- heading -->
                    <div class="d-flex flex-column gap-sm-6">
                        <a class="sub-heading-4" href="{{ app()->getLocale() == 'ar' ? route('gallery.details', [5]) : route('gallery.details.en', [5]) }}">فندق المها – الرياض</a>
                        <div class="d-flex gap-sm-5 align-items-center">
                            <p class="body-3 text-subheading mb-0">باكج غرفة نوم واحدة</p>

                            <div class="d-flex gap-sm-6 align-items-center"
                                style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Bed" />
                                <p class="body-4 text-subheading mb-0">60 قطعة</p>
                            </div>
                        </div>
                    </div>

                    <!-- feature item 1 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <!-- feature item 2 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <!-- feature item 3 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <!-- feature item 4 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <!-- feature item 5 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 6 -->
            <div class="gallery-item">
                <!-- image & widget -->
                <div class="gallery-item-image">
                    <div class="gallery-item-widget">
                        <p class="body-4 mb-0 text-white">تم التسليم: مارس 2025</p>
                    </div>
                    <img src="{{ asset('assets/images/gallery/gallery-01.jpg') }}" alt="Gallery Image" />
                </div>

                <!-- content -->
                <div class="gallery-item-content d-flex flex-column gap-sm-5">
                    <!-- heading -->
                    <div class="d-flex flex-column gap-sm-6">
                        <a class="sub-heading-4" href="{{ app()->getLocale() == 'ar' ? route('gallery.details', [6]) : route('gallery.details.en', [6]) }}">فندق المها – الرياض</a>
                        <div class="d-flex gap-sm-5 align-items-center">
                            <p class="body-3 text-subheading mb-0">باكج غرفة نوم واحدة</p>

                            <div class="d-flex gap-sm-6 align-items-center"
                                style="border: 1px solid var(--surface-border); border-radius: var(--radius-small-box-2); padding: 2px 14px;">
                                <img src="{{ asset('assets/images/gallery/icon-01.svg') }}" alt="Bed" />
                                <p class="body-4 text-subheading mb-0">60 قطعة</p>
                            </div>
                        </div>
                    </div>

                    <!-- feature item 1 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الألوان</p>
                            <p class="body-4 text-subheading mb-0">بيج، أبيض، بني (دافئة)</p>
                        </div>
                    </div>

                    <!-- feature item 2 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المعيشة</p>
                            <p class="body-4 text-subheading mb-0">فاخرة صغيرة – كنبة + كراسي + طاولات + لوحة</p>
                        </div>
                    </div>

                    <!-- feature item 3 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/kitchen.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">المطبخ</p>
                            <p class="body-4 text-subheading mb-0">فاخر بلس – جزيرة وإضاءة وأجهزة مدمجة</p>
                        </div>
                    </div>

                    <!-- feature item 4 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/tv.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">التلفزيون</p>
                            <p class="body-4 text-subheading mb-0">تصميم خشبي كلاسيكي</p>
                        </div>
                    </div>

                    <!-- feature item 5 -->
                    <div class="gallery-item-feature-item d-flex gap-sm-5 align-items-center">
                        <img src="{{ asset('assets/images/gallery/cabinet.svg') }}" alt="Bed" />
                        <div class="d-flex gap-sm-5">
                            <p class="body-3 mb-0">الخزائن</p>
                            <p class="body-4 text-subheading mb-0">تصميم عصري مفتوح – (200x240 / 150x240 / 80x240)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/gallery.css') }}">
<style>

</style>
@endpush

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
