@extends('admin.layouts.app')

@section('title', 'إنشاء باكج جديد')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إنشاء باكج جديد</h1>
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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">اسم الباكج (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">اسم الباكج (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">السعر (ريال) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الوصف (عربي)</label>
                        <textarea name="description_ar" class="form-control" rows="2"></textarea>
                        <label class="form-label mt-2">الوصف (إنجليزي)</label>
                        <textarea name="description_en" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">صور الباكج (اختر أكثر من صورة)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>

                <div class="mb-3">
                    <h6>الوحدات الفرعية</h6>
                    <div id="units-container"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addUnit()">
                        <i class="fas fa-plus me-1"></i> إضافة وحدة جديدة
                    </button>
                </div>

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

<script>
let unitIndex = 0;

function addUnit() {
    const container = document.getElementById('units-container');
    const unitDiv = document.createElement('div');
    unitDiv.className = 'card mb-3';
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
    if (confirm('هل أنت متأكد من حذف هذه الوحدة؟')) {
        btn.closest('.card').remove();
    }
}
</script>
@endsection
