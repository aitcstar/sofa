@extends('admin.layouts.app')

@section('title', 'إحصائيات الموقع')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">إحصائيات الموقع</h1>

    <div class="row text-center mb-4">
        <div class="col-md-2">
            <div class="card p-3 shadow-sm">
                <h6>إجمالي الزوار</h6>
                <p>{{ number_format($stats['total_visitors'] ?? 0) }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 shadow-sm">
                <h6>عدد المشاهدات</h6>
                <p>{{ number_format($stats['page_views'] ?? 0) }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 shadow-sm">
                <h6>معدل الارتداد</h6>
                <p>{{ round($stats['bounce_rate'] ?? 0, 2) }}%</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 shadow-sm">
                <h6>مدة الجلسة</h6>
                <p>{{ round($stats['avg_session_duration'] ?? 0, 2) }} ثانية</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 shadow-sm">
                <h6>معدل التحويل</h6>
                <p>{{ round($stats['conversion_rate'] ?? 0, 2) }}%</p>
            </div>
        </div>
    </div>

    <h4>الصفحات الأكثر زيارة</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>العنوان</th>
                <th>الرابط</th>
                <th>عدد الزيارات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($popularPages as $page)
                <tr>
                    <td>{{ $page->page_title }}</td>
                    <td><a href="{{ $page->page_url }}" target="_blank">{{ $page->page_url }}</a></td>
                    <td>{{ $page->visits }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
