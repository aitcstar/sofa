@extends('admin.layouts.app')

@section('title', 'تقارير الطلبات')

@section('content')
<div class="container">
    <h1>تقارير الطلبات</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>إجمالي الطلبات</th>
                <th>قيد الانتظار</th>
                <th>تم التأكيد</th>
                <th>قيد المعالجة</th>
                <th>تم الشحن</th>
                <th>تم التسليم</th>
                <th>متأخرة</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $stats['total'] }}</td>
                <td>{{ $stats['pending'] }}</td>
                <td>{{ $stats['confirmed'] }}</td>
                <td>{{ $stats['processing'] }}</td>
                <td>{{ $stats['shipped'] }}</td>
                <td>{{ $stats['delivered'] }}</td>
                <td>{{ $stats['overdue'] }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
