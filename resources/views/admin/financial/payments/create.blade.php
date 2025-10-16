@extends('admin.layouts.app')

@section('title', 'إضافة دفعة جديدة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إضافة دفعة جديدة</h1>

    <form action="{{ route('admin.financial.payments.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="customer_id" class="form-label">العميل</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                @foreach(\App\Models\User::where('role', 'customer')->get() as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="invoice_id" class="form-label">فاتورة مرتبطة (اختياري)</label>
            <select name="invoice_id" id="invoice_id" class="form-select">
                <option value="">-- بدون فاتورة --</option>
                @foreach(\App\Models\Invoice::all() as $invoice)
                    <option value="{{ $invoice->id }}">{{ $invoice->invoice_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">المبلغ</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">طريقة الدفع</label>
            <select name="payment_method" id="payment_method" class="form-select" required>
                @foreach(\App\Models\PaymentMethod::active()->get() as $method)
                    <option value="{{ $method->name }}">{{ $method->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">ملاحظات</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">إنشاء الدفعة</button>
    </form>
</div>
@endsection
