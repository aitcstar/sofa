@extends('admin.layouts.app')

@section('title', 'تحليلات العملاء')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">تحليلات العملاء</h1>

    <h4>أفضل العملاء</h4>
    <table class="table table-bordered">
        <thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>عدد الطلبات</th><th>إجمالي الإنفاق</th></tr></thead>
        <tbody>
            @foreach($topCustomers ?? [] as $customer)
                <tr>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td>{{ number_format($customer->total_spent, 2) }} ر.س</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
