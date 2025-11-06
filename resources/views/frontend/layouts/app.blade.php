<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (View::hasSection('meta'))
  @yield('meta')
@else
{{-- Title --}}
<title>
  @if(  app()->getLocale() === 'ar')
      {{ $seo->meta_title_ar ?? 'العنوان الافتراضي' }}
  @else
      {{ $seo->meta_title_en ?? 'Default Title' }}
  @endif
</title>
{{-- Description --}}
<meta name="description" content="{{  app()->getLocale() === 'ar' ? ($seo->meta_description_ar ?? 'الوصف الافتراضي') : ($seo->meta_description_en ?? 'Default description') }}">
{{-- Canonical URL --}}
<link rel="canonical" href="{{  url()->current() }}{{ $seo->slug_en }}">
{{-- Index/NoIndex --}}
@if($seo && $seo->index_status === 'noindex')
  <meta name="robots" content="noindex, follow">
@else
  <meta name="robots" content="index, follow">
@endif
{{-- hreflang (علشان SEO متعدد اللغات) --}}
<link rel="alternate" href="{{ url()->current() }}" hreflang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" />
{{-- OpenGraph --}}
<meta property="og:title" content="{{  app()->getLocale() === 'ar' ? ($seo->meta_title_ar ?? '') : ($seo->meta_title_en ?? '') }}">
<meta property="og:description" content="{{  app()->getLocale() === 'ar' ? ($seo->meta_description_ar ?? '') : ($seo->meta_description_en ?? '') }}">
<meta property="og:url" content="{{  url()->current() }}{{ $seo->slug_en }}">
@endif

<!-- ===== EXTERNAL LIBRARIES ===== -->
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
  <!-- ===== CUSTOM CSS ===== -->
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/pages/homepage' . (app()->getLocale() === 'ar' ? '' : '_en') . '.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/utilities/translations.css') }}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.0/css/flag-icons.min.css" />

  <!-- ===== FAVICON ===== -->
  <link rel="shortcut icon" href="{{ asset('assets/images/logos/Logo.png') }}" type="image/x-icon" />
  <style>
    .process-section .step-icon.no-after::after {
        display: none;
    }
    .btn:hover {
    color: var(--bs-btn-hover-color);
    background-color: #6c757d2e;
    border-color: #6c757d82;
}
.dropdown-toggle {
border: none !important;
}


.dropdown-menu {

    min-width: 245px;
}
        </style>
  @stack('styles')
</head>
<body>
  <!-- ===== HEADER SECTION ===== -->
  <header class="header container">
    <div class="header-container">
      <!-- Logo -->
      <div class="header-logo">
        <img src="{{ asset('assets/images/logos/logo-white.svg') }}" alt="SOFA Experience" />
      </div>
      <!-- Navigation -->
      <nav class="header-nav">
        <ul class="header-nav-list">
            <li class="header-nav-item {{ request()->routeIs('home*') ? 'active' : '' }}">
                <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="header-nav-link">{{ __('site.home') }}</a>
            </li>
            <li class="header-nav-item {{ request()->routeIs('packages.*') ? 'active' : '' }}">
                <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="header-nav-link">{{ __('site.categories') }}</a>
            </li>
            <li class="header-nav-item {{ request()->routeIs('about*') ? 'active' : '' }}">
                <a href="{{ app()->getLocale() == 'ar' ? route('about') : route('about.en') }}" class="header-nav-link">{{ __('site.about_us') }}</a>
            </li>
            <li class="header-nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}">
                <a href="{{ app()->getLocale() == 'ar' ? route('gallery.index') : route('gallery.index.en') }}" class="header-nav-link">{{ __('site.mgallery') }}</a>
            </li>
            <li class="header-nav-item {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                <a href="{{ app()->getLocale() == 'ar' ? route('blog.index') : route('blog.index.en') }}" class="header-nav-link">{{ __('site.blog') }}</a>
            </li>
            <li class="header-nav-item {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                <a href="{{ app()->getLocale() == 'ar' ? route('contact.index') : route('contact.index.en') }}" class="header-nav-link">{{ __('site.contact') }}</a>
            </li>
        </ul>
    </nav>
      <!-- Actions -->
      <div class="header-actions">
        <!-- Auth -->
        @auth
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="countryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('assets/images/icons/user-white.svg') }}" alt="User" class="dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;" />
                <span class="selected-code" style="color: white">{{auth()->user()->name}}</span>
              </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ app()->getLocale() == 'ar' ? route('profile.index') : route('profile.index.en') }}">
                    <img src="{{ asset('assets/images/icons/user.svg') }}" />
                    {{ __('site.my_account') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ app()->getLocale() == 'ar' ? route('order.my') : route('order.my.en') }}">
                    <img src="{{ asset('assets/images/icons/Profile_IconAcount.png') }}" />
                    {{ __('site.my_orders') }}
                </a>
            </li>
              @if(auth()->user()->isAdmin())
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ app()->getLocale() == 'ar' ? route('admin.dashboard') : route('admin.dashboard.en') }}">{{ __('site.dashboard') }}</a></li>
              @endif
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();" style="color: #d4342b">
                    <img src="{{ asset('assets/images/icons/logout.svg') }}" />
                    {{ __('site.logout') }}
                  </a>
                </form>
              </li>
            </ul>
          </div>
        @else
          <img src="{{ asset('assets/images/icons/user-white.svg') }}" alt="User" data-bs-toggle="modal" data-bs-target="#authModal" style="cursor: pointer;" />
        @endauth
        <!-- Language Dropdown -->
        <div class="dropdown language-dropdown">
            <!-- Button -->
            <button class="bg-transparent border-0 dropdown-toggle language-dropdown-btn" type="button"
              id="languageDropdown" aria-expanded="false">
              <img src="{{ asset('assets/images/icons/globle-white.svg') }}" alt="Globe" />
            </button>
            <!-- Dropdown Menu -->
            <ul class="dropdown-menu language-dropdown-menu" aria-labelledby="languageDropdown">
                <li class="language-option" data-language="ar" onclick="changeLanguage('ar')">
                    <div class="d-flex gap-sm-5 align-items-center">
                        🇸🇦
                        <span class="body-2 text-body">العربية</span>
                    </div>
                    <input  onclick="changeLanguage('ar')" type="radio" name="language" id="arabicRadio" {{ app()->getLocale() == 'ar' ? 'checked' : '' }} />
                </li>
                <li class="hr"></li>
                <li class="language-option" data-language="en" onclick="changeLanguage('en')">
                    <div class="d-flex gap-sm-5 align-items-center">
                        🇺🇲
                        <span class="body-2 text-body">English</span>
                    </div>
                    <input onclick="changeLanguage('en')" type="radio" name="language" id="englishRadio" {{ app()->getLocale() == 'en' ? 'checked' : '' }} />
                </li>
            </ul>
          </div>
        <!-- Help -->
        <a class="btn border-btn" href="{{ app()->getLocale() == 'ar' ? route('help.index') : route('help.index.en') }}" style="min-width: 132px;">
          {{ __('site.help') }}
        </a>
    </div>
      <!-- Mobile Menu -->
      <div class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fa-solid fa-bars text-white" style="font-size: 20px;"></i>
      </div>
    </div>
  </header>
  <!-- NAV MOBILE OVERLAY -->
  <div class="nav-mobile-overlay" id="navMobileOverlay"></div>
  <!-- NAV MOBILE DRAWER -->
  <div class="nav-mobile-drawer" id="navMobileDrawer">
    <!-- Close Button -->
    <div class="nav-mobile-header" id="navMobileClose">
      <i class="fas fa-times" style="font-size: 18px;"></i>
      <p class="body-1 text-subheading mb-0"> {{ __('site.close') }}</p>
    </div>
    <!-- Content -->
    <div class="nav-mobile-content">
      <!-- Navigation Links -->
      <div class="nav-mobile-group">
        <ul class="nav-mobile-nav-list">
          <li class="nav-mobile-nav-item {{ request()->routeIs('home*') ? 'active' : '' }}">
            <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="nav-mobile-nav-link body-1">{{ __('site.home') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('packages.*') ? 'active' : '' }}">
            <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="nav-mobile-nav-link body-1">{{ __('site.categories') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('about*') ? 'active' : '' }}">
            <a href="{{ app()->getLocale() == 'ar' ? route('about') : route('about.en') }}" class="nav-mobile-nav-link body-1">{{ __('site.about_us') }} </a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}">
            <a href="{{ app()->getLocale() == 'ar' ? route('gallery.index') : route('gallery.index.en') }}" class="nav-mobile-nav-link body-1">{{ __('site.mgallery') }}</a>
          </li>
          <li class="nav-mobile-nav-item  {{ request()->routeIs('blog.*') ? 'active' : '' }}">
            <a href="{{ app()->getLocale() == 'ar' ? route('blog.index') : route('blog.index.en') }}" class="nav-mobile-nav-link body-1">{{ __('site.blog') }}</a>
          </li>
          <li class="nav-mobile-nav-item  {{ request()->routeIs('contact.*') ? 'active' : '' }}">
            <a href="{{ app()->getLocale() == 'ar' ? route('contact.index') : route('contact.index.en') }}" class="nav-mobile-nav-link body-1">{{ __('site.contact') }}</a>
          </li>
        </ul>
      </div>
      <div class="nav-mobile-hr"></div>
      <!-- Language Selection -->
      <div class="nav-mobile-language">
        <div class="nav-mobile-language-option" data-language="ar" onclick="changeLanguage('ar')">
          <div class="d-flex align-items-center gap-sm-5">
            🇸🇦
            <p class="body-2 mb-0">العربية</p>
          </div>
          <input type="radio" name="mobile-language" id="mobileArabicRadio" {{ app()->getLocale() == 'ar' ? 'checked' : '' }} />
        </div>
        <div class="nav-mobile-language-option" data-language="en" onclick="changeLanguage('en')" >
          <div class="d-flex align-items-center gap-sm-5">
            🇺🇲
            <p class="body-2 mb-0">English</p>
          </div>
          <input type="radio" name="mobile-language" id="mobileEnglishRadio" {{ app()->getLocale() == 'en' ? 'checked' : '' }} />
        </div>
      </div>
      <div class="nav-mobile-hr"></div>
      <!-- Actions Buttons -->
      <div class="nav-mobile-action-button">
        @auth
          <div class="d-flex align-items-center gap-sm-4">
            <img src="{{ asset('assets/images/icons/user.svg') }}" alt="User" />
            <p class="body-2 mb-0 sub-heading-4 text-body">حسابي</p>
          </div>
          <div class="d-flex align-items-center gap-sm-4">
            <img src="{{ asset('assets/images/icons/order.svg') }}" alt="Cart" />
            <p class="body-2 mb-0 sub-heading-4 text-body">طلباتي</p>
          </div>
          <div class="d-flex align-items-center gap-sm-4 text-danger">
            <img src="{{ asset('assets/images/icons/logout.svg') }}" alt="Logout" />
            <p class="body-2 mb-0 sub-heading-4 text-danger">تسجيل خروج</p>
          </div>
        @else
          <div class="d-flex align-items-center gap-sm-4" data-bs-toggle="modal" data-bs-target="#authModal">
            <img src="{{ asset('assets/images/icons/user.svg') }}" alt="User" />
            <p class="body-2 mb-0 sub-heading-4 text-body">{{ __('site.Log in / Create an account') }}</p>
          </div>
        @endauth
      </div>
    </div>
    <!-- Help Button -->
    <div class="flex-grow-1 d-flex">
      <button class="btn btn-custom-primary w-100 mt-auto">
        طلب بمساعدة
      </button>
    </div>
  </div>
  <!-- Main Content -->
  <main>
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
        <div class="container">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
        <div class="container">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      </div>
    @endif
    @yield('content')
  </main>
  <!-- ===== FOOTER SECTION ===== -->
  <footer class="footer">
    <div class="footer-container">
        <!-- Content -->
        <div class="container">
            <!-- Pattern background element -->
            <img class="footer-pattern" src="{{ asset('assets/images/footer/pattern.svg') }}" alt="Pattern" />
            <div class="row text-center">
                <!-- Column 4: Logo -->
                <div class="footer-item align-items-center align-items-md-start col-md-3 gap-sm">
                    <img src="{{ asset('assets/images/logos/logo-white.svg') }}" alt="SOFA Logo" />
                    <p class="body-2 text-white mb-0" style="opacity: 0.8">
                        {{ __('site.footer_vision') }}
                    </p>
                </div>
                <!-- Column 3: Quick Links -->
                <div class="footer-item col-md-3 gap-sm-3">
                    <h6 class="sub-heading-4 mb-0 text-white text-start">
                        {{ __('site.footer_quick_links') }}
                    </h6>
                    <div class="d-flex flex-column text-start gap-sm-4">
                        <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_home') }}
                        </a>
                        <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.categories') }}
                        </a>
                        <a href="{{ app()->getLocale() == 'ar' ? route('about') : route('about.en') }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_about') }}
                        </a>
                        <a href="{{ app()->getLocale() == 'ar' ? route('faq') : route('faq.en') }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_faq') }}
                        </a>
                        <a href="{{ app()->getLocale() == 'ar' ? route('contact.index') : route('contact.index.en') }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_contact') }}
                        </a>
                    </div>
                </div>
                <!-- Column 2: Contact Information -->
                <div class="footer-item col-md-3 gap-sm-3">
                    <h6 class="sub-heading-4 mb-0 text-white text-start">
                        {{ __('site.footer_contact_info') }}
                    </h6>
                    <div class="d-flex flex-column gap-sm-4">
                        <div class="mb-0 d-flex flex-row gap-sm-5 align-items-center">
                            <div class="footer-contact-icon">
                                <img src="{{ asset('assets/images/footer/phone.svg') }}" alt="phone" />
                            </div>
                            <span class="body-2 text-white mb-0" style="opacity: 0.8">
                                {{ $siteSettings->phone }}
                            </span>
                        </div>
                        <div class="mb-0 d-flex flex-row gap-sm-5">
                            <div class="footer-contact-icon">
                                <img src="{{ asset('assets/images/footer/email.svg') }}" alt="email" />
                            </div>
                            <span class="body-2 text-white mb-0" style="opacity: 0.8">
                                {{ $siteSettings->email }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Column 1: Follow Us -->
                <div class="footer-item col-md-3 gap-sm-3">
                    <h6 class="sub-heading-4 mb-0 text-white text-start">
                        {{ __('site.footer_follow_us') }}
                    </h6>
                    <div class="d-flex flex-row gap-sm-4">
                        <a href="{{ $siteSettings->snapchat }}"><img src="{{ asset('assets/images/social/snapchat.png') }}" alt="Snapchat" /></a>
                        <a href="{{ $siteSettings->youtube }}"><img src="{{ asset('assets/images/social/youtube.png') }}" alt="YouTube" /></a>
                        <a href="{{ $siteSettings->tiktok }}"><img src="{{ asset('assets/images/social/tiktok.png') }}" alt="TikTok" /></a>
                        <a href="{{ $siteSettings->Instagram }}"><img src="{{ asset('assets/images/social/instagram.png') }}" alt="Instagram" /></a>
                    </div>
                </div>
            </div>
            <!-- Divider -->
            <hr class="m-0" style="opacity: 0.1" />
            <!-- Copyright Section -->
            <div class="footer-bottom body-2 text-caption">
                {{ __('site.footer_copyright') }}
            </div>
        </div>
    </div>
</footer>
  <!-- Auth Modal -->
  @guest
  <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal-dialog">
      <div class="modal-content rounded-4">
        <!-- Header -->
        <div class="border-0 d-flex flex-column gap-sm-6">
          <!-- Close Button -->
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <!-- Tabs -->
          <ul class="nav nav-tabs w-100 d-flex flex-column justify-content-between border-0" id="myTab" role="tablist">
            <div class="d-flex mt-3">
              <li class="nav-item tabs_pup" role="presentation">
                <button class="nav-link nav-link sub-heading-5 active" id="home-tab" data-bs-toggle="tab"
                  data-bs-target="#login" type="button" role="tab" aria-controls="home" aria-selected="true"> @lang('site.login')</button>
              </li>
              <li class="nav-item tabs_pup" role="presentation">
                <button class="nav-link nav-link sub-heading-5" id="profile-tab" data-bs-toggle="tab"
                  data-bs-target="#register" type="button" role="tab" aria-controls="profile"
                  aria-selected="false">
                  @lang('site.register')</button>
              </li>
            </div>
          </ul>
          <!-- Body -->
          <div class="tab-content mt-4" id="myTabContent">
            <!-- Sign In -->
            <div class="tab-pane fade show active d-flex flex-column gap-sm-3" id="login" role="tabpanel" aria-labelledby="home-tab">
                <form action="{{ route('login.check') }}" method="POST">
                    @csrf
                    <!-- Phone -->
                    <div class="form-group">
                        <label class="form-label mb-0">@lang('site.phone_number')</label>
                        <div class="input-phone position-relative">
                            <div class="country-select dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="flag fi fi-sa selected-flag-login"></span>
                                <span class="code selected-code-login">+966</span>
                            </div>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="input-with-icon" style="min-height: 45px;">
                                      <input type="text" class="form-control" placeholder=" @lang('site.search_here')" />
                                      <i class="input-icon">
                                        <img src="{{asset('assets/images/icons/search-normal.png')}}" alt="@lang('site.search')">
                                      </i>
                                    </div>
                                  </li>
                                @foreach($countries as $country)
                                    <li>
                                        <a class="dropdown-item country-item-login d-flex justify-content-between align-items-center" href="#"
                                        data-flag="{{ $country['code'] }}" data-code="{{ $country['dial_code'] }}">
                                            <span class="d-flex align-items-center gap-sm-3">
                                                <span class="flag fi fi-{{ $country['code'] }}"></span>
                                                <span>{{ app()->getLocale() == 'ar' ? $country['name_ar'] : $country['name_en'] }}</span>
                                            </span>
                                            <span>{{ $country['dial_code'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="code" class="country_code_login" value="{{ old('country_code', '+966') }}" />
                            <input type="tel" name="phone"  value="{{ old('phone') }}" class="phone-number form-control mt-2" placeholder="@lang('site.example_number')" required />

                        </div>
                        @error('phone')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                    </div>
                    <br>
                    <button type="submit" class="btn btn-custom-primary w-100">@lang('site.continue')</button>
                </form>
            </div>
            <!-- Register -->

            <!-- التسجيل -->
            <div class="tab-pane fade d-flex flex-column gap-sm-3" id="register" role="tabpanel" aria-labelledby="register-tab">
                <form action="{{ route('auth.register') }}" method="POST" class="register-form">
                    @csrf
                    <!-- الاسم -->
                    <div class="form-group">
                        <label class="form-label mb-0">@lang('site.name')</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="@lang('site.name')" required />
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- رقم الهاتف + اختيار الدولة -->
                    <div class="form-group">
                        <label class="form-label mb-0">@lang('site.phone_number')</label>
                        <div class="input-phone position-relative">
                            <div class="country-select dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="flag fi fi-sa selected-flag"></span>
                                <span class="code selected-code">+966</span>
                            </div>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="input-with-icon" style="min-height: 45px;">
                                      <input type="text" class="form-control" placeholder=" @lang('site.search_here')" />
                                      <i class="input-icon">
                                        <img src="{{asset('assets/images/icons/search-normal.png')}}" alt="@lang('site.search')">
                                      </i>
                                    </div>
                                  </li>
                                @foreach($countries as $country)
                                    <li>
                                        <a class="dropdown-item country-item d-flex justify-content-between align-items-center" href="#"
                                        data-flag="{{ $country['code'] }}" data-code="{{ $country['dial_code'] }}">
                                            <span class="d-flex align-items-center gap-sm-3">
                                                <span class="flag fi fi-{{ $country['code'] }}"></span>
                                                <span>{{ app()->getLocale() == 'ar' ? $country['name_ar'] : $country['name_en'] }}</span>
                                            </span>
                                            <span>{{ $country['dial_code'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="country_code" class="country_code" value="{{ old('country_code', '+966') }}"/>
                            <input type="tel" name="phone"  value="{{ old('phone') }}"  class="phone-number form-control mt-2" placeholder="@lang('site.example_number')" required />
                        </div>
                        @error('phone')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="form-group">
                        <label class="form-label mb-0">@lang('site.email')</label>
                        <input type="email"  value="{{ old('email') }}"  name="email" class="form-control" placeholder="@lang('site.email')" required/>
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <br>
                    <button type="submit" id="registerSubmit" class="btn btn-custom-primary w-100">@lang('site.continue')</button>
                </form>
            </div>





          </div>
        </div>
      </div>
    </div>
  </div>


    <!-- ✅ مودال OTP منفصل (خارج authModal) -->
    <div class="modal fade" id="codeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 p-3">
        <h3 class="mb-3 text-center">@lang('site.verification code')</h3>
        <h6 class="mb-3 text-center">@lang('site.mailsend')</h6>
        <form method="POST" action="{{ route('verify.code') }}">
            @csrf
            <div class="d-flex gap-2 justify-content-center mb-3">
            <input type="text" name="code[0]" maxlength="1" class="otp-input form-control text-center" style="width: 50px;" required />
            <input type="text" name="code[1]" maxlength="1" class="otp-input form-control text-center" style="width: 50px;" required />
            <input type="text" name="code[2]" maxlength="1" class="otp-input form-control text-center" style="width: 50px;" required />
            <input type="text" name="code[3]" maxlength="1" class="otp-input form-control text-center" style="width: 50px;" required />
            <input type="text" name="code[4]" maxlength="1" class="otp-input form-control text-center" style="width: 50px;" required />
            </div>

            @if(session('otp_error'))
            <div class="text-danger text-center mb-2">{{ session('otp_error') }}</div>
            @endif

            <button type="submit" class="btn btn-custom-primary w-100"> @lang('site.verification')</button>
        </form>
        </div>
    </div>
    </div>


    @if(session('open_login_tab'))
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const authModal = new bootstrap.Modal(document.getElementById('authModal'));
        authModal.show();
        const loginTab = document.querySelector('#home-tab');
        if (loginTab) {
            const tab = new bootstrap.Tab(loginTab);
            tab.show();
        }
    });
    </script>
    @endif

    @if(session('open_register_tab'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registerTab = document.getElementById('profile-tab'); // زر التسجيل
            if (registerTab) {
                const authModal = new bootstrap.Modal(document.getElementById('authModal'));
                authModal.show();
                // تفعيل تاب التسجيل
                const tab = new bootstrap.Tab(registerTab);
                tab.show();
            }
        });
    </script>
    @endif

    <!-- في نهاية الصفحة (قبل </body>) -->
    @if(session('show_otp_modal'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // إغلاق مودال التسجيل أولًا
            const authModal = bootstrap.Modal.getInstance(document.getElementById('authModal'));
            if (authModal) authModal.hide();

            // فتح مودال OTP بعد تأخير بسيط
            setTimeout(function() {
                const otpModal = new bootstrap.Modal(document.getElementById('codeModal'));
                otpModal.show();
            }, 300);
        });
    </script>
     @endif
  @endguest
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="{{ asset('assets/js/language.js') }}"></script>
    <script src="{{ asset('assets/js/nav-bar.js') }}"></script>
  <!-- Custom Scripts -->

  <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ===== Mobile Menu Toggle =====
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const navOverlay = document.getElementById('navMobileOverlay');
        const navDrawer = document.getElementById('navMobileDrawer');

        if (mobileToggle && navOverlay && navDrawer) {
            mobileToggle.addEventListener('click', function() {
                navOverlay.classList.add('active');
                navDrawer.classList.add('active');
            });
            document.getElementById('navMobileClose').addEventListener('click', function() {
                navOverlay.classList.remove('active');
                navDrawer.classList.remove('active');
            });
            navOverlay.addEventListener('click', function() {
                navOverlay.classList.remove('active');
                navDrawer.classList.remove('active');
            });
        }

        // ===== Language Dropdown =====
        document.querySelectorAll('.language-option').forEach(option => {
            option.addEventListener('click', function() {
                const language = this.getAttribute('data-language');
                console.log('Language changed to:', language);
            });
        });

        // ===== Owl Carousel =====
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

        // ===== Change Language (AJAX) =====
        window.changeLanguage = function(locale) {
            let currentUrl = window.location.href.replace('/public', '');
            fetch("{{ route('setLocale') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ locale: locale, current_url: currentUrl })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (locale === 'ar') {
                        window.location.href = data.redirect.replace('/ar', '');
                    } else {
                        window.location.href = data.redirect;
                    }
                }
            });
        };


        // ===== اختيار الدولة =====
    const countryItems = document.querySelectorAll('.country-item');
    const countryCodeInput = document.querySelector('.country_code');
    const selectedCode = document.querySelector('.selected-code');
    const selectedFlag = document.querySelector('.selected-flag');

    countryItems.forEach(item => {
        item.addEventListener('click', function(e){
            //e.preventDefault();
            const code = this.dataset.code;
            const flag = this.dataset.flag;

            if (countryCodeInput) countryCodeInput.value = code;
            if (selectedCode) selectedCode.textContent = code;
            if (selectedFlag) selectedFlag.className = 'flag fi fi-' + flag + ' selected-flag';
        });
    });


    // ===== اختيار الدولة في تاب تسجيل الدخول =====
const countryItemsLogin = document.querySelectorAll('.country-item-login');
const countryCodeInputLogin = document.querySelector('.country_code_login');
const selectedCodeLogin = document.querySelector('.selected-code-login');
const selectedFlagLogin = document.querySelector('.selected-flag-login');

countryItemsLogin.forEach(item => {
    item.addEventListener('click', function(e){
        e.preventDefault();
        const code = this.dataset.code;
        const flag = this.dataset.flag;
        if (countryCodeInputLogin) countryCodeInputLogin.value = code;
        if (selectedCodeLogin) selectedCodeLogin.textContent = code;
        if (selectedFlagLogin) selectedFlagLogin.className = 'flag fi fi-' + flag + ' selected-flag-login';
    });
});

   // دعم التنقل بين حقول OTP
const otpInputs = document.querySelectorAll('.otp-input');
otpInputs.forEach((input, index) => {
    input.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 1);
        if (this.value && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }
    });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !this.value && index > 0) {
            otpInputs[index - 1].focus();
        }
    });
});


    });
    </script>

  @stack('scripts')
</body>
</html>
