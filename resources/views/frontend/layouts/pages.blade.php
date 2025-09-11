<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  @php
  $locale = app()->getLocale(); // ar أو en
@endphp

{{-- Title --}}
<title>
  @if($locale === 'ar')
      {{ $seo->meta_title_ar ?? 'العنوان الافتراضي' }}
  @else
      {{ $seo->meta_title_en ?? 'Default Title' }}
  @endif
</title>

{{-- Description --}}
<meta name="description" content="{{ $locale === 'ar' ? ($seo->meta_description_ar ?? 'الوصف الافتراضي') : ($seo->meta_description_en ?? 'Default description') }}">

{{-- Canonical URL --}}
@if($locale === 'ar' && !empty($seo->canonical_ar))
  <link rel="canonical" href="{{ $seo->canonical_ar }}">
@elseif($locale === 'en' && !empty($seo->canonical_en))
  <link rel="canonical" href="{{ $seo->canonical_en }}">
@endif

{{-- Index/NoIndex --}}
@if($seo && $seo->index_status === 'noindex')
  <meta name="robots" content="noindex, follow">
@else
  <meta name="robots" content="index, follow">
@endif

{{-- hreflang (علشان SEO متعدد اللغات) --}}
@if(!empty($seo->canonical_ar))
  <link rel="alternate" href="{{ $seo->canonical_ar }}" hreflang="ar" />
@endif
@if(!empty($seo->canonical_en))
  <link rel="alternate" href="{{ $seo->canonical_en }}" hreflang="en" />
@endif

{{-- OpenGraph --}}
<meta property="og:title" content="{{ $locale === 'ar' ? ($seo->meta_title_ar ?? '') : ($seo->meta_title_en ?? '') }}">
<meta property="og:description" content="{{ $locale === 'ar' ? ($seo->meta_description_ar ?? '') : ($seo->meta_description_en ?? '') }}">
<meta property="og:url" content="{{ $locale === 'ar' ? ($seo->canonical_ar ?? url('/')) : ($seo->canonical_en ?? url('/')) }}">


  <!-- ===== EXTERNAL LIBRARIES ===== -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

  <!-- ===== CUSTOM CSS ===== -->
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/pages/categories.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/utilities/translations.css') }}" />


  <!-- ===== FAVICON ===== -->
  <link rel="shortcut icon" href="{{ asset('assets/images/logos/Logo.png') }}" type="image/x-icon" />

  @stack('styles')
</head>

<body>
    <!-- ===== HEADER SECTION ===== -->
    <header class="header container">
        <div class="header-container">
          <!-- Logo -->
          <div class="header-logo">
            <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="SOFA Experience" />
          </div>
          <!-- Navigation -->
          <nav class="header-nav">
            <ul class="header-nav-list">
                <li class="header-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="header-nav-link">{{ __('site.home') }}</a>
                </li>
                <li class="header-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <a href="{{ route('categories.index', ['locale' => app()->getLocale()]) }}" class="header-nav-link">{{ __('site.categories') }}</a>
                </li>
                <li class="header-nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                    <a href="{{ route('about', ['locale' => app()->getLocale()]) }}" class="header-nav-link">{{ __('site.about_us') }}</a>
                </li>
                <li class="header-nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}">
                    <a href="{{ route('gallery.index', ['locale' => app()->getLocale()]) }}" class="header-nav-link">{{ __('site.mgallery') }}</a>
                </li>
                <li class="header-nav-item {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                    <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="header-nav-link">{{ __('site.blog') }}</a>
                </li>
                <li class="header-nav-item {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                    <a href="{{ route('contact.index', ['locale' => app()->getLocale()]) }}" class="header-nav-link">{{ __('site.contact') }}</a>
                </li>
            </ul>

        </nav>


           <!-- Actions -->
            <div class="header-actions">
                <!-- Auth -->
                @auth
                <div class="dropdown">
                    <img src="{{ asset('assets/images/icons/user.svg') }}" alt="User"
                        class="dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;" />
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('profile.edit',['locale' => app()->getLocale()]) }}">حسابي</a></li>
                    <li><a class="dropdown-item" href="#">طلباتي</a></li>
                    @if(auth()->user()->isAdmin())
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard',['locale' => app()->getLocale()]) }}">لوحة التحكم</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="#"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            تسجيل خروج
                        </a>
                        </form>
                    </li>
                    </ul>
                </div>
                @else
                <img src="{{ asset('assets/images/icons/user.svg') }}" alt="User"
                    data-bs-toggle="modal" data-bs-target="#authModal" style="cursor: pointer;" />
                @endauth

                <!-- Language Dropdown -->
                <div class="dropdown language-dropdown">
                    <!-- Button -->
                    <button class="bg-transparent border-0 dropdown-toggle language-dropdown-btn" type="button"
                    id="languageDropdown" aria-expanded="false">
                    <img src="{{ asset('assets/images/icons/translation.svg') }}" alt="Globe" />
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
                <a class="btn btn-custom-primary" href="{{ route('help.index',['locale' => app()->getLocale()]) }}">
                    {{ __('site.help') }}
                  </a>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fa-solid fa-bars text-white" style="font-size: 20px;color:#444f5a !important;"></i>
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
      <p class="body-1 text-subheading mb-0">{{ __('site.close') }}</p>
    </div>

    <!-- Content -->
    <div class="nav-mobile-content">
      <!-- Navigation Links -->
      <div class="nav-mobile-group">
        <ul class="nav-mobile-nav-list">
          <li class="nav-mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a href="{{ route('home',['locale' => app()->getLocale()]) }}" class="nav-mobile-nav-link body-1">{{ __('site.home') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <a href="{{ route('categories.index',['locale' => app()->getLocale()]) }}" class="nav-mobile-nav-link body-1">{{ __('site.categories') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
            <a href="{{ route('about',['locale' => app()->getLocale()]) }}" class="nav-mobile-nav-link body-1">{{ __('site.about_us') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}">
            <a href="{{ route('gallery.index',['locale' => app()->getLocale()]) }}" class="nav-mobile-nav-link body-1">{{ __('site.mgallery') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('blog.*') ? 'active' : '' }}">
            <a href="{{ route('blog.index',['locale' => app()->getLocale()]) }}" class="nav-mobile-nav-link body-1">{{ __('site.blog') }}</a>
          </li>
          <li class="nav-mobile-nav-item {{ request()->routeIs('contact.*') ? 'active' : '' }}">
            <a href="{{ route('contact.index',['locale' => app()->getLocale()]) }}" class="nav-mobile-nav-link body-1">{{ __('site.contact') }}</a>
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
                        <a href="{{ route('home',['locale' => app()->getLocale()]) }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_home') }}
                        </a>
                        <a href="{{ route('categories.index',['locale' => app()->getLocale()]) }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_packages') }}
                        </a>
                        <a href="{{ route('about',['locale' => app()->getLocale()]) }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_about') }}
                        </a>
                        <a href="{{ route('faq',['locale' => app()->getLocale()]) }}" class="body-2 text-white" style="opacity: 0.8">
                            {{ __('site.footer_faq') }}
                        </a>
                        <a href="{{ route('contact.index',['locale' => app()->getLocale()]) }}" class="body-2 text-white" style="opacity: 0.8">
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
           <!-- Sign In -->
           <div class="tab-pane fade show active d-flex flex-column gap-sm-3" id="login" role="tabpanel"
           aria-labelledby="home-tab" style="display: flex;">
           <!-- Phone -->
           <div class="form-group">
             <label class="form-label mb-0"> @lang('site.phone_number')</label>
             <div class="input-phone">
               <!-- Country Select -->
               <div class="country-select" data-bs-toggle="dropdown" aria-expanded="false">
                 <span class="flag fi fi-sa" id="selected-flag"></span>
                 <span class="code" id="selected-code">+966</span>
                 <i class="fas fa-chevron-down dropdown-icon"></i>
               </div>

               <!-- Dropdown Menu -->
               <ul class="dropdown-menu">
                 <li>
                   <div class="input-with-icon" style="min-height: 45px;">
                     <input type="text" class="form-control" placeholder=" @lang('site.search_here')" />
                     <i class="input-icon">
                       <img src="assets/images/icons/search-normal.png" alt="@lang('site.search')">
                     </i>
                   </div>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="sa"
                     data-code="+966">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-sa"></span>
                       <span class="body-2">السعودية</span>
                     </span>
                     <span class="body-2">+966</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ae"
                     data-code="+971">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ae"></span>
                       <span class="body-2">الإمارات</span>
                     </span>
                     <span class="body-2">+971</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="kw"
                     data-code="+965">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-kw"></span>
                       <span class="body-2">الكويت</span>
                     </span>
                     <span class="body-2">+965</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="qa"
                     data-code="+974">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-qa"></span>
                       <span class="body-2">قطر</span>
                     </span>
                     <span class="body-2">+974</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="bh"
                     data-code="+973">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-bh"></span>
                       <span class="body-2">البحرين</span>
                     </span>
                     <span class="body-2">+973</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="om"
                     data-code="+968">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-om"></span>
                       <span class="body-2">عمان</span>
                     </span>
                     <span class="body-2">+968</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="jo"
                     data-code="+962">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-jo"></span>
                       <span class="body-2">الأردن</span>
                     </span>
                     <span class="body-2">+962</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="lb"
                     data-code="+961">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-lb"></span>
                       <span class="body-2">لبنان</span>
                     </span>
                     <span class="body-2">+961</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="eg"
                     data-code="+20">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-eg"></span>
                       <span class="body-2">مصر</span>
                     </span>
                     <span class="body-2">+20</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ma"
                     data-code="+212">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ma"></span>
                       <span class="body-2">المغرب</span>
                     </span>
                     <span class="body-2">+212</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="dz"
                     data-code="+213">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-dz"></span>
                       <span class="body-2">الجزائر</span>
                     </span>
                     <span class="body-2">+213</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="tn"
                     data-code="+216">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-tn"></span>
                       <span class="body-2">تونس</span>
                     </span>
                     <span class="body-2">+216</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ly"
                     data-code="+218">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ly"></span>
                       <span class="body-2">ليبيا</span>
                     </span>
                     <span class="body-2">+218</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="sd"
                     data-code="+249">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-sd"></span>
                       <span class="body-2">السودان</span>
                     </span>
                     <span class="body-2">+249</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="iq"
                     data-code="+964">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-iq"></span>
                       <span class="body-2">العراق</span>
                     </span>
                     <span class="body-2">+964</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="sy"
                     data-code="+963">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-sy"></span>
                       <span class="body-2">سوريا</span>
                     </span>
                     <span class="body-2">+963</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ye"
                     data-code="+967">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ye"></span>
                       <span class="body-2">اليمن</span>
                     </span>
                     <span class="body-2">+967</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ps"
                     data-code="+970">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ps"></span>
                       <span class="body-2">فلسطين</span>
                     </span>
                     <span class="body-2">+970</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="us"
                     data-code="+1">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-us"></span>
                       <span class="body-2">الولايات المتحدة</span>
                     </span>
                     <span class="body-2">+1</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="gb"
                     data-code="+44">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-gb"></span>
                       <span class="body-2">المملكة المتحدة</span>
                     </span>
                     <span class="body-2">+44</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="de"
                     data-code="+49">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-de"></span>
                       <span class="body-2">ألمانيا</span>
                     </span>
                     <span class="body-2">+49</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="fr"
                     data-code="+33">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-fr"></span>
                       <span class="body-2">فرنسا</span>
                     </span>
                     <span class="body-2">+33</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="it"
                     data-code="+39">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-it"></span>
                       <span class="body-2">إيطاليا</span>
                     </span>
                     <span class="body-2">+39</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="es"
                     data-code="+34">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-es"></span>
                       <span class="body-2">إسبانيا</span>
                     </span>
                     <span class="body-2">+34</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ca"
                     data-code="+1">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ca"></span>
                       <span class="body-2">كندا</span>
                     </span>
                     <span class="body-2">+1</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="au"
                     data-code="+61">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-au"></span>
                       <span class="body-2">أستراليا</span>
                     </span>
                     <span class="body-2">+61</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="in"
                     data-code="+91">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-in"></span>
                       <span class="body-2">الهند</span>
                     </span>
                     <span class="body-2">+91</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="pk"
                     data-code="+92">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-pk"></span>
                       <span class="body-2">باكستان</span>
                     </span>
                     <span class="body-2">+92</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="tr"
                     data-code="+90">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-tr"></span>
                       <span class="body-2">تركيا</span>
                     </span>
                     <span class="body-2">+90</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ir"
                     data-code="+98">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ir"></span>
                       <span class="body-2">إيران</span>
                     </span>
                     <span class="body-2">+98</span>
                   </a>
                 </li>
               </ul>

               <!-- Phone Number Input -->
               <input type="tel" class="phone-number" dir="rtl" placeholder="@lang('site.example_number')" style="height: 44px" />
             </div>
           </div>

           <!-- Submit Button -->
           <button type="submit" class="btn btn-custom-primary w-100">@lang('site.continue')</button>
         </div>

         <!-- Register -->
         <div class="tab-pane fade d-flex flex-column gap-sm-3" id="register" role="tabpanel"
           aria-labelledby="register-tab" style="display: none;">

           <!-- Name -->
           <div class="form-group">
             <label class="form-label mb-0">@lang('site.name')</label>
             <input type="text" class="form-control" placeholder="@lang('site.name')" />
           </div>

           <!-- Phone -->
           <div class="form-group">
             <label class="form-label mb-0">@lang('site.phone_number')</label>
             <div class="input-phone">
               <!-- Country Select -->
               <div class="country-select" data-bs-toggle="dropdown" aria-expanded="false">
                 <span class="flag fi fi-sa" id="selected-flag"></span>
                 <span class="code" id="selected-code">+966</span>
                 <i class="fas fa-chevron-down dropdown-icon"></i>
               </div>

               <!-- Dropdown Menu -->
               <ul class="dropdown-menu">
                 <li>
                   <div class="input-with-icon" style="min-height: 45px;">
                     <input type="text" class="form-control" placeholder="@lang('site.search_here')" />
                     <i class="input-icon">
                       <img src="assets/images/icons/search-normal.png" alt="@lang('site.search')">
                     </i>
                   </div>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="sa"
                     data-code="+966">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-sa"></span>
                       <span class="body-2">السعودية</span>
                     </span>
                     <span class="body-2">+966</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ae"
                     data-code="+971">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ae"></span>
                       <span class="body-2">الإمارات</span>
                     </span>
                     <span class="body-2">+971</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="kw"
                     data-code="+965">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-kw"></span>
                       <span class="body-2">الكويت</span>
                     </span>
                     <span class="body-2">+965</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="qa"
                     data-code="+974">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-qa"></span>
                       <span class="body-2">قطر</span>
                     </span>
                     <span class="body-2">+974</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="bh"
                     data-code="+973">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-bh"></span>
                       <span class="body-2">البحرين</span>
                     </span>
                     <span class="body-2">+973</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="om"
                     data-code="+968">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-om"></span>
                       <span class="body-2">عمان</span>
                     </span>
                     <span class="body-2">+968</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="jo"
                     data-code="+962">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-jo"></span>
                       <span class="body-2">الأردن</span>
                     </span>
                     <span class="body-2">+962</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="lb"
                     data-code="+961">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-lb"></span>
                       <span class="body-2">لبنان</span>
                     </span>
                     <span class="body-2">+961</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="eg"
                     data-code="+20">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-eg"></span>
                       <span class="body-2">مصر</span>
                     </span>
                     <span class="body-2">+20</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ma"
                     data-code="+212">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ma"></span>
                       <span class="body-2">المغرب</span>
                     </span>
                     <span class="body-2">+212</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="dz"
                     data-code="+213">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-dz"></span>
                       <span class="body-2">الجزائر</span>
                     </span>
                     <span class="body-2">+213</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="tn"
                     data-code="+216">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-tn"></span>
                       <span class="body-2">تونس</span>
                     </span>
                     <span class="body-2">+216</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ly"
                     data-code="+218">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ly"></span>
                       <span class="body-2">ليبيا</span>
                     </span>
                     <span class="body-2">+218</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="sd"
                     data-code="+249">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-sd"></span>
                       <span class="body-2">السودان</span>
                     </span>
                     <span class="body-2">+249</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="iq"
                     data-code="+964">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-iq"></span>
                       <span class="body-2">العراق</span>
                     </span>
                     <span class="body-2">+964</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="sy"
                     data-code="+963">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-sy"></span>
                       <span class="body-2">سوريا</span>
                     </span>
                     <span class="body-2">+963</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ye"
                     data-code="+967">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ye"></span>
                       <span class="body-2">اليمن</span>
                     </span>
                     <span class="body-2">+967</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ps"
                     data-code="+970">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ps"></span>
                       <span class="body-2">فلسطين</span>
                     </span>
                     <span class="body-2">+970</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="us"
                     data-code="+1">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-us"></span>
                       <span class="body-2">الولايات المتحدة</span>
                     </span>
                     <span class="body-2">+1</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="gb"
                     data-code="+44">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-gb"></span>
                       <span class="body-2">المملكة المتحدة</span>
                     </span>
                     <span class="body-2">+44</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="de"
                     data-code="+49">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-de"></span>
                       <span class="body-2">ألمانيا</span>
                     </span>
                     <span class="body-2">+49</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="fr"
                     data-code="+33">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-fr"></span>
                       <span class="body-2">فرنسا</span>
                     </span>
                     <span class="body-2">+33</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="it"
                     data-code="+39">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-it"></span>
                       <span class="body-2">إيطاليا</span>
                     </span>
                     <span class="body-2">+39</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="es"
                     data-code="+34">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-es"></span>
                       <span class="body-2">إسبانيا</span>
                     </span>
                     <span class="body-2">+34</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ca"
                     data-code="+1">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ca"></span>
                       <span class="body-2">كندا</span>
                     </span>
                     <span class="body-2">+1</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="au"
                     data-code="+61">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-au"></span>
                       <span class="body-2">أستراليا</span>
                     </span>
                     <span class="body-2">+61</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="in"
                     data-code="+91">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-in"></span>
                       <span class="body-2">الهند</span>
                     </span>
                     <span class="body-2">+91</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="pk"
                     data-code="+92">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-pk"></span>
                       <span class="body-2">باكستان</span>
                     </span>
                     <span class="body-2">+92</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="tr"
                     data-code="+90">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-tr"></span>
                       <span class="body-2">تركيا</span>
                     </span>
                     <span class="body-2">+90</span>
                   </a>
                 </li>
                 <li>
                   <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-flag="ir"
                     data-code="+98">
                     <span class="d-flex align-items-center gap-sm-3">
                       <span class="flag fi fi-ir"></span>
                       <span class="body-2">إيران</span>
                     </span>
                     <span class="body-2">+98</span>
                   </a>
                 </li>
               </ul>

               <!-- Phone Number Input -->
               <input type="tel" class="phone-number" dir="rtl" placeholder="@lang('site.example_number')" style="height: 44px" />
             </div>
           </div>

           <!-- Email -->
           <div class="form-group">
             <label class="form-label mb-0">@lang('site.email')</label>
             <input type="email" class="form-control" placeholder="@lang('site.email')" />
           </div>

           <!-- Submit Button -->
           <button type="submit" class="btn btn-custom-primary w-100">@lang('site.continue')</button>
         </div>
          </div>
        </div>
      </div>
    </div>
  </div>


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
    // Mobile Menu Toggle
    document.getElementById('mobileMenuToggle').addEventListener('click', function() {
      document.getElementById('navMobileOverlay').classList.add('active');
      document.getElementById('navMobileDrawer').classList.add('active');
    });

    document.getElementById('navMobileClose').addEventListener('click', function() {
      document.getElementById('navMobileOverlay').classList.remove('active');
      document.getElementById('navMobileDrawer').classList.remove('active');
    });

    document.getElementById('navMobileOverlay').addEventListener('click', function() {
      document.getElementById('navMobileOverlay').classList.remove('active');
      document.getElementById('navMobileDrawer').classList.remove('active');
    });

    // Language Dropdown
    document.querySelectorAll('.language-option').forEach(function(option) {
      option.addEventListener('click', function() {
        const language = this.getAttribute('data-language');
        // Handle language change logic here
        console.log('Language changed to:', language);
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


function changeLanguage(locale) {
    fetch("{{ route('setLocale') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            locale: locale,
            current_url: window.location.href
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = data.redirect; // الآن رابط نسبي
        }
    });
}

</script>


  @stack('scripts')
</body>
</html>

