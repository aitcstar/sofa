@extends('frontend.layouts.pages')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('order.my') }}" class="body-1 text-primary">{{ __('site.my_orders') }}</a>
</div>

<!-- ===== PROFILE SECTION ===== -->
<section class="profile">
    <div class="container">
        <!-- Tabs -->
        <div class="profile-tabs">
            <a class="profile-item" href="{{ app()->getLocale() == 'ar' ? route('profile.index') : route('profile.index.en') }}">
                <img src="{{ asset('assets/images/icons/user.svg') }}" alt="User" />
                <p class="sub-heading-3 text-body mb-0">{{ __('site.my_account') }}</p>
            </a>
            <a class="profile-item active" href="{{ route('order.my') }}">
                <img src="{{ asset('assets/images/icons/order.svg') }}" alt="Cart" />
                <p class="sub-heading-3 text-body mb-0">{{ __('site.my_orders') }}</p>
            </a>
            <div class="profile-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" class="d-flex align-items-center gap-sm-3" onclick="event.preventDefault(); this.closest('form').submit();">
                        <img src="{{ asset('assets/images/icons/logout.svg') }}" alt="Logout" />
                        <p class="sub-heading-3 mb-0 text-danger">{{ __('site.logout') }}</p>
                    </a>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="profile-content d-flex flex-column gap-sm">
            <!-- Header -->
            <div class="profile-content-item d-flex flex-column gap-sm-5">
                <p class="heading-h8 mb-0">{{ __('site.my_orders') }}</p>
                <p class="body-3 text-caption mb-0">{{ __('site.track_orders_description') }}</p>
            </div>


            <!-- Orders List -->
            <div class="profile-order-list">
                @if($orders->count())
                    @foreach($orders as $order)

            ```
                    @php
                        // كل عناصر الطلب
                        $items = $order->orderItems;

                        // تجهيز كل الباكجات داخل الطلب
                        $packages = $items->map(function ($item) {
                            return [
                                'package' => $item->package,
                                'qty' => $item->quantity,
                                'colors' => $item->package?->packageUnitItems
                                                ->pluck('item.background_color')
                                                ->filter()
                                                ->unique()
                                                ->take(4),
                                'groupedItems' => $item->package?->packageUnitItems->groupBy('unit_id'),
                            ];
                        });

                        // أول باكج للعرض في الهيدر (الصورة والعنوان)
                        $mainPackage = $packages->first()['package'] ?? null;
                    @endphp

                    <div class="profile-order-item d-flex flex-column gap-sm-4">
                        <!-- Header -->
                        <div class="d-flex gap-sm-3">
                            <!-- Image -->
                            <div class="profile-order-img">
                                <img src="{{ $mainPackage?->image ? asset('storage/' . $mainPackage->image) : asset('assets/images/category/category-01.jpg') }}" alt="Order" />
                            </div>

                            <!-- Content -->
                            <div class="d-flex flex-column gap-sm-6" style="flex: 1;">
                                <h4 class="sub-heading-4 mb-0">{{ $mainPackage?->{'name_'.app()->getLocale()} }}</h4>
                                <p class="body-4 mb-0">{{ __('site.order_date') }}: {{ $order->created_at->format('d F Y') }}</p>
                                <div class="d-flex gap-sm-5 align-items-center">
                                    <p class="body-4 mb-0" style="white-space: nowrap;">{{ __('site.total_price') }}:</p>
                                    @php
                                        $totalPrice = $packages->sum(function($pkg){
                                            return ($pkg['package']?->price ?? 0) * $pkg['qty'] * 1.15;
                                        });
                                    @endphp
                                    <p class="heading-h8 mb-0 d-flex align-items-center gap-sm-6">
                                        {{ number_format($totalPrice) }}
                                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="Currency" />
                                    </p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="profile-order-status">
                                <span class="badge bg-{{ $order->status_color }} text-white px-3 py-2 rounded-pill">{{ __('site.' . $order->status) }}</span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <hr class="divider" />

                        <!-- Details -->
                        <div class="profile-order-item-details d-flex gap-sm-3">
                            <div style="flex: 1;">
                                <ul class="mb-0">
                                    @foreach($packages as $pkg)
                                        @php $package = $pkg['package']; @endphp
                                        <li class="body-4 mb-2">
                                            <strong>{{ $package?->{'name_'.app()->getLocale()} }}:</strong> ({{ __('site.quantity') }}: {{ $pkg['qty'] }})<br>

                                            <!-- Colors -->
                                            {{ __('site.colors') }}:
                                            @foreach($pkg['colors'] as $color)
                                                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $color }};"></span>
                                            @endforeach

                                            <!-- Units / Items -->
                                            @foreach($pkg['groupedItems'] as $unitId => $items)
                                                @php $unit = $items->first()->unit; @endphp
                                                <br><strong>{{ $unit?->{'name_'.app()->getLocale()} }}:</strong>
                                                {{ $items->pluck('item.' . (app()->getLocale() === 'ar' ? 'item_name_ar' : 'item_name_en'))->implode(', ') }}
                                            @endforeach
                                        </li>
                                        <hr>
                                    @endforeach

                                    <li class="body-4">{{ __('site.units_count') }}: {{ $order->units_count }}</li>
                                    <li class="body-4">{{ __('site.project_type') }}: {{ __('site.' . $order->project_type . '_project') }}</li>
                                </ul>
                            </div>

                            <!-- Button -->
                            <div class="d-flex align-items-end">
                                <a class="d-flex align-items-center gap-sm-4" href="{{ route('order.details', $order) }}">
                                    <p class="mb-0 text-primary">{{ __('site.view_details') }}</p>
                                    <i class="fa-solid fa-chevron-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" style="font-size: 12px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                @endforeach
            @else
                <div class="text-center py-5">
                    <p class="body-2 text-muted">{{ __('site.no_orders_found') }}</p>
                </div>
            @endif
            ```

            </div>


        </div>
    </div>
</section>
@endsection
@push('styles')

<link rel="stylesheet" href="{{ asset('assets/css/pages/profile.css') }}">

@endpush
