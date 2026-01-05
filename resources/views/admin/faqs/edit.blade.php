@extends('admin.layouts.app')

@section('title', 'تعديل السؤال')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل السؤال</h1>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تعديل معلومات السؤال</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">السؤال (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="question_ar" class="form-control"
                               value="{{ old('question_ar', $faq->question_ar) }}" required>
                        @error('question_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">السؤال (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" name="question_en" class="form-control"
                               value="{{ old('question_en', $faq->question_en) }}" required>
                        @error('question_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الإجابة (عربي) <span class="text-danger">*</span></label>
                        <textarea name="answer_ar" class="form-control" rows="4" required>{{ old('answer_ar', $faq->answer_ar) }}</textarea>
                        @error('answer_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الإجابة (إنجليزي) <span class="text-danger">*</span></label>
                        <textarea name="answer_en" class="form-control" rows="4" required>{{ old('answer_en', $faq->answer_en) }}</textarea>
                        @error('answer_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- الأقسام --}}
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
                <div class="col-md-6 mb-3">
                    <label for="page" class="form-label">الصفحة <span class="text-danger">*</span></label>
                    <select id="page" name="page" class="form-select" required>
                        <option value="home" {{ old('page', $faq->page) == 'home' ? 'selected' : '' }}>الصفحة الرئيسية</option>
                        <option value="category" {{ old('page', $faq->page) == 'category' ? 'selected' : '' }}> الباكجات</option>
                    </select>
                    @error('page')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                {{-- Dropdown الباكجات --}}
                <div class="row" id="package-row" style="display: {{ old('page', $faq->page) == 'category' ? 'block' : 'none' }};">
                    <div class="col-md-6 mb-3">
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
                </div>

                <div class="form-group">
                    <label for="order">الترتيب</label>
                    <input type="number" name="sort" id="sort" class="form-control" value="{{ old('sort', $faq->sort ?? 0) }}">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث السؤال
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pageSelect = document.getElementById('page');
        const packageRow = document.getElementById('package-row');

        pageSelect.addEventListener('change', function () {
            if (this.value === 'category') {
                packageRow.style.display = 'block';
            } else {
                packageRow.style.display = 'none';
                document.getElementById('package_id').value = '';
            }
        });
    });
</script>
@endsection
