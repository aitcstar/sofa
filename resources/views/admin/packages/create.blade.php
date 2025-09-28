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
               <!-- <div class="col-md-6">
                    <label class="form-label">صور إضافية</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                </div>-->
            </div>

              <!-- الوحدات -->
              <div class="mb-3">
                  <h6>الوحدات الفرعية</h6>
                  <div id="units-container"></div>
                  <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addUnit()">
                      <i class="fas fa-plus me-1"></i> إضافة وحدة جديدة
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
<script>
let unitIndex = 0;

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
            <div class="mb-2">
                <label class="form-label">صور الوحدة</label>
                <input type="file" name="units[${unitIndex}][images][]" class="form-control" multiple accept="image/*">
            </div>

            <div class="mt-3">
                <h6>العناصر</h6>
                <div class="items-container" style="background-color: #fdf8eb;"></div>
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
    const div = document.createElement('div');
    div.className = 'border p-2 mb-2 item-card';
    div.innerHTML = `
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h6>قطعة ${itemIndex + 1}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-card').remove()">
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
                <label>اللون (عربي)</label>
                <input type="text" name="units[${uIndex}][items][${itemIndex}][color_ar]" class="form-control">
                <label>اللون (إنجليزي)</label>
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
</script>
@endsection
