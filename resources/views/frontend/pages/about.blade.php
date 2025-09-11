@extends('frontend.layouts.pages')

@section('title', __('site.about_us') . ' - SOFA Experience')
@section('description', 'تعرف على رؤية وقيم SOFA Experience في تقديم حلول التأثيث الفندقي الذكية')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('about') : route('about.en') }}" class="body-2 text-primary">{{ __('site.about_us') }}</a>
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
                <h6 class="heading-h6">{{ __('site.our_vision_title') }}</h6>
                <p class="caption-4 mb-0">
                    {{ __('site.our_vision_text') }}
                </p>
            </div>

            <!-- List -->
            <ul class="d-flex flex-column gap-sm-4">
                <li class="sub-heading-4">{{ __('site.our_vision_item1') }}</li>
                <li class="sub-heading-4">{{ __('site.our_vision_item2') }}</li>
                <li class="sub-heading-4">{{ __('site.our_vision_item3') }}</li>
            </ul>
        </div>

        <!-- Image -->
        <div class="our-vision-image">
            <img src="{{ asset('assets/images/about/about-01.jpg') }}" alt="{{ __('site.our_vision_title') }}" />
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
            <img src="{{ asset('assets/images/about/about-02.jpg') }}" alt="{{ __('site.values') }}" />
        </div>

        <!-- Content -->
        <div class="our-values-content d-flex flex-column gap-md">
            <!-- Heading -->
            <div class="d-flex flex-column gap-sm-5">
                <h6 class="heading-h6">{{ __('site.values') }}</h6>
                <p class="caption-4 mb-0" style="max-width: 274px;">
                    {{ __('site.values_description') }}
                </p>
            </div>

            <!-- List -->
            <ul class="list-unstyled d-flex flex-column gap-sm-4">
                <li class="sub-heading-4">{{ __('site.value_1') }}</li>
                <li class="sub-heading-4">{{ __('site.value_2') }}</li>
                <li class="sub-heading-4">{{ __('site.value_3') }}</li>
                <li class="sub-heading-4">{{ __('site.value_4') }}</li>
                <li class="sub-heading-4">{{ __('site.value_5') }}</li>
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
                <h2 class="heading-h6 text-white">{{ __('site.why_sofa_title') }}</h2>
                <p class="caption-4 mb-0">{{ __('site.why_sofa_description') }}</p>
            </div>

            <!-- List -->
            <ul class="list-unstyled d-flex flex-column gap-sm-4">
                <li class="body-3 mb-0">{{ __('site.why_sofa_item_1') }}</li>
                <li class="body-3 mb-0">{{ __('site.why_sofa_item_2') }}</li>
                <li class="body-3 mb-0">{{ __('site.why_sofa_item_3') }}</li>
                <li class="body-3 mb-0">{{ __('site.why_sofa_item_4') }}</li>
            </ul>
        </div>

        <!-- Image -->
        <div class="why-sofa-image flex-1">
            <img src="{{ asset('assets/images/about/about-03.jpg') }}" alt="{{ __('site.why_sofa_title') }}" />
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
                    <p class="sub-heading-5 text-white mb-0">{{ __('site.smart_steps_user_name') }}</p>
                    <p class="body-4 text-white mb-0">{{ __('site.smart_steps_units') }}</p>
                </div>
            </div>

            <!-- info item -->
            <div class="our-smart-steps-info-item">
                <!-- content -->
                <div class="our-smart-steps-info-content d-flex flex-column gap-sm-4">
                    <p class="body-4 text-white mb-0" style="opacity: 0.8">{{ __('site.smart_steps_status_label') }}</p>
                    <p class="body-4 text-white mb-0">{{ __('site.smart_steps_status') }}</p>
                    <div class="our-smart-steps-info-progress">
                        <div class="our-smart-steps-info-progress-bar" style="width: {{ __('site.smart_steps_progress') }};"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- steps -->
        <div class="our-smart-steps-steps d-flex flex-column gap-md">
            <!-- heading -->
            <div class="our-smart-steps-heading">
                <h2 class="heading-h6">{{ __('site.smart_steps_title') }}</h2>
                <p class="caption-3 mb-0">{{ __('site.smart_steps_description') }}</p>
            </div>

            <!-- list -->
            <ul class="our-smart-list list-unstyled d-flex flex-column gap-sm-4 p-0">
                @for ($i = 1; $i <= 7; $i++)
                    <li class="d-flex gap-sm-3 align-items-center">
                        <div class="our-smart-steps-step-icon">
                            <img src="{{ asset('assets/images/about/step-0' . $i . '.svg') }}" alt="Step {{ $i }}" />
                        </div>

                        <!-- content -->
                        <div class="our-smart-steps-step-content">
                            <p class="sub-heading-5 mb-0">{{ __('site.smart_steps_step_' . $i) }}</p>
                        </div>
                    </li>
                @endfor
            </ul>

            <!-- start button -->
            <div class="our-smart-steps-start-button">
                <a href="{{ app()->getLocale() == 'ar' ? route('categories.index') : route('categories.index.en') }}" class="btn btn-custom-secondary w-100">
                    {{ __('site.smart_steps_start_button') }}
                </a>
            </div>
        </div>
    </div>
</section>


<!-- ===== READY TO FURNISH YOUR UNIT SECTION ===== -->
<section class="ready-to-furnish-your-unit">
    <div class="ready-to-furnish-your-unit-container container position-relative bg-primary">
        <!-- pattern -->
        <img class="ready-to-furnish-your-unit-pattern" src="{{ asset('assets/images/about/pattern.svg') }}" alt="{{ __('site.pattern') }}" />

        <!-- content -->
        <div class="ready-to-furnish-your-unit-content d-flex flex-column gap-sm">
            <div class="d-flex flex-column gap-sm-5">
                <h2 class="heading-h6 text-white">{{ __('site.ready_to_furnish_heading') }}</h2>
                <p class="caption-4 mb-0">
                    {{ __('site.ready_to_furnish_text') }}
                </p>
            </div>

            <!-- buttons -->
            <div class="ready-to-furnish-your-unit-buttons d-flex gap-sm-3">
                <a href="https://wa.me/{{$siteSettings->whatsapp}}" target="_blank" class="btn btn-custom-secondary d-flex align-items-center gap-2">
                    <p class="text-nowrap mb-0">{{ __('site.whatsapp_button') }}</p>
                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                </a>
                <a href="{{ app()->getLocale() == 'ar' ? route('contact.index') : route('contact.index.en') }}" class="btn border-btn">
                    <p class="text-nowrap mb-0">{{ __('site.order_now_button') }}</p>
                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-icon" style="font-size: 18px;"></i>
                </a>
            </div>
        </div>

        <!-- image -->
        <div class="ready-to-furnish-your-unit-image-container">
            <div class="ready-to-furnish-your-unit-image">
                <img src="{{ asset('assets/images/about/about-05.jpg') }}" alt="{{ __('site.ready_to_furnish_heading') }}" />
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
                <h6 class="heading-h6">{{ __('site.why_do_we_serve_heading') }}</h6>
                <p class="caption-4 mb-0">{{ __('site.why_do_we_serve_text') }}</p>
            </div>

            <!-- List -->
            <ul class="d-flex flex-column gap-sm-4">
                @foreach(__('site.serve_list') as $item)
                    <li class="sub-heading-4">{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        <!-- images -->
        <div class="why-do-we-serve-images d-flex gap-sm-3">
            <div class="d-flex flex-column gap-sm-3 mt-4">
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-06.jpg') }}" alt="{{ __('site.why_do_we_serve_heading') }}" />
                </div>
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-07.jpg') }}" alt="{{ __('site.why_do_we_serve_heading') }}" />
                </div>
            </div>
            <div class="d-flex flex-column gap-sm-3">
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-08.jpg') }}" alt="{{ __('site.why_do_we_serve_heading') }}" />
                </div>
                <div class="why-do-we-serve-image">
                    <img src="{{ asset('assets/images/about/about-09.jpg') }}" alt="{{ __('site.why_do_we_serve_heading') }}" />
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/about.css') }}">
@endpush
