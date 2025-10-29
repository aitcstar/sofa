@extends('frontend.layouts.pages')


@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('gallery.index') : route('gallery.index.en') }}" class="body-2 text-body">
        {{ app()->getLocale() == 'ar' ? 'المعرض' : 'Gallery' }}
    </a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">
        {{ app()->getLocale() == 'ar' ? 'تفاصيل المشروع' : 'Project Details' }}
    </a>
</div>

<!-- ===== GALLERY HEADING SECTION ===== -->
<section class="gallery-heading-section">
    <div class="container">
        <div class="gallery-heading-container d-flex flex-column text-center gap-md">
            <!-- Heading -->
            <div class="gallery-heading-title d-flex flex-column gap-sm-5">
                <h2 class="heading-h7 text-heading">
                    {{ app()->getLocale() == 'ar' ? $pageData['project']['name_ar'] : $pageData['project']['name_en'] }}
                </h2>
                <p class="caption-4 text-caption mb-0 mx-auto" style="max-width: 712px;">
                    {{ app()->getLocale() == 'ar'
                        ? "تم تنفيذ المشروع في فندق المها بمدينة الرياض باستخدام باكج جاهز لغرفة نوم واحدة من SOFA، مع تخصيصات بسيطة تناسب الطابع الفندقي. تنسيق الألوان المستخدم هو {$pageData['project']['colors'] }، وتم التسليم في {$pageData['project']['delivery_date']}"
                        : "The project was implemented at Al Maha Hotel in Riyadh using a ready package for one bedroom from SOFA, with minor customizations to suit the hotel style. The color scheme used is {$pageData['project']['colors']}, and delivery was completed on {$pageData['project']['delivery_date']}"
                    }}
                </p>

                <div class="gallery-features d-flex gap-sm-4 mx-auto">
                    <div class="gallery-feature-item d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/user.svg') }}" alt="Delivery" />
                        <p class="body-2 text-body mb-0" style="white-space: nowrap;">
                            {{ app()->getLocale() == 'ar' ? $pageData['project']['type_ar'] : $pageData['project']['type_en'] }}
                        </p>
                    </div>
                    <div class="gallery-feature-item d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/book-mark.svg') }}" alt="Delivery" />
                        <p class="body-2 text-body mb-0" style="white-space: nowrap;">
                            {{ app()->getLocale() == 'ar' ? $pageData['project']['area_ar'] : $pageData['project']['area_en'] }}
                        </p>
                    </div>
                    <div class="gallery-feature-item d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/user.svg') }}" alt="Delivery" />
                        <p class="body-2 text-body mb-0" style="white-space: nowrap;">
                            {{ app()->getLocale() == 'ar' ? $pageData['project']['kitchen_ar'] : $pageData['project']['kitchen_en'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="gallery-heading-images-grid">
                @php
                $allImages = $pageData['project']['images'] ?? [];
                $mainImage = null;
                $otherImages = [];

                foreach ($allImages as $img) {
                    if ($img['is_primary'] == 1) {
                        $mainImage = $img['path'];
                    } else {
                        $otherImages[] = $img['path'];
                    }
                }

                // احتياطي: إذا لم توجد صورة رئيسية، خذ الأولى
                if (!$mainImage && !empty($allImages)) {
                    $mainImage = $allImages[0]['path'];
                    $otherImages = array_column(array_slice($allImages, 1), 'path');
                }
            @endphp

            <!-- الصورة الرئيسية -->
            <div class="grid-item grid-item-large">
                @if($mainImage)
                    <img src="{{ asset('storage/' . $mainImage) }}" alt="Main Image">
                @endif
            </div>

            <!-- الصور الصغيرة -->
            <div class="grid-item grid-column-small">
                @foreach(array_slice($otherImages, 0, 2) as $img)
                    <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image">
                @endforeach
            </div>
            <div class="grid-item grid-column-small">
                @foreach(array_slice($otherImages, 2, 2) as $img)
                    <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image">
                @endforeach
            </div>



<style>
.gallery-heading-images-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr; /* العمود الأول أكبر من العمودين الصغيرين */
    gap: 10px;
}

.gallery-heading-images-grid .grid-item-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    display: block;
}

.gallery-heading-images-grid .grid-column-small {
    display: grid;
    grid-template-rows: 1fr 1fr; /* صورتين بنفس الطول */
    gap: 10px;
}

.gallery-heading-images-grid .grid-column-small img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    display: block;
}




    </style>
        </div>
    </div>
</section>

<!-- ===== GALLERY DETAILS SECTION ===== -->
<section class="gallery-details-section">
    <div class="container">
        <div class="gallery-details-container d-flex flex-column text-center gap-md">
            <!-- Heading -->
            <div class="gallery-details-title d-flex flex-column gap-sm-5">
                <h2 class="heading-h7 text-heading">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل الباكج' : 'Package Details' }}
                </h2>
                <p class="caption-4 text-caption mb-0 mx-auto" style="max-width: 712px;">
                    {{ app()->getLocale() == 'ar'
                        ? "الباكج المستخدم يحتوي على {$pageData['project']['pieces_count']} قطعة، ويشمل الأثاث الكامل لغرفة نوم واحدة، المعيشة، والمطبخ. تم اختيار {$pageData['project']['tv_design']} للتلفزيون وتصميم عمراني مفتوح لطاولة الطعام"
                        : "The package contains {$pageData['project']['pieces_count']} pieces, including full furniture for one bedroom, living area, and kitchen. The TV design selected was {$pageData['project']['tv_design']} with an open architectural design for the dining table."
                    }}
                </p>
            </div>

            <!-- Images -->
            <div class="gallery-details-images">
                <div class="gallery-details-image-grid">
                    @foreach($pageData['project']['details_images'] as $detail)
                        <div class="gallery-details-image-item">
                            <div class="gallery-details-image-sub-item">
                                <img src="{{ asset('storage/' . $detail['image_path']) }}" alt="Gallery Image" />
                            </div>
                        </div>
                    @endforeach
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
                <h2 class="heading-h7 text-heading">{{ app()->getLocale() == 'ar' ? 'خطوات تنفيذ المشروع' : 'Project Implementation Steps' }}</h2>
                <p class="caption-4 text-caption mb-0 mx-auto">
                    {{ app()->getLocale() == 'ar' ? 'اعتمد المشروع على باكج جاهز من SOFA، وتم تخصيصه حسب المساحة والتشطيب. شملت الخدمة' : 'The project relied on a ready SOFA package, customized according to space and finishing. The service included' }}
                </p>
            </div>

            <!-- steps -->
            <div class="steps-implement-project-steps d-flex gap-md">
                @foreach($pageData['project']['steps'] as $step)
                    <div class="steps-implement-project-step-item d-flex flex-column align-items-center gap-sm-4">
                        <div class="steps-implement-project-step-icon">
                            <img src="{{ asset('storage/' .$step['icon']) }}" alt="Step Image" />
                        </div>
                        <div class="steps-implement-project-step-content">
                            <p class="body-2 text-subheading mb-0">
                                {{ app()->getLocale() == 'ar' ? $step['title_ar'] : $step['title_en'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- images -->
            <div class="steps-implement-project-images">
                <div class="steps-implement-project-image-grid">
                    @foreach($pageData['project']['steps_images'] as $img)
                        <div class="steps-implement-project-image-item {{ $img['column'] }}">
                            <div class="steps-implement-project-image-sub-item {{ $img['size'] }}">
                                <img src="{{ asset('storage/' .$img['image']) }}" alt="Gallery Image" />
                                @if(isset($img['title_ar']))
                                <div class="image-overlay">
                                    <span class="sub-heading-2">{{ app()->getLocale() == 'ar' ? $img['title_ar'] : $img['title_en'] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PACKAGE CONTAIN SECTION ===== -->

<section class="package-contain-section">
    <div class="container">
        <div class="package-contain-container d-flex flex-column gap-sm-4">
            <h2 class="heading-h7 text-heading">
                {{ app()->getLocale() == 'ar' ? 'مكونات وصور الباكج' : 'Package Contents & Images' }}
            </h2>

            <!-- Details -->
            <div class="package-contain-details-grid">
                @php
                    // تجميع العناصر حسب الوحدة
                    $groupedItems = collect($pageData['project']['packages'])->groupBy('unit_id');
                @endphp

                @foreach($groupedItems as $unitId => $items)
                    @php
                        $unit = $items->first();
                    @endphp
                    <div class="package-contain-detail-item d-flex flex-column gap-sm-4">
                        <!-- Heading -->
                        <div class="d-flex align-items-center gap-sm-5">
                            <p class="heading-h9 mb-0">
                                {{ app()->getLocale() == 'ar' ? $unit['title_ar'] : $unit['title_en'] }}
                            </p>
                        </div>

                        <!-- Table -->
                        <div class="package-contain-detail-item-table">
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>{{ app()->getLocale() == 'ar' ? 'القطعة' : 'Piece' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'المقاس' : 'Size' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'الخامة' : 'Material' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'اللون' : 'Color' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'الصورة' : 'Image' }}</th>
                                            <th>{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Quantity' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="body-2">
                                                    {{ app()->getLocale() == 'ar' ? $item['name_ar'] : $item['name_en'] }}
                                                </td>
                                                <td class="body-2">{{ $item['size'] }}</td>
                                                <td class="body-2">
                                                    {{ app()->getLocale() == 'ar' ? $item['material_ar'] : $item['material_en'] }}
                                                </td>
                                                <td class="color-box">
                                                    <p class="body-2 text-subheading mb-0" style="background-color: {{ $item['color_code'] }};">
                                                        {{ app()->getLocale() == 'ar' ? $item['color_ar'] : $item['color_en'] }}
                                                    </p>
                                                </td>
                                                <td class="image-box">
                                                    <div class="img-box">
                                                        <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('assets/images/no-image.png') }}"
                                                             alt="Item Image" />
                                                    </div>
                                                </td>
                                                <td class="body-2">{{ $item['quantity'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
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
