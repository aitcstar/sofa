@extends('admin.layouts.app')

@section('title', 'تعديل الباكج')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل الباكج: {{ $package->name_en }} / {{ $package->name_ar }}</h1>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات الباكج</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.packages.update', $package) }}"  method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <!-- الاسم والسعر -->
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">اسم الباكج (عربي) <span class="text-danger">*</span></label>
                      <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $package->name_ar) }}" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">اسم الباكج (إنجليزي) <span class="text-danger">*</span></label>
                      <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $package->name_en) }}" required>
                  </div>
              </div>

              <!-- السعر والوصف -->
              <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">الوصف (عربي)</label>
                      <textarea name="description_ar" class="form-control" rows="2">{{ old('description_ar', $package->description_ar) }}</textarea>
                  </div>
                  <div class="col-md-6">

                      <label class="form-label mt-2">الوصف (إنجليزي)</label>
                      <textarea name="description_en" class="form-control" rows="2">{{ old('description_en', $package->description_en) }}</textarea>
                  </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"> مدة التنفيذ (عربي) <span class="text-danger">*</span></label>
                    <input type="text" name="period_ar" class="form-control" value="{{ old('period_ar', $package->period_ar) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"> مدة التنفيذ (إنجليزي) <span class="text-danger">*</span></label>
                    <input type="text" name="period_en" class="form-control" value="{{ old('period_en', $package->period_en) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"> الخدمة (عربي) <span class="text-danger">*</span></label>
                    <input type="text" name="service_includes_ar" class="form-control" value="{{ old('service_includes_ar', $package->service_includes_ar) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"> الخدمة (إنجليزي) <span class="text-danger">*</span></label>
                    <input type="text" name="service_includes_en" class="form-control" value="{{ old('service_includes_en', $package->service_includes_en) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"> خطة الدفع (عربي) <span class="text-danger">*</span></label>
                    <input type="text" name="payment_plan_ar" class="form-control" value="{{ old('payment_plan_ar', $package->payment_plan_ar) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"> خطة الدفع (إنجليزي) <span class="text-danger">*</span></label>
                    <input type="text" name="payment_plan_en" class="form-control" value="{{ old('payment_plan_en', $package->payment_plan_en) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">  الديكور (عربي) <span class="text-danger">*</span></label>
                    <input type="text" name="decoration_ar" class="form-control" value="{{ old('decoration_ar', $package->decoration_ar) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"> الديكور (إنجليزي) <span class="text-danger">*</span></label>
                    <input type="text" name="decoration_en" class="form-control" value="{{ old('decoration_en', $package->decoration_en) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">السعر (ريال) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $package->price) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control"value="{{ old('sort_order', $package->sort_order) }}" required >
                </div>
            </div>



                <!-- صورة الباكج الرئيسية -->

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">الصورة الرئيسية</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                        @if($package->image)
                        <div class="mb-3">
                            <h6>الصورة الحالية</h6>
                            <img src="{{ asset('storage/' . $package->image) }}" class="img-fluid rounded" width="150">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">صور إضافية</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>

                    <h6>الصور الحالية</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($package->images as $img)
                            <div class="position-relative img-container" style="width:120px" id="img-{{ $img->id }}">
                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                     class="img-thumbnail rounded" width="120">

                                <button type="button"
                                        class="btn btn-sm btn-danger rounded-circle p-1 d-flex align-items-center justify-content-center delete-image"
                                        style="width: 24px; height: 24px; position:absolute; top:5px; right:5px;"
                                        data-url="{{ route('admin.packages.images.destroy', [$package->id, $img->id]) }}"
                                        data-id="{{ $img->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

           <!-- صور إضافية -->













              <!-- الوحدات والعناصر -->
              <div class="mb-3">
                  <h6>الوحدات الفرعية</h6>
                  <div id="units-container">
                      @foreach($package->units as $uIndex => $unit)
                          <div class="card mb-3 unit-card">
                              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                  <h5 class="mb-0">وحدة {{ $uIndex + 1 }}</h5>
                                  <button type="button" class="btn btn-sm btn-danger" onclick="removeUnit(this)">حذف</button>
                              </div>
                              <div class="card-body">
                                  <input type="hidden" name="units[{{ $uIndex }}][id]" value="{{ $unit->id }}">

                                  <!-- بيانات الوحدة -->
                                  <div class="mb-2">
                                      <label class="form-label">اسم الوحدة (عربي)</label>
                                      <input type="text" name="units[{{ $uIndex }}][name_ar]" class="form-control" value="{{ $unit->name_ar }}" required>
                                  </div>
                                  <div class="mb-2">
                                      <label class="form-label">اسم الوحدة (إنجليزي)</label>
                                      <input type="text" name="units[{{ $uIndex }}][name_en]" class="form-control" value="{{ $unit->name_en }}" required>
                                  </div>
                                  <div class="mb-2">
                                      <label class="form-label">نوع الفئة</label>
                                      <select name="units[{{ $uIndex }}][type]" class="form-control" required>
                                          <option value="bedroom" {{ $unit->type == 'bedroom' ? 'selected' : '' }}>غرفة نوم</option>
                                          <option value="living_room" {{ $unit->type == 'living_room' ? 'selected' : '' }}>معيشة</option>
                                          <option value="kitchen" {{ $unit->type == 'kitchen' ? 'selected' : '' }}>مطبخ</option>
                                          <option value="bathroom" {{ $unit->type == 'bathroom' ? 'selected' : '' }}>حمام</option>
                                          <option value="external" {{ $unit->type == 'external' ? 'selected' : '' }}>الملحقات الخارجية والإضافية</option>
                                        </select>
                                  </div>

                                  <!-- عناصر الوحدة -->
                                  <div class="mt-3">
                                      <h6>العناصر</h6>
                                      <div class="items-container" style="background-color: #fdf8eb;">
                                        @foreach($unit->items as $iIndex => $item)
                                              <div class="border p-2 mb-2 item-card">


                                                  <input type="hidden" name="units[{{ $uIndex }}][items][{{ $iIndex }}][id]" value="{{ $item->id }}">
                                                  <input type="hidden" name="units[{{ $uIndex }}][design_id]" value="{{ $unit->designs->first()->id ?? '' }}">

                                                  <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                    <h6>قطعة {{ $iIndex + 1 }}</h6>
                                                    <button type="button" class="btn btn-sm btn-danger delete-item" data-url="{{ route('admin.items.destroy', $item->id) }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                  </div>

                                                  <div class="row">
                                                      <div class="col-md-6">
                                                          <label>اسم القطعة (عربي)</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][item_name_ar]" class="form-control" value="{{ $item->item_name_ar }}">
                                                      </div>
                                                      <div class="col-md-6">
                                                          <label>اسم القطعة (إنجليزي)</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][item_name_en]" class="form-control" value="{{ $item->item_name_en }}">
                                                      </div>
                                                  </div>
                                                  <div class="row mt-2">
                                                      <div class="col-md-3">
                                                          <label>الكمية</label>
                                                          <input type="number" name="units[{{ $uIndex }}][items][{{ $iIndex }}][quantity]" class="form-control" value="{{ $item->quantity }}">
                                                      </div>
                                                      <div class="col-md-3">
                                                          <label>الأبعاد</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][dimensions]" class="form-control" value="{{ $item->dimensions }}">
                                                      </div>
                                                      <div class="col-md-3">
                                                          <label>الخامة (عربي)</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][material_ar]" class="form-control" value="{{ $item->material_ar }}">
                                                      </div>
                                                      <div class="col-md-3">
                                                        <label>الخامة (إنجليزي)</label>
                                                        <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][material_en]" class="form-control" value="{{ $item->material_en }}">
                                                    </div>
                                                      <div class="col-md-3">
                                                          <label>اللون (عربي)</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][color_ar]" class="form-control" value="{{ $item->color_ar }}">
                                                          <label>اللون (إنجليزي)</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][color_en]" class="form-control" value="{{ $item->color_en }}">
                                                          <input type="color" name="units[{{ $uIndex }}][items][{{ $iIndex }}][background_color]" class="form-control" value="{{ $item->background_color }}">
                                                      </div>


                                                      <div class="col-md-3">
                                                        <label>صورة القطعة</label>
                                                        <input type="file" name="units[{{ $uIndex }}][items][{{ $iIndex }}][image]" class="form-control" accept="image/*">

                                                        @if($item->image_path)
                                                            <input type="hidden" name="units[{{ $uIndex }}][items][{{ $iIndex }}][image_path]"
                                                                   value="{{ $item->image_path }}">
                                                            <div class="mt-2 position-relative" style="width:100px">
                                                                <img src="{{ asset('storage/' . $item->image_path) }}" class="img-thumbnail" width="100">
                                                                <button type="button"
                                                                        class="btn btn-sm btn-danger rounded-circle p-1 position-absolute top-0 end-0 delete-item-image"
                                                                        data-url="{{ route('admin.items.image.destroy', $item->id) }}"
                                                                        style="width:22px; height:22px;">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>


                                                  </div>
                                              </div>
                                          @endforeach
                                      </div>
                                      <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addItem(this, {{ $uIndex }})">
                                          <i class="fas fa-plus"></i> إضافة قطعة
                                      </button>
                                  </div>

                              </div>
                          </div>
                      @endforeach
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addUnit()">
                      <i class="fas fa-plus me-1"></i> إضافة وحدة جديدة
                  </button>
              </div>

              <div class="d-flex gap-2 mt-4">
                  <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save me-1"></i> تحديث الباكج
                  </button>
                  <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                      <i class="fas fa-times me-1"></i> إلغاء
                  </a>
              </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
//let unitIndex = {{ $package->units->count() }};

let unitIndex = document.querySelectorAll('.unit-card').length;

window.addUnit = function () {
    const container = document.getElementById('units-container');
    const unitDiv = document.createElement('div');
    unitDiv.className = 'card mb-3 unit-card';
    unitDiv.innerHTML = `
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">وحدة ${unitIndex + 1}</h5>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeUnit(this)">حذف</button>
        </div>
        <div class="card-body">
            <!-- بيانات الوحدة -->
            <div class="mb-2">
                <label class="form-label">اسم الوحدة (عربي)</label>
                <input type="text" name="units[${unitIndex}][name_ar]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">اسم الوحدة (إنجليزي)</label>
                <input type="text" name="units[${unitIndex}][name_en]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">نوع الفئة</label>
                <select name="units[${unitIndex}][type]" class="form-control" required>
                    <option value="bedroom">غرفة نوم</option>
                    <option value="living_room">معيشة</option>
                    <option value="kitchen">مطبخ</option>
                    <option value="bathroom">حمام</option>
                    <option value="external">الملحقات الخارجية والإضافية</option>
                </select>
            </div>

            <!-- عناصر الوحدة -->
            <div class="mt-3">
                <h6>العناصر</h6>
                <div class="items-container" style="background-color: #fdf8eb;">
                    <!-- العناصر الجديدة هتضاف هنا -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addItem(this, ${unitIndex})">
                    <i class="fas fa-plus"></i> إضافة عنصر
                </button>
            </div>
        </div>
    `;
    container.appendChild(unitDiv);
    unitIndex++;
}



    function removeUnit(btn) {
        if (!confirm('هل أنت متأكد من حذف هذه الوحدة؟')) return;
        btn.closest('.unit-card').remove();
    }
    function addItem(btn, uIndex) {
        const itemsContainer = btn.closest('.card-body').querySelector('.items-container');
        const itemIndex = itemsContainer.querySelectorAll('.item-card').length;

        // ناخد الـ design_id من نفس الوحدة
        const designInput = btn.closest('.card-body').querySelector(`input[name="units[${uIndex}][design_id]"]`);
        const designId = designInput ? designInput.value : '';

        const div = document.createElement('div');
        div.className = 'border p-2 mb-2 item-card';
        div.innerHTML = `
            <input type="hidden" name="units[${uIndex}][items][${itemIndex}][design_id]" value="${designId}">

            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h6>قطعة ${itemIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger delete-item" data-url="">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>اسم القطعة (عربي)</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][item_name_ar]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>اسم القطعة (إنجليزي)</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][item_name_en]" class="form-control">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3">
                    <label>الكمية</label>
                    <input type="number" name="units[${uIndex}][items][${itemIndex}][quantity]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>الأبعاد</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][dimensions]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>الخامة (عربي)</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][material_ar]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>الخامة (إنجليزي)</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][material_en]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label> (عربي) اللون</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][color_ar]" class="form-control">
                    <label> (إنجليزي) اللون</label>
                    <input type="text" name="units[${uIndex}][items][${itemIndex}][color_en]" class="form-control">
                    <input type="color" name="units[${uIndex}][items][${itemIndex}][background_color]" class="form-control">
                </div>

                <div class="col-md-3 mt-2">
                    <label>صورة القطعة</label>
                    <input type="file" name="units[${uIndex}][items][${itemIndex}][image]" class="form-control" accept="image/*">
                </div>
            </div>
        `;
        itemsContainer.appendChild(div);
    }


    $(document).on('click', '.delete-item', function () {
        if (!confirm('هل تريد حذف هذا العنصر؟')) return;

        let button = $(this);
        let url = button.data('url');
        let card = button.closest('.item-card');

        if (url) {
            // العنصر موجود في قاعدة البيانات
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function () {
                    card.fadeOut(300, function () { $(this).remove(); });
                },
                error: function () {
                    alert('حدث خطأ أثناء الحذف');
                }
            });
        } else {
            // عنصر جديد مش محفوظ في قاعدة البيانات
            card.fadeOut(300, function () { $(this).remove(); });
        }
    });



    $(document).on('click', '.delete-image', function () {
        if (!confirm('هل تريد حذف الصورة؟')) return;

        let button = $(this);
        let url = button.data('url');
        let id = button.data('id');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                $('#img-' + id).fadeOut(300, function () { $(this).remove(); });
            },
            error: function (xhr) {
                alert('حدث خطأ أثناء الحذف');
            }
        });
    });


    $(document).on('click', '.delete-item-image', function () {
        if (!confirm('هل تريد حذف الصورة؟')) return;

        let button = $(this);
        let url = button.data('url');
        let id = button.data('id');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                $('#img-' + id).fadeOut(300, function () { $(this).remove(); });
            },
            error: function (xhr) {
                alert('حدث خطأ أثناء الحذف');
            }
        });
    });



</script>
@endsection
