@extends('admin.layouts.app')

@section('title', 'تقرير الأحداث')

@section('content')
<div class="container">
    <h1>تقرير الأحداث</h1>

    {{-- جدول الأحداث --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>الإجراء</th>
                <th>الوصف</th>
                <th>المستخدم</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->order->order_number ?? '-' }}</td>
                <td>{{ $log->formatted_action }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->user->name ?? 'النظام' }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.orders.reports') }}" class="btn btn-secondary mt-3">رجوع</a>
</div>
@endsection
