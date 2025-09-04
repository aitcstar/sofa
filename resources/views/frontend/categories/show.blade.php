@extends('frontend.layouts.pages')

@section('title', 'تفاصيل التصنيف - SOFA Experience')
@section('description', 'اكتشف تفاصيل باكجات التأثيث الفندقية الجاهزة من SOFA بأسعار تنافسية وجودة عالية')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home') }}" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('categories.index') }}" class="body-2 text-body">التصنيفات</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">تفاصيل التصنيف</a>
</div>

<!-- ===== CATEGORY DETAILS ===== -->
<section class="category-details">
    <div class="container">
        <!-- Accordion Details -->
        <div class="accordion-details">
            <div class="accordion" id="accordionExample">
                <!-- Item 1 -->
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button d-flex align-items-center gap-sm-5" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                            aria-controls="collapseOne">
                            <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="arrow-down" />
                            <p class="body-2 mb-0">اختر نمط غرفة المعيشة المناسب لك</p>
                        </button>
                    </div>
                    <div class="accordion-collapse collapse show" id="collapseOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body d-flex flex-column gap-sm-4">
                            <div class="d-flex gap-sm-4 flex-wrap">
                                <div class="accordion-details-content-item">
                                    <p class="sub-heading-5 text-body mb-0">فئة فاخرة كبيرة</p>
                                </div>
                                <div class="accordion-details-content-item active">
                                    <p class="sub-heading-5 text-body mb-0">فئة فاخرة صغيرة</p>
                                </div>
                                <div class="accordion-details-content-item">
                                    <p class="sub-heading-5 text-body mb-0">فئة قياسية كبيرة</p>
                                </div>
                                <div class="accordion-details-content-item">
                                    <p class="sub-heading-5 text-body mb-0">فئة قياسية صغيرة</p>
                                </div>
                            </div>

                            <div>
                                <ul class="d-flex flex-wrap gap-md p-0 m-0" style="margin-right: 20px !important;">
                                    <li class="body-4 mb-0">طاولة جانبية</li>
                                    <li class="body-4 mb-0">لوحة فنيّة واحدة</li>
                                    <li class="body-4 mb-0">كنبة 3 مقاعد</li>
                                    <li class="body-4 mb-0">طاولة قهوة</li>
                                    <li class="body-4 mb-0">كرسي كبير ×1</li>
                                    <li class="body-4 mb-0">كرسي صغير ×1</li>
                                </ul>
                            </div>

                            <div class="category-details-images">
                                <div class="category-details-images-grid">
                                    <div class="category-details-images-grid-col-one">
                                        @foreach($images as $index => $image)
                                        <div class="category-details-images-grid-col-one-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset($image) }}" alt="image" />
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="category-details-images-grid-col-two">
                                        <!-- Resize button -->
                                        <div class="category-details-images-grid-col-two-resize">
                                            <button class="btn">
                                                <i class="fa-solid fa-maximize"></i>
                                            </button>
                                        </div>

                                        <img src="{{ asset($images[0]) }}" alt="image" id="mainImage" />

                                        <!-- owl carousel button -->
                                        <div class="category-details-images-grid-col-two-button">
                                            <button class="btn" id="prevBtn">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>
                                            <button class="btn" id="nextBtn">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button d-flex align-items-center gap-sm-5" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true"
                            aria-controls="collapseTwo">
                            <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="arrow-down" />
                            <p class="body-2 mb-0">اختر تصميم المطبخ المناسب</p>
                        </button>
                    </div>
                    <div class="accordion-collapse collapse" id="collapseTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body d-flex gap-sm-4 flex-wrap">
                            <div class="accordion-details-content-item">
                                <p class="sub-heading-5 text-body mb-0">تصميم خشبي كلاسيكي</p>
                            </div>
                            <div class="accordion-details-content-item">
                                <p class="sub-heading-5 text-body mb-0">تصميم طوبي صناعي</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button d-flex align-items-center gap-sm-5" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true"
                            aria-controls="collapseThree">
                            <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="arrow-down" />
                            <p class="body-2 mb-0">اختر تصميم وحدة التلفزيون</p>
                        </button>
                    </div>
                    <div class="accordion-collapse collapse" id="collapseThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body d-flex gap-sm-4 flex-wrap">
                            <div class="accordion-details-content-item">
                                <p class="sub-heading-5 text-body mb-0">تصميم خشبي كلاسيكي</p>
                            </div>
                            <div class="accordion-details-content-item">
                                <p class="sub-heading-5 text-body mb-0">تصميم طوبي صناعي</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button d-flex align-items-center gap-sm-5" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true"
                            aria-controls="collapseFour">
                            <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="arrow-down" />
                            <p class="body-2 mb-0">اختر تصميم الخزائن</p>
                        </button>
                    </div>
                    <div class="accordion-collapse collapse" id="collapseFour" data-bs-parent="#accordionExample">
                        <div class="accordion-body d-flex gap-sm-4 flex-wrap">
                            <div class="accordion-details-content-item">
                                <p class="sub-heading-5 text-body mb-0">التصميم العصري المفتوح</p>
                            </div>
                            <div class="accordion-details-content-item">
                                <p class="sub-heading-5 text-body mb-0">التصميم التقليدي المغلق</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Details & Order Button -->
        <div class="category-details-order-button">
            <!-- heading -->
            <div class="category-details-order-button-heading d-flex flex-column gap-sm-7">
                <h2 class="heading-h7">باكج غرفة نوم واحدة</h2>
                <div class="d-flex align-items-center gap-sm-5 mb-1">
                    <p class="body-2 text-body mb-0">السعر يبدأ من</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="heading-h7 mb-0">26,000</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>

                <div class="d-flex align-items-center gap-sm-5">
                    <div class="category-details-widget d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/category/layers.svg') }}" alt="layers" />
                        <p class="body-4 text-body mb-0">60 قطعة</p>
                    </div>
                    <div class="category-details-widget d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/category/delivery.svg') }}" alt="delivery" />
                        <p class="body-4 text-body mb-0">التوصيل خلال 90 يوم</p>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="category-details-order-button-pricing d-flex flex-column gap-sm-7">
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">السعر الأساسي</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="body-2 text-body mb-0">23,000</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">الضريبة (15%)</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="body-2 text-body mb-0">3,450</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">السعر النهائي</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="heading-h7 mb-0">26,450</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
            </div>

            <!-- Order Button -->
            <a href="" class="btn btn-custom-primary">
                أضف إلى الطلب
            </a>
        </div>
    </div>
</section>

<!-- ===== TABS SECTION ===== -->
<section class="tabs">
    <div class="container">
        <div class="tab-options">
            <div class="tab-option">
                <p class="body-2 text-body mb-0">جدول الكميات</p>
            </div>
            <div class="tab-option active">
                <p class="body-2 text-body mb-0">صور جدول الكميات</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== TABLE SECTION ===== -->
<section class="table-section">
    <div class="container">
        <div class="table-section-grid">
            <!-- Single category item (since this is show page) -->
            <div class="table-section-item d-flex flex-column gap-sm-4">
                <!-- heading -->
                <div class="d-flex align-items-center gap-sm-5">
                    <img src="{{ asset('assets/images/gallery/living-room.svg') }}" alt="living-room" />
                    <p class="heading-h9 mb-0">{{ $category->name }}</p>
                </div>

                <!-- Table -->
                <div class="table-section-item">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th class="body-2">القطعة</th>
                                    <th class="body-2">المقاس</th>
                                    <th class="body-2">الخامة</th>
                                    <th class="body-2">اللون</th>
                                    <th class="body-2">الصورة</th>
                                    <th class="body-2">الكمية</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- You need to define items for your category or remove this section -->
                                <tr>
                                    <td class="body-2">كنبة</td>
                                    <td class="body-2">200×90 سم</td>
                                    <td class="body-2">قُماش</td>
                                    <td class="color-box">
                                        <p class="body-2 text-subheading mb-0" style="background-color: #f5f1e6;">
                                            بيج
                                        </p>
                                    </td>
                                    <td class="image-box">
                                        <div class="img-box">
                                            <img src="{{ asset('assets/images/products/product-1.jpg') }}" alt="Gallery Image" />
                                        </div>
                                    </td>
                                    <td class="body-2">1</td>
                                </tr>
                                <!-- Add more items as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- DIVIDER -->
<div class="container mt-4">
    <hr class="divider">
</div>

<!-- ===== CUSTOMER REVIEW SECTION ===== -->
<section class="customer-review-section">
    <div>
        <div class="customer-review-section-container">
            <div class="d-flex flex-column gap-sm-5 text-center">
                <h2 class="heading-h6 text-heading">آراء عملائنا</h2>
                <p class="body-2 text-body mx-auto" style="max-width: 482px;">تعليقات عملائنا تعبّر عن رضاهم عن
                    مستوى التشطيب، سرعة التنفيذ، وتجربة التأثيث المتكاملة التي نقدمها في SOFA</p>
            </div>

            <!-- Content -->
            <div id="testimonial-carousel-category-details" class="testimonial-carousel owl-carousel owl-theme">
                @foreach($testimonials as $testimonial)
                <!-- Item {{ $loop->iteration }} -->
                <div class="d-flex flex-column gap-sm">
                    <div class="testimonial-item">
                        <img src="{{ asset('assets/images/icons/quotation.png') }}" />
                        <div>
                            <p class="caption-2 text-subheading mb-0">
                                {{ $testimonial['comment'] }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-sm-4">
                            <img src="{{ asset($testimonial['avatar']) }}" class="imgprofile" alt="" />
                            <div style="flex: 1">
                                <p class="mb-0 sub-heading-4 text-subheading">{{ $testimonial['name'] }}</p>
                                <span class="text-body body-3">{{ $testimonial['location'] }}</span>
                            </div>
                            <div style="color: var(--system-yellow) !important">★★★★★</div>
                        </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- DIVIDER -->
<div class="container mb-4">
    <hr class="divider">
</div>

<!-- ===== FAQ SECTION ===== -->
<section class="faq-section">
    <div class="container">
        <div class="faq-section-container">
            <div class="faq-section-heading d-flex flex-column gap-sm-5">
                <h2 class="heading-h6 text-heading">الأسئلة المتكررة</h2>
                <p class="body-2 text-body">جمعنا لك أهم الأسئلة التي قد تدور في بالك قبل طلب هذا الباكج، لتكون
                    الصورة أوضح وأسهل في اتخاذ القرار</p>
            </div>

            <!-- Accordion -->
            <div class="faq-section-content">
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                    <!-- Item {{ $index + 1 }} -->
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $index }}">
                                <p class="sub-heading-3 mb-0">{{ $faq['question'] }}</p>
                            </button>
                        </div>
                        <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="body-2 text-body mb-0">
                                    {{ $faq['answer'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/category-details.css') }}">
<style>
    /* تحسين مظهر الأزرار */
    .category-details-images-grid-col-two-button button,
    .category-details-images-grid-col-two-resize button {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .category-details-images-grid-col-two-button button:hover,
    .category-details-images-grid-col-two-resize button:hover {
        background-color: #33415C !important;
        color: white !important;
        transform: scale(1.1);
    }

    /* تحسين مظهر الصور المصغرة */
    .category-details-images-grid-col-one-item {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .category-details-images-grid-col-one-item:hover {
        border-color: #33415C;
        transform: scale(1.05);
    }

    .category-details-images-grid-col-one-item.active {
        border-color: #33415C;
        box-shadow: 0 0 10px rgba(51, 65, 92, 0.3);
    }

    /* تحسين مظهر الديالوج */
    #imageModal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    #imageModal .modal-header {
        border-bottom: 1px solid #e9ecef;
        background-color: #f8f9fa;
    }

    #imageModal .modal-footer {
        border-top: 1px solid #e9ecef;
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // current image index
    let currentImageIndex = 0;

    // get elements
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.category-details-images-grid-col-one-item img');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const maximizeBtn = document.querySelector('.fa-maximize').closest('button');

    // enable click on thumbnails
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', function() {
            // change main image
            mainImage.src = this.src;
            currentImageIndex = index;

            // remove active class from all thumbnails
            thumbnails.forEach(t => t.closest('.category-details-images-grid-col-one-item').classList.remove('active'));

            // add active class to the selected thumbnail
            this.closest('.category-details-images-grid-col-one-item').classList.add('active');
        });
    });

    // next button
    nextBtn.addEventListener('click', function() {
        currentImageIndex = (currentImageIndex + 1) % thumbnails.length;
        mainImage.src = thumbnails[currentImageIndex].src;

        // update active image
        thumbnails.forEach(t => t.closest('.category-details-images-grid-col-one-item').classList.remove('active'));
        thumbnails[currentImageIndex].closest('.category-details-images-grid-col-one-item').classList.add('active');
    });

    // previous button
    prevBtn.addEventListener('click', function() {
        currentImageIndex = (currentImageIndex - 1 + thumbnails.length) % thumbnails.length;
        mainImage.src = thumbnails[currentImageIndex].src;

        // update active image
        thumbnails.forEach(t => t.closest('.category-details-images-grid-col-one-item').classList.remove('active'));
        thumbnails[currentImageIndex].closest('.category-details-images-grid-col-one-item').classList.add('active');
    });

    // maximize button
    maximizeBtn.addEventListener('click', function() {
        // create modal
        const modalHTML = `
            <div class="modal fade" id="imageModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-xl p-2">
                    <div class="modal-content">
                        <div class="d-flex justify-content-end p-2">
                            <button type="button" data-bs-dismiss="modal" style="width: 36px; height: 36px; border-radius: 50%; background-color: #33415C; color: #fff !important;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="modal-body text-center p-4">
                             <img src="${mainImage.src}" alt="صورة مكبرة" style="max-width: 100%; max-height: 350px; object-fit: contain; border-radius: 8px;">
                         </div>
                    </div>
                </div>
            </div>
        `;

        // add modal to page
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // show modal
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();

        // delete modal after close
        document.getElementById('imageModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    });

    // Initialize Owl Carousel
    $('#testimonial-carousel-category-details').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        rtl: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });
});
</script>
@endpush
