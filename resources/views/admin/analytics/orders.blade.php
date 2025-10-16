@extends('admin.layouts.app')

@section('title', 'تحليلات الطلبات')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">تحليلات الطلبات</h1>

    <div class="row mb-4 text-center">
        <div class="col-md-3">
            <div class="card p-3">
                <h6>إجمالي الطلبات</h6>
                <p>{{ $stats['total_orders'] ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6>الطلبات قيد التنفيذ</h6>
                <p>{{ $stats['pending_orders'] ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6>الطلبات المكتملة</h6>
                <p>{{ $stats['completed_orders'] ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6>نسبة الإلغاء</h6>
                <p>{{ round($stats['cancel_rate'] ?? 0, 2) }}%</p>
            </div>
        </div>
    </div>

    <h4>حالات الطلبات</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الحالة</th>
                <th>عدد الطلبات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderStatuses ?? [] as $status)
                <tr>
                    <td>{{ $status->status }}</td>
                    <td>{{ $status->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
