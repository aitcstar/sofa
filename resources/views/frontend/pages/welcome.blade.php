@extends('frontend.layouts.pages')

@section('content')

 <!-- ===== REQUEST SUCCESSFULLY SECTION ===== -->
 <section class="request-successfully">
    <div class="container d-flex flex-column gap-md align-items-center justify-content-center text-center">
        <!-- Icon -->
        <div class="request-successfully-icon">
            <img src="../assets/images/icons/party-popper.svg" alt="Party Popper" />
        </div>

        <!-- Content -->
        <div class="d-flex flex-column gap-sm-5">
            <h1 class="heading-h7"> تم إنشاء حسابك بنجاح! </h1>
            <p class="body-1 mb-0" style="max-width: 448px;">
                ابدأ الآن باستكشاف باكجات التأثيث المصممة خصيصًا لوحدتك
            </p>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-sm-4">
            <a href="{{ app()->getLocale() == 'ar' ? route('packages.index') : route('packages.index.en') }}" class="btn btn-custom-primary">
                {{ __('site.browse_packages') }}
            </a>


        </div>
    </div>
</section>

@endsection


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/request-successfully.css') }}">
@endpush
