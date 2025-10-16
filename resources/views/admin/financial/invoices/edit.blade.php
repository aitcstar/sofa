@extends('admin.layouts.app')

@section('title', 'تعديل الفاتورة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">تعديل الفاتورة: {{ $invoice->invoice_number }}</h1>

    <form action="{{ route('admin.financial.invoices.update', $invoice) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="customer_id" class="form-label">العميل</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                @foreach(\App\Models\User::where('role', 'customer')->get() as $customer)
                    <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">المبلغ الإجمالي</label>
            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="{{ $invoice->total_amount }}" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">الحالة</label>
            <select name="status" id="status" class="form-select">
                <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
            </select>
        </div>

        <button class="btn btn-primary">تحديث الفاتورة</button>
    </form>
</div>
@endsection
