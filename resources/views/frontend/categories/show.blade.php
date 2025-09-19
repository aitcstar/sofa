@extends('frontend.layouts.pages')


@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="body-2 text-body">{{ __('site.package') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">  {{ __('site.Classification details') }} </a>
</div>

<section class="category-details">
    <div class="container">
        <!-- Accordion Details -->
        <div class="accordion-details">
            <div class="accordion" id="accordionExample">

                @foreach($package->units as $uIndex => $unit)
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button d-flex align-items-center gap-sm-5 {{ $loop->first ? '' : 'collapsed' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $uIndex }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $uIndex }}">

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
                        id="collapse{{ $uIndex }}"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body d-flex flex-column gap-sm-4">


                            <!-- Items -->
                            @if($unit->items->count())
                            <div>
                                <ul class="d-flex flex-wrap gap-md p-0 m-0" style="margin-right: 20px !important;">
                                    @foreach($unit->items as $item)
                                    <li class="body-4 mb-0">
                                        {{ $item->{'item_name_'.app()->getLocale()} }} ×{{ $item->quantity }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <!-- Images -->
                            <!-- Images -->
                            @if($loop->first && ($package->image || $package->images?->count()))
                            <div class="category-details-images">
                                <div class="category-details-images-grid">

                                    <!-- العمود الأول (الصور الصغيرة) -->
                                    <div class="category-details-images-grid-col-one">
                                        {{-- الصورة الأساسية أولاً --}}
                                        @if($package->image)
                                            <div class="category-details-images-grid-col-one-item active">
                                                <img src="{{ asset('storage/' . $package->image) }}" alt="main-image" />
                                            </div>
                                        @endif

                                        {{-- باقي الصور --}}
                                        @foreach($package->images as $pIndex => $img)
                                            <div class="category-details-images-grid-col-one-item">
                                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="image" />
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- العمود الثاني (الصورة الكبيرة + الأزرار) -->
                                    <div class="category-details-images-grid-col-two">
                                        <!-- زر التكبير -->
                                        <div class="category-details-images-grid-col-two-resize">
                                            <button class="btn">
                                                <i class="fa-solid fa-maximize"></i>
                                            </button>
                                        </div>

                                        {{-- الصورة الرئيسية --}}
                                        <img src="{{ asset('storage/' . ($package->image ?? $package->images->first()->image_path ?? 'assets/images/no-image.png')) }}"
                                            alt="image" id="mainImage" />

                                        <!-- أزرار السلايدر -->
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
                        <p class="body-4 text-body mb-0">{{ $package->units->sum(fn($u) => $u->items->sum('quantity')) }} {{ __('site.piece') }}</p>
                    </div>
                    <div class="category-details-widget d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/category/delivery.svg') }}" alt="delivery" />
                        <p class="body-4 text-body mb-0">{{ __('site.Delivery') }}  {{ $package->{'period_'.app()->getLocale()}  }} </p>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="category-details-order-button-pricing d-flex flex-column gap-sm-7">
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0"> {{ __('site.Base price') }} </p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="body-2 text-body mb-0">{{ number_format($package->price) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0">{{ __('site.Tax') }} (15%)</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="body-2 text-body mb-0">{{ ($package->price * 0.15) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
                <div class="category-details-order-button-pricing-item">
                    <p class="body-2 text-body mb-0"> {{ __('site.Final price') }}</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <p class="heading-h7 mb-0">{{number_format($package->price * 1.15) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="SAR" />
                    </div>
                </div>
            </div>

            <!-- Order Button -->
            <a href="#" class="btn btn-custom-primary">
               {{ __('site.Add to order') }}
            </a>
        </div>
    </div>
</section>
<!-- ===== TABS SECTION ===== -->


<!-- ===== TABS ===== -->
 <section class="tabs">
    <div class="container">
      <div class="tab-options">
        <div class="tab-option active" data-tab="quantities">
          <p class="body-2 text-body mb-0">  {{ __('site.Table of quantities') }} </p>
        </div>
        <div class="tab-option" data-tab="with-images">
          <p class="body-2 text-body mb-0">  {{ __('site.Pictures of table of quantities') }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TABLE SECTION ===== -->
  <section class="table-section">
    <div class="container">
      <div class="tab-content">

        <!-- ===== TAB بدون صور ===== -->
        <div class="tab-pane active" id="quantities">
          <div class="table-section-grid" style="width: 100%;">
            @foreach($package->units as $unit)
            <div class="table-section-item d-flex flex-column gap-sm-4">
              <!-- heading -->
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

              <!-- Table -->
              <div class="table-container">
                <table>
                  <thead>
                    <tr>
                      <th class="body-2"> {{ __('site.Widget') }}</th>
                      <th class="body-2"> {{ __('site.Size') }} </th>
                      <th class="body-2"> {{ __('site.Material') }}</th>
                      <th class="body-2"> {{ __('site.the color') }}</th>
                      <th class="body-2"> {{ __('site.Quantity') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($unit->items as $item)
                    <tr>
                      <td class="body-2">{{ $item->{'item_name_'.app()->getLocale()} }}</td>
                      <td class="body-2">{{ $item->dimensions }}</td>
                      <td class="body-2">{{ $item->{'material_'.app()->getLocale()}  }}</td>
                      <td class="color-box">
                        <p class="body-2 text-subheading mb-0" style="background-color: {{ $item->background_color }}">
                          {{ $item->color_name }}
                        </p>
                      </td>
                      <td class="body-2">{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <!-- ===== TAB بالصور ===== -->
        <div class="tab-pane" id="with-images">
          <div class="table-section-grid" style="width: 100%;">
            @foreach($package->units as $unit)
            <div class="table-section-item d-flex flex-column gap-sm-4">
              <!-- heading -->
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

              <!-- Table -->
              <div class="table-container">
                <table>
                  <thead>
                    <tr>
                        <th class="body-2"> {{ __('site.Widget') }}</th>
                        <th class="body-2"> {{ __('site.Size') }} </th>
                        <th class="body-2"> {{ __('site.Material') }}</th>
                        <th class="body-2"> {{ __('site.the color') }}</th>
                        <th class="body-2"> {{ __('site.image') }}</th>
                        <th class="body-2"> {{ __('site.Quantity') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($unit->items as $item)
                    <tr>
                      <td class="body-2">{{ $item->{'item_name_'.app()->getLocale()} }}</td>
                      <td class="body-2">{{ $item->dimensions }}</td>
                      <td class="body-2">{{ $item->material }}</td>
                      <td class="color-box">
                        <p class="body-2 text-subheading mb-0" style="background-color: {{ $item->background_color }}">
                          {{ $item->color_name }}
                        </p>
                      </td>
                      <td class="image-box">
                        <div class="img-box">
                          <img src="{{ $item->image_path ? asset('storage/'.$item->image_path) : asset('assets/images/no-image.png') }}" alt="item-image" />
                        </div>
                      </td>
                      <td class="body-2">{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endforeach
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
                <h2 class="heading-h6 text-heading">  {{ __('site.testimonials') }}</h2>
                <p class="body-2 text-body mx-auto" style="max-width: 482px;"> {{ __('site.testimonials_desc') }}</p>
            </div>

            <!-- Content -->
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
                <h2 class="heading-h6 text-heading"> {{ __('site.faq_title') }}</h2>
                <p class="body-2 text-body">  {{ __('site.faq_subtitle') }}</p>
            </div>

            <!-- Accordion -->
            <div class="faq-section-content">
                <div class="accordion" id="accordionFaq">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button @if($index != 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                <p class="sub-heading-3 mb-0">
                                    {{ app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en }}
                                </p>
                            </button>
                        </div>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse @if($index == 0) show @endif" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionFaq">
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


  $(document).ready(function () {
        if ($("#testimonial-carousel-homepage").length > 0) {
            $("#testimonial-carousel-homepage").owlCarousel({
  rtl: true,
  loop: true,
  margin: 0, // مهم جداً: شيل المارجن من هنا
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
