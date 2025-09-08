@extends('admin.layouts.app')

@section('title', 'تعديل المدونة')

@section('content')
<div class="container">


    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">تعديل المدونة</h1>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">تعديل محتوى المدونة</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">العنوان بالعربية</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ $blog->title_ar }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">العنوان بالإنجليزية</label>
                            <input type="text" name="title_en" class="form-control" value="{{ $blog->title_en }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">الفئة بالعربية</label>
                            <select name="category_ar" class="form-select" required>
                                <option value="" disabled {{ old('category_ar', $blog->category_ar ?? '') == '' ? 'selected' : '' }}>اختر الفئة</option>
                                <option value="نصائح التأثيث" {{ old('category_ar', $blog->category_ar ?? '') == 'نصائح التأثيث' ? 'selected' : '' }}>نصائح التأثيث</option>
                                <option value="العروض والخدمات" {{ old('category_ar', $blog->category_ar ?? '') == 'العروض والخدمات' ? 'selected' : '' }}>العروض والخدمات</option>
                                <option value="تنسيقات الألوان والديكور" {{ old('category_ar', $blog->category_ar ?? '') == 'تنسيقات الألوان والديكور' ? 'selected' : '' }}>تنسيقات الألوان والديكور</option>
                                <option value="مقارنات وتجارب المنتجات" {{ old('category_ar', $blog->category_ar ?? '') == 'مقارنات وتجارب المنتجات' ? 'selected' : '' }}>مقارنات وتجارب المنتجات</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">الفئة بالإنجليزية</label>
                            <select name="category_en" class="form-select" required>
                                <option value="" disabled {{ old('category_en', $blog->category_en ?? '') == '' ? 'selected' : '' }}>Select category</option>
                                <option value="Furniture Tips" {{ old('category_en', $blog->category_en ?? '') == 'Furniture Tips' ? 'selected' : '' }}>Furniture Tips</option>
                                <option value="Offers & Services" {{ old('category_en', $blog->category_en ?? '') == 'Offers & Services' ? 'selected' : '' }}>Offers & Services</option>
                                <option value="Color Schemes & Décor" {{ old('category_en', $blog->category_en ?? '') == 'Color Schemes & Décor' ? 'selected' : '' }}>Color Schemes & Décor</option>
                                <option value="Product Comparisons & Reviews" {{ old('category_en', $blog->category_en ?? '') == 'Product Comparisons & Reviews' ? 'selected' : '' }}>Product Comparisons & Reviews</option>
                            </select>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المؤلف بالعربية</label>
                            <input type="text" name="author_ar" class="form-control" value="{{ $blog->author_ar }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المؤلف بالإنجليزية</label>
                            <input type="text" name="author_en" class="form-control" value="{{ $blog->author_en }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ملخص بالعربية</label>
                            <textarea name="excerpt_ar" class="form-control" rows="3">{{ $blog->excerpt_ar }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ملخص بالإنجليزية</label>
                            <textarea name="excerpt_en" class="form-control" rows="3">{{ $blog->excerpt_en }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المحتوى بالعربية</label>
                        <textarea name="content_ar" class="form-control" rows="5">{{ $blog->content_ar }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المحتوى بالإنجليزية</label>
                        <textarea name="content_en" class="form-control" rows="5">{{ $blog->content_en }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الصورة الحالية</label>
                        <div>
                            @if($blog->image)
                                <img src="{{ asset('storage/'.$blog->image) }}" class="current-image me-3 border"  width="200">
                                <a href="{{  asset('storage/'.$blog->image)  }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> عرض الصورة
                                </a>
                            @else
                                <span class="text-muted">لا توجد صورة</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تغيير الصورة</label>
                        <input type="file" name="image" id="imageUpload" class="form-control" accept="image/*">
                        <div class="form-text">اتركه فارغاً إذا كنت لا تريد تغيير الصورة</div>
                        <img id="imagePreview" class="image-preview">
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> تحديث المدونة
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // تهيئة محرر النصوص TinyMCE
        ClassicEditor
    .create( document.querySelector( 'textarea[name="content_ar"]' ), {
        language: 'ar',
        toolbar: [ 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'undo', 'redo' ]
    })
    .catch( error => { console.error( error ); } );

ClassicEditor
    .create( document.querySelector( 'textarea[name="content_en"]' ), {
        toolbar: [ 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'undo', 'redo' ]
    })
    .catch( error => { console.error( error ); } );
    </script>
@endpush
