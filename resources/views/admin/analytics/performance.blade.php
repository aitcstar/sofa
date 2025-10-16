@extends('admin.layouts.app')

@section('title', 'أداء الموظفين')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">أداء الموظفين</h1>

    <table class="table table-striped">
        <thead><tr><th>الموظف</th><th>عدد الطلبات المنجزة</th><th>نسبة الإنجاز</th></tr></thead>
        <tbody>
            @foreach($employeePerformance ?? [] as $emp)
                <tr>
                    <td>{{ $emp->name }}</td>
                    <td>{{ $emp->completed_orders }}</td>
                    <td>{{ round($emp->performance_rate ?? 0, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
