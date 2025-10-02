@extends('admin.layouts.app')

@section('title', 'إضافة باكج جديد')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة باكج جديد</h1>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات الباكج</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <!-- الاسم -->
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">اسم الباكج (عربي) <span class="text-danger">*</span></label>
                      <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">اسم الباكج (إنجليزي) <span class="text-danger">*</span></label>
                      <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
                  </div>
              </div>

              <!-- الوصف -->
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">الوصف (عربي)</label>
                      <textarea name="description_ar" class="form-control" rows="2">{{ old('description_ar') }}</textarea>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">الوصف (إنجليزي)</label>
                      <textarea name="description_en" class="form-control" rows="2">{{ old('description_en') }}</textarea>
                  </div>
              </div>

              <!-- باقي الحقول -->
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">مدة التنفيذ (عربي)</label>
                      <input type="text" name="period_ar" class="form-control" value="{{ old('period_ar') }}">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">مدة التنفيذ (إنجليزي)</label>
                      <input type="text" name="period_en" class="form-control" value="{{ old('period_en') }}">
                  </div>
              </div>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">الخدمة (عربي)</label>
                      <input type="text" name="service_includes_ar" class="form-control" value="{{ old('service_includes_ar') }}">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">الخدمة (إنجليزي)</label>
                      <input type="text" name="service_includes_en" class="form-control" value="{{ old('service_includes_en') }}">
                  </div>
              </div>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">خطة الدفع (عربي)</label>
                      <input type="text" name="payment_plan_ar" class="form-control" value="{{ old('payment_plan_ar') }}">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">خطة الدفع (إنجليزي)</label>
                      <input type="text" name="payment_plan_en" class="form-control" value="{{ old('payment_plan_en') }}">
                  </div>
              </div>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">الديكور (عربي)</label>
                      <input type="text" name="decoration_ar" class="form-control" value="{{ old('decoration_ar') }}">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">الديكور (إنجليزي)</label>
                      <input type="text" name="decoration_en" class="form-control" value="{{ old('decoration_en') }}">
                  </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">السعر (ريال) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control"value="{{ old('sort_order') }}" required >
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">الصورة الرئيسية</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>

              <!-- الوحدات -->
<div class="mb-3">
    <h6>الوحدات الفرعية</h6>
    <div id="units-container"></div>
    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addUnitFromList()">
        <i class="fas fa-plus me-1"></i> إضافة وحدة من القائمة
    </button>
</div>

              <!-- الأزرار -->
              <div class="d-flex gap-2 mt-4">
                  <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save me-1"></i> حفظ الباكج
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
let unitIndex = 0;


// ✅ تعريف الدالة المطلوبة
window.addUnitFromList = function () {
    const container = document.getElementById('units-container');
    const unitDiv = document.createElement('div');
    unitDiv.className = 'card mb-3 unit-card';
    unitDiv.innerHTML = `
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">وحدة ${unitIndex + 1}</h5>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.unit-card').remove()">
                حذف
            </button>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <label>اختر الوحدة</label>
                <select name="units[${unitIndex}][unit_id]" class="form-control" required>
                    <option value="">-- اختر --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">
                            {{ $unit->name_ar }} / {{ $unit->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- يمكنك إضافة الحقول المخفية لاحقًا -->
        </div>
    `;
    container.appendChild(unitDiv);
    unitIndex++;
}

// جلب تفاصيل الوحدة (بما في ذلك الصور)
function fetchUnitDetails(unitId, callback) {
    if (!unitId) {
        callback(null);
        return;
    }
    $.get("{{ route('admin.units.details', ['unit' => 'UNIT_ID']) }}".replace('UNIT_ID', unitId), function(data) {
        callback(data);
    }).fail(function() {
        callback(null);
    });
}

// جلب القطع حسب unit_id
function fetchItemsByUnitId(unitId, callback) {
    if (!unitId) {
        callback([]);
        return;
    }
    $.get("{{ route('admin.items.by-unit', ['unitId' => 'UNIT_ID']) }}".replace('UNIT_ID', unitId), function(data) {
        callback(data);
    }).fail(function() {
        callback([]);
    });
}

window.addUnitFromList = function () {
    const container = document.getElementById('units-container');
    const unitDiv = document.createElement('div');
    unitDiv.className = 'card mb-3 unit-card';
    unitDiv.innerHTML = `
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">وحدة ${unitIndex + 1}</h5>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeUnit(this)">حذف</button>
    </div>
    <div class="card-body">
        <div class="mb-2">
            <label>اختر الوحدة</label>
            <select name="units[${unitIndex}][unit_id]" class="form-control select2-unit" required>
                <option value="">-- اختر وحدة --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">
                        {{ $unit->name_ar }} / {{ $unit->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- الحقول المخفية لتخزين بيانات الوحدة -->
        <input type="hidden" name="units[${unitIndex}][name_ar]" class="unit-name-ar">
        <input type="hidden" name="units[${unitIndex}][name_en]" class="unit-name-en">
        <input type="hidden" name="units[${unitIndex}][type]" class="unit-type">
        <input type="hidden" name="units[${unitIndex}][description_ar]" class="unit-desc-ar">
        <input type="hidden" name="units[${unitIndex}][description_en]" class="unit-desc-en">

        <!-- عرض الصور الحالية للوحدة -->
        <div class="mb-2">
            <label>صور الوحدة الحالية</label>
            <div class="unit-images-preview d-flex flex-wrap gap-2"></div>
        </div>

        <!-- الحقول المخفية لتخزين صور الوحدة -->
        <div class="unit-images-hidden"></div>

        <div class="mt-3">
            <h6>القطع</h6>
            <div class="items-container"></div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addItemToUnit(this, ${unitIndex})">
                <i class="fas fa-plus"></i> إضافة قطعة
            </button>
        </div>
    </div>
`;

    container.appendChild(unitDiv);

    const select = unitDiv.querySelector('.select2-unit');
    $(select).select2();

    // عند تغيير اختيار الوحدة
    $(select).on('change', function() {
        const unitId = $(this).val();
        if (!unitId) {
            // مسح البيانات
            unitDiv.querySelector('.unit-name-ar').value = '';
            unitDiv.querySelector('.unit-name-en').value = '';
            unitDiv.querySelector('.unit-type').value = '';
            unitDiv.querySelector('.unit-desc-ar').value = '';
            unitDiv.querySelector('.unit-desc-en').value = '';
            unitDiv.querySelector('.unit-images-preview').innerHTML = '';
            return;
        }

        // جلب التفاصيل
        fetchUnitDetails(unitId, function(unitData) {
            if (!unitData) return;

            unitDiv.querySelector('.unit-name-ar').value = unitData.name_ar || '';
            unitDiv.querySelector('.unit-name-en').value = unitData.name_en || '';
            unitDiv.querySelector('.unit-type').value = unitData.type || '';
            unitDiv.querySelector('.unit-desc-ar').value = unitData.description_ar || '';
            unitDiv.querySelector('.unit-desc-en').value = unitData.description_en || '';

            // عرض الصور
            const preview = unitDiv.querySelector('.unit-images-preview');
            preview.innerHTML = '';
            if (unitData.images && unitData.images.length > 0) {
                unitData.images.forEach(img => {
                    const imgEl = document.createElement('img');
                    imgEl.src = img.image_path;
                    imgEl.alt = img.alt_text || '';
                    imgEl.style.width = '120px';
                    imgEl.style.height = '120px';
                    imgEl.style.objectFit = 'cover';
                    imgEl.style.border = '1px solid #ddd';
                    preview.appendChild(imgEl);
                });
            } else {
                preview.innerHTML = '<span class="text-muted">لا توجد صور</span>';
            }
        });
    });

    unitIndex++;
}

function removeUnit(btn) {
    if (!confirm('هل أنت متأكد من حذف هذه الوحدة؟')) return;
    btn.closest('.unit-card').remove();
}
function addItemToUnit(btn, uIndex) {
    const unitCard = btn.closest('.unit-card');
    const unitId = unitCard.querySelector('[name="units[' + uIndex + '][unit_id]"]').value;

    if (!unitId) {
        alert('يرجى اختيار وحدة أولاً');
        return;
    }

    fetchItemsByUnitId(unitId, function(items) {
        const itemsContainer = unitCard.querySelector('.items-container');
        const itemIndex = itemsContainer.querySelectorAll('.item-card').length;

        let optionsHtml = '<option value="">-- اختر قطعة --</option>';
        items.forEach(item => {
            optionsHtml += `<option value="${item.id}" data-item='${JSON.stringify(item).replace(/'/g, "\\'")}'>
                ${item.item_name_ar} / ${item.item_name_en}
            </option>`;
        });

        const div = document.createElement('div');
        div.className = 'border p-2 mb-2 item-card';
        div.innerHTML = `
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h6>قطعة ${itemIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-card').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div style="background-color: #fdf8eb;">
                <div class="row mb-2" >
                    <div class="col-md-6">
                        <label>اختر القطعة</label>
                        <select name="units[${uIndex}][items][${itemIndex}][item_id]" class="form-control select2-item" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>الكمية <span class="text-danger">*</span></label>
                        <input type="number" name="units[${uIndex}][items][${itemIndex}][quantity]" class="form-control item-quantity-input" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label>الأبعاد</label>
                        <input type="text" name="units[${uIndex}][items][${itemIndex}][dimensions]" class="form-control item-dimensions-input">
                    </div>

                </div>

                <!-- الحقول المخفية (لإرسال البيانات إلى السيرفر) -->
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][item_name_ar]" class="item-name-ar">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][item_name_en]" class="item-name-en">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][material_ar]" class="item-material-ar">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][material_en]" class="item-material-en">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][color_ar]" class="item-color-ar">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][color_en]" class="item-color-en">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][background_color]" class="item-bg-color">
                <input type="hidden" name="units[${uIndex}][items][${itemIndex}][image_path]" class="item-image-path">

                <!-- عرض بيانات القطعة (للعرض فقط - غير قابلة للتعديل) -->

                <div class="row mb-2">
                    <div class="col-md-6">
                        <label>الخامة (عربي)</label>
                        <p class="form-control-plaintext item-material-ar-display">—</p>
                    </div>
                    <div class="col-md-6">
                        <label>الخامة (إنجليزي)</label>
                        <p class="form-control-plaintext item-material-en-display">—</p>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <label>اللون (عربي)</label>
                        <p class="form-control-plaintext item-color-ar-display">—</p>
                    </div>
                    <div class="col-md-6">
                        <label>اللون (إنجليزي)</label>
                        <p class="form-control-plaintext item-color-en-display">—</p>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <label>لون الخلفية</label>
                        <div class="item-bg-color-preview" style="width:40px; height:40px; border:1px solid #ccc; display:inline-block;"></div>
                    </div>
                    <div class="col-md-6">
                        <label>الصورة</label>
                        <div class="item-image-preview text-muted">لا توجد صورة</div>
                    </div>
                </div>
            </div>
        `;
        itemsContainer.appendChild(div);

        // تفعيل Select2
        $(div).find('.select2-item').select2();

        // ربط حقول الإدخال (الكمية، الأبعاد) بالحقول المخفية
        const qtyInput = div.querySelector('.item-quantity-input');
        const dimInput = div.querySelector('.item-dimensions-input');
        qtyInput.addEventListener('input', () => div.querySelector('.item-quantity').value = qtyInput.value);
        dimInput.addEventListener('input', () => div.querySelector('.item-dimensions').value = dimInput.value);

        // عند اختيار قطعة من القائمة
        $(div).find('.select2-item').on('change', function() {
    const selected = $(this).find(':selected');
    const itemData = selected.data('item');

    if (itemData) {
        // ملء الحقول المخفية (البيانات الثابتة)
        div.querySelector('.item-name-ar').value = itemData.item_name_ar || '';
        div.querySelector('.item-name-en').value = itemData.item_name_en || '';
        div.querySelector('.item-material-ar').value = itemData.material_ar || '';
        div.querySelector('.item-material-en').value = itemData.material_en || '';
        div.querySelector('.item-color-ar').value = itemData.color_ar || '';
        div.querySelector('.item-color-en').value = itemData.color_en || '';
        div.querySelector('.item-bg-color').value = itemData.background_color || '';
        div.querySelector('.item-image-path').value = itemData.image_path || '';

        // تحديث حقول العرض
        //div.querySelector('.item-name-ar-display').textContent = itemData.item_name_ar || '—';
        //div.querySelector('.item-name-en-display').textContent = itemData.item_name_en || '—';
        div.querySelector('.item-material-ar-display').textContent = itemData.material_ar || '—';
        div.querySelector('.item-material-en-display').textContent = itemData.material_en || '—';
        div.querySelector('.item-color-ar-display').textContent = itemData.color_ar || '—';
        div.querySelector('.item-color-en-display').textContent = itemData.color_en || '—';

        // عرض لون الخلفية
        const bgColorPreview = div.querySelector('.item-bg-color-preview');
        bgColorPreview.style.backgroundColor = itemData.background_color || '#ffffff';
        bgColorPreview.title = itemData.background_color || '';

        // عرض الصورة
        window.API_BASE_URL = "{{ url('/') }}";

        const imgPreview = div.querySelector('.item-image-preview');
        if (itemData.image_path) {
            imgPreview.innerHTML = `<img src="${API_BASE_URL}/storage/${itemData.image_path}" alt="صورة القطعة" style="max-width:100px; max-height:100px; object-fit:cover; border:1px solid #ddd;">`;
        } else {
            imgPreview.innerHTML = '<span class="text-muted">لا توجد صورة</span>';
        }

        // ✅ تحديث حقول الإدخال (الكمية، الأبعاد)
        qtyInput.value = itemData.quantity || 1;
        dimInput.value = itemData.dimensions || '';

        // ✅ تحديث الحقول المخفية يدويًا (لأن الأحداث لم تُفعّل بعد)
        div.querySelector('.item-quantity').value = qtyInput.value;
        div.querySelector('.item-dimensions').value = dimInput.value;
    } else {
        // مسح القيم
        qtyInput.value = 1;
        dimInput.value = '';
        div.querySelector('.item-quantity').value = '1';
        div.querySelector('.item-dimensions').value = '';
        // ... مسح باقي الحقول
    }
});
    });
}
</script>
@endsection
