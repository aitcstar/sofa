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
            {{ optional($steps->where('order', 0)->first())->{'title_'.app()->getLocale()} }}
        </h5>

        <!-- ===== Desktop Layout ===== -->
        <div class="steps-desktop d-none d-md-flex gap-sm-3 position-relative mx-auto">
            <div class="line"></div>

            @foreach($steps as $step)
                @if($step->order != 0)
                <div class="step-box d-flex flex-column align-items-center gap-sm-4">
                    <div class="step-item-icon">
                        <img src="{{ asset('storage/' . $step->icon) }}" />
                    </div>

                    <div class="d-flex flex-column align-items-center">
                        <p class="sub-heading-4 mb-0" style="font-size: 12px">
                            {{ $step->{'title_'.app()->getLocale()} }}
                        </p>

                        <div class="body-3 text-caption mb-0" style="font-size: 10px">
                            {{ $step->{'desc_'.app()->getLocale()} }}
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- ===== Mobile Slider (No Libraries) ===== -->
        <div class="steps-mobile d-block d-md-none">
            <div class="steps-track" id="steps-track">

                @foreach($steps as $step)
                    @if($step->order != 0)
                    <div class="step-slide">
                        <img src="{{ asset('storage/' . $step->icon) }}" class="slide-icon" />

                        <p class="sub-heading-4 mb-1" style="font-size: 13px">
                            {{ $step->{'title_'.app()->getLocale()} }}
                        </p>

                        <div class="body-3 text-caption" style="font-size: 10px">
                            {{ $step->{'desc_'.app()->getLocale()} }}
                        </div>
                    </div>
                    @endif
                @endforeach

            </div>
        </div>

    </div>
</section>


<style>
/* ===== DESKTOP ===== */
.steps-desktop {
    display: flex;
    gap: 24px;
    position: relative;
    max-width: 930px;
    margin: 0 auto;
}

.steps-desktop .line {
    position: absolute;
    top: 20px;
    left: 60px;
    height: 4px;
    width: calc(100% - 120px);
    background: #ccc;
}

.step-box {
    width: 150px;
    text-align: center;
}

/* ===== MOBILE SLIDER ===== */
.steps-mobile {
    overflow-x: scroll;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; /* إخفاء السكرول */
}
.steps-mobile::-webkit-scrollbar {
    display: none;
}

.steps-track {
    display: flex;
    gap: 20px;
}

.step-slide {
    min-width: 75%;
    scroll-snap-align: center;
    text-align: center;
    background: #fff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.slide-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 8px;
}

/* bootstrap-like helpers */
.d-none { display: none !important; }
.d-block { display: block !important; }
@media(min-width:768px){
    .d-md-flex { display:flex !important; }
    .d-md-none { display:none !important; }
}
@media(max-width:767px){
    .d-md-flex { display:none !important; }
    .d-md-none { display:block !important; }
}

</style>


<!-- تأكد قبل هذا إن jQuery + Owl Carousel JS محمّلين -->
<script>
    document.addEventListener("DOMContentLoaded", function () {

        if (window.innerWidth > 767) return;

        const container = document.querySelector('.steps-mobile');
        const slides = document.querySelectorAll('.step-slide');
        let index = 0;

        setInterval(() => {
            index = (index + 1) % slides.length;
            const slide = slides[index];

            container.scrollTo({
                left: slide.offsetLeft - 20, // يروح لنفس الخطوة
                behavior: 'smooth'
            });

        }, 2500);

    });
    </script>






  <!-- ===== ABOUT US SECTION ===== -->
  <section class="about-us-section">
    <div class="container position-relative">
        <img src="{{ asset('assets/images/about/pattern.svg') }}" alt="pattern" class="pattern" />

        @if($about)
            <div class="about-us-content d-flex gap-5xl">
                <!-- Image -->
                <div class="order-1 order-md-2 position-relative">
                    <div class="image border-surface radius-normal-box overflow-hidden">
                        <img class="w-100 h-100"
                             src="{{ asset('storage/'.$about->image) }}"
                             alt="{{ $about->{'title_'.app()->getLocale()} }}" />
                    </div>
                </div>

                <!-- Content -->
                <div class="order-2 order-md-1 d-flex flex-column gap-l mx-auto" style="max-width: 470px;">
                    <div class="d-flex flex-column gap-sm-5">
                        <p class="sub-heading-4" style="color: var(--secondary);">
                            {{ $about->{'sub_title_'.app()->getLocale()} }}
                        </p>
                        <h4 class="heading-h6">{{ $about->{'title_'.app()->getLocale()} }}</h4>
                        <p class="caption-3 text-caption mb-0">
                            {{ $about->{'desc_'.app()->getLocale()} }}
                        </p>
                    </div>

                    <!-- Features -->
                    @if($about->icons->count())
                        <div class="d-flex flex-column gap-sm-3">
                            @foreach($about->icons as $icon)
                                <div class="d-flex gap-sm-4 align-items-center">
                                    <img src="{{ asset('storage/'.$icon->icon) }}"
                                         alt="{{ $icon->{'title_'.app()->getLocale()} }}"
                                         style="width: 32px; height: 32px; object-fit: contain;" />
                                    <span class="body-1">{{ $icon->{'title_'.app()->getLocale()} }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($about->button_link && $about->{'button_text_'.app()->getLocale()})
                        <a href="{{ app()->getLocale() == 'ar' ? route('about') : route('about.en') }}" class="btn btn-custom-primary w-100 mt-3">
                            {{ $about->{'button_text_'.app()->getLocale()} }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>


  <!-- ===== PACKAGE SELECTION SECTION ===== -->
  <section id="package-selection" class="package-selection-section">
    <div class="container d-flex flex-column gap-xl">
        <div class="heading text-center d-flex flex-column gap-sm-5">
            <h3 class="heading-h6 mb-0">  {{ $section->{'title_'.app()->getLocale()} }}</h3>
            <p class="caption-3 text-caption mb-0 mx-auto" style="max-width: 440px;">
                {{ $section->{'desc_'.app()->getLocale()} }}
            </p>
        </div>

        <form id="package-filter-form" class="package-selection-form padding-box-normal d-flex flex-column gap-md border border-surface radius-small-box mx-auto">
            @csrf
           {{-- 🔹 اختيار اسم الباقة --}}
            <div class="mb-3">
                <label class="form-label body-1">{{ __('site.unit_type') }}</label>
                <div class="d-flex gap-sm-5 flex-wrap question" data-type="radio" data-required="0">
                    @foreach($packages as $package)
                        @php
                            $name = app()->getLocale() === 'ar' ? $package->name_ar : $package->name_en;
                        @endphp
                        <div class="d-flex align-items-center gap-sm-5">
                            <input type="radio" name="answers[5]" id="package_{{ $package->id }}" value="{{ $name }}">
                            <label for="package_{{ $package->id }}">{{ $name }}</label>
                        </div>
                    @endforeach
                    {{-- رسالة خطأ --}}
                    <div class="error-message text-danger" style="display:none; margin-top:5px;">
                        يرجى اختيار خيار واحد على الأقل
                    </div>
                </div>
            </div>

            {{-- 🔸 فلترة حسب اللون --}}
            <div class="mb-3">
                <label class="form-label body-1">{{ __('site.package_colors') }}</label>
                <div class="d-flex gap-sm-5 flex-wrap question" data-type="radio" data-required="0">
                    @foreach($allColors as $color)
                        @php
                            $colorName = app()->getLocale() === 'ar' ? $color['name_ar'] : $color['name_en'];
                        @endphp
                        <div class="d-flex align-items-center gap-sm-5">
                            <input type="radio" name="answers[6]" id="color_{{ $loop->index }}" value="{{ $colorName }}">
                            <label for="color_{{ $loop->index }}">{{ $colorName }}</label>
                        </div>
                    @endforeach
                    {{-- رسالة خطأ --}}
                    <div class="error-message text-danger" style="display:none; margin-top:5px;">
                        يرجى اختيار خيار واحد على الأقل
                    </div>
                </div>
            </div>


            <button type="submit" class="btn btn-custom-primary w-100">
                {{ __('site.show_packages') }}
            </button>
        </form>
    </div>
</section>

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
                                <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}"
                                     alt="{{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                                <div class="image-overlay">
                                    <span class="sub-heading-2">
                                        {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                    </span>
                                </div>
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
                                <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}"
                                     alt="{{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                                <div class="image-overlay">
                                    <span class="sub-heading-2">
                                        {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($exhibitions->count() > 4 && $exhibitions[4]->primaryImage)
                    @php $exhibition = $exhibitions[4]; @endphp
                    <div class="gallery-details-image-item mt-sm">
                        <div class="gallery-details-image-sub-item gallery-large-img">
                            <img src="{{ asset('storage/' . $exhibition->primaryImage->image) }}"
                                 alt="{{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}">
                            <div class="image-overlay">
                                <span class="sub-heading-2">
                                    {{ app()->getLocale() === 'ar' ? $exhibition->name_ar : $exhibition->name_en }}
                                </span>
                            </div>
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

