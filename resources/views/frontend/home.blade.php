@extends('frontend.layouts.app')

@section('title', 'SOFA Experience')
@section('description', 'منصة تأثيث ذكية للوحدات الفندقية - نقدم حلولاً متكاملة ومبتكرة لتأثيث الفنادق والمنتجعات')

@section('content')
  <!-- ===== HERO SLIDER SECTION ===== -->


  <section id="hero-carousel" class="hero-section carousel slide p-0" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($sliders as $key => $slider)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
            <div class="overlay"></div>
            <img src="{{ asset('storage/' . $slider->image) }}" class="d-block w-100" alt="slider{{ $key+1 }}" />
            <div class="carousel-caption d-flex justify-content-center align-items-center flex-column gap-l" style="text-align: center;">
                <div class="d-flex flex-column gap-sm-3">
                    <h2 class="mb-0">{{ $slider->title() }}</h2>
                    <p class="body-1 text-white mb-0" style="opacity: 0.8;">{{ $slider->description() }}</p>
                </div>
                <div class="hero-section-actions d-flex justify-content-center gap-sm-3">
                    <a href="{{ route('categories.index') }}" class="btn btn-custom-outline">
                      استعرض الباكجات
                    </a>
                    <a href="{{ route('help.index') }}" class="btn btn-custom-secondary">
                      اطلب الان
                    </a>
                  </div>
            </div>
        </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
        <div class="carousel-control-button">
            <i class="fa-solid fa-chevron-left"></i>
        </div>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
        <div class="carousel-control-button">
            <i class="fa-solid fa-chevron-right"></i>
        </div>
    </button>
</section>



  <!-- ===== STEPS SECTION  ===== -->
  <section class="step-section">
    <div class="content mx-auto">
      <!-- Heading -->
      <h5 class="heading-h8 text-center">مراحل تجهيز وحدتك</h5>

      <!-- Steps -->
      <div class="d-flex gap-sm-3 position-relative mx-auto">
        <div class="line"></div>

        <!-- Item 1 -->
        <div class="d-flex flex-column align-items-center gap-sm-4" style="width: 140px;">
          <div class="step-item-icon">
            <img src="{{ asset('assets/images/hero/logo2.png') }}" alt="" />
          </div>
          <div class="d-flex flex-column align-items-center gap-sm-7">
            <p class="sub-heading-4 mb-0">
              اختيار الوحدة
            </p>
            <div class="body-3 text-caption mb-0">
              استوديو أو غرف
            </div>
          </div>
        </div>

        <!-- Item 2 -->
        <div class="d-flex flex-column align-items-center gap-sm-4" style="width: 140px;">
          <div class="step-item-icon">
            <img src="{{ asset('assets/images/hero/Frame 42 (1).png') }}" alt="" />
          </div>
          <div class="d-flex flex-column align-items-center gap-sm-7">
            <p class="sub-heading-4 mb-0">
              تخصيص الألوان
            </p>
            <div class="body-3 text-caption mb-0">
              نمط وتأثيث
            </div>
          </div>
        </div>

        <!-- Item 3 -->
        <div class="d-flex flex-column align-items-center gap-sm-4" style="width: 140px;">
          <div class="step-item-icon">
            <img src="{{ asset('assets/images/hero/icone3.png') }}" alt="" />
          </div>
          <div class="d-flex flex-column align-items-center gap-sm-7">
            <p class="sub-heading-4 mb-0">
              اعتماد الباكج
            </p>
            <div class="body-3 text-caption mb-0">
              مخطط تجهيز
            </div>
          </div>
        </div>

        <!-- Item 4 -->
        <div class="d-flex flex-column align-items-center gap-sm-4" style="width: 140px;">
          <div class="step-item-icon">
            <img src="{{ asset('assets/images/hero/logo6.png') }}" alt="" />
          </div>
          <div class="d-flex flex-column align-items-center gap-sm-7">
            <p class="sub-heading-4 mb-0">
              التوريد والتركيب
            </p>
            <div class="body-3 text-caption mb-0">
              شحن وتجهيز
            </div>
          </div>
        </div>

        <!-- Item 5 -->
        <div class="d-flex flex-column align-items-center align-items-center gap-sm-4" style="width: 140px;">
          <div class="step-item-icon">
            <img src="{{ asset('assets/images/hero/loho7.png') }}" alt="" />
          </div>
          <div class="d-flex flex-column align-items-center gap-sm-7">
            <p class="sub-heading-4 mb-0">
              التسليم
            </p>
            <div class="body-3 text-caption mb-0">
              جاهزة للسكن
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== ABOUT US SECTION ===== -->
  <section class="about-us-section">
    <div class="container position-relative">
      <!-- Pattern -->
      <img src="{{ asset('assets/images/about/pattern.svg') }}" alt="pattern" class="pattern" />

      <!-- Content -->
      <div class="about-us-content d-flex gap-5xl">
        <!-- Image -->
        <div class="order-1 order-md-2 position-relative">
          <div class="image border-surface radius-normal-box overflow-hidden">
            <img class="w-100 h-100" src="{{ asset('assets/images/hero/imagehome.png') }}" alt="صورة أثاث" />
          </div>
        </div>

        <!-- Content -->
        <div class="order-2 order-md-1 d-flex flex-column gap-l mx-auto" style="max-width: 470px;">
          <!-- Heading -->
          <div class="d-flex flex-column gap-sm-5">
            <p class="sub-heading-4" style="color: var(--secondary);">من نحن</p>
            <h4 class="heading-h6">منصة تأثيث ذكية للوحدات الفندقية</h4>
            <p class="caption-3 text-caption mb-0">
              نقدم اختيارات متنوعة متكاملة لتأثيث وحدتك الفندقية بالكامل بأعلى
              التصميمات الجذابة ومحددة مسبقًا لتسهيل إنشاء الحلول الجاهزة لأوامر
              الشراء الكامل من خلال منصة موحدة وسلسة.
            </p>
          </div>

          <!-- Details -->
          <div class="d-flex flex-column gap-sm-3">
            <div class="d-flex gap-sm-4">
              <img src="{{ asset('assets/images/icons/iconeclock.png') }}" alt="توفير الوقت" />
              <span class="body-1">توفير الوقت</span>
            </div>

            <div class="d-flex gap-sm-4">
              <img src="{{ asset('assets/images/icons/shield-check.png') }}" alt="توفير التكلفة" />
              <span class="body-1">توفير عالي للتكلفة</span>
            </div>

            <div class="d-flex gap-sm-4">
              <img src="{{ asset('assets/images/icons/icone5.png') }}" alt="جاهزية" />
              <span class="body-1">جاهزية كاملة للتسليم</span>
            </div>
          </div>
          <a href="{{ route('about') }}" class="btn btn-custom-primary w-100">
            تعرف على طريقتنا العمل
        </a>


        </div>
      </div>
    </div>
  </section>

  <!-- ===== PACKAGE SELECTION SECTION ===== -->
  <section id="package-selection" class="package-selection-section">
    <div class="container d-flex flex-column gap-xl">
      <!-- Heading -->
      <div class="heading text-center d-flex flex-column gap-sm-5">
        <h3 class="heading-h6 mb-0">اختر الباكج المناسب لوحدتك</h3>
        <p class="caption-3 text-caption mb-0 mx-auto" style="max-width: 440px;">
          باكجات فندقية مؤثثة بالكامل، جاهزة للتنفيذ خلال 90 يومًا، بخيارات تصميم وألوان راقية
        </p>
      </div>

      <!-- Form -->
      <form class="package-selection-form padding-box-normal d-flex flex-column gap-md border border-surface radius-small-box mx-auto">
        <!-- Body -->
        <div class="d-flex flex-column gap-sm">
          <!-- Unit Type Selection -->
          <div class="d-flex flex-column gap-sm-5 mb-2">
            <label class="form-label body-1">نوع الوحدة</label>
            <div class="d-flex gap-sm-3">
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="unit-type" id="studio" value="studio" checked />
                <label class="form-check-label body-1 text-nowrap" for="studio">استوديو</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="unit-type" id="one-bedroom" value="one-bedroom" />
                <label class="form-check-label body-1 text-nowrap" for="one-bedroom">غرفة نوم واحدة</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="unit-type" id="two-bedroom" value="two-bedroom" />
                <label class="form-check-label body-1 text-nowrap" for="two-bedroom">غرفتين نوم</label>
              </div>
            </div>
          </div>

          <!-- Area Selection -->
          <div class="d-flex flex-column gap-sm-5 mb-2">
            <label class="form-label body-1">المساحة (متر مربع)</label>
            <div class="d-flex gap-sm-3">
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="area" id="area-30-50" value="30-50" checked />
                <label class="form-check-label body-1 text-nowrap" for="area-30-50">30-50</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="area" id="area-50-80" value="50-80" />
                <label class="form-check-label body-1 text-nowrap" for="area-50-80">50-80</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="area" id="area-80-120" value="80-120" />
                <label class="form-check-label body-1 text-nowrap" for="area-80-120">80-120</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="area" id="area-120+" value="120+" />
                <label class="form-check-label body-1 text-nowrap" for="area-120+">120+</label>
              </div>
            </div>
          </div>

          <!-- Budget Selection -->
          <div class="d-flex flex-column gap-sm-5 mb-2">
            <label class="form-label body-1">الميزانية المتوقعة</label>
            <div class="d-flex gap-sm-3">
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="budget" id="budget-low" value="low" checked />
                <label class="form-check-label body-1 text-nowrap" for="budget-low">أقل من 50,000 ر.س</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="budget" id="budget-medium" value="medium" />
                <label class="form-check-label body-1 text-nowrap" for="budget-medium">50,000 - 100,000 ر.س</label>
              </div>

            </div>
          </div>

          <!-- Electric Installation -->
          <div class="d-flex flex-column gap-sm-5 mb-2">
            <label class="form-label body-1">هل تحتاج تمديدات كهربائية؟</label>
            <div class="d-flex gap-sm-3">
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="electric" id="electric-yes" value="yes" checked />
                <label class="form-check-label body-1 text-nowrap" for="electric-yes">نعم</label>
              </div>
              <div class="d-flex align-items-center gap-sm-5">
                <input class="form-check-input" type="radio" name="electric" id="electric-no" value="no" />
                <label class="form-check-label body-1 text-nowrap" for="electric-no">لا</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-custom-primary w-100">
          عرض الباكيجات
        </button>
      </form>
    </div>
  </section>

  <!-- ===== PACKAGES SECTION ===== -->
  <section class="package-section">
    <div class="container d-flex flex-column gap-xl">
      <!-- Heading -->
      <div class="heading text-center d-flex flex-column gap-sm-5">
        <h3 class="heading-h6 mb-0">اختر الباكج المناسب لوحدتك</h3>
        <p class="caption-3 text-caption mb-0 mx-auto" style="max-width: 440px;">
          باكجات فندقية مؤثثة بالكامل، جاهزة للتنفيذ خلال 90 يومًا، بخيارات تصميم وألوان راقية
        </p>
      </div>

      <!-- Rooms -->
      <div class="rooms-container">
        <div class="row">
          @foreach($categories as $category)
          <!-- Package Item -->
          <div class="col-sm-12 col-md-6 mb-sm-4">
            <div class="room-item">
              <!-- image & widget -->
              <div class="image">
                <div class="widget text-center">
                  <span class="body-4 text-white">جاهز للتسليم السريع</span>
                </div>
                @if($category->image)
                  <img src="{{ asset('storage/' . $category->image) }}" class="w-100 h-100" alt="{{ $category->name }}" />
                @else
                  <img src="{{ asset('assets/images/category/category-01.jpg') }}" class="w-100 h-100" alt="{{ $category->name }}" />
                @endif
              </div>

              <!-- Content -->
              <div class="content d-flex flex-column gap-sm-3">
                <!-- Title & Quantity & Description -->
                <div class="d-flex justify-content-between">
                  <div class="d-flex flex-column gap-sm-6">
                    <h5 class="sub-heading-3">
                      باكدج {{ $category->name }}
                    </h5>
                    <p class="body-3 mb-0">
                      {{ $category->description ?: 'مثالي للمساحات الصغيرة، يوفر الراحة والأناقة' }}
                    </p>
                  </div>
                  <p class="body-2" style="color: var(--secondary);">{{ $category->products()->count() }} قطعة</p>
                </div>

                <!-- Price -->
                <div class="d-flex align-items-center gap-sm-5 mb-2">
                  <p class="body-2 text-caption mb-0">ابتداءً من</p>
                  <h4 class="heading-h6 mb-0">
                    {{ number_format($category->products()->min('price') ?: 12055, 0) }}
                    <img src="{{ asset('assets/images/hero/Platform Subtitle.png') }}" alt="" />
                  </h4>
                </div>

                <!-- Options -->
                <div class="d-flex flex-column gap-sm-4">
                  <!-- Including -->
                  <div class="d-flex gap-sm-3 align-items-center">
                    <p class="body-2 text-caption mb-0" style="width: 90px;">يشمل:</p>
                    <div class="d-flex gap-sm-5">
                      <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                        <img src="{{ asset('assets/images/icons/caricone.png') }}" alt="" />
                        <span class="body-4">غرفة نوم</span>
                      </div>
                      <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                        <img src="{{ asset('assets/images/icons/sofa.png') }}" alt="" />
                        <span class="body-4">مجلس</span>
                      </div>
                      <span class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                        <img src="{{ asset('assets/images/icons/foot.png') }}" alt="" />
                        <span class="body-4">طاولة طعام</span>
                      </span>
                    </div>
                  </div>

                  <!-- Colors -->
                  <div class="d-flex gap-sm-3 align-items-center">
                    <p class="body-2 text-caption mb-0" style="width: 90px;">الألوان المتاحة:</p>
                    <div class="d-flex gap-sm-5">
                      <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #f5f1e6"></span>
                      <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #aaaaaa"></span>
                      <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #a1866f"></span>
                      <span class="rounded-pill" style="width: 34px; height: 16px;background-color: #8b5e3c"></span>
                    </div>
                  </div>

                  <!-- Time implementation -->
                  <div class="d-flex gap-sm-3 align-items-center">
                    <p class="body-2 text-caption mb-0" style="width: 90px;">مدة التنفيذ:</p>
                    <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                      <img src="{{ asset('assets/images/icons/clock-watch.png') }}" alt="" />
                      <span class="body-4">15–20 يوم عمل</span>
                    </div>
                  </div>

                  <!-- Service -->
                  <div class="d-flex gap-sm-3 align-items-center">
                    <p class="body-2 text-caption mb-0" style="width: 90px;">الخدمة</p>
                    <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                      <img src="{{ asset('assets/images/icons/tools-wench-ruler.png') }}" alt="" />
                      <span class="body-4">يشمل التركيب والتوصيل الكامل</span>
                    </div>
                  </div>

                  <!-- Payment Plan -->
                  <div class="d-flex gap-sm-3 align-items-center">
                    <p class="body-2 text-caption mb-0" style="width: 90px;"> خطة الدفع :</p>
                    <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                      <img src="{{ asset('assets/images/icons/wallet-2.png') }}" alt="" />
                      <span class="body-4">50٪ مقدم – 50٪ قبل التسليم</span>
                    </div>
                  </div>

                  <!-- Decoration -->
                  <div class="d-flex gap-sm-3 align-items-center">
                    <p class="body-2 text-caption mb-0" style="width: 90px;"> الديكور :</p>
                    <div class="feature-item d-flex gap-sm-6 border rounded-pill border-surface">
                      <img src="{{ asset('assets/images/icons/brush-ruler.png') }}" alt="" />
                      <span class="body-4"> ديكور أساسي بسيط</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Actions Buttons -->
              <div class="actions d-flex gap-sm-2">


                <a href="https://wa.me/{{$siteSettings->whatsapp}}" target="_blank" class="btn btn-custom-primary w-100">
                    <p class="text-nowrap mb-0">أرسل لي عرض السعر عبر الوتساب</p>
                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                 </a>


                <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-custom-secondary w-100">
                  <span style="white-space: nowrap;">عرض التفاصيل</span>
                  <i class="fa-solid fa-arrow-left action-icon"></i>
                </a>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>


  <!-- ===== PROCESS STEPS SECTION ===== -->
  <section class="process-section">
    <div class="container">
      <div class="process-section-content">
        <!-- image -->
        <div
          class="info w-100 mr-auto h-100 bg-secondary radius-normal-box d-flex flex-column gap-sm-2 justify-content-center padding-box-normal"
          style="flex: 0.5">
          <!-- Decorative Pattern -->
          <img src="assets/images/ui/pattern.svg" alt="pattern" class="pattern" />

          <!-- Image Info -->
          <div class="info-item radius-small-box d-flex gap-sm-3 z-1">
            <div class="avatar">
              <img class="w-100 h-100" src="assets/images/avatar/avatar-01.jpg" alt="" />
            </div>
            <div class="d-flex flex-column gap-sm-7">
              <p class="sub-heading-5 text-white mb-0"> م. أحمد ابراهيم </p>
              <p class="body-4 text-white mb-0">2 وحدة</p>
            </div>
          </div>

          <!-- Progress -->
          <div class="info-item radius-small-box d-flex flex-column gap-sm-3 z-1">
            <p class="body-4 text-white mb-0">حالة الطلب الحالية</p>
            <p class="sub-heading-5 text-white mb-0">جاري التركيب 80%</p>
            <div class="progress" role="progressbar" aria-label="Progress" aria-valuenow="80" aria-valuemin="0"
              aria-valuemax="100">
              <div class="progress-bar" style="width: 80%"></div>
            </div>
          </div>
        </div>

        <!-- Column 2 -->
        <div class="d-flex flex-column gap-md" style="flex: 0.5;">
          <!-- Heading -->
          <div class="d-flex flex-column gap-sm-5">
            <h6 class="heading-h6">
              خطوات سهلة لإنهاء تأثيث وحدتك
            </h6>
            <p class="text-caption mb-0">
              خطوات واضحة وسريعة من اختيار الباكدج حتى استلام وحدتك مؤثثة
              بالكامل
            </p>
          </div>

          <!-- Steps -->
          <div class="d-flex flex-column gap-sm">
            <!-- Item 1 -->
            <div class="step d-flex gap-sm-3">
              <div class="step-icon">
                <img src="assets/images/hero/Platform Text Container.png" />
              </div>
              <div class="d-flex flex-column gap-sm-5">
                <h2 class="sub-heading-3 mb-0">اختر الباكدج واللون</h2>
                <p class="text-caption mb-0">
                  اختر التصميم المناسب من بين خيارات الألوان: هادئ ومحايد أو
                  ترابي ودافئ
                </p>
              </div>
            </div>

            <!-- Item 2 -->
            <div class="step d-flex gap-sm-3">
              <div class="step-icon">
                <img src="assets/images/hero/icone9.png" />
              </div>
              <div class="d-flex flex-column gap-sm-5">
                <h2 class="sub-heading-3 mb-0">املأ النموذج الذكي</h2>
                <p class="text-caption mb-0">
                  أدخل بياناتك وعدد الوحدات، وسنتولى الباقي
                </p>
              </div>
            </div>

            <!-- Item 3 -->
            <div class="step d-flex gap-sm-3">
              <div class="step-icon">
                <img src="assets/images/hero/icone10.png" />
              </div>
              <div class="d-flex flex-column gap-sm-5">
                <h2 class="sub-heading-3 mb-0">استلم عرض السعر والموافقة</h2>
                <p class="text-caption mb-0">
                  نرسل لك تسعيرة تفصيلية مع الموافقة قبل التنفيذ
                </p>
              </div>
            </div>

            <!-- Item 4 -->
            <div class="step d-flex gap-sm-3">
              <div class="step-icon2">
                <img src="assets/images/hero/icone11.png" />
              </div>
              <div class="d-flex flex-column gap-sm-5">
                <h2 class="sub-heading-3 mb-0">استلم وحدتك مؤثثة</h2>
                <p class="text-caption mb-0">
                  نقوم بالتنفيذ والشحن والتركيب حتى باب وحدتك
                </p>
              </div>
            </div>
          </div>

          <!-- Button
          <button class="btn btn-custom-primary w-100">
            ابدأ الآن أو استلم عرض السعر
          </button> -->
          <a href="{{ route('help.index') }}" class="btn btn-custom-primary w-100">
            ابدأ الآن أو استلم عرض السعر
        </a>

        </div>
      </div>
    </div>
  </section>

  <!-- ===== WHY CHOOSE US SECTION ===== -->
  <section class="why-choose-section bg-primary-light overflow-hidden">
    <!-- Decorative Pattern -->
    <img src="assets/images/about/pattern.svg" alt="pattern" class="pattern" />

    <!-- Content -->
    <div class="container d-flex flex-column gap-md">
      <!-- Heading -->
      <div class="heading d-flex flex-column gap-sm-5">
        <h3 class="heading-h6">
          لماذا نحن؟
        </h3>
        <p class="body-2 text-caption mb-0">
          لأنك تستحق تأثيثًا فندقيًا متكاملًا... بجودة، سرعة، وراحة.
        </p>
      </div>

      <!-- Content -->
      <div class="row">
        <!-- Item 1 -->
        <div class="why-choose-col radius-small-box-2 padding-box-small-3">
          <div class="d-flex gap-sm align-items-center">
            <p class="heading-h6 mb-0">1.</p>
            <div class="d-flex flex-column gap-sm-6">
              <h5 class="sub-heading-4 mb-0">
                إنجاز فوري خلال 14 يومًا
              </h5>
              <p class="body-3 mb-0">
                نوفر لك باكج جاهز يتم تسليمه خلال فترة قصيرة دون تأخير
              </p>
            </div>
          </div>
        </div>

        <!-- Item 2 -->
        <div class="why-choose-col radius-small-box-2 padding-box-small-3">
          <div class="d-flex gap-sm align-items-center">
            <h2 class="heading-h6 mb-0">2.</h2>
            <div class="d-flex flex-column gap-sm-6">
              <h5 class="sub-heading-4 mb-0">
                أسعار شاملة وواضحة
              </h5>
              <p class="body-3 mb-0">
                سعر موحّد يشمل كل شيء: الأثاث، الشحن، التركيب
              </p>
            </div>
          </div>
        </div>

        <!-- Item 3 -->
        <div class="why-choose-col radius-small-box-2 padding-box-small-3">
          <div class="d-flex gap-sm align-items-center">
            <h2 class="heading-h6 mb-0">3.</h2>
            <div class="d-flex flex-column gap-sm-6">
              <h5 class="sub-heading-4 mb-0">
                جودة فندقية مضمونة
              </h5>
              <p class="body-3 mb-0">
                اختيار دقيق للخامات وتنفيذ بمعايير تناسب الاستخدام
              </p>
            </div>
          </div>
        </div>

        <!-- Item 4 -->
        <div class="why-choose-col radius-small-box-2 padding-box-small-3">
          <div class="d-flex gap-sm align-items-center">
            <h2 class="heading-h6 mb-0">4.</h2>
            <div class="d-flex flex-column gap-sm-6">
              <h5 class="sub-heading-4 mb-0">نظام متابعة ذكي</h5>
              <p class="body-3 mb-0">
                تابع حالة الطلب خطوة بخطوة من خلال لوحة رقمية
              </p>
            </div>
          </div>
        </div>

        <!-- Item 5 -->
        <div class="why-choose-col radius-small-box-2 padding-box-small-3">
          <div class="d-flex gap-sm align-items-center">
            <h2 class="heading-h6 mb-0">5.</h2>
            <div class="d-flex flex-column gap-sm-6">
              <h5 class="sub-heading-4 mb-0">
                إشعارات في كل مرحلة
              </h5>
              <p class="body-3 mb-0">
                نُبقيك على اطلاع تام في كل مرحلة: من التصنيع
              </p>
            </div>
          </div>
        </div>

        <!-- Item 6 -->
        <div class="why-choose-col radius-small-box-2 padding-box-small-3">
          <div class="d-flex gap-sm align-items-center">
            <h2 class="heading-h6 mb-0">6.</h2>
            <div class="d-flex flex-column gap-sm-6">
              <h5 class="sub-heading-4 mb-0">
                استلام مؤثث بالكامل
              </h5>
              <p class="body-3 mb-0">
                كل ما تحتاجه في مكان واحد – تستلم وحدتك جاهزة
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== ORDER TIMELINE SECTION ===== -->
  <section class="order-time-line-section">
    <div class="container d-flex flex-column gap-l">
      <!-- Heading -->
      <div class="heading text-center d-flex flex-column gap-sm-5">
        <h3 class="heading-h6 mb-0">تابع تقدم طلبك لحظة بلحظة</h3>
        <p class="body-2 text-caption mb-0">
          مخطط زمني يوضح كل مرحلة من الطلب حتى التسليم النهائي، دائماً في الصورة
        </p>
      </div>

      <!-- Timeline Rows -->
      <div class="timeline-wrapper position-relative">
        <!-- Decorative Pattern -->
        <img src="assets/images/about/pattern.svg" alt="pattern" class="pattern" />

        <!-- Row 1 -->
        <div class="timeline-row d-flex align-items-center">
          <div class="col-5"></div>
          <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
            <div class="dot-wrapper position-relative d-flex flex-column align-items-center">
              <div class="timeline-line"></div>
              <div class="dot-item" style="background-color: #08203E;"></div>
            </div>
          </div>
          <div class="col-5">
            <div class="timeline-item" style="background-color: #08203E;">
              <div class="arrow right-arrow" style="border-left-color: #08203E;"></div>
              <div class="sub-heading-3 text-white">تم الطلب</div>
              <div class="body-3 text-white">تم استلام طلبك بنجاح وجاري مراجعته داخلياً</div>
            </div>
          </div>
        </div>

        <!-- Row 2 -->
        <div class="timeline-row d-flex align-items-center">
          <div class="col-5">
            <div class="timeline-item bg-secondary" style="margin-right: auto;">
              <div class="arrow left-arrow" style="border-right-color: #ad996f;"></div>
              <div class="sub-heading-3 text-white">التصميم</div>
              <div class="body-3 text-white">يتم تجهيز الرسومات واختيار النمط والألوان النهائية</div>
            </div>
          </div>
          <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
            <div class="dot-item" style="background-color: #ad996f;"></div>
          </div>
          <div class="col-5"></div>
        </div>

        <!-- Row 3 -->
        <div class="timeline-row d-flex align-items-center">
          <div class="col-5"></div>
          <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
            <div class="dot-item" style="background-color: #979DAC;"></div>
          </div>
          <div class="col-5">
            <div class="timeline-item" style="background-color: #979DAC;">
              <div class="arrow right-arrow" style="border-left-color: #979DAC;"></div>
              <div class="sub-heading-3 text-white">التصنيع</div>
              <div class="body-3 text-white">نبدأ بتنفيذ الأثاث حسب المقاسات والمواصفات المعتمدة</div>
            </div>
          </div>
        </div>

        <!-- Row 4 -->
        <div class="timeline-row d-flex align-items-center">
          <div class="col-5">
            <div class="timeline-item" style="margin-right: auto; background-color: #33415C;">
              <div class="arrow left-arrow" style="border-right-color: #33415C;"></div>
              <div class="sub-heading-3 text-white">الشحن</div>
              <div class="body-3 text-white">يتم شحن الطلب إلى موقعك مع إشعار بالموعد المتوقع</div>
            </div>
          </div>
          <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
            <div class="dot-item" style="background-color: #33415C;"></div>
          </div>
          <div class="col-5"></div>
        </div>

        <!-- Row 5 -->
        <div class="timeline-row d-flex align-items-center">
          <div class="col-5"></div>
          <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
            <div class="dot-item" style="background-color: #32B828;"></div>
          </div>
          <div class="col-5">
            <div class="timeline-item" style="background-color: #32B828;">
              <div class="arrow right-arrow" style="border-left-color: #32B828;"></div>
              <div class="sub-heading-3 text-white">الدفعة الثانية</div>
              <div class="body-3 text-white">يتم تحصيل الدفعة النهائية قبل التركيب</div>
            </div>
          </div>
        </div>

        <!-- Row 6 -->
        <div class="timeline-row d-flex align-items-center">
          <div class="col-5">
            <div class="timeline-item" style="margin-right: auto; background-color: #C1B41C;">
              <div class="arrow left-arrow" style="border-right-color: #C1B41C;"></div>
              <div class="sub-heading-3 text-white">التركيب والتسليم</div>
              <div class="body-3 text-white">يتم تركيب الأثاث بالكامل وتسليمه للوحدة</div>
            </div>
          </div>
          <div class="col-2 d-flex justify-content-center" style="max-width: 120px;">
            <div class="dot-item" style="background-color: #C1B41C;"></div>
          </div>
          <div class="col-5"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CALL TO ACTION SECTION ===== -->
  <section class="ready-to-furnish-section">
    <div class="ready-to-furnish-your-unit-container container position-relative bg-primary">
      <!-- pattern -->
      <img class="ready-to-furnish-your-unit-pattern" src="../assets/images/about/pattern.svg" alt="Pattern" />

      <!-- content -->
      <div class="ready-to-furnish-your-unit-content d-flex flex-column gap-sm">
        <div class="d-flex flex-column gap-sm-5">
          <h2 class="heading-h6 text-white">جاهز لتأثيث وحدتك؟</h2>
          <p class="caption-4 mb-0">
            ابدأ خطوتك الأولى الآن… باكجاتنا جاهزة للتنفيذ والتسليم في وقت قياسي
          </p>
        </div>

        <!-- buttons -->
        <div class="ready-to-furnish-your-unit-buttons d-flex gap-sm-3">
          <!--<button class="btn btn-custom-secondary">
            <p class="text-nowrap mb-0">تحدث معنا عبر واتساب</p>
            <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
          </button>-->
          <a href="https://wa.me/{{$siteSettings->whatsapp}}" target="_blank" class="btn btn-custom-secondary d-flex align-items-center gap-2">
            <p class="text-nowrap mb-0">تحدث معنا عبر واتساب</p>
            <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
        </a>
          <!--<button class="btn border-btn">
            <p class="text-nowrap mb-0">اطلب الان</p>
            <i class="fa-solid fa-arrow-left" style="font-size: 18px;"></i>
          </button>-->
          <a href="{{ route('help.index') }}" class="btn border-btn d-flex align-items-center gap-2">
            <p class="text-nowrap mb-0">اطلب الان</p>
            <i class="fa-solid fa-arrow-left" style="font-size: 18px;"></i>
        </a>
        </div>


      </div>

      <!-- image -->
      <div class="ready-to-furnish-your-unit-image-container">
        <div class="ready-to-furnish-your-unit-image">
          <img src="../assets/images/about/about-05.jpg" alt="Ready to Furnish Your Unit" />
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONIAL SECTION ===== -->
  <section class="testimonial-section">
    <div class="container d-flex flex-column gap-l">
      <!-- Heading -->
      <div class="heading text-center d-flex flex-column gap-sm-5">
        <h6 class="sub-title bg-primary-light px-2 pt-1 pb-1 mx-auto mb-0" style="width: fit-content">
          آراء العملاء
        </h6>
        <h3 class="heading-h6 mb-0">ثقة عملائنا... هي أكبر إنجاز لنا</h3>
        <p class="body-2 text-caption mb-0">
          آراء عملائنا تعكس مستوى الجودة والراحة والاحترافية بكل خطوة من تجربة الشراء.
        </p>
      </div>

      <!-- Carousel -->
      <div id="testimonial-carousel-homepage" class="testimonial-carousel owl-carousel owl-theme">
        @foreach($testimonials as $testimonial)
          <div class="testimonial-item card shadow-sm h-100 p-3">
            <img src="{{ asset('assets/images/icons/quotation.png') }}" class="mb-3" />
            <p class="caption-2 text-subheading mb-3">
              {{ $testimonial->message }}
            </p>
            <div class="d-flex align-items-center gap-sm-4 mt-auto">
              <img src="{{ $testimonial->image ? asset('storage/' . $testimonial->image) : asset('assets/images/avatar/default.png') }}"
                   class="imgprofile" alt="" />
              <div style="flex: 1">
                <p class="mb-0 sub-heading-4 text-subheading">{{ $testimonial->name }}</p>
                <span class="text-body body-3">{{ $testimonial->location }}</span>
              </div>
              <div style="color: var(--system-yellow) !important">
                {{ str_repeat('★', $testimonial->rating) }}
                {{ str_repeat('☆', 5 - $testimonial->rating) }}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ===== GALLERY SECTION ===== -->
  <section class="gallery-section bg-primary-light">
    <div class="container d-flex flex-column gap-l">
      <!-- Heading -->
      <div class="heading text-center d-flex flex-column gap-sm-5">
        <h3 class="heading-h6 mb-0">
          شاهد نماذج تأثيثنا
        </h3>
        <p class="body-2 text-caption mb-0">
          نقدّم لك لمحة بصرية عن مستوى التأثيث، التفاصيل، والتشطيب في مشاريعنا السابقة
        </p>
      </div>

      <!-- images -->
      <div class="gallery-details-images">
        <div class="gallery-details-image-grid">
          <div class="gallery-details-image-item">
            <div class="gallery-details-image-sub-item gallery-large-img">
              <img src="../assets/images/gallery/gallery-01.jpg" alt="Gallery Image" />
              <div class="image-overlay">
                <span class="sub-heading-2">Bedroom</span>
              </div>
            </div>
          </div>
          <div class="gallery-details-image-item d-flex gap-sm">
            <div class="gallery-details-image-sub-item gallery-small-img" style="flex: 0.5">
              <img src="../assets/images/gallery/gallery-01.jpg" alt="Gallery Image" />
              <div class="image-overlay">
                <span class="sub-heading-2">Bedroom</span>
              </div>
            </div>
            <div class="gallery-details-image-sub-item gallery-small-img" style="flex: 0.5">
              <img src="../assets/images/gallery/gallery-01.jpg" alt="Gallery Image" />
              <div class="image-overlay">
                <span class="sub-heading-2">Bedroom</span>
              </div>
            </div>
          </div>
          <div class="gallery-details-image-item">
            <div class="gallery-details-image-sub-item gallery-large-img">
              <img src="../assets/images/gallery/gallery-01.jpg" alt="Gallery Image" />
              <div class="image-overlay">
                <span class="sub-heading-2">Bedroom</span>
              </div>
            </div>
          </div>
          <div class="gallery-details-image-item">
            <div class="gallery-details-image-sub-item gallery-large-img">
              <img src="../assets/images/gallery/gallery-01.jpg" alt="Gallery Image" />
              <div class="image-overlay">
                <span class="sub-heading-2">Bedroom</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA Button -->
      <div class="w-100">
        <button class="btn btn-custom-primary mx-auto">
          شاهد المعرض كاملا
        </button>
      </div>
    </div>
  </section>

  <!-- ===== FAQ SECTION ===== -->
  <section class="faq-section">
    <div class="container d-flex flex-column gap-l">
        <!-- Heading -->
        <div class="heading text-center d-flex flex-column gap-sm-5">
            <h3 class="heading-h6 mb-0" data-translate="faq.title">
                {{ __('أسئلتك… نُجيب عليها بكل وضوح') }}
            </h3>
            <p class="body-2 text-caption mb-0" data-translate="faq.subtitle">
                {{ __('كل ما تحتاج معرفته قبل الطلب، جمعناه لك هنا باختصار وشفافية') }}
            </p>
        </div>

        <!-- Accordion -->
        <div class="faq-section-content">
            <div class="accordion" id="accordionFaq">
                @foreach($faqs as $index => $faq)
                <div class="accordion-item">
                    <div class="accordion-header">
                        <button class="accordion-button @if($index != 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                            <p class="sub-heading-3 mb-0">
                                {{ app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en }}
                            </p>
                        </button>
                    </div>
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse @if($index == 0) show @endif" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionFaq">
                        <div class="accordion-body">
                            <p class="body-2 text-body mb-0">
                                {{ app()->getLocale() == 'ar' ? $faq->answer_ar : $faq->answer_en }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>




@endsection

@push('scripts')
<script>
  // Hero Carousel Auto-play
  document.addEventListener('DOMContentLoaded', function() {
    var heroCarousel = new bootstrap.Carousel(document.getElementById('hero-carousel'), {
      interval: 5000,
      wrap: true
    });
  });

  // Package Selection Form
  document.querySelector('.package-selection-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Get form data
    const formData = new FormData(this);
    const unitType = formData.get('unit-type');
    const area = formData.get('area');
    const budget = formData.get('budget');
    const electric = formData.get('electric');

    // Scroll to packages section
    document.querySelector('.package-section').scrollIntoView({
      behavior: 'smooth'
    });

    // You can add filtering logic here based on form data
    console.log('Form submitted:', { unitType, area, budget, electric });
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });


</script>
@endpush

