@extends('admin.layouts.app')

@section('title', 'تعديل معرض')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل معرض</h1>
        <a href="{{ route('admin.exhibitions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">
            <form action="{{ route('admin.exhibitions.update', $exhibition) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')


                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                         إعدادات SEO
                    </div>
                    <div class="card-body">
                        {{-- العنوان --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title (AR)</label>
                                <input type="text" name="meta_title_ar" class="form-control" value="{{ old('meta_title_ar', $exhibition->meta_title_ar) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title (EN)</label>
                                <input type="text" name="meta_title_en" class="form-control" value="{{ old('meta_title_en', $exhibition->meta_title_en) }}">
                            </div>
                        </div>

                        {{-- الوصف --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Description (AR)</label>
                                <textarea name="meta_description_ar" class="form-control">{{ old('meta_description_ar', $exhibition->meta_description_ar) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Description (EN)</label>
                                <textarea name="meta_description_en" class="form-control">{{ old('meta_description_en', $exhibition->meta_description_en) }}</textarea>
                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (AR)</label>
                                <input type="text" name="slug_ar" class="form-control" value="{{ old('slug_ar', $exhibition->slug_ar) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug (EN)</label>
                                <input type="text" name="slug_en" class="form-control" value="{{ old('slug_en', $exhibition->slug_en) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تفاصيل المعرض</h5>
                </div>
                <div class="row">
                    <!-- التصنيف -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">التصنيف <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $exhibition->category_id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? $cat->name_ar : $cat->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الباقة -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الباكج</label>
                        <select name="package_id" class="form-select">
                            <option value="">بدون</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" {{ $pkg->id == $exhibition->package_id ? 'selected' : '' }}>
                                    {{ $pkg->name_ar}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- العناوين -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (عربي)</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ $exhibition->name_ar }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (إنجليزي)</label>
                        <input type="text" name="name_en" class="form-control" value="{{ $exhibition->name_en }}" required>
                    </div>



                    <!-- الملخص -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص (عربي)</label>
                        <textarea name="summary_ar" class="form-control" rows="3">{{ $exhibition->summary_ar }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص (إنجليزي)</label>
                        <textarea name="summary_en" class="form-control" rows="3">{{ $exhibition->summary_en }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">المدينه (عربي)</label>
                        <input type="text" name="city_ar" class="form-control" value="{{ $exhibition->city_ar }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المدينه (إنجليزي)</label>
                        <input type="text" name="city_en" class="form-control" value="{{ $exhibition->city_en }}">
                    </div>



                    <!-- التاريخ -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تاريخ التسليم</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ $exhibition->delivery_date }}">
                    </div>

                    <!-- صور جديدة -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">إضافة صور جديدة</label>
                        <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                    </div>
                </div>

                <!-- الصور الحالية -->
                <div class="mb-4">
                    <h5>الصور الحالية</h5>
                    <div class="d-flex flex-wrap">
                        @foreach($exhibition->images as $img)
                            <div class="card m-2" style="width: 150px;">
                                <img src="{{ asset('storage/'.$img->image) }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body text-center p-2">
                                        <button type="button"  onclick="setPrimary({{ $img->id }})"  class="btn btn-sm {{ $img->is_primary ? 'btn-success' : 'btn-outline-primary' }}">
                                            {{ $img->is_primary ? 'الصورة الرئيسية' : 'تعيين كرئيسية' }}
                                        </button>


                                        <button type="button" onclick="deleteImage({{ $img->id }})" class="btn btn-sm btn-danger mt-1" >
                                            حذف
                                        </button>


                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- خطوات المعرض -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">خطوات المعرض</h5>
                        <button type="button" id="add-step" class="btn btn-sm btn-light">إضافة خطوة</button>
                    </div>
                    <div class="card-body" id="steps-wrapper">
                        @foreach($exhibition->steps as $i => $step)


                            <div class="row step-item border p-3 mb-3 rounded">
                                <input type="hidden" name="steps[{{ $i }}][id]" value="{{ $step->id }}">
                        <input type="hidden" name="steps[{{ $i }}][sort_order]" value="{{ $step->sort_order }}">

                        <input type="hidden" name="steps[{{ $i }}][_delete]" value="0" id="delete_{{ $i }}">

                                <div class="col-md-4">
                                    <input type="text" name="steps[{{ $i }}][title_ar]" value="{{ $step->title_ar }}" class="form-control mb-2" placeholder="عنوان (عربي)">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="steps[{{ $i }}][title_en]" value="{{ $step->title_en }}" class="form-control mb-2" placeholder="عنوان (إنجليزي)">
                                </div>
                                <div class="col-md-3">
                                    @if($step->icon)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/'.$step->icon) }}" width="60" class="border rounded mb-1">
                                        </div>
                                    @endif
                                    <input type="file" name="steps[{{ $i }}][icon]" class="form-control">
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-step-btn">حذف</button>

                                    <!--<button type="button" class="btn btn-danger btn-sm" onclick="removeStep(this)">حذف</button>-->
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث المعرض
                    </button>
                    <a href="{{ route('admin.exhibitions.index') }}" class="btn btn-outline-secondary">
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

console.log('JavaScript loaded successfully'); // للتأكد من تحميل الملف

// اختبر إذا كان الزر موجوداً
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    const buttons = document.querySelectorAll('.remove-step-btn, [onclick*="removeStep"]');
    console.log('Found buttons:', buttons.length);
});

document.addEventListener('DOMContentLoaded', function() {
    // إضافة خطوات جديدة
    let stepIndex = {{ count($exhibition->steps) }};

    document.getElementById('add-step').addEventListener('click', function() {
        const wrapper = document.getElementById('steps-wrapper');
        const html = `
        <div class="row step-item border p-3 mb-3 rounded">
            <input type="hidden" name="steps[${stepIndex}][step_order]" value="${stepIndex}">
            <input type="hidden" name="steps[${stepIndex}][_delete]" value="0" class="delete-flag">

            <div class="col-md-4">
                <input type="text" name="steps[${stepIndex}][title_ar]"
                       class="form-control mb-2" placeholder="عنوان (عربي)" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="steps[${stepIndex}][title_en]"
                       class="form-control mb-2" placeholder="عنوان (إنجليزي)" required>
            </div>
            <div class="col-md-3">
                <input type="file" name="steps[${stepIndex}][icon]"
                       class="form-control" accept="image/*">
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-sm remove-step-btn">حذف</button>
            </div>
        </div>`;
        wrapper.insertAdjacentHTML('beforeend', html);
        stepIndex++;
    });

    // event delegation للزر الجديد والقديم
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-step-btn')) {
            e.preventDefault();
            removeStep(e.target);
        }
    });

    function removeStep(button) {
    console.log('removeStep function called'); // للتأكد

    const stepItem = button.closest('.step-item');
    if (!stepItem) {
        console.error('❌ لم يتم العثور على step-item');
        return;
    }

    const deleteInput = stepItem.querySelector('input[name*="[_delete]"]');
    if (!deleteInput) {
        console.error('❌ لم يتم العثور على حقل _delete داخل step-item');
        return;
    }

    deleteInput.value = '1';
    stepItem.style.opacity = '0.3';
    stepItem.style.backgroundColor = '#ffe6e6';
    button.textContent = 'سيتم الحذف';
    button.disabled = true;

    console.log('✅ Delete value set to:', deleteInput.value);
}

});



    const exhibitionId = {{ $exhibition->id }};

    function setPrimary(imageId) {
    fetch(`/admin/exhibitions/${exhibitionId}/images/${imageId}/set-primary`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('تم تعيين الصورة الرئيسية');
            location.reload();
        }
    })
    .catch(err => console.error(err));
}

function deleteImage(imageId) {
    if(!confirm('هل أنت متأكد؟')) return;

    fetch(`/admin/exhibitions/${exhibitionId}/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('تم حذف الصورة');
            location.reload();
        }
    })
    .catch(err => console.error(err));
}



</script>
@endpush
