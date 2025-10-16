@extends('admin.layouts.app')
s
@section('content')
<h3>تقارير CRM - {{ ucfirst($period) }}</h3>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <h5>عدد العملاء المحتملين</h5>
            <p>{{ $reports['leads']['total'] ?? 0 }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h5>عدد العروض</h5>
            <p>{{ $reports['quotes']['total'] ?? 0 }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h5>قيمة خطوط المبيعات</h5>
            <p>{{ $reports['pipeline']['total_value'] ?? 0 }}</p>
        </div>
    </div>
</div>

<h4>الأداء حسب المصدر</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>المصدر</th>
            <th>عدد العملاء المحتملين</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports['sources'] as $source => $count)
        <tr>
            <td>{{ $source }}</td>
            <td>{{ $count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4>أداء الموظفين</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>الموظف</th>
            <th>عدد العملاء المحتملين</th>
            <th>عدد العملاء المحولين</th>
            <th>نسبة التحويل %</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports['performance'] as $perf)
        <tr>
            <td>{{ $perf['name'] }}</td>
            <td>{{ $perf['leads_count'] }}</td>
            <td>{{ $perf['converted_leads_count'] }}</td>
            <td>{{ $perf['conversion_rate'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
