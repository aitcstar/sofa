@extends('admin.layouts.app')

@section('title', 'اتجاهات الأداء')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">اتجاهات الأداء العام</h1>

    <h4>الإحصائيات الشهرية</h4>
    <table class="table table-bordered">
        <thead><tr><th>الشهر</th><th>الإيرادات</th><th>عدد الطلبات</th></tr></thead>
        <tbody>
            @foreach($monthlySales ?? [] as $month)
                <tr>
                    <td>{{ $month->year }}-{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ number_format($month->revenue, 2) }} ر.س</td>
                    <td>{{ $month->orders }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
