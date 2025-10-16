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
            <h1 class="heading-h7">تم إرسال طلبك بنجاح</h1>
            <p class="body-1 mb-0" style="max-width: 448px;">شكراً لثقتك بنا، سيقوم فريق SOFA بمراجعة تفاصيل طلبك
                والتواصل معك خلال 24 ساعة لتأكيد التفاصيل</p>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-sm-4">

            @if($order_id)
                <a href="{{ route('order.details', $order_id) }}" class="btn btn-custom-primary">
                    {{ __('site.view_order_details') }}
                </a>
            @endif

            <a href="{{ route('home') }}" class="btn btn-custom-outline">
                {{ __('site.back_to_home') }}
            </a>


        </div>
    </div>
</section>

@endsection


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/request-successfully.css') }}">
@endpush
