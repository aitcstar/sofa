@extends('admin.layouts.app')

@section('title', 'سجلات الأمان')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">سجلات الأمان</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.security.index') }}">الأمان والحماية</a></li>
                    <li class="breadcrumb-item active">سجلات الأمان</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.security.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="login">تسجيل دخول</option>
                            <option value="logout">تسجيل خروج</option>
                            <option value="create">إنشاء</option>
                            <option value="update">تعديل</option>
                            <option value="delete">حذف</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>سجلات النشاط</h5>
        </div>
        <div class="card-body">
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد سجلات أمان متاحة حالياً</p>
            </div>
        </div>
    </div>
</div>
@endsection

