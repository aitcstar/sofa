

    <!-- Packages -->
    <div class="rooms-container">
        <div class="row">
            @foreach($packages as $package)
            <!-- Package Item -->
            <div class="col-sm-12 col-md-6 mb-sm-4">
                <div class="room-item">
                    <!-- image & widget -->
                    <div class="image">
                        <!--<div class="widget text-center">
                            <span class="body-4 text-white">جاهز للتسليم السريع</span>
                        </div>-->
                        @if($package->image)
                            <img src="{{ asset('storage/' . $package->image) }}" class="w-100 h-100" alt="{{ $package->name }}" />
                        @else
                            <img src="{{ asset('assets/images/category/category-01.jpg') }}" class="w-100 h-100" alt="{{ $package->name }}" />
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="content d-flex flex-column gap-sm-3">
                        <!-- Title & Quantity & Description -->
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column gap-sm-6">
                                <h5 class="sub-heading-3">{{ __('site.package') }} {{ $package->name }}</h5>
                                <p class="body-3 mb-0">
                                    {{ $package->description ?: 'مثالي للمساحات الصغيرة، يوفر الراحة والأناقة' }}
                                </p>
                            </div>
                            <p class="body-2" style="color: var(--secondary);">
                                {{ $package->units->sum(fn($u) => $u->items->count()) }}  {{ __('site.piece') }}
                            </p>
                        </div>

                        <!-- Price -->
                        <div class="d-flex align-items-center gap-sm-5 mb-2">
                            <p class="body-2 text-caption mb-0">{{ __('site.Starting_from') }}</p>
                            <h4 class="heading-h6 mb-0">
                                {{ number_format($package->price)  }}
                                <img src="{{ asset('assets/images/hero/Platform Subtitle.png') }}" alt="" />
                            </h4>
                        </div>

                        <!-- Options -->
                        <div class="d-flex flex-column gap-sm-4">
                            <!-- Including -->
                            <div class="d-flex gap-sm-3 align-items-center">
                                <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Includes') }}</p>
                                <div class="d-flex flex-wrap gap-sm-3">
                                    @foreach($package->units as $unit)
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            @if($unit->type == "bedroom")
                                                <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                            @elseif($unit->type == "living_room")
                                                <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="" />
                                            @elseif($unit->type == "kitchen")
                                                <img src="{{ asset('assets/images/icons/foot.png') }}" alt="" />
                                            @elseif($unit->type == "external")
                                                <img src="{{ asset('assets/images/icons/Group.png') }}" alt="" />
                                            @else
                                                <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                                            @endif
                                            <span class="body-4">{{ $unit->{'name_'.app()->getLocale()} }}</span>

                                        </div>

                                    @endforeach
                                </div>
                            </div>

                            @php
                                $colors = $package->units
                                    ->flatMap->items
                                    ->pluck('background_color')
                                    ->filter()
                                    ->unique()
                                    ->take(4);
                            @endphp

                            <!-- Colors -->
                            <div class="d-flex gap-sm-3 align-items-center">
                                <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Available_colors') }}</p>
                                <div class="d-flex gap-sm-5">
                                    @forelse($colors as $color)
                                    <span class="rounded-pill" style="width: 34px; height: 16px; background-color: {{ $color }}"></span>
                                @empty
                                    <span>لا توجد ألوان</span>
                                @endforelse
                                </div>
                            </div>

                            <!-- Time implementation -->
                            <div class="d-flex gap-sm-3 align-items-center">
                                <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Duration') }}</p>
                                <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                    <img src="{{ asset('assets/images/icons/clock-watch.png') }}" alt="" />
                                    <span class="body-4">{{ $package->{'period_'.app()->getLocale()} }}</span>
                                </div>
                            </div>

                            <!-- Service -->
                            <div class="d-flex gap-sm-3 align-items-center">
                                <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Service') }}</p>
                                <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                    <img src="{{ asset('assets/images/icons/tools-wench-ruler.png') }}" alt="" />
                                    <span class="body-4">{{ $package->{'service_includes_'.app()->getLocale()} }}</span>
                                </div>
                            </div>

                            <!-- Payment Plan -->
                            <div class="d-flex gap-sm-3 align-items-center">
                                <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Payment_plan') }}</p>
                                <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                    <img src="{{ asset('assets/images/icons/wallet-2.png') }}" alt="" />
                                    <span class="body-4">{{ $package->{'payment_plan_'.app()->getLocale()} }}</span>
                                </div>
                            </div>

                            <!-- Decoration -->
                            <div class="d-flex gap-sm-3 align-items-center">
                                <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Decoration') }}</p>
                                <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                    <img src="{{ asset('assets/images/icons/brush-ruler.png') }}" alt="" />
                                    <span class="body-4">{{ $package->{'decoration_'.app()->getLocale()} }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Buttons -->
                    <div class="actions d-flex gap-sm-2">
                        <a href="https://wa.me/{{ $siteSettings->whatsapp }}" target="_blank" class="btn btn-custom-primary w-100">
                            <p class="text-nowrap mb-0">{{ __('site.send_whatsapp_quote') }}</p>
                            <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                        </a>
                        <a href="{{ app()->getLocale() == 'ar' ? route('packages.show', ['id' => $package->id]) : route('packages.show.en', ['id' => $package->id]) }}" class="btn btn-custom-secondary w-100">
                            <span style="white-space: nowrap;">{{ __('site.view_details') }}</span>
                            <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-icon"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

