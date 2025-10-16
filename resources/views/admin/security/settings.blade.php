@extends('admin.layouts.app')

@section('title', 'إعدادات الأمان')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">إعدادات الأمان</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.security.index') }}">الأمان والحماية</a></li>
                    <li class="breadcrumb-item active">الإعدادات</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>إعدادات الأمان والحماية</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">إعدادات الأمان قيد التطوير</p>
        </div>
    </div>
</div>
@endsection
