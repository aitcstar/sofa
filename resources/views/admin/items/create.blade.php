@extends('admin.layouts.app')

@section('title', isset($item) ? 'تعديل القطعة' : 'إضافة قطعة جديدة')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ isset($item) ? 'تعديل القطعة' : 'إضافة قطعة جديدة' }}</h1>
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات القطعة</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($item) ? route('admin.items.update',$item) : route('admin.items.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($item)) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>اسم القطعة (AR)</label>
                        <input type="text" name="item_name_ar" class="form-control" value="{{ $item->item_name_ar ?? old('item_name_ar') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>اسم القطعة (EN)</label>
                        <input type="text" name="item_name_en" class="form-control" value="{{ $item->item_name_en ?? old('item_name_en') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>الوحدة</label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">اختر الوحدة</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ isset($item) && $item->unit_id == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name_ar }} / {{ $unit->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الكمية</label>
                        <input type="number" name="quantity" class="form-control" value="{{ $item->quantity ?? old('quantity') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>المقاس</label>
                        <input type="text" name="dimensions" class="form-control" value="{{ $item->dimensions ?? old('dimensions') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الخامة (AR)</label>
                        <input type="text" name="material_ar" class="form-control" value="{{ $item->material_ar ?? old('material_ar') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>الخامة (EN)</label>
                        <input type="text" name="material_en" class="form-control" value="{{ $item->material_en ?? old('material_en') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>اللون (AR)</label>
                        <input type="text" name="color_ar" class="form-control" value="{{ $item->color_ar ?? old('color_ar') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>اللون (EN)</label>
                        <input type="text" name="color_en" class="form-control" value="{{ $item->color_en ?? old('color_en') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>  درجه اللون</label>
                        <input type="color" name="background_color" class="form-control" value="{{ $item->background_color ?? old('background_color') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>رفع صورة للقطعة</label>
                        <input type="file" name="image_path" class="form-control">
                    </div>
                </div>

                @if(isset($item) && $item->image_path)
                    <div class="mb-3">
                        <label>الصورة الحالية</label>
                        <div class="position-relative d-inline-block">
                            <img src="{{ asset('storage/'.$item->image_path) }}" class="img-fluid border rounded" style="max-width: 200px;">
                            <form action="{{ route('admin.items.destroy-image', $item) }}" method="POST"
                                  style="position:absolute; top:5px; right:5px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل تريد حذف الصورة؟')">✕</button>
                            </form>
                        </div>
                    </div>
                @endif


                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> {{ isset($item) ? 'تحديث القطعة' : 'إضافة القطعة' }}
                    </button>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
