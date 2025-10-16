\@extends('admin.layouts.app')

@section('title', 'تعديل الطلب')

@section('content')
<div class="container">
    <h1>تعديل الطلب: {{ $order->order_number }}</h1>

    <form action="{{ route('admin.orders.enhanced.update', $order) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>اسم العميل</label>
            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $order->name) }}">
        </div>

        <div class="mb-3">
            <label>البريد الإلكتروني</label>
            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $order->email) }}">
        </div>

        <div class="mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $order->phone) }}">
        </div>

        <div class="mb-3">
            <label>الحزمة</label>
            <select name="package_id" class="form-select">
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" {{ $order->package_id == $package->id ? 'selected' : '' }}>
                        {{ $package->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>عدد الوحدات</label>
            <input type="number" name="units_count" class="form-control" value="{{ old('units_count', $order->units_count) }}">
        </div>

        <div class="mb-3">
            <label>الإجمالي</label>
            <input type="number" name="total_amount" class="form-control" value="{{ old('total_amount', $order->total_amount) }}">
        </div>

        <div class="mb-3">
            <label>الأولوية</label>
            <input type="number" name="priority" class="form-control" value="{{ old('priority', $order->priority) }}">
        </div>

        <button type="submit" class="btn btn-primary">تحديث</button>
        <a href="{{ route('admin.orders.enhanced.show', $order) }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection
