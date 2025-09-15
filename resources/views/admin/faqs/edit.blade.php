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
                        <label class="form-label">القسم (عربي) <span class="text-danger">*</span></label>
                        <select name="category_ar" class="form-select" required>
                            <option value="التوصيل والتركيب" {{ old('category_ar', $faq->category_ar) == 'التوصيل والتركيب' ? 'selected' : '' }}>التوصيل والتركيب</option>
                            <option value="تفاصيل الباكجات" {{ old('category_ar', $faq->category_ar) == 'تفاصيل الباكجات' ? 'selected' : '' }}>تفاصيل الباكجات</option>
                            <option value="الدفع والفوترة" {{ old('category_ar', $faq->category_ar) == 'الدفع والفوترة' ? 'selected' : '' }}>الدفع والفوترة</option>
                            <option value="الضمان وخدمة ما بعد البيع" {{ old('category_ar', $faq->category_ar) == 'الضمان وخدمة ما بعد البيع' ? 'selected' : '' }}>الضمان وخدمة ما بعد البيع</option>
                            <option value="التخصيص والمشاريع الخاصة" {{ old('category_ar', $faq->category_ar) == 'التخصيص والمشاريع الخاصة' ? 'selected' : '' }}>التخصيص والمشاريع الخاصة</option>
                        </select>
                        @error('category_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">القسم (إنجليزي) <span class="text-danger">*</span></label>
                        <select name="category_en" class="form-select" required>
                            <option value="Delivery & Installation" {{ old('category_en', $faq->category_en) == 'Delivery & Installation' ? 'selected' : '' }}>Delivery & Installation</option>
                            <option value="Package Details" {{ old('category_en', $faq->category_en) == 'Package Details' ? 'selected' : '' }}>Package Details</option>
                            <option value="Payment & Billing" {{ old('category_en', $faq->category_en) == 'Payment & Billing' ? 'selected' : '' }}>Payment & Billing</option>
                            <option value="Warranty & After-Sales" {{ old('category_en', $faq->category_en) == 'Warranty & After-Sales' ? 'selected' : '' }}>Warranty & After-Sales</option>
                            <option value="Customization & Special Projects" {{ old('category_en', $faq->category_en) == 'Customization & Special Projects' ? 'selected' : '' }}>Customization & Special Projects</option>
                        </select>
                        @error('category_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="page" class="form-label">الصفحة <span class="text-danger">*</span></label>
                    <select id="page" name="page" class="form-select" required>
                        <option value="home" {{ old('page', $faq->page) == 'home' ? 'selected' : '' }}>الصفحة الرئيسية</option>
                        <option value="category" {{ old('page', $faq->page) == 'category' ? 'selected' : '' }}>التصنيفات</option>
                    </select>
                    @error('page')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
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
