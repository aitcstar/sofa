@extends('admin.layouts.app')

@section('title', 'تعديل السؤال')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل السؤال</h1>
        <a href="{{ route('admin.survey-questions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تفاصيل السؤال</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.survey-questions.update', $survey_question->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- العنوان عربي -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control"
                               value="{{ old('title_ar', $survey_question->title_ar) }}" required>
                    </div>

                    <!-- العنوان إنجليزي -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control"
                               value="{{ old('title_en', $survey_question->title_en) }}" required>
                    </div>
                </div>

                <div class="row">
                    <!-- النوع -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نوع الحقل</label>
                        <select name="type" class="form-select" required>
                            <option value="radio" {{ $survey_question->type == 'radio' ? 'selected' : '' }}>اختيار واحد (Radio)</option>
                            <option value="checkbox" {{ $survey_question->type == 'checkbox' ? 'selected' : '' }}>اختيارات متعددة (Checkbox)</option>
                            <option value="select" {{ $survey_question->type == 'select' ? 'selected' : '' }}>قائمة منسدلة (Select)</option>
                            <option value="text" {{ $survey_question->type == 'text' ? 'selected' : '' }}>نص قصير (Text)</option>
                            <option value="number" {{ $survey_question->type == 'number' ? 'selected' : '' }}>رقم (Number)</option>
                        </select>
                    </div>

                    <!-- مطلوب -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_required" class="form-check-input" id="is_required" value="1"
                                {{ $survey_question->is_required ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required">هل السؤال إجباري؟</label>
                        </div>
                    </div>
                </div>

                <!-- الخيارات (للأنواع radio/checkbox/select) -->
                @if(in_array($survey_question->type, ['radio','checkbox','select']))
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الخيارات</h5>
                        <button type="button" id="add-option" class="btn btn-sm btn-light">+ إضافة خيار</button>
                    </div>
                    <div class="card-body" id="options-wrapper">
                        @foreach($survey_question->options as $index => $option)
                        <div class="row mb-2 option-row align-items-end">
                            <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">

                            <div class="col-md-3">
                                <input type="text" name="options[{{ $index }}][label_ar]" class="form-control"
                                       placeholder="النص (عربي)" value="{{ $option->label_ar }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="options[{{ $index }}][value_ar]" class="form-control"
                                       placeholder="القيمة (عربي)" value="{{ $option->value_ar }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="options[{{ $index }}][label_en]" class="form-control"
                                       placeholder="Text (EN)" value="{{ $option->label_en }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="options[{{ $index }}][value_en]" class="form-control"
                                       placeholder="Value (EN)" value="{{ $option->value_en }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-option">حذف</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث السؤال
                    </button>
                    <a href="{{ route('admin.survey-questions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('options-wrapper');
    const addBtn = document.getElementById('add-option');
    let index = wrapper ? wrapper.children.length : 0;

    if(addBtn){
        addBtn.addEventListener('click', function () {
            const html = `
                <div class="row mb-2 option-row align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="options[${index}][label_ar]" class="form-control" placeholder="النص (عربي)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="options[${index}][value_ar]" class="form-control" placeholder="القيمة (عربي)">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="options[${index}][label_en]" class="form-control" placeholder="Text (EN)">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="options[${index}][value_en]" class="form-control" placeholder="Value (EN)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-option">حذف</button>
                    </div>
                </div>`;
            wrapper.insertAdjacentHTML('beforeend', html);
            index++;
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-option')) {
            e.target.closest('.option-row').remove();
        }
    });
});
</script>
@endpush
