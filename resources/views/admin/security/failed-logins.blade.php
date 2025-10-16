@extends('admin.layouts.app')

@section('title', 'محاولات تسجيل الدخول الفاشلة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">محاولات تسجيل الدخول الفاشلة</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.security.index') }}">الأمان والحماية</a></li>
                    <li class="breadcrumb-item active">محاولات الدخول الفاشلة</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.security.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Failed Logins Table -->
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>محاولات الدخول الفاشلة</h5>
        </div>
        <div class="card-body">
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="text-muted">لا توجد محاولات دخول فاشلة</p>
            </div>
        </div>
    </div>
</div>
@endsection

