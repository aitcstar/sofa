@extends('frontend.layouts.pages')

@section('content')

<!-- ===== BREADCRUMB ===== -->

<div class="breadcrumb-container container">
    <a href="{{ app()->getLocale() == 'ar' ? route('home') : route('home.en') }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ app()->getLocale() == 'ar' ? route('order.my') : route('order.my.en') }}" class="body-2 text-body">{{ __('site.my_orders') }}</a>
    <span class="body-2 text-body">/</span>
    <a class="body-2 text-primary">{{ __('site.order_details') }}</a>
</div>

<!-- ===== ORDER DETAILS SECTION ===== -->

<section class="order-details-section">
    <div class="container d-flex flex-column gap-md">

    @foreach($order->packages as $package)
    <!-- heading -->
    <div class="order-details-heading d-flex gap-sm-3">
        <div class="d-flex gap-sm-3" style="flex: 1;">
            <!-- image -->
            <div class="order-details-image">
                <img src="{{ $package->image ? asset('storage/' . $package->image) : asset('assets/images/category/category-01.jpg') }}" alt="Order Detail" />
            </div>

            <!-- content -->
            <div class="d-flex flex-column justify-content-center gap-sm-5" style="flex: 1;">
                <h1 class="heading-h7">{{ $package->{'name_'.app()->getLocale()} }}</h1>
                <p class="body-2 mb-0">{{ __('site.order_date') }}: {{ $order->created_at->format('d F Y') }}</p>
                <div class="d-flex gap-sm-6 align-items-center">
                    <p class="body-2 mb-0">{{ __('site.total_price') }}:</p>
                    <div class="d-flex gap-sm-6">
                        <p class="heading-h7 mb-0">{{ number_format($package->price * 1.15) }}</p>
                        <img src="{{ asset('assets/images/currency/sar.svg') }}" alt="currency" />
                    </div>
                </div>
            </div>
        </div>

        <!-- actions -->
        <div class="order-details-action d-flex gap-sm-4">
            <div class="d-flex gap-sm-4" style="background-color: #EEF1F5;">
                <a href="{{ app()->getLocale() == 'ar' ? route('order.invoice', $order) : route('order.invoice.en', $order) }}" target="_blank" class="d-flex gap-sm-4" >
                    <p class="body-3 mb-0">{{ __('site.download_invoice') }}</p>
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>

            <div class="d-flex gap-sm-4" style="background-color: #979DAC;">
                <p class="body-3 mb-0 text-white">
                    {{ __('site.' . $order->status) }}
                </p>
            </div>
        </div>
    </div>

    <!-- divider -->
    <hr />

    <!-- page data -->
    <div class="d-flex flex-column gap-sm-4">
        <h3 class="sub-heading-3 mb-0">{{ __('site.package_data') }}</h3>
        <div class="d-flex flex-wrap gap-sm-3">
            @php
                $unitTypes = $package->packageUnitItems->pluck('unit.type')->unique();
            @endphp
            <div class="package-data-item d-flex flex-column gap-sm-6">
                <p class="body-3 mb-0">{{ __('site.unit_type') }}</p>
                <p class="body-3 text-subheading mb-0">
                    @foreach($unitTypes as $type)
                        {{ __('site.' . $type) }}@if(!$loop->last), @endif
                    @endforeach
                </p>
            </div>

            <div class="package-data-item d-flex flex-column gap-sm-6">
                <p class="body-3 mb-0">{{ __('site.project_type') }}</p>
                <p class="body-3 text-subheading mb-0">
                    {{ __('site.' . $order->project_type . '_project') }}
                </p>
            </div>

            <div class="package-data-item d-flex flex-column gap-sm-6">
                <p class="body-3 mb-0">{{ __('site.has_interior_design') }}</p>
                <p class="body-3 text-subheading mb-0">
                    {{ $order->has_interior_design ? __('site.yes') : __('site.no') }}
                </p>
            </div>

            <div class="package-data-item d-flex flex-column gap-sm-6">
                <p class="body-3 mb-0">{{ __('site.needs_color_help') }}</p>
                <p class="body-3 text-subheading mb-0">
                    {{ $order->needs_color_help ? __('site.yes') : __('site.no') }}
                </p>
            </div>
        </div>
    </div>

    <!-- divider -->
    <hr />

    <!-- page selection -->
    <div class="d-flex flex-column gap-sm-4">
        <h3 class="sub-heading-3 mb-0">{{ __('site.package_selection') }}</h3>
        <div class="d-flex flex-wrap gap-sm-3">
            @php
                $colors = $package->packageUnitItems->pluck('item.background_color')->filter()->unique()->take(4);
                $groupedItems = $package->packageUnitItems->groupBy('unit_id');
            @endphp

            <div class="package-selection-item d-flex flex-column gap-sm-6">
                <div class="d-flex align-items-center gap-sm-6">
                    <img src="{{ asset('assets/images/gallery/color.svg') }}" alt="color" />
                    <p class="body-3 mb-0">{{ __('site.colors') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @foreach($colors as $color)
                        <span class="body-4" style="background-color: {{ $color }}; padding: 2px 6px; border-radius: 4px; color:white;">
                            {{ $package->packageUnitItems->firstWhere('item.background_color', $color)?->item->{'color_'.app()->getLocale()} }}
                        </span>
                    @endforeach
                </div>
            </div>

            @foreach($groupedItems as $unitId => $items)
                @php $unit = $items->first()->unit; @endphp
                <div class="package-selection-item d-flex flex-column gap-sm-6">
                    <div class="d-flex align-items-center gap-sm-6">
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
                        <p class="body-3 mb-0">{{ $unit->{'name_'.app()->getLocale()} }}</p>
                    </div>
                    <ul class="m-0 p-0" style="list-style: none;">
                        @foreach($items as $item)
                            <li class="body-4 text-subheading mb-1">
                                • {{ $item->item->{'item_name_'.app()->getLocale()} }} ({{ $item->item->quantity }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <!-- divider -->
    <hr />

    <!-- package components table -->
    <div class="d-flex flex-column gap-sm-4">
        <h3 class="sub-heading-3 mb-0">{{ __('site.package_components') }}</h3>
        <div class="package-table-grid">
            @foreach($groupedItems as $unitId => $items)
                @php $unit = $items->first()->unit; @endphp
                <div class="table-section-item d-flex flex-column gap-sm-4">
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
                    <div class="table-section-item">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="body-2">{{ __('site.Widget') }}</th>
                                        <th class="body-2">{{ __('site.Size') }}</th>
                                        <th class="body-2">{{ __('site.Material') }}</th>
                                        <th class="body-2">{{ __('site.the color') }}</th>
                                        <th class="body-2">{{ __('site.image') }}</th>
                                        <th class="body-2">{{ __('site.Quantity') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="body-2">{{ $item->item->{'item_name_'.app()->getLocale()} }}</td>
                                            <td class="body-2">{{ $item->item->dimensions }}</td>
                                            <td class="body-2">{{ $item->item->{'material_'.app()->getLocale()} }}</td>
                                            <td class="color-box">
                                                <p class="body-2 text-subheading mb-0" style="background-color: {{ $item->item->background_color }};">
                                                    {{ $item->item->{'color_'.app()->getLocale()} }}
                                                </p>
                                            </td>
                                            <td class="image-box">
                                                <div class="img-box">
                                                    @if($item->item->image_path)
                                                        <img src="{{ asset('storage/' . $item->item->image_path) }}" alt="Item Image" />
                                                    @else
                                                        <img src="{{ asset('assets/images/no-image.png') }}" alt="No Image" />
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="body-2">{{ $item->item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- divider -->
    <hr />
    @endforeach

    <!-- steps -->
    <div class="d-flex flex-column gap-sm-4">
        <h3 class="sub-heading-3 mb-0">{{ __('site.order_timeline') }}</h3>
        <div class="d-flex flex-column gap-sm-3">
            @foreach(\App\Models\OrderStage::whereNull('parent_id')->orderBy('order_number')->get() as $index => $stage)
                @php
                    $subStages = $stage->children()->orderBy('order_number')->get();
                    $allSubCompleted = $subStages->count() && $subStages->every(fn($sub) => $order->stageStatuses->firstWhere('order_stage_id', $sub->id)?->status === 'completed');
                    $stageStatus = $order->stageStatuses->firstWhere('order_stage_id', $stage->id);
                    $isCompleted = $allSubCompleted || ($stageStatus && $stageStatus->status === 'completed');
                @endphp

                <div class="d-flex gap-md">
                    <div class="d-flex flex-column gap-sm-5 position-relative">
                        @if($isCompleted)
                            <div class="step-checked"></div>
                        @else
                            <div class="step-pending"></div>
                        @endif

                        @if(!$loop->last)
                            <div class="step-line"></div>
                        @endif
                    </div>

                    <div class="package-step-item d-flex flex-column gap-sm-3" style="width: 90%;">
                        <h4 class="sub-heading-4 mb-0">
                            {{ app()->getLocale() == 'ar'
                                ? 'المرحلة (' . ($index+1) . '): ' . $stage->title_ar
                                : 'Stage (' . ($index+1) . '): ' . $stage->title_en }}
                        </h4>

                        @if($subStages->count())
                            <ul class="d-flex flex-column gap-1 m-0 ms-3">
                                @foreach($subStages as $sub)
                                    @php
                                        $subStatus = $order->stageStatuses->firstWhere('order_stage_id', $sub->id);
                                        $subCompleted = $subStatus && $subStatus->status === 'completed';
                                    @endphp
                                    <li>
                                        <span class="{{ $subCompleted ? 'text-success' : '' }}">
                                            @if($subCompleted) ✔ @endif
                                            {{ app()->getLocale() == 'ar' ? $sub->title_ar : $sub->title_en }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="d-flex gap-sm-6 align-items-center pr-2">
                            <img src="{{ asset('assets/images/icons/time.svg') }}" alt="time" />
                            <p class="body-3 mb-0">
                                {{ $stageStatus && $stageStatus->completed_at
                                    ? $stageStatus->completed_at->format('d F Y \ف\ي H:i')
                                    : $stageStatus?->created_at?->format('d F Y \ف\ي H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
```

</section>
@endsection

@push('styles')

<link rel="stylesheet" href="{{ asset('assets/css/pages/my-order-detail.css') }}">
<style>
.step-pending {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #ccc;
}
</style>
@endpush
