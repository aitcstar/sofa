@extends('admin.layouts.app')

@section('title', 'إضافة سؤال جديد')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة سؤال جديد للاستبيان</h1>
        <a href="{{ route('admin.survey-questions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            تفاصيل السؤال
        </div>
        <div class="card-body">
            <form action="{{ route('admin.survey-questions.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- نص السؤال (عربي) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نص السؤال (عربي) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title_ar" value="{{ old('title_ar') }}" required>
                        @error('title_ar')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <!-- نص السؤال (إنجليزي) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نص السؤال (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title_en" value="{{ old('title_en') }}" required>
                        @error('title_en')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <!-- نوع الحقل -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نوع الحقل</label>
                        <select class="form-select" name="type" id="fieldType" required>
                            <option value="">اختر النوع</option>
                            <option value="radio" {{ old('type') == 'radio' ? 'selected' : '' }}>اختيار واحد (Radio)</option>
                            <option value="checkbox" {{ old('type') == 'checkbox' ? 'selected' : '' }}>اختيارات متعددة (Checkbox)</option>
                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>قائمة منسدلة (Select)</option>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>نص قصير (Text)</option>
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>رقم (Number)</option>
                        </select>
                    </div>

                    <!-- هل مطلوب -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_required" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required">هل السؤال إجباري؟</label>
                        </div>
                    </div>
                </div>

                <!-- الترتيب -->
                <div class="mb-3">
                    <label class="form-label">الترتيب (أقل = يظهر أولًا)</label>
                    <input type="number" class="form-control" name="order" value="{{ old('order', 0) }}">
                </div>

                <!-- خيارات السؤال -->
                <div class="card mt-4" id="optionsContainer" style="display: none;">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الخيارات</h5>
                        <button type="button" class="btn btn-sm btn-light" id="addOption">+ إضافة خيار</button>
                    </div>
                    <div class="card-body" id="optionsList">
                        <!-- تضاف هنا ديناميكيًا -->
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> حفظ السؤال</button>
                    <a href="{{ route('admin.survey-questions.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fieldType = document.getElementById('fieldType');
    const optionsContainer = document.getElementById('optionsContainer');
    const optionsList = document.getElementById('optionsList');
    const addOptionBtn = document.getElementById('addOption');

    function toggleOptions() {
        const show = ['radio', 'checkbox', 'select'].includes(fieldType.value);
        optionsContainer.style.display = show ? 'block' : 'none';
    }

    function addOption(label_ar = '', value_ar = '', label_en = '', value_en = '') {
        const index = optionsList.children.length;
        const div = document.createElement('div');
        div.className = 'row mb-2 align-items-end option-item';
        div.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="options[${index}][label_ar]" class="form-control" placeholder="نص الخيار (AR)" value="${label_ar}" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="options[${index}][value_ar]" class="form-control" placeholder="القيمة (AR)" value="${value_ar}" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="options[${index}][label_en]" class="form-control" placeholder="Option Label (EN)" value="${label_en}" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="options[${index}][value_en]" class="form-control" placeholder="Value (EN)" value="${value_en}" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-option">حذف</button>
            </div>
        `;
        optionsList.appendChild(div);

        div.querySelector('.remove-option').addEventListener('click', () => {
            div.remove();
        });
    }

    fieldType.addEventListener('change', toggleOptions);
    addOptionBtn.addEventListener('click', () => addOption());

    toggleOptions();
});
</script>
@endpush
@endsection
