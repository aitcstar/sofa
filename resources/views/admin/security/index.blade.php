@extends('admin.layouts.app')

@section('title', 'الأمان والحماية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="mb-1">الأمان والحماية</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">الأمان والحماية</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">0</h3>
                    <p class="mb-0">محاولات تسجيل دخول فاشلة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">0</h3>
                    <p class="mb-0">المستخدمون النشطون</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">0</h3>
                    <p class="mb-0">سجلات الأمان</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">0</h3>
                    <p class="mb-0">التنبيهات الأمنية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Options -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                    <h5>سجلات الأمان</h5>
                    <p class="text-muted">عرض جميع سجلات النشاط الأمني</p>
                    <a href="{{ route('admin.security.logs') }}" class="btn btn-primary">عرض السجلات</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>محاولات الدخول الفاشلة</h5>
                    <p class="text-muted">مراقبة محاولات تسجيل الدخول الفاشلة</p>
                    <a href="{{ route('admin.security.failed-logins') }}" class="btn btn-primary">عرض المحاولات</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-cog fa-3x text-success mb-3"></i>
                    <h5>إعدادات الأمان</h5>
                    <p class="text-muted">تكوين إعدادات الأمان والحماية</p>
                    <a href="{{ route('admin.security.settings') }}" class="btn btn-primary">الإعدادات</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

