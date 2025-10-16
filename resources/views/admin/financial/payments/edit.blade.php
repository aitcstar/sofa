@extends('admin.layouts.app')

@section('title', 'تعديل الدفعة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">تعديل الدفعة: {{ $payment->payment_number }}</h1>

    <form action="{{ route('admin.financial.payments.update', $payment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="amount" class="form-label">المبلغ</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ $payment->amount }}" required>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">طريقة الدفع</label>
            <select name="payment_method" id="payment_method" class="form-select" required>
                @foreach(\App\Models\PaymentMethod::active()->get() as $method)
                    <option value="{{ $method->name }}" {{ $payment->payment_method == $method->name ? 'selected' : '' }}>{{ $method->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">الحالة</label>
            <select name="status" id="status" class="form-select">
                <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>فاشلة</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">ملاحظات</label>
            <textarea name="notes" id="notes" class="form-control">{{ $payment->notes }}</textarea>
        </div>

        <button class="btn btn-primary">تحديث الدفعة</button>
    </form>
</div>
@endsection
