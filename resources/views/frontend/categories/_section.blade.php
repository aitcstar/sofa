<!-- Packages -->
<div class="rooms-container" style="width: 100%;">
    <div class="row">
        @foreach($packages as $package)
        <!-- Package Item -->
        <div class="col-sm-12 col-md-6 mb-sm-4 package-cards"
            data-package-name="{{ $package->{'name_'.app()->getLocale()} }}"
            data-colors="{{ $package->packageUnitItems->pluck('item.background_color')->filter()->unique()->implode(',') }}"
            data-unit-types="{{ $package->packageUnitItems->pluck('unit.type')->unique()->implode(',') }}">

            <div class="room-item">
                <!-- image & widget -->
                <div class="image">
                    @if($package->{'title_'.app()->getLocale()})
                    <div class="widget text-center">
                        <span class="body-4 text-white"> {{ $package->{'title_'.app()->getLocale()} }} </span>
                    </div>
                    @endif
                    @if($package->image)
                        <img src="{{ asset('storage/' . $package->image) }}" class="w-100 h-100" alt="{{ $package->{'name_'.app()->getLocale()} }}" />
                    @else
                        <img src="{{ asset('assets/images/category/category-01.jpg') }}" class="w-100 h-100" alt="{{ $package->{'name_'.app()->getLocale()} }}" />
                    @endif
                </div>

                <!-- Content -->
                <div class="content d-flex flex-column gap-sm-3">
                    <!-- Title & Quantity & Description -->
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-column gap-sm-6">
                            <h5 class="sub-heading-3"> {{ $package->{'name_'.app()->getLocale()} }}</h5>
                            <p class="body-3 mb-0">
                                {{ $package->{'description_'.app()->getLocale()} ?: 'مثالي للمساحات الصغيرة، يوفر الراحة والأناقة' }}
                            </p>
                        </div>
                        <p class="body-2" style="color: var(--secondary);font-weight:700;">
                            {{ $package->packageUnitItems->count() }} {{ __('site.piece') }}
                        </p>
                    </div>

                    <!-- Price -->
                    <div class="d-flex align-items-center gap-sm-5 mb-2">
                        <p class="body-2 text-caption mb-0">{{ __('site.Starting_from') }}</p>
                        <h4 class="heading-h6 mb-0">
                            {{ number_format($package->price) }}
                            <img src="{{ asset('assets/images/hero/Platform Subtitle.png') }}" alt="" />
                        </h4>
                    </div>

                    <!-- Options -->
                    <div class="d-flex flex-column gap-sm-4">
                        <!-- Including -->
                        <div class="d-flex gap-sm-3 align-items-center">
                            <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Includes') }}</p>
                            <div class="d-flex flex-wrap gap-sm-3">
                                @php
                                        $units = $package->packageUnitItems->pluck('unit')->unique('id')->values();
                                    @endphp

                                    @foreach($units->take(2) as $puiUnit)
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            @php
                                                $icon = match($puiUnit->type) {
                                                    'bedroom' => 'caricone.png',
                                                    'living_room' => 'sofa.png',
                                                    'kitchen' => 'foot.png',
                                                    'external' => 'Group.png',
                                                    default => 'caricone.png',
                                                };
                                            @endphp
                                            <img src="{{ asset('assets/images/icons/'.$icon) }}" alt="" />
                                            <span class="body-4">{{ $puiUnit->{'name_'.app()->getLocale()} }}</span>
                                        </div>
                                    @endforeach

                                    {{-- لو فيه أكتر من 4 وحدات، أضف عنصر "أخرى" --}}
                                    @if($units->count() > 2)
                                        <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface px-2 py-1">
                                            <img src="{{ asset('assets/images/icons/Group.png') }}" alt="" />
                                            <span class="body-4">{{ app()->getLocale() == 'ar' ? 'أخرى' : 'Other' }}</span>
                                        </div>
                                    @endif
                            </div>
                        </div>

                        @php
                        $locale = app()->getLocale();
                        $colors = collect($package->available_colors)
                            ->filter() // نتأكد إن في ألوان
                            ->take(4); // نعرض أول 4 فقط
                    @endphp

                    <!-- Colors -->
                    <div class="d-flex gap-sm-3 align-items-center">
                        <p class="body-2 text-caption mb-0" style="width: 90px;">{{ __('site.Available_colors') }}</p>
                        <div class="d-flex gap-sm-5">
                            @forelse($colors as $color)
                                <span class="rounded-pill"
                                      style="width: 34px; height: 16px; background-color: {{ $color['color_code'] ?? '#000' }}"
                                      title="{{ $locale === 'ar' ? $color['name_ar'] : $color['name_en'] }}">
                                </span>
                            @empty
                                <span> {{ app()->getLocale() == 'ar' ? 'لا توجد ألوان' : 'No Color' }} </span>
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
                    <a href="#" class="btn btn-custom-primary add-to-cart-btn"
                       data-package-id="{{ $package->id }}"
                       data-package-name="{{ $package->{'name_'.app()->getLocale()} }}"
                       data-package-price="{{ $package->price }}"
                       data-package-image="{{ $package->image ? asset('storage/' . $package->image) : asset('assets/images/category/category-01.jpg') }}"
                       data-package-description="{{ $package->{'description_'.app()->getLocale()} }}"
                       data-package-pieces="{{ $package->packageUnitItems->count() }}">
                        <p class="text-nowrap mb-0">{{ __('site.addtocart') }}</p>
                           <i class="fas fa-shopping-cart" style="font-size: 20px"></i>
                       </a>

                    <!--   <a href="https://wa.me/{{ $siteSettings->whatsapp }}" target="_blank" class="btn btn-custom-primary w-100">
                        <p class="text-nowrap mb-0">{{ __('site.send_whatsapp_quote') }}</p>
                        <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                    </a>-->

                    <a href="{{ app()->getLocale() == 'ar'
                        ? route('packages.show', ['slug' => $package->slug_ar ?? $package->id])
                        : route('packages.show.en', ['slug' => $package->slug_en ?? $package->id]) }}"
                        class="btn btn-custom-secondary w-100">
                        <span style="white-space: nowrap;">{{ __('site.view_details') }}</span>
                        <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-icon"></i>
                    </a>



                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
