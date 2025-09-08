@extends('admin.layouts.app')

@section('title', 'تعديل القطعة')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">تعديل القطعة: {{ $item->item_name }}</h1>
        <a href="{{ route('admin.designs.items.index', $design) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة
        </a>
    </div>

    <form action="{{ route('admin.designs.items.update', [$design, $item]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="unit_id" class="form-label">الوحدة</label>
                    <select name="unit_id" id="unit_id" class="form-control" required>
                        <option value="">اختر الوحدة</option>
                        @foreach($design->units as $unit)
                            <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="item_name" class="form-label">اسم القطعة</label>
                    <input type="text" name="item_name" id="item_name" class="form-control" value="{{ old('item_name', $item->item_name) }}" required>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="quantity" class="form-label">الكمية</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="{{ old('quantity', $item->quantity) }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dimensions" class="form-label">المقاس</label>
                    <input type="text" name="dimensions" id="dimensions" class="form-control" value="{{ old('dimensions', $item->dimensions) }}" placeholder="مثال: 200x100x50">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="material" class="form-label">الخامة</label>
                    <input type="text" name="material" id="material" class="form-control" value="{{ old('material', $item->material) }}" required>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="color" class="form-label">اللون</label>
                    <input type="text" name="color" id="color" class="form-control" value="{{ old('color', $item->color) }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="image" class="form-label">صورة القطعة</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                @if($item->image_path)
                    <div class="mb-3">
                        <label>الصورة الحالية</label>
                        <div>
                            <img src="{{ asset('storage/' . $item->image_path) }}" width="150" class="rounded">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> تحديث القطعة
            </button>
            <a href="{{ route('admin.designs.items.index', $design) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
