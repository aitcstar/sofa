@extends('admin.layouts.app')

@section('title', 'تحليلات المبيعات')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">تحليلات المبيعات</h1>

    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card p-3"><h6>إجمالي الإيرادات</h6><p>{{ number_format($stats['total_revenue'] ?? 0, 2) }} ر.س</p></div>
        </div>
        <div class="col-md-3">
            <div class="card p-3"><h6>متوسط قيمة الطلب</h6><p>{{ number_format($stats['avg_order_value'] ?? 0, 2) }} ر.س</p></div>
        </div>
        <div class="col-md-3">
            <div class="card p-3"><h6>عدد الطلبات</h6><p>{{ $stats['total_orders'] ?? 0 }}</p></div>
        </div>
        <div class="col-md-3">
            <div class="card p-3"><h6>العملاء الجدد</h6><p>{{ $stats['new_customers'] ?? 0 }}</p></div>
        </div>
    </div>

    <h4>الإيرادات اليومية</h4>
    <table class="table table-striped">
        <thead><tr><th>التاريخ</th><th>الإيرادات</th><th>عدد الطلبات</th></tr></thead>
        <tbody>
            @foreach($dailyRevenue ?? [] as $day)
                <tr>
                    <td>{{ $day->date }}</td>
                    <td>{{ number_format($day->revenue, 2) }} ر.س</td>
                    <td>{{ $day->orders }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
