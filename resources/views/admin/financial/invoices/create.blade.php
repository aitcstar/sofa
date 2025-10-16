@extends('admin.layouts.app')

@section('title', 'إضافة فاتورة جديدة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إضافة فاتورة جديدة</h1>

    <form action="{{ route('admin.financial.invoices.store') }}" method="POST">
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
            <label for="order_id" class="form-label">الطلب (اختياري)</label>
            <select name="order_id" id="order_id" class="form-select">
                <option value="">-- بدون طلب --</option>
                @foreach(\App\Models\Order::all() as $order)
                    <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="issue_date" class="form-label">تاريخ الإصدار</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
            <input type="date" name="due_date" id="due_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">المبلغ الإجمالي</label>
            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
        </div>

        <button class="btn btn-success">إنشاء الفاتورة</button>
    </form>
</div>
@endsection
