@extends('admin.layouts.app')

@section('title', 'تحليلات التسويق')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تحليلات التسويق</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">التسويق</a></li>
                    <li class="breadcrumb-item active">التحليلات</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total_campaigns'] ?? 0 }}</h3>
                    <p class="mb-0">إجمالي الحملات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $stats['active_campaigns'] ?? 0 }}</h3>
                    <p class="mb-0">الحملات النشطة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $stats['total_coupons'] ?? 0 }}</h3>
                    <p class="mb-0">إجمالي الكوبونات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $stats['active_coupons'] ?? 0 }}</h3>
                    <p class="mb-0">الكوبونات النشطة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">أداء الحملات</h5>
                </div>
                <div class="card-body">
                    <canvas id="campaignsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">استخدام الكوبونات</h5>
                </div>
                <div class="card-body">
                    <canvas id="couponsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">النشاط الأخير</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">لا توجد بيانات نشاط متاحة حالياً</p>
        </div>
    </div>
</div>
@endsection

