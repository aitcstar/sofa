@extends('admin.layouts.app')

@section('title', 'إضافة سؤال جديد')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة سؤال جديد</h1>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات السؤال</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">القسم <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">اختر القسم</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $faq->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar }} / {{ $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="question_ar" class="form-label">السؤال (عربي) <span class="text-danger">*</span></label>
                        <input type="text" id="question_ar" name="question_ar" class="form-control" value="{{ old('question_ar') }}" placeholder="أدخل السؤال باللغة العربية" required>
                        @error('question_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="question_en" class="form-label">السؤال (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" id="question_en" name="question_en" class="form-control" value="{{ old('question_en') }}" placeholder="Enter the question in English" required>
                        @error('question_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="answer_ar" class="form-label">الإجابة (عربي) <span class="text-danger">*</span></label>
                        <textarea id="answer_ar" name="answer_ar" class="form-control" rows="4" placeholder="أدخل الإجابة باللغة العربية" required>{{ old('answer_ar') }}</textarea>
                        @error('answer_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="answer_en" class="form-label">الإجابة (إنجليزي) <span class="text-danger">*</span></label>
                        <textarea id="answer_en" name="answer_en" class="form-control" rows="4" placeholder="Enter the answer in English" required>{{ old('answer_en') }}</textarea>
                        @error('answer_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="page" class="form-label">الصفحة <span class="text-danger">*</span></label>
                        <select id="page" name="page" class="form-select" required>
                            <option value="">اختر الصفحة</option>
                            <option value="home" {{ old('page') == 'home' ? 'selected' : '' }}>الصفحة الرئيسية</option>
                            <option value="category" {{ old('page') == 'category' ? 'selected' : '' }}> الباكجات</option>
                        </select>
                        @error('page')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3" id="package_div" style="display: none;">
                    <label for="package_id" class="form-label">الباكج <span class="text-danger">*</span></label>
                    <select id="package_id" name="package_id" class="form-select">
                        <option value="">اختر الباكج</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}"
                                {{ old('package_id', $faq->package_id ?? '') == $package->id ? 'selected' : '' }}>
                                {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('package_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const pageSelect = document.getElementById('page');
                        const packageDiv = document.getElementById('package_div');

                        function togglePackageDropdown() {
                            if (pageSelect.value === 'category') {
                                packageDiv.style.display = 'block';
                                document.getElementById('package_id').setAttribute('required', 'required');
                            } else {
                                packageDiv.style.display = 'none';
                                document.getElementById('package_id').removeAttribute('required');
                            }
                        }

                        // عند تغير الاختيار
                        pageSelect.addEventListener('change', togglePackageDropdown);

                        // للتحقق عند تحميل الصفحة (مثلاً عند إعادة التوجيه بعد Validation Error)
                        togglePackageDropdown();
                    });
                </script>



                <div class="form-group">
                    <label for="order">الترتيب</label>
                    <input type="number" name="sort" id="sort" class="form-control" value="{{ old('sort', $faq->sort ?? 0) }}">
                </div>


                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ السؤال
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
