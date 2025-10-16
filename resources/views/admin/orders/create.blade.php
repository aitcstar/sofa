{{-- resources/views/admin/orders/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة طلب جديد')

@section('content')
<div class="container">
    <h1>إضافة طلب جديد</h1>

    <form action="{{ route('admin.orders.enhanced.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>اسم العميل</label>
            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}">
        </div>

        <div class="mb-3">
            <label>البريد الإلكتروني</label>
            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}">
        </div>

        <div class="mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}">
        </div>

        <div class="mb-3">
            <label>الحزمة</label>
            <select name="package_id" class="form-select">
                @foreach($packages as $package)
                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>عدد الوحدات</label>
            <input type="number" name="units_count" class="form-control" value="{{ old('units_count', 1) }}">
        </div>

        <div class="mb-3">
            <label>الإجمالي</label>
            <input type="number" name="total_amount" class="form-control" value="{{ old('total_amount') }}">
        </div>

        <div class="mb-3">
            <label>الأولوية</label>
            <input type="number" name="priority" class="form-control" value="{{ old('priority', 1) }}">
        </div>

        <button type="submit" class="btn btn-primary">حفظ</button>
        <a href="{{ route('admin.orders.enhanced.index') }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection
