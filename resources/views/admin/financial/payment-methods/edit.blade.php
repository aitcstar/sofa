@extends('admin.layouts.app')

@section('title', 'تعديل طريقة الدفع')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">تعديل طريقة الدفع: {{ $paymentMethod->name }}</h1>

    <form action="{{ route('admin.financial.payment-methods.update', $paymentMethod) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">الاسم</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $paymentMethod->name }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">النوع</label>
            <input type="text" name="type" id="type" class="form-control" value="{{ $paymentMethod->type }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea name="description" id="description" class="form-control">{{ $paymentMethod->description }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $paymentMethod->is_active ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">نشط</label>
        </div>

        <button class="btn btn-primary">تحديث طريقة الدفع</button>
    </form>
</div>
@endsection
