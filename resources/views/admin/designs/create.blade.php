@extends('admin.layouts.app')

@section('title', 'إنشاء تصميم جديد / Create New Design')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">إنشاء تصميم جديد / Create New Design</h1>
        <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة / Back
        </a>
    </div>

    <form action="{{ route('admin.designs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-4">
            <!-- اسم التصميم عربي -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name_ar" class="form-label">اسم التصميم (عربي)</label>
                    <input type="text" name="name_ar" id="name_ar" class="form-control" required>
                </div>
            </div>

            <!-- اسم التصميم انجليزي -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name_en" class="form-label">اسم التصميم (English)</label>
                    <input type="text" name="name_en" id="name_en" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- الفئة -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="category" class="form-label">الفئة / Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">اختر الفئة / Select Category</option>
                        <option value="bedroom">غرفة نوم / Bedroom</option>
                        <option value="living_room">معيشة / Living Room</option>
                        <option value="kitchen">مطبخ / Kitchen</option>
                        <option value="bathroom">حمام / Bathroom</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- الوصف عربي -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="description_ar" class="form-label">الوصف (عربي)</label>
                    <textarea name="description_ar" id="description_ar" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <!-- الوصف انجليزي -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="description_en" class="form-label">Description (English)</label>
                    <textarea name="description_en" id="description_en" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="image" class="form-label">صورة التصميم / Design Image</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> حفظ التصميم / Save Design
            </button>
            <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء / Cancel
            </a>
        </div>
    </form>
</div>
@endsection
