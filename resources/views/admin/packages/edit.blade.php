@extends('admin.layouts.app')

@section('title', 'تعديل الباكج')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">تعديل الباكج: {{ $package->name_en }} / {{ $package->name_ar }}</h1>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> رجوع
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">معلومات الباكج</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- الاسم والسعر -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم الباكج (عربي) <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" class="form-control"
                                value="{{ old('name_ar', $package->name_ar) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الباكج (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" class="form-control"
                                value="{{ old('name_en', $package->name_en) }}" required>
                        </div>
                    </div>

                    <!-- السعر والوصف -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الوصف (عربي)</label>
                            <textarea name="description_ar" class="form-control" rows="2">{{ old('description_ar', $package->description_ar) }}</textarea>
                        </div>
                        <div class="col-md-6">

                            <label class="form-label mt-2">الوصف (إنجليزي)</label>
                            <textarea name="description_en" class="form-control" rows="2">{{ old('description_en', $package->description_en) }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"> مدة التنفيذ (عربي) <span class="text-danger">*</span></label>
                            <input type="text" name="period_ar" class="form-control"
                                value="{{ old('period_ar', $package->period_ar) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> مدة التنفيذ (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" name="period_en" class="form-control"
                                value="{{ old('period_en', $package->period_en) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"> الخدمة (عربي) <span class="text-danger">*</span></label>
                            <input type="text" name="service_includes_ar" class="form-control"
                                value="{{ old('service_includes_ar', $package->service_includes_ar) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> الخدمة (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" name="service_includes_en" class="form-control"
                                value="{{ old('service_includes_en', $package->service_includes_en) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"> خطة الدفع (عربي) <span class="text-danger">*</span></label>
                            <input type="text" name="payment_plan_ar" class="form-control"
                                value="{{ old('payment_plan_ar', $package->payment_plan_ar) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> خطة الدفع (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" name="payment_plan_en" class="form-control"
                                value="{{ old('payment_plan_en', $package->payment_plan_en) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"> الديكور (عربي) <span class="text-danger">*</span></label>
                            <input type="text" name="decoration_ar" class="form-control"
                                value="{{ old('decoration_ar', $package->decoration_ar) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> الديكور (إنجليزي) <span class="text-danger">*</span></label>
                            <input type="text" name="decoration_en" class="form-control"
                                value="{{ old('decoration_en', $package->decoration_en) }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">السعر (ريال) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                value="{{ old('price', $package->price) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الترتيب</label>
                            <input type="number" name="sort_order"
                                class="form-control"value="{{ old('sort_order', $package->sort_order) }}" required>
                        </div>
                    </div>



                    <!-- صورة الباكج الرئيسية -->

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الصورة الرئيسية</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if ($package->image)
                                <div class="mb-3">
                                    <h6>الصورة الحالية</h6>
                                    <img src="{{ asset('storage/' . $package->image) }}" class="img-fluid rounded"
                                        width="150">
                                </div>
                            @endif
                        </div>
                        {{--
                <div class="col-md-6">
                    <label class="form-label">صور إضافية</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>

                    <h6>الصور الحالية</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($package->images as $img)
                            <div class="position-relative img-container" style="width:120px" id="img-{{ $img->id }}">
                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                     class="img-thumbnail rounded" width="120">

                                <button type="button"
                                        class="btn btn-sm btn-danger rounded-circle p-1 d-flex align-items-center justify-content-center delete-image"
                                        style="width: 24px; height: 24px; position:absolute; top:5px; right:5px;"
                                        data-url="{{ route('admin.packages.images.destroy', [$package->id, $img->id]) }}"
                                        data-id="{{ $img->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div> --}}
                    </div>

                    <!-- صور إضافية -->













                    <div class="mb-3">
                        <h5>الوحدات</h5>
                        <div id="units-container">
                            @php
                            $groupedUnits = $package->packageUnitItems->groupBy('unit_id');
                            @endphp

                            @foreach($groupedUnits as $unitId => $unitItems)
                                @php
                                    $unit = $unitItems->first()->unit;
                                @endphp
                                <div class="card mb-3 unit-card">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                                        <h6>وحدة {{ $unit->name_ar }} / {{ $unit->name_en }}</h6>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeUnit(this)">حذف</button>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" name="units[{{ $unitId }}][unit_id]" value="{{ $unit->id }}">

                                        <!-- دروب داون لاختيار الوحدة -->
                                        <div class="mb-2">
                                            <label>اختر الوحدة</label>
                                            <select name="units[{{ $unitId }}][selected_unit_id]" class="form-control select2-unit" required>
                                                <option value="">-- اختر وحدة --</option>
                                                @foreach($units as $u)
                                                    <option value="{{ $u->id }}" {{ $u->id == $unit->id ? 'selected' : '' }}>
                                                        {{ $u->name_ar }} / {{ $u->name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- صور الوحدة -->
                                        <div class="unit-images-preview d-flex flex-wrap gap-2 mb-3">
                                            @foreach($unit->images as $img)
                                                <img src="{{ asset('storage/'.$img->image_path) }}" style="width:120px;height:120px;object-fit:cover;border:1px solid #ddd;">
                                            @endforeach
                                        </div>

                                        <!-- عناصر الوحدة -->
                                        <div class="items-container">
                                            @foreach($unitItems as $iIndex => $unitItem)
                                                @php $item = $unitItem->item; @endphp
                                                @if($item)
                                                    <div class="border p-2 mb-2 item-card">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <strong>قطعة {{ $iIndex + 1 }}</strong>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.item-card').remove()">حذف</button>
                                                        </div>
                                                        <select class="form-control select2-item" name="units[{{ $unitId }}][items][{{ $iIndex }}][item_id]" required>
                                                            <option value="{{ $item->id }}" selected>{{ $item->item_name_ar }} / {{ $item->item_name_en }}</option>
                                                        </select>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addItemToUnit(this, {{ $unitId }})">
                                            إضافة قطعة
                                        </button>
                                    </div>
                                </div>
                            @endforeach



                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addUnit()">إضافة وحدة</button>
                    </div>

                    <button type="submit" class="btn btn-success">تحديث الباكج</button>
                </form>
            </div>

            <!-- Scripts -->
            <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <script>
            let unitIndex = {{ $package->packageUnitItems->count() }};

            // تفعيل Select2 على جميع القوائم
            $(document).ready(function(){
                $('.select2-unit').select2();
                $('.select2-item').select2();
            });

            window.addUnitFromList = function () {
    const container = document.getElementById('units-container');
    const unitDiv = document.createElement('div');
    unitDiv.className = 'card mb-3 unit-card';
    unitDiv.innerHTML = `
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">وحدة ${unitIndex + 1}</h5>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.unit-card').remove()">
                حذف
            </button>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <label>اختر الوحدة</label>
                <select name="units[${unitIndex}][unit_id]" class="form-control" required>
                    <option value="">-- اختر --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">
                            {{ $unit->name_ar }} / {{ $unit->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- يمكنك إضافة الحقول المخفية لاحقًا -->
        </div>
    `;
    container.appendChild(unitDiv);
    unitIndex++;
}

// جلب تفاصيل الوحدة (بما في ذلك الصور)
function fetchUnitDetails(unitId, callback) {
    if (!unitId) {
        callback(null);
        return;
    }
    $.get("{{ route('admin.units.details', ['unit' => 'UNIT_ID']) }}".replace('UNIT_ID', unitId), function(data) {
        callback(data);
    }).fail(function() {
        callback(null);
    });
}

// جلب القطع حسب unit_id
function fetchItemsByUnitId(unitId, callback) {
    if (!unitId) {
        callback([]);
        return;
    }
    $.get("{{ route('admin.items.by-unit', ['unitId' => 'UNIT_ID']) }}".replace('UNIT_ID', unitId), function(data) {
        callback(data);
    }).fail(function() {
        callback([]);
    });
}

            // حذف وحدة
            function removeUnit(btn){
                if(confirm('هل أنت متأكد من حذف هذه الوحدة؟')){
                    $(btn).closest('.unit-card').remove();
                }
            }

            // إضافة وحدة جديدة
            function addUnit(){
                const container = $('#units-container');
                let html = `
                <div class="card mb-3 unit-card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h6>وحدة ${unitIndex+1}</h6>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeUnit(this)">حذف</button>
                    </div>
                    <div class="card-body">
                        <label>اختر الوحدة</label>
                        <select name="units[${unitIndex}][unit_id]" class="form-control select2-unit" onchange="loadUnitDetails(this)" required>
                            <option value="">-- اختر وحدة --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name_ar }} / {{ $u->name_en }}</option>
                            @endforeach
                        </select>
                        <div class="unit-images-preview d-flex flex-wrap gap-2 mt-2"></div>
                        <div class="items-container mt-3"></div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addItemToUnit(this, ${unitIndex})">
                            إضافة قطعة
                        </button>
                    </div>
                </div>`;
                container.append(html);
                $('.select2-unit').last().select2();
                unitIndex++;
            }

            // تحميل تفاصيل الوحدة عبر AJAX
            function loadUnitDetails(select){
                let unitId = $(select).val();
                let unitCard = $(select).closest('.unit-card');
                if(!unitId) return;

                $.get("{{ route('admin.units.details', ['unit' => 'UNIT_ID']) }}".replace('UNIT_ID', unitId), function(data){
                    // عرض الصور
                    let preview = unitCard.find('.unit-images-preview');
                    preview.html('');
                    data.images.forEach(img=>{
                        preview.append(`<img src="${img.image_path}" style="width:120px;height:120px;object-fit:cover;border:1px solid #ddd;">`);
                    });

                    // تحميل القطع في select2 عند إضافة عناصر جديدة
                    unitCard.data('items', data.items);
                });
            }

            // إضافة عنصر للوحدة
            function addItemToUnit(btn, uIndex){
                let unitCard = $(btn).closest('.unit-card');
                let items = unitCard.data('items') || [];
                if(items.length == 0){
                    alert('اختر الوحدة أولاً لتحميل العناصر.');
                    return;
                }
                let itemIndex = unitCard.find('.item-card').length;
                let options = items.map(i=>`<option value="${i.id}">${i.item_name_ar} / ${i.item_name_en}</option>`).join('');
                let html = `
                <div class="border p-2 mb-2 item-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>قطعة ${itemIndex+1}</strong>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.item-card').remove()">حذف</button>
                    </div>
                    <select name="units[${uIndex}][items][${itemIndex}][item_id]" class="form-control select2-item" required>
                        <option value="">-- اختر قطعة --</option>
                        ${options}
                    </select>
                </div>`;
                unitCard.find('.items-container').append(html);
                unitCard.find('.select2-item').last().select2();
            }

            function addItemToUnit(btn, uIndex) {
    const unitCard = btn.closest('.unit-card');
    const unitSelect = unitCard.querySelector('[name="units[' + uIndex + '][unit_id]"]');
    const unitId = unitSelect.value; // نأخذ القيمة الحالية من select

    if (!unitId) {
        alert('يرجى اختيار وحدة أولاً');
        return;
    }

    fetchItemsByUnitId(unitId, function(items) {
        const itemsContainer = unitCard.querySelector('.items-container');
        const itemIndex = itemsContainer.querySelectorAll('.item-card').length;

        let optionsHtml = '<option value="">-- اختر قطعة --</option>';
        items.forEach(item => {
            optionsHtml += `<option value="${item.id}" data-item='${JSON.stringify(item).replace(/'/g, "\\'")}'>
                ${item.item_name_ar} / ${item.item_name_en}
            </option>`;
        });

        const div = document.createElement('div');
        div.className = 'border p-2 mb-2 item-card';
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong>قطعة ${itemIndex + 1}</strong>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.item-card').remove()">حذف</button>
            </div>
            <select name="units[${uIndex}][items][${itemIndex}][item_id]" class="form-control select2-item" required>
                ${optionsHtml}
            </select>
        `;
        itemsContainer.appendChild(div);
        $(div).find('.select2-item').select2();
    });
}

            </script>
            @endsection
