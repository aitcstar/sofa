@extends('frontend.layouts.app')

@section('content')
  <!-- ===== HERO SLIDER SECTION ===== -->


  <section id="hero-carousel" class="hero-section carousel slide p-0" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($sliders as $key => $slider)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
            <div class="overlay"></div>
            <img src="{{ asset('storage/' . $slider->image) }}" class="d-block w-100" alt="slider{{ $key+1 }}" />
            <div class="carousel-caption d-flex justify-content-center align-items-center flex-column gap-l" style="text-align: center;">
                <div class="d-flex flex-column gap-sm-3">
                    <h2 class="mb-0">{{ $slider->title() }}</h2>
                    <p class="body-1 text-white mb-0" style="opacity: 0.8;">{{ $slider->description() }}</p>
                </div>
                <div class="hero-section-actions d-flex justify-content-center gap-sm-3">
                    <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="btn btn-custom-outline">
                        {{ __('site.browse_packages') }}
                    </a>
                    <a href="{{ app()->getLocale() == 'ar' ? route('help.index') : route('help.index.en') }}" class="btn btn-custom-secondary">
                        {{ __('site.order_now') }}
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
        <div class="carousel-control-button">
            <i class="fa-solid fa-chevron-left"></i>
        </div>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
        <div class="carousel-control-button">
            <i class="fa-solid fa-chevron-right"></i>
        </div>
    </button>
</section>



  <!-- ===== STEPS SECTION  ===== -->
  <section class="step-section">
    <div class="content mx-auto">
        <h5 class="heading-h8 text-center mb-4">
            {{ optional($steps->where('order', 0)->first())->{'title_'.app()->getLocale()} }} &nbsp;
            <i class="fa-solid fa-hourglass-half hourglass-mobile-safe"
   style="color:#ad996f; font-size:32px;"></i>

<style>
.hourglass-mobile-safe {
  animation: hourglassFlip 8s linear infinite;
  will-change: transform;
}

@keyframes hourglassFlip {
  0%   { transform: rotate(0deg); }
  50%  { transform: rotate(180deg); }
  100% { transform: rotate(180deg); }
}

/* احترام إعدادات تقليل الحركة */
@media (prefers-reduced-motion: reduce) {
  .hourglass-mobile-safe {
    animation-duration: 8s;
  }
}
</style>

        </h5>

        <!-- نفس الشكل للديسكتوب والموبايل -->
        <div class="steps-mobile-wrapper">
            <div class="steps-container d-flex gap-sm-3 position-relative mx-auto">


                @foreach($steps as $step)
                    @if($step->order != 0)

                    <div class="step-box d-flex flex-column align-items-center gap-sm-4">
                        <div class="step-item-icon">
                            <img src="{{ asset('storage/' . $step->icon) }}" />
                        </div>
                        @if($step->order != 1)<div class="line"></div> @endif
                        <div class="d-flex flex-column align-items-center text-center">
                            <p class="sub-heading-4 mb-0" style="font-size: 15px">
                                {{ $step->{'title_'.app()->getLocale()} }}
                            </p>

                            <div class="body-3 text-caption mb-0" style="font-size: 13px">
                                {{ $step->{'desc_'.app()->getLocale()} }}
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>







    </div>
</section>



<style>
.steps-container {
    display: flex;
    gap: 24px;
    position: relative;
    max-width: 1110px;
    margin: 0 auto;
}



.carousel-control-prev {
    right: auto!important;
}



.step-box {
    /*width: 150px;*/
    text-align: center;
}

/* ===== الموبايل ===== */
@media(max-width: 767px) {
    .steps-mobile-wrapper {
        overflow-x: hidden; /* لا يظهر scrollbar */
        position: relative;
        max-width: 100%;
    }

    .steps-container {
        flex-wrap: nowrap;  /* منع الالتفاف */
    }


    .step-box {
        flex: 0 0 auto; /* حجم ثابت لكل عنصر */
        scroll-snap-align: center;
    }
}




</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.innerWidth > 767) return; // فقط الموبايل

    const wrapper = document.querySelector('.steps-mobile-wrapper');
    const boxes = document.querySelectorAll('.step-box');
    let index = 0;

    setInterval(() => {
        const nextBox = boxes[index];
        const offset = nextBox.offsetLeft;

        wrapper.scrollTo({
            left: offset,
            behavior: 'smooth'
        });

        index = (index + 1) % boxes.length;
    }, 1500);
});

    </script>




  <!-- ===== PACKAGES SECTION ===== -->


<!-- مكان عرض الباكجات -->

  <section class="package-section">
    <div class="container d-flex flex-column gap-xl">

        <!-- Heading -->
        <div class="heading text-center d-flex flex-column gap-sm-5">
            <h3 class="heading-h6 mb-0">{{ $section->{'title_'.app()->getLocale()} }}</h3>
            <p class="caption-3 text-caption mb-0 mx-auto" style="max-width: 440px;">
                {{ $section->{'desc_'.app()->getLocale()} }}
            </p>
        </div>

        <!-- Packages Wrapper -->
        <div id="packages-wrapper">
            {{-- سيتم تحميل الباكجات هنا عن طريق AJAX --}}
            @include('frontend.categories._section', ['packages' => $packages ?? []])
        </div>

    </div>
</section>



  <!-- ===== PROCESS STEPS SECTION ===== -->
  <section class="process-section">
    <div class="container">
        <div class="process-section-content">
            <!-- image -->
            <div class="info w-100 mr-auto h-100 bg-secondary radius-normal-box d-flex flex-column gap-sm-2 justify-content-center padding-box-normal"
                 style="flex: 0.5">
                <!-- Decorative Pattern -->
                <img src="{{ asset('assets/images/ui/pattern.svg') }}" alt="pattern" class="pattern" />

                <!-- Image Info -->
                <div class="info-item radius-small-box d-flex gap-sm-3 z-1">
                    <div class="avatar">
                        <img class="w-100 h-100" src="{{ asset('storage/'.$process->avatar) }}" alt="" />
                    </div>
                    <div class="d-flex flex-column gap-sm-7">
                        <p class="sub-heading-5 text-white mb-0">{{ $process->name }}</p>
                        <p class="body-4 text-white mb-0">{{ $process->units }} {{ __('site.unit') }}</p>
                    </div>
                </div>

                <!-- Progress -->
                <div class="info-item radius-small-box d-flex flex-column gap-sm-3 z-1">
                    <p class="body-4 text-white mb-0">{{ __('site.smart_steps_status_label') }}</p>
                    <p class="sub-heading-5 text-white mb-0">
                        {{ $process->status }} {{ $process->progress }}%
                    </p>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $process->progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $process->progress }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Column 2 -->
            <div class="d-flex flex-column gap-md" style="flex: 0.5;">
                <!-- Heading -->
                <div class="d-flex flex-column gap-sm-5">
                    <h6 class="heading-h6">
                        {{ $process->{'title_'.app()->getLocale()} }}
                    </h6>
                    <p class="text-caption mb-0">
                        {{ $process->{'desc_'.app()->getLocale()} }}
                    </p>
                </div>

                <!-- Steps -->
                <div class="d-flex flex-column gap-sm">
                    @foreach($processsteps as $i => $step)
                        <div class="step d-flex gap-sm-3">
                            <div class="step-icon @if($loop->last) no-after @endif">
                                <img src="{{ asset('storage/'.$step->icon) }}" alt="{{ $step->{'title_'.app()->getLocale()} }}" />
                            </div>
                            <div class="d-flex flex-column gap-sm-5">
                                <h2 class="sub-heading-3 mb-0">{{ $step->{'title_'.app()->getLocale()} }}</h2>
                                <p class="text-caption mb-0">
                                    {{ $step->{'desc_'.app()->getLocale()} }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>


                <a href="{{ app()->getLocale() == 'ar' ? route('help.index') : route('help.index.en') }}" class="btn btn-custom-primary w-100">
                    {{ __('site.start_now') }}
                </a>
            </div>
        </div>
    </div>
</section>


  <!-- ===== WHY CHOOSE US SECTION ===== -->
  <section class="why-choose-section bg-primary-light overflow-hidden">
    <!-- Decorative Pattern -->
    <img src="{{ asset('assets/images/about/pattern.svg') }}" alt="pattern" class="pattern" />

    <!-- Content -->
    <div class="container d-flex flex-column gap-md">
        <!-- Heading -->
        <div class="heading d-flex flex-column gap-sm-5">
            <h3 class="heading-h6">
                {{ app()->getLocale() == 'ar' ? $whyChoose->title_ar : $whyChoose->title_en }}
            </h3>
            <p class="body-2 text-caption mb-0">
                {{ app()->getLocale() == 'ar' ? $whyChoose->desc_ar : $whyChoose->desc_en }}
            </p>
        </div>

        <!-- Content -->
        <div class="row">
            @foreach($whyChoose->items as $index => $item)
                <div class="why-choose-col radius-small-box-2 padding-box-small-3">
                    <div class="d-flex gap-sm align-items-center">
                        <h2 class="heading-h6 mb-0">{{ $index + 1 }}.</h2>
                        <div class="d-flex flex-column gap-sm-6">
                            <h5 class="sub-heading-4 mb-0">
                                {{ app()->getLocale() == 'ar' ? $item->title_ar : $item->title_en }}
                            </h5>
                            <p class="body-3 mb-0">
                                {{ app()->getLocale() == 'ar' ? $item->desc_ar : $item->desc_en }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
  </section>



  <!-- ===== ORDER TIMELINE SECTION ===== -->
  <section class="order-time-line-section">
    <div class="container d-flex flex-column gap-l">
        <!-- Heading -->
        <div class="heading text-center d-flex flex-column gap-sm-5">
            <h3 class="heading-h6 mb-0">{{ app()->getLocale() == 'ar' ? $timelines->title_ar : $timelines->title_en }}</h3>
            <p class="body-2 text-caption mb-0">
                {{ app()->getLocale() == 'ar' ? $timelines->desc_ar : $timelines->desc_en }}
            </p>
        </div>

        <!-- Timeline Rows -->
        <div class="timeline-wrapper position-relative">
            <img src="{{ asset('assets/images/about/pattern.svg') }}" alt="pattern" class="pattern" />

            @foreach($timelines->items as $index => $item)
                <div class="timeline-row d-flex align-items-center">
                    @if($index % 2 == 0)
                        <div class="col-5"></div>
                        <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
                            <div class="dot-item" style="background-color: {{ $item->color }}"></div>
                        </div>
                        <div class="col-5">
                            <div class="timeline-item" style="background-color: {{ $item->color }}">
                                <div class="arrow right-arrow" style="border-left-color: {{ $item->color }}"></div>
                                <div class="sub-heading-3 text-white">
                                    {{ app()->getLocale() == 'ar' ? $item->title_ar : $item->title_en }}
                                </div>
                                <div class="body-3 text-white">
                                    {{ app()->getLocale() == 'ar' ? $item->desc_ar : $item->desc_en }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-5">
                            <div class="timeline-item" style="{{ app()->getLocale() === 'ar' ? 'margin-right: auto;' : 'margin-left: auto;' }}; background-color: {{ $item->color }}">
                                <div class="arrow left-arrow" style="border-right-color: {{ $item->color }}"></div>
                                <div class="sub-heading-3 text-white">
                                    {{ app()->getLocale() == 'ar' ? $item->title_ar : $item->title_en }}
                                </div>
                                <div class="body-3 text-white">
                                    {{ app()->getLocale() == 'ar' ? $item->desc_ar : $item->desc_en }}
                                </div>
                            </div>
                        </div>
                        <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
                            <div class="dot-item" style="background-color: {{ $item->color }}"></div>
                        </div>
                        <div class="col-5"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>



  <!-- ===== CALL TO ACTION SECTION ===== -->
  <section class="ready-to-furnish-section">
    <div class="ready-to-furnish-your-unit-container container position-relative bg-primary">
        <!-- pattern -->
        <img src="{{ asset('assets/images/about/pattern.svg') }}" alt="pattern" class="ready-to-furnish-your-unit-pattern" />

        <!-- content -->
        <div class="ready-to-furnish-your-unit-content d-flex flex-column gap-sm">
            <div class="d-flex flex-column gap-sm-5">
                <h2 class="heading-h6 text-white">
                    {{ app()->getLocale() === 'ar' ? $readyToFurnish->title_ar : $readyToFurnish->title_en }}
                </h2>
                <p class="caption-4 mb-0">
                    {{ app()->getLocale() === 'ar' ? $readyToFurnish->desc_ar : $readyToFurnish->desc_en }}
                </p>
            </div>

            <!-- buttons -->
            <div class="ready-to-furnish-your-unit-buttons d-flex gap-sm-3">
                <a href="https://wa.me/{{ $readyToFurnish->whatsapp }}" target="_blank" class="btn btn-custom-secondary d-flex align-items-center gap-2">
                    <p class="text-nowrap mb-0">
                        {{ app()->getLocale() === 'ar' ? 'دردشة واتساب' : 'WhatsApp Chat' }}
                    </p>
                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                </a>

                <a href="{{ $readyToFurnish->start_order_link }}" class="btn border-btn d-flex align-items-center gap-2">
                    <p class="text-nowrap mb-0">
                        {{ app()->getLocale() === 'ar' ? 'ابدأ الطلب' : 'Start Order' }}
                    </p>
                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-icon" style="font-size: 18px;"></i>
                </a>
            </div>
        </div>

        <!-- image -->
        <div class="ready-to-furnish-your-unit-image-container">
            <div class="ready-to-furnish-your-unit-image">
                <img src="{{ asset('storage/' . $readyToFurnish->image) }}"
                     alt="{{ app()->getLocale() === 'ar' ? $readyToFurnish->title_ar : $readyToFurnish->title_en }}" />
            </div>
        </div>
    </div>
</section>



  <!-- ===== TESTIMONIAL SECTION ===== -->
  <section class="testimonial-section">
    <div class="container d-flex flex-column gap-l">
      <!-- Heading -->
      <div class="heading text-center d-flex flex-column gap-sm-5">
        <h6 class="sub-title bg-primary-light px-2 pt-1 pb-1 mx-auto mb-0" style="width: fit-content">
          {{ __('site.testimonials') }}
        </h6>
        <h3 class="heading-h6 mb-0">{{ __('site.testimonials_heading') }}</h3>
        <p class="body-2 text-caption mb-0">
          {{ __('site.testimonials_desc') }}
        </p>
      </div>

      <!-- Carousel -->
      <div id="testimonial-carousel-homepage" class="testimonial-carousel owl-carousel owl-theme">
        @foreach($testimonials as $testimonial)
          <div class="testimonial-item card shadow-sm h-100 p-3">
            <img src="{{ asset('assets/images/icons/quotation.png') }}" class="mb-3" alt="quotation icon" />
            <p class="caption-2 text-subheading mb-3">
              {{ $testimonial->message }}
            </p>
            <div class="d-flex align-items-center gap-sm-4 mt-auto">
              <div class="cam">
                <p class="mb-0 sub-heading-4 text-subheading">{{ $testimonial->name }}</p>
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


  <!-- ===== GALLERY SECTION ===== -->
  <section class="gallery-section bg-primary-light">
    <div class="container d-flex flex-column gap-l">

        <!-- Heading -->
        <div class="heading text-center d-flex flex-column gap-sm-5">
            <h3 class="heading-h6 mb-0">
                {{ __('site.gallery_heading') }}
            </h3>
            <p class="body-2 text-caption mb-0">
                {{ __('site.gallery_desc') }}
            </p>
        </div>

        <!-- images -->
        @php
        // آخر 6 معارض مع الصورة الأساسية
        $exhibitions = $exhibitions->take(6);
    @endphp

    <div class="gallery-details-images">
        <div class="gallery-details-image-grid d-flex gap-sm">

            {{-- العمود الأول --}}
            <div class="gallery-details-col d-flex flex-column gap-sm" style="flex:1;">
                @foreach($exhibitions->take(2) as $exhibition)
                    @if($exhibition->primaryImage)
                        <div class="gallery-details-image-item">
                            <div class="gallery-details-image-sub-item gallery-large-img">
                                <a class="sub-heading-4" href="{{ app()->getLocale() === 'ar'
                                    ? route('gallery.details', $exhibition->id)
                                    : route('gallery.details.en', $exhibition->id) }}">
                                    <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}"
                                        alt="{{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                                    <div class="image-overlay">
                                        <span class="sub-heading-2">
                                            {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- العمود الثاني --}}
            <div class="gallery-details-col d-flex flex-column gap-sm" style="flex:1;">
                <div class="d-flex gap-sm">
                    @foreach($exhibitions->slice(2, 2) as $exhibition)
                        @if($exhibition->primaryImage)
                            <div class="gallery-details-image-sub-item gallery-small-img" style="flex:0.5;">
                                <a class="sub-heading-4" href="{{ app()->getLocale() === 'ar'
                                    ? route('gallery.details', $exhibition->id)
                                    : route('gallery.details.en', $exhibition->id) }}">
                                    <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}"
                                        alt="{{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                                    <div class="image-overlay">
                                        <span class="sub-heading-2">
                                            {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($exhibitions->count() > 4 && $exhibitions[4]->primaryImage)
                    @php $exhibition = $exhibitions[4]; @endphp
                    <div class="gallery-details-image-item mt-sm">
                        <div class="gallery-details-image-sub-item gallery-large-img">
                            <a class="sub-heading-4" href="{{ app()->getLocale() === 'ar'
                                ? route('gallery.details', $exhibition->id)
                                : route('gallery.details.en', $exhibition->id) }}">
                                <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}"
                                    alt="{{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                                <div class="image-overlay">
                                    <span class="sub-heading-2">
                                        {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>



        <!-- CTA Button -->
        <div class="w-100 text-center">
            <button onclick="window.location.href='{{ route('gallery.index') }}'" class="btn btn-custom-primary mx-auto">
                {{ __('site.view_gallery') }}
            </button>
        </div>

    </div>
</section>


  <!-- ===== FAQ SECTION ===== -->
  <section class="faq-section">
    <div class="container d-flex flex-column gap-l">
        <!-- Heading -->
        <div class="heading text-center d-flex flex-column gap-sm-5">
            <h3 class="heading-h6 mb-0">
                {{ __('site.faq_title') }}
            </h3>
            <p class="body-2 text-caption mb-0">
                {{ __('site.faq_subtitle') }}
            </p>
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
 </section>




@endsection

@push('scripts')
<script>
  // Hero Carousel Auto-play
  document.addEventListener('DOMContentLoaded', function() {
    var heroCarousel = new bootstrap.Carousel(document.getElementById('hero-carousel'), {
      interval: 5000,
      wrap: true
    });
  });

  // Package Selection Form
  document.querySelector('.package-selection-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Get form data
    const formData = new FormData(this);
    const unitType = formData.get('unit-type');
    const area = formData.get('area');
    const budget = formData.get('budget');
    const electric = formData.get('electric');

    // Scroll to packages section
    document.querySelector('.package-section').scrollIntoView({
      behavior: 'smooth'
    });

    // You can add filtering logic here based on form data
    console.log('Form submitted:', { unitType, area, budget, electric });
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    const packagesWrapper = document.getElementById('packages-wrapper');
    const filterForm = document.getElementById('package-filter-form');
    const filterUrl = "{{ app()->getLocale() === 'en' ? route('packages.filter.en') : route('packages.filter') }}";

    function fetchPackages(formData = null) {
        let url = filterUrl;

        // اجبار POST حتى بدون فلتر
        const data = formData || new FormData();

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: data
        })
        .then(res => res.text())
        .then(html => {
            packagesWrapper.innerHTML = html;
        })
        .catch(err => console.error(err));
    }

    // أول تحميل الصفحة
    fetchPackages();

    // عند الضغط على فلتر
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetchPackages(formData);
    });
});


document.getElementById('package-filter-form').addEventListener('submit', function(e) {
    let valid = true;

    document.querySelectorAll('.question').forEach(function(questionDiv) {
        const type = questionDiv.dataset.type;
        const required = questionDiv.dataset.required === '1';
        const errorMessage = questionDiv.querySelector('.error-message');

        if (!required) {
            // إذا لم يكن مطلوبًا، لا داعي للتحقق
            if (errorMessage) errorMessage.style.display = 'none';
            return;
        }

        let hasValue = false;

        if (type === 'checkbox') {
            const checkboxes = questionDiv.querySelectorAll('input[type="checkbox"]:checked');
            hasValue = checkboxes.length > 0;
        }
        else if (type === 'radio') {
            const radios = questionDiv.querySelectorAll('input[type="radio"]:checked');
            hasValue = radios.length > 0;
        }

        if (!hasValue) {
            valid = false;
            if (errorMessage) errorMessage.style.display = 'block';
        } else {
            if (errorMessage) errorMessage.style.display = 'none';
        }
    });

    if (!valid) {
        e.preventDefault();
        const firstInvalid = document.querySelector('.error-message[style*="block"]');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

</script>
@endpush

