@extends('frontend.layouts.pages')

@section('meta')
    <title>
        @if(app()->getLocale() === 'ar')
            {{ $package->meta_title_ar ?? 'العنوان الافتراضي' }}
        @else
            {{ $package->meta_title_en ?? 'Default Title' }}
        @endif
    </title>

    <meta name="description" content="{{ app()->getLocale() === 'ar' ? ($package->meta_description_ar ?? 'الوصف الافتراضي') : ($package->meta_description_en ?? 'Default description') }}">

    <link rel="canonical" href="{{ url()->current() }}/{{ $package->slug_en }}">

    <meta property="og:title" content="{{ app()->getLocale() === 'ar' ? ($package->meta_title_ar ?? '') : ($package->meta_title_en ?? '') }}">
    <meta property="og:description" content="{{ app()->getLocale() === 'ar' ? ($package->meta_description_ar ?? '') : ($package->meta_description_en ?? '') }}">
    <meta property="og:url" content="{{ url()->current() }}">
@endsection

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="body-2 text-body">{{ __('site.package') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">{{ app()->getLocale() == 'ar' ? $package->name_ar : $package->name_en }}</a><br>
</div>

<section class="category-details">
    <div class="container">
        <!-- Accordion Details -->
        <div class="accordion-details">
            <div class="accordion" id="accordionExample">
                @php
                    $groupedItems = $package->packageUnitItems->groupBy('unit_id');
                @endphp

                @foreach($groupedItems as $unitId => $items)
                    @php
                        $unit = $items->first()->unit;
                    @endphp
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button d-flex align-items-center gap-sm-5 {{ $loop->first ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $loop->index }}"
                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="collapse{{ $loop->index }}">

                                @if($unit->type == "bedroom")
                                    <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                @elseif($unit->type == "living_room")
                                    <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="" />
                                @elseif($unit->type == "kitchen")
                                    <img src="{{ asset('assets/images/icons/foot.png') }}" alt="" />
                                @elseif($unit->type == "external")
                                    <img src="{{ asset('assets/images/icons/Group.png') }}" alt="" />
                                @else
                                    <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                @endif
                                <p class="body-2 mb-0">
                                    {{ $unit->{'name_'.app()->getLocale()} }}
                                </p>
                            </button>
                        </div>

                        <div class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                            id="collapse{{ $loop->index }}"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body d-flex flex-column gap-sm-4">

                                @if($unit->designs->count())
                                <div class="accordion-body d-flex gap-sm-4 flex-wrap">
                                     @foreach($unit->designs as $design)
                                        <div class="accordion-details-content-item">
                                            <p class="sub-heading-5 text-body mb-0">   {{ $design->{'name_'.app()->getLocale()} }}</p>
                                        </div>
                                        @endforeach
                                </div>
                                @endif




                                <!-- Items -->
                                @if($items->count())
                                <div>
                                    <ul class="d-flex flex-wrap  gap-sm-2 p-0 m-0" style="margin-right: 14px !important;">
                                        @foreach($items as $item)
                                        <li class="body-4 mb-0">
                                            {{ $item->item?->{'item_name_'.app()->getLocale()} ?? '—' }} ×{{ $item->item?->quantity ?? 0 }}

                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <!-- Images -->
                                @if($unit->images->count())
                                <div class="category-details-images">
                                    <div class="category-details-images-grid" data-unit-id="{{ $unit->id }}">
                                        <!-- العمود الأول (الصور الصغيرة) -->
                                        <div class="category-details-images-grid-col-one">
                                            @foreach($unit->images as $uIndexImg => $uImg)
                                                <div class="category-details-images-grid-col-one-item {{ $uIndexImg == 0 ? 'active' : '' }}">
                                                    <img src="{{ asset('storage/' . $uImg->image_path) }}" alt="unit-image" />
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- العمود الثاني (الصورة الكبيرة + الأزرار) -->
                                        <div class="category-details-images-grid-col-two">
                                            <!-- زر التكبير -->
                                            <div class="category-details-images-grid-col-two-resize">
                                                <button class="btn maximizeBtn">
                                                    <i class="fa-solid fa-maximize"></i>
                                                </button>
                                            </div>

                                            <img src="{{ asset('storage/' . ($unit->images->first()->image_path ?? 'assets/images/no-image.png')) }}"
                                                alt="unit-main-image" class="mainUnitImage" />

                                            <!-- أزرار السلايدر -->
                                            <div class="category-details-images-grid-col-two-button">
                                                <button class="btn prevUnitBtn">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </button>
                                                <button class="btn nextUnitBtn">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Category Details & Order Button -->
        <div class="category-details-order-button">
            <!-- heading -->
            <div class="category-details-order-button-heading d-flex flex-column gap-sm-7">
                <h2 class="heading-h7">{{ $package->{'name_'.app()->getLocale()} }}</h2>
                <div class="d-flex align-items-center gap-sm-5 mb-1">
                    <p class="body-2 text-body mb-0">{{ __('site.Starting_from') }}</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="heading-h7 mb-0">{{ number_format($package->price) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>

                <div class="d-flex align-items-center gap-sm-5">
                    <div class="category-details-widget d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/category/layers.svg') }}" alt="layers" />
                        <p class="body-4 text-body mb-0">
                            {{ $package->packageUnitItems->count() }} {{ __('site.piece') }}
                        </p>
                    </div>
                    <div class="category-details-widget d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/category/delivery.svg') }}" alt="delivery" />
                        <p class="body-4 text-body mb-0">{{ __('site.Delivery') }} {{ $package->{'period_'.app()->getLocale()} }}</p>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="category-details-order-button-pricing d-flex flex-column gap-sm-7">
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">{{ __('site.Base price') }}</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="body-2 text-body mb-0">{{ number_format($package->price) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">{{ __('site.Tax') }} (15%)</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="body-2 text-body mb-0">{{ number_format($package->price * 0.15) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">{{ __('site.Final price') }}</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="heading-h7 mb-0">{{ number_format($package->price * 1.15) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
            </div>

            <!-- Order Button -->
            <a href="{{ route('order.confirm', $package->id) }}" class="btn btn-custom-primary">
                {{ __('site.Add to order') }}
            </a>

        </div>
    </div>
</section>

<!-- ===== TABS ===== -->
<section class="tabs">
    <div class="container">
        <div class="tab-options">
            <div class="tab-option active" data-tab="quantities">
                <p class="body-2 text-body mb-0">{{ __('site.Table of quantities') }}</p>
            </div>
            <div class="tab-option" data-tab="with-images">
                <p class="body-2 text-body mb-0">{{ __('site.Pictures of table of quantities') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== TABLE SECTION ===== -->
<section class="table-section">
    <div class="container">
        <div class="tab-content">
            <!-- TAB بدون صور -->
            <div class="tab-pane active" id="quantities">
                <div class="table-section-grid" style="width: 100%;">
                    @foreach($groupedItems as $unitId => $items)
                        @php $unit = $items->first()->unit; @endphp
                        <div class="table-section-item d-flex flex-column gap-sm-4">
                            <div class="d-flex align-items-center gap-sm-5">
                                @if($unit->type == "bedroom")
                                    <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="bedroom" />
                                @elseif($unit->type == "living_room")
                                    <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="living-room" />
                                @elseif($unit->type == "kitchen")
                                    <img src="{{ asset('assets/images/icons/foot.png') }}" alt="kitchen" />
                                @elseif($unit->type == "external")
                                    <img src="{{ asset('assets/images/icons/Group.png') }}" alt="external" />
                                @else
                                    <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                @endif
                                <p class="heading-h9 mb-0">{{ $unit->{'name_'.app()->getLocale()} }}</p>
                            </div>

                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="body-2">{{ __('site.Widget') }}</th>
                                            <th class="body-2">{{ __('site.Size') }}</th>
                                            <th class="body-2">{{ __('site.Material') }}</th>
                                            <th class="body-2">{{ __('site.the color') }}</th>
                                            <th class="body-2">{{ __('site.Quantity') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr>
                                            <td class="body-2">{{ $item->item?->{'item_name_'.app()->getLocale()} ?? '' }}</td>
                                            <td class="body-2">{{ $item->item->dimensions  ?? ''}}</td>
                                            <td class="body-2">{{ $item->item->{'material_'.app()->getLocale()}  ?? ''}}</td>
                                            <td class="color-box">
                                                <p class="body-2 text-subheading mb-0" style="background-color: {{ $item->item->background_color  ?? ''}}">
                                                    {{ $item->item->{'color_'.app()->getLocale()}  ?? ''}}
                                                </p>
                                            </td>
                                            <td class="body-2">{{ $item->item->quantity  ?? 0}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- TAB بالصور -->
            <div class="tab-pane" id="with-images">
                <div class="table-section-grid" style="width: 100%;">
                    @foreach($groupedItems as $unitId => $items)
                        @php $unit = $items->first()->unit; @endphp
                        <div class="table-section-item d-flex flex-column gap-sm-4">
                            <div class="d-flex align-items-center gap-sm-5">
                                @if($unit->type == "bedroom")
                                    <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="bedroom" />
                                @elseif($unit->type == "living_room")
                                    <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="living-room" />
                                @elseif($unit->type == "kitchen")
                                    <img src="{{ asset('assets/images/icons/foot.png') }}" alt="kitchen" />
                                @elseif($unit->type == "external")
                                    <img src="{{ asset('assets/images/icons/Group.png') }}" alt="external" />
                                @else
                                    <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                @endif
                                <p class="heading-h9 mb-0">{{ $unit->{'name_'.app()->getLocale()} }}</p>
                            </div>

                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="body-2">{{ __('site.Widget') }}</th>
                                            <th class="body-2">{{ __('site.Size') }}</th>
                                            <th class="body-2">{{ __('site.Material') }}</th>
                                            <th class="body-2">{{ __('site.the color') }}</th>
                                            <th class="body-2">{{ __('site.image') }}</th>
                                            <th class="body-2">{{ __('site.Quantity') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr>
                                            <td class="body-2">{{ $item->item->{'item_name_'.app()->getLocale()} ?? ''}}</td>
                                            <td class="body-2">{{ $item->item->dimensions ?? ''}}</td>
                                            <td class="body-2">{{ $item->item->{'material_'.app()->getLocale()}?? '' }}</td>
                                            <td class="color-box">
                                                <p class="body-2 text-subheading mb-0" style="background-color: {{ $item->item->background_color ?? ''}}">
                                                    {{ $item->item->{'color_'.app()->getLocale()} ?? ''}}
                                                </p>
                                            </td>
                                            <td class="image-box">
                                                <div class="img-box">
                                                    <img
                                                    src="{{ $item->item?->image_path
                                                        ? asset('storage/'.$item->item->image_path)
                                                        : asset('assets/images/no-image.png') }}" alt="item-image"
                                                        class="popup-image"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModal"
                                                        data-image="{{ $item->item?->image_path
                                                            ? asset('storage/'.$item->item->image_path)
                                                            : asset('assets/images/no-image.png') }}"
                                                                                                            />
                                                </div>
                                            </td>
                                            <td class="body-2">{{ $item->item->quantity ?? 0}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal for image popup -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <img src="" id="modalImage" class="img-fluid w-100" alt="Popup Image">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('site.close') }}</button>
                        </div>
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
    <div class="customer-review-section-container">
        <div class="d-flex flex-column gap-sm-5 text-center">
            <h2 class="heading-h6 text-heading">{{ __('site.testimonials') }}</h2>
            <p class="body-2 text-body mx-auto" style="max-width: 482px;">{{ __('site.testimonials_desc') }}</p>
        </div>

        <div id="testimonial-carousel-homepage" class="testimonial-carousel owl-carousel owl-theme">
            @foreach($testimonials as $testimonial)
                <div class="testimonial-item card shadow-sm h-100 p-3">
                    <img src="{{ asset('assets/images/icons/quotation.png') }}" class="mb-3" alt="quotation icon" />
                    <p class="caption-2 text-subheading mb-3">
                        {{ $testimonial->message }}
                    </p>
                    <div class="d-flex align-items-center gap-sm-4 mt-auto">
                        <img src="{{ $testimonial->image ? asset('storage/' . $testimonial->image) : asset('assets/images/avatar/default.png') }}"
                             class="imgprofile" alt="{{ $testimonial->name }}" />
                        <div class="cam">
                            <p class="mb-0 sub-heading-4 text-subheading">{{ $testimonial->name }}</p>
                            <span class="text-body body-3">{{ $testimonial->location }}</span>
                        </div>
                        <div style="color: var(--system-yellow) !important">
                            {{ str_repeat('★', $testimonial->rating) }}
                            {{ str_repeat('☆', 5 - $testimonial->rating) }}
                        </div>
                    </div>
                </div>
            @endforeach
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
                <h2 class="heading-h6 text-heading">{{ __('site.faq_title') }}</h2>
                <p class="body-2 text-body">{{ __('site.faq_subtitle') }}</p>
            </div>

            <div class="accordion" id="accordionFaq">
                @foreach($faqs as $index => $faq)
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button @if($index != 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapseFaq{{ $index }}">
                            <p class="sub-heading-3 mb-0">
                                {{ app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en }}
                            </p>
                        </button>
                    </div>
                    <div id="collapseFaq{{ $index }}" class="accordion-collapse collapse @if($index == 0) show @endif" data-bs-parent="#accordionFaq">
                        <div class="accordion-body">
                            <p class="body-2 text-body mb-0">
                                {{ app()->getLocale() == 'ar' ? $faq->answer_ar : $faq->answer_en }}
                            </p>
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
</style>
@endpush

@push('scripts')
<script>
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    document.querySelectorAll('.popup-image').forEach(img => {
        img.addEventListener('click', function() {
            modalImage.src = this.dataset.image;
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.category-details-images-grid').forEach(grid => {
        let thumbnails = grid.querySelectorAll('.category-details-images-grid-col-one-item img');
        let mainImage = grid.querySelector('.mainUnitImage');
        let prevBtn = grid.querySelector('.prevUnitBtn');
        let nextBtn = grid.querySelector('.nextUnitBtn');
        let maximizeBtn = grid.querySelector('.maximizeBtn');

        let currentIndex = 0;

        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', function () {
                mainImage.src = this.src;
                currentIndex = index;
                thumbnails.forEach(t => t.closest('.category-details-images-grid-col-one-item').classList.remove('active'));
                this.closest('.category-details-images-grid-col-one-item').classList.add('active');
            });
        });

        nextBtn.addEventListener('click', function () {
            currentIndex = (currentIndex + 1) % thumbnails.length;
            mainImage.src = thumbnails[currentIndex].src;
            thumbnails.forEach(t => t.closest('.category-details-images-grid-col-one-item').classList.remove('active'));
            thumbnails[currentIndex].closest('.category-details-images-grid-col-one-item').classList.add('active');
        });

        prevBtn.addEventListener('click', function () {
            currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
            mainImage.src = thumbnails[currentIndex].src;
            thumbnails.forEach(t => t.closest('.category-details-images-grid-col-one-item').classList.remove('active'));
            thumbnails[currentIndex].closest('.category-details-images-grid-col-one-item').classList.add('active');
        });

        maximizeBtn.addEventListener('click', function () {
            const modalHTML = `
                <div class="modal fade" id="unitImageModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-xl p-2">
                        <div class="modal-content">
                            <div class="d-flex justify-content-end p-2">
                                <button type="button" data-bs-dismiss="modal"
                                    style="width: 36px; height: 36px; border-radius: 50%; background-color: #33415C; color: #fff !important;">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div class="modal-body text-center p-4">
                                <img src="${mainImage.src}" alt="صورة مكبرة"
                                    style="max-width: 100%; max-height: 450px; object-fit: contain; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            const modal = new bootstrap.Modal(document.getElementById('unitImageModal'));
            modal.show();
            document.getElementById('unitImageModal').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        });
    });

    // Tabs functionality
    document.querySelectorAll(".tab-option").forEach(tab => {
        tab.addEventListener("click", function () {
            document.querySelectorAll(".tab-option").forEach(t => t.classList.remove("active"));
            this.classList.add("active");
            let target = this.getAttribute("data-tab");
            document.querySelectorAll(".tab-pane").forEach(pane => {
                pane.classList.remove("active");
                if (pane.id === target) {
                    pane.classList.add("active");
                }
            });
        });
    });
});
</script>

<script>
$(document).ready(function () {
    if ($("#testimonial-carousel-homepage").length > 0) {
        $("#testimonial-carousel-homepage").owlCarousel({
            rtl: true,
            loop: true,
            margin: 0,
            stagePadding: 0,
            dots: true,
            autoplay: true,
            autoplayTimeout: 4000,
            responsive: {
                0: { items: 1 },
                576: { items: 1 },
                768: { items: 2 },
                992: { items: 3 },
                1200: { items: 3 }
            }
        });
    }
});
</script>
@endpush
