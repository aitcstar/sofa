\@extends('admin.layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="container">
    <h1>تفاصيل الطلب: {{ $order->order_number }}</h1>

    <p><strong>العميل:</strong> {{ $order->user->name ?? $order->name }}</p>
    <p><strong>الحزمة:</strong> {{ $order->package->name ?? '-' }}</p>
    <p><strong>الحالة:</strong> {{ $order->status_text }}</p>
    <p><strong>الأولوية:</strong> {{ $order->priority_text }}</p>
    <p><strong>الإجمالي:</strong> {{ $order->total_amount }}</p>
    <p><strong>تاريخ الإنشاء:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>

    <h3>الملاحظات:</h3>
    <pre>{{ $order->internal_notes }}</pre>

    <h3>الموظفين المعينين:</h3>
    <ul>
        @foreach($order->activeAssignments as $assignment)
            <li>{{ $assignment->user->name }} - {{ $assignment->formatted_role }}</li>
        @endforeach
    </ul>

    <a href="{{ route('admin.orders.enhanced.index') }}" class="btn btn-secondary">رجوع</a>
</div>
@endsection
