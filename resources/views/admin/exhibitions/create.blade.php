@extends('admin.layouts.app')

@section('title', 'إضافة معرض')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة معرض</h1>
        <a href="{{ route('admin.exhibitions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">
            <form action="{{ route('admin.exhibitions.store') }}" method="POST" enctype="multipart/form-data">
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
                    تفاصيل المعرض
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>التصنيف <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">اختر تصنيف</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الباكج</label>
                        <select name="package_id" class="form-select">
                            <option value="">بدون</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>العنوان (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>ملخص (عربي)</label>
                        <textarea name="summary_ar" class="form-control"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>ملخص (إنجليزي)</label>
                        <textarea name="summary_en" class="form-control"></textarea>
                    </div>

                    <!--<div class="col-md-6 mb-3">
                        <label>الوصف (عربي)</label>
                        <textarea name="description_ar" class="form-control"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الوصف (إنجليزي)</label>
                        <textarea name="description_en" class="form-control"></textarea>
                    </div>-->

                    <div class="col-md-6 mb-3">
                        <label>تاريخ التسليم</label>
                        <input type="date" name="delivery_date" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>الصور</label>
                        <input type="file" name="images[]" multiple class="form-control" id="imagesUpload">
                        <div id="imagesPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <!-- خطوات المعرض -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">خطوات المعرض</h5>
                        <button type="button" id="add-step" class="btn btn-sm btn-light">إضافة خطوة</button>
                    </div>
                    <div class="card-body" id="steps-wrapper">
                        <div class="row step-item mb-2">
                            <div class="col-md-4">
                                <input type="text" name="steps[0][title_ar]" class="form-control" placeholder="عنوان الخطوة (عربي)">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="steps[0][title_en]" class="form-control" placeholder="عنوان الخطوة (إنجليزي)">
                            </div>
                            <div class="col-md-3">
                                <input type="file" name="steps[0][icon]" class="form-control" accept="image/*">
                                <img id="stepIconPreview0" class="mt-2" style="max-width: 50px; display: none;">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeStep(this)">حذف</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ</button>
                    <a href="{{ route('admin.exhibitions.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // إضافة وحذف خطوات المعرض
    let stepIndex = 1;
    document.getElementById('add-step').addEventListener('click', function() {
        const wrapper = document.getElementById('steps-wrapper');
        const div = document.createElement('div');
        div.classList.add('row', 'step-item', 'mb-2');
        div.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="steps[${stepIndex}][title_ar]" class="form-control" placeholder="عنوان الخطوة (عربي)">
            </div>
            <div class="col-md-4">
                <input type="text" name="steps[${stepIndex}][title_en]" class="form-control" placeholder="عنوان الخطوة (إنجليزي)">
            </div>
            <div class="col-md-3">
                <input type="file" name="steps[${stepIndex}][icon]" class="form-control" accept="image/*" onchange="previewStepIcon(this, ${stepIndex})">
                <img id="stepIconPreview${stepIndex}" class="mt-2" style="max-width: 50px; display: none;">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeStep(this)">حذف</button>
            </div>`;
        wrapper.appendChild(div);
        stepIndex++;
    });
    function removeStep(btn) {
        btn.closest('.step-item').remove();
    }

    // معاينة الصور
    document.getElementById('imagesUpload').addEventListener('change', function() {
        const previewWrapper = document.getElementById('imagesPreview');
        previewWrapper.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100px';
                img.style.borderRadius = '5px';
                previewWrapper.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    function previewStepIcon(input, index) {
    const preview = document.getElementById('stepIconPreview' + index);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

</script>
@endpush
@endsection
