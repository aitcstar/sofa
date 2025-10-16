@extends('admin.layouts.app')

@section('title', 'إضافة طريقة دفع جديدة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إضافة طريقة دفع جديدة</h1>

    <form action="{{ route('admin.financial.payment-methods.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">الاسم</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">النوع</label>
            <input type="text" name="type" id="type" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">الوصف</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
            <label for="is_active" class="form-check-label">نشط</label>
        </div>

        <button class="btn btn-success">إضافة طريقة الدفع</button>
    </form>
</div>
@endsection
