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
                      <label class="form-label">السعر (ريال) <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $package->price) }}" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">الوصف (عربي)</label>
                      <textarea name="description_ar" class="form-control" rows="2">{{ old('description_ar', $package->description_ar) }}</textarea>
                      <label class="form-label mt-2">الوصف (إنجليزي)</label>
                      <textarea name="description_en" class="form-control" rows="2">{{ old('description_en', $package->description_en) }}</textarea>
                  </div>
              </div>

              <!-- صورة الباكج -->
<div class="mb-3">
    <label class="form-label">صورة الباكج</label>
    <input type="file" name="image" class="form-control" accept="image/*">
</div>

@if($package->image)
    <div class="mb-3">
        <h6>الصورة الحالية</h6>
        <img src="{{ asset('storage/' . $package->image) }}" class="img-fluid rounded" alt="صورة الباكج">
    </div>
@endif


              <!-- الوحدات والعناصر -->
              <div class="mb-3">
                  <h6>الوحدات الفرعية</h6>
                  <div id="units-container">
                      @foreach($package->units as $uIndex => $unit)
                          <div class="card mb-3 unit-card">
                              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                  <h6>وحدة {{ $uIndex + 1 }}</h6>
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
                                      <label class="form-label">نوع الوحدة</label>
                                      <select name="units[{{ $uIndex }}][type]" class="form-control" required>
                                          <option value="bedroom" {{ $unit->type == 'bedroom' ? 'selected' : '' }}>غرفة نوم</option>
                                          <option value="living_room" {{ $unit->type == 'living_room' ? 'selected' : '' }}>معيشة</option>
                                          <option value="kitchen" {{ $unit->type == 'kitchen' ? 'selected' : '' }}>مطبخ</option>
                                          <option value="bathroom" {{ $unit->type == 'bathroom' ? 'selected' : '' }}>حمام</option>
                                      </select>
                                  </div>

                                  <!-- عناصر الوحدة -->
                                  <div class="mt-3">
                                      <h6>العناصر</h6>
                                      <div class="items-container">
                                          @foreach($unit->items as $iIndex => $item)
                                              <div class="border p-2 mb-2 item-card">
                                                  <input type="hidden" name="units[{{ $uIndex }}][items][{{ $iIndex }}][id]" value="{{ $item->id }}">
                                                  <div class="row">
                                                      <div class="col-md-6">
                                                          <label>اسم العنصر (عربي)</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][item_name_ar]" class="form-control" value="{{ $item->item_name_ar }}">
                                                      </div>
                                                      <div class="col-md-6">
                                                          <label>اسم العنصر (إنجليزي)</label>
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
                                                          <label>الخامة</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][material]" class="form-control" value="{{ $item->material }}">
                                                      </div>
                                                      <div class="col-md-3">
                                                          <label>اللون</label>
                                                          <input type="text" name="units[{{ $uIndex }}][items][{{ $iIndex }}][color]" class="form-control" value="{{ $item->color }}">
                                                      </div>
                                                  </div>
                                              </div>
                                          @endforeach
                                      </div>
                                      <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addItem(this, {{ $uIndex }})">
                                          <i class="fas fa-plus"></i> إضافة عنصر
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

<script>
let unitIndex = {{ $package->units->count() }};

function addUnit() {
    const container = document.getElementById('units-container');
    const unitDiv = document.createElement('div');
    unitDiv.className = 'card mb-3 unit-card';
    unitDiv.innerHTML = `
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6>وحدة ${unitIndex + 1}</h6>
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
                <label class="form-label">نوع الوحدة</label>
                <select name="units[${unitIndex}][type]" class="form-control" required>
                    <option value="bedroom">غرفة نوم</option>
                    <option value="living_room">معيشة</option>
                    <option value="kitchen">مطبخ</option>
                    <option value="bathroom">حمام</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">الوصف (عربي)</label>
                <textarea name="units[${unitIndex}][description_ar]" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">الوصف (إنجليزي)</label>
                <textarea name="units[${unitIndex}][description_en]" class="form-control" rows="2"></textarea>
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
        <div class="row">
            <div class="col-md-6">
                <label>اسم العنصر (عربي)</label>
                <input type="text" name="units[${uIndex}][items][${itemIndex}][item_name_ar]" class="form-control">
            </div>
            <div class="col-md-6">
                <label>اسم العنصر (إنجليزي)</label>
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
                <label>الخامة</label>
                <input type="text" name="units[${uIndex}][items][${itemIndex}][material]" class="form-control">
            </div>
            <div class="col-md-3">
                <label>اللون</label>
                <input type="text" name="units[${uIndex}][items][${itemIndex}][color]" class="form-control">
            </div>
        </div>
    `;
    itemsContainer.appendChild(div);
}
</script>
@endsection
