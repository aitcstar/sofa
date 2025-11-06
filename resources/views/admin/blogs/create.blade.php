@extends('admin.layouts.app')

@section('title', 'إضافة مدونة جديدة')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة مدونة جديدة</h1>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body">
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                         إعدادات SEO
                    </div>
                    <div class="card-body">
                        {{-- العنوان --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title (AR)</label>
                                <input type="text" name="meta_title_ar" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title (EN)</label>
                                <input type="text" name="meta_title_en" class="form-control">
                            </div>
                        </div>

                        {{-- الوصف --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Description (AR)</label>
                                <textarea name="meta_description_ar" class="form-control"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Description (EN)</label>
                                <textarea name="meta_description_en" class="form-control"></textarea>
                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (AR)</label>
                                <input type="text" name="slug_ar" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (EN)</label>
                                <input type="text" name="slug_en" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تفاصيل المدونة</h5>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar') }}" required>
                        @error('title_ar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}" required>
                        @error('title_en') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (AR) <span class="text-danger">*</span></label>
                        <input type="text" name="slug_ar" class="form-control" value="{{ old('slug_ar') }}" required>
                        @error('slug_ar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="slug_en" class="form-control" value="{{ old('slug_en') }}" required>
                        @error('slug_en') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الفئة <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المؤلف (عربي)</label>
                        <input type="text" name="author_ar" class="form-control" value="{{ old('author_ar') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المؤلف (إنجليزي)</label>
                        <input type="text" name="author_en" class="form-control" value="{{ old('author_en') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص (عربي)</label>
                        <textarea name="excerpt_ar" class="form-control" rows="3">{{ old('excerpt_ar') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص (إنجليزي)</label>
                        <textarea name="excerpt_en" class="form-control" rows="3">{{ old('excerpt_en') }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">المحتوى (عربي)</label>
                    <textarea name="content_ar" class="form-control" rows="5">{{ old('content_ar') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">المحتوى (إنجليزي)</label>
                    <textarea name="content_en" class="form-control" rows="5">{{ old('content_en') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" id="imageUpload" class="form-control" accept="image/*">
                    <img id="imagePreview" class="image-preview mt-2" style="max-width: 200px; display: none;">
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الأسئلة الشائعة</h5>
                        <button type="button" id="add-faq" class="btn btn-sm btn-light">إضافة سؤال</button>
                    </div>
                    <div class="card-body" id="faq-wrapper">
                        <div class="faq-item border p-3 mb-3 rounded">
                            <div class="mb-2">
                                <label>السؤال (AR)</label>
                                <input type="text" name="faqs[0][question_ar]" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>السؤال (EN)</label>
                                <input type="text" name="faqs[0][question_en]" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>الإجابة (AR)</label>
                                <textarea name="faqs[0][answer_ar]" class="form-control"></textarea>
                            </div>
                            <div class="mb-2">
                                <label>الإجابة (EN)</label>
                                <textarea name="faqs[0][answer_en]" class="form-control"></textarea>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeFaqRow(this)">حذف</button>

                        </div>
                    </div>
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
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // CKEditor
    ClassicEditor.create(document.querySelector('textarea[name="content_ar"]'), { language: 'ar' })
        .catch(error => console.error(error));
    ClassicEditor.create(document.querySelector('textarea[name="content_en"]'))
        .catch(error => console.error(error));

    // FAQs
    let faqIndex = 1;
    document.getElementById('add-faq').addEventListener('click', function () {
        const wrapper = document.getElementById('faq-wrapper');
        const html = `
        <div class="faq-item border p-3 mb-3 rounded">
            <div class="mb-2">
                <label>السؤال (AR)</label>
                <input type="text" name="faqs[${faqIndex}][question_ar]" class="form-control">
            </div>
            <div class="mb-2">
                <label>السؤال (EN)</label>
                <input type="text" name="faqs[${faqIndex}][question_en]" class="form-control">
            </div>
            <div class="mb-2">
                <label>الإجابة (AR)</label>
                <textarea name="faqs[${faqIndex}][answer_ar]" class="form-control"></textarea>
            </div>
            <div class="mb-2">
                <label>الإجابة (EN)</label>
                <textarea name="faqs[${faqIndex}][answer_en]" class="form-control"></textarea>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeFaqRow(this)">حذف</button>
        </div>`;
        wrapper.insertAdjacentHTML('beforeend', html);
        faqIndex++;
    });
    function removeFaqRow(button) {
        button.closest('.faq-item').remove();
    }
</script>
@endpush
