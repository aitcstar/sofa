{{-- resources/views/admin/orders/performance-report.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تقرير الأداء')

@section('content')
<div class="container">
    <h1>تقرير الأداء</h1>

    {{-- يمكن إضافة رسومات بيانية أو جداول حسب الحاجة --}}
    <p>سيتم إضافة تفاصيل الأداء هنا.</p>

    <a href="{{ route('admin.orders.reports') }}" class="btn btn-secondary mt-3">رجوع</a>
</div>
@endsection
