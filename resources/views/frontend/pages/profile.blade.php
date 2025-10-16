@extends('frontend.layouts.pages')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a class="body-1 text-primary">{{ __('site.my_account') }}</a>
</div>

<!-- ===== PROFILE SECTION ===== -->
<section class="profile">
    <div class="container">
        <!-- Tabs -->
        <div class="profile-tabs">
            <a class="profile-item active" href="{{ app()->getLocale() == 'ar' ? route('profile.index.en') : route('profile.index.en') }}">
                <img src="{{ asset('assets/images/icons/user.svg') }}" alt="User" />
                <p class="sub-heading-3 text-body mb-0">{{ __('site.my_account') }}</p>
            </a>
            <a class="profile-item" href="{{ app()->getLocale() == 'ar' ? route('order.my') : route('order.my.en') }}">
                <img src="{{ asset('assets/images/icons/order.svg') }}" alt="Cart" />
                <p class="sub-heading-3 text-body mb-0">{{ __('site.my_orders') }}</p>
            </a>
            <a class="profile-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <img src="{{ asset('assets/images/icons/logout.svg') }}" alt="Logout" />
                <p class="sub-heading-3 mb-0 text-danger">{{ __('site.logout') }}</p>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <!-- Content -->
        <div class="profile-content d-flex flex-column gap-sm">
            <!-- Account Type
            <div class="profile-content-item d-flex flex-column gap-sm-5">
                <p class="heading-h8 mb-0">{{ __('site.my_account') }}</p>
                <p class="body-3 text-caption mb-0">
                    {{ __('site.account_type') }}:
                    {{ auth()->user()->client_type === 'company' ? __('site.company') : __('site.individual') }}
                </p>
            </div> -->

            <!-- Profile Form -->
            <form class="profile-content-form d-flex flex-column gap-sm" method="POST" action="{{ app()->getLocale() == 'ar' ? route('profile.update') : route('profile.update.en') }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="form-group d-flex flex-column gap-sm-5">
                    <label for="name" class="body-2 text-body">{{ __('site.name') }}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" placeholder="{{ __('site.enter_your_name') }}" required />
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group d-flex flex-column gap-sm-5">
                    <label for="phone" class="body-2 text-body">{{ __('site.phone') }}</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="{{ __('site.enter_your_phone') }}" required />
                    @error('phone')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group d-flex flex-column gap-sm-5">
                    <label for="email" class="body-2 text-body">{{ __('site.email') }}</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="{{ __('site.enter_your_email') }}" required />
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Save Button -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-custom-primary" style="min-width: 253px;">{{ __('site.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/profile.css') }}">
@endpush
