@extends('admin.layouts.app')

@section('title', 'عرض الدفعة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">دفعة: {{ $payment->payment_number }}</h1>

    <div class="card p-3">
        <p><strong>العميل:</strong> {{ $payment->customer->name ?? '-' }}</p>
        <p><strong>المبلغ:</strong> {{ $payment->amount }}</p>
        <p><strong>طريقة الدفع:</strong> {{ $payment->paymentMethod->name ?? '-' }}</p>
        <p><strong>تاريخ الدفع:</strong> {{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '-' }}</p>
        <p><strong>الحالة:</strong> {{ ucfirst($payment->status) }}</p>
        <p><strong>ملاحظات:</strong> {{ $payment->notes ?? '-' }}</p>
    </div>
</div>
@endsection
