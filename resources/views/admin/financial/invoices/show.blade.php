@extends('admin.layouts.app')

@section('title', 'عرض الفاتورة')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">فاتورة: {{ $invoice->invoice_number }}</h1>

    <div class="card p-3">
        <p><strong>العميل:</strong> {{ $invoice->customer->name ?? '-' }}</p>
        <p><strong>المبلغ:</strong> {{ $invoice->total_amount }}</p>
        <p><strong>تاريخ الإصدار:</strong> {{ $invoice->issue_date->format('Y-m-d') }}</p>
        <p><strong>الحالة:</strong> {{ ucfirst($invoice->status) }}</p>
    </div>

    <h3 class="mt-4">المدفوعات المرتبطة</h3>
    @include('admin.financial.payments.partials.table', ['payments' => $invoice->payments])
</div>
@endsection
