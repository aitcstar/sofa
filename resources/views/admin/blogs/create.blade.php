@extends('admin.layouts.app')

@section('title', 'إضافة مدونة جديدة')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة مدونة جديدة</h1>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تفاصيل المدونة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">العنوان بالعربية</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">العنوان بالإنجليزية</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">الفئة بالعربية</label>
                        <select name="category_ar" class="form-select" required>
                            <option value="" disabled {{ old('category_ar') ? '' : 'selected' }}>اختر الفئة</option>
                            <option value="نصائح التأثيث" {{ old('category_ar') == 'نصائح التأثيث' ? 'selected' : '' }}>نصائح التأثيث</option>
                            <option value="العروض والخدمات" {{ old('category_ar') == 'العروض والخدمات' ? 'selected' : '' }}>العروض والخدمات</option>
                            <option value="تنسيقات الألوان والديكور" {{ old('category_ar') == 'تنسيقات الألوان والديكور' ? 'selected' : '' }}>تنسيقات الألوان والديكور</option>
                            <option value="مقارنات وتجارب المنتجات" {{ old('category_ar') == 'مقارنات وتجارب المنتجات' ? 'selected' : '' }}>مقارنات وتجارب المنتجات</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">الفئة بالإنجليزية</label>
                        <select name="category_en" class="form-select" required>
                            <option value="" disabled {{ old('category_en') ? '' : 'selected' }}>Select category</option>
                            <option value="Furniture Tips" {{ old('category_en') == 'Furniture Tips' ? 'selected' : '' }}>Furniture Tips</option>
                            <option value="Offers & Services" {{ old('category_en') == 'Offers & Services' ? 'selected' : '' }}>Offers & Services</option>
                            <option value="Color Schemes & Décor" {{ old('category_en') == 'Color Schemes & Décor' ? 'selected' : '' }}>Color Schemes & Décor</option>
                            <option value="Product Comparisons & Reviews" {{ old('category_en') == 'Product Comparisons & Reviews' ? 'selected' : '' }}>Product Comparisons & Reviews</option>
                        </select>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المؤلف بالعربية</label>
                        <input type="text" name="author_ar" class="form-control" value="{{ old('author_ar') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المؤلف بالإنجليزية</label>
                        <input type="text" name="author_en" class="form-control" value="{{ old('author_en') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص بالعربية</label>
                        <textarea name="excerpt_ar" class="form-control" rows="3">{{ old('excerpt_ar') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص بالإنجليزية</label>
                        <textarea name="excerpt_en" class="form-control" rows="3">{{ old('excerpt_en') }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">المحتوى بالعربية</label>
                    <textarea name="content_ar" class="form-control" rows="5">{{ old('content_ar') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">المحتوى بالإنجليزية</label>
                    <textarea name="content_en" class="form-control" rows="5">{{ old('content_en') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" id="imageUpload" class="form-control" accept="image/*">
                    <img id="imagePreview" class="image-preview mt-2" style="max-width: 200px; display: none;">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ المدونة
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // معاينة الصورة قبل الرفع
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // CKEditor للمحتوى العربي
    ClassicEditor
        .create(document.querySelector('textarea[name="content_ar"]'), {
            language: 'ar',
            toolbar: [ 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'undo', 'redo' ]
        })
        .catch(error => { console.error(error); });

    // CKEditor للمحتوى الإنجليزي
    ClassicEditor
        .create(document.querySelector('textarea[name="content_en"]'), {
            toolbar: [ 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'undo', 'redo' ]
        })
        .catch(error => { console.error(error); });
</script>
@endpush
