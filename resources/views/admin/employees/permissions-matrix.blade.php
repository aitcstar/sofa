@extends('admin.layouts.app')

@section('title', 'مصفوفة الصلاحيات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">مصفوفة الصلاحيات</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">الموظفون</a></li>
                    <li class="breadcrumb-item active">مصفوفة الصلاحيات</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>مصفوفة الصلاحيات حسب الأدوار</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>ملاحظة:</strong> هذه المصفوفة توضح الصلاحيات المتاحة لكل دور في النظام.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th style="width: 30%;">القسم / الصلاحية</th>
                            @foreach($roles as $role)
                            <th class="text-center">{{ $role->display_name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionGroups as $groupName => $permissions)
                        <tr class="table-secondary">
                            <td colspan="{{ count($roles) + 1 }}">
                                <strong><i class="fas fa-folder me-2"></i>{{ $groupName }}</strong>
                            </td>
                        </tr>
                        @foreach($permissions as $permission)
                        <tr>
                            <td class="ps-4">
                                <i class="fas fa-key text-muted me-2"></i>
                                {{ $permission->display_name ?? $permission->name }}
                            </td>
                            @foreach($roles as $role)
                            <td class="text-center">
                                @php
                                    $hasPermission = $role->hasPermission($permission->name);
                                @endphp
                                @if($hasPermission)
                                    <i class="fas fa-check-circle text-success fs-5"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger fs-5"></i>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Roles Summary -->
    <div class="row mt-4">
        @foreach($roles as $role)
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-user-tag me-2"></i>{{ $role->display_name }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">{{ $role->description ?? 'لا يوجد وصف' }}</p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span><strong>عدد الصلاحيات:</strong></span>
                        <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span><strong>الحالة:</strong></span>
                        <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }}">
                            {{ $role->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
    .table-responsive {
        max-height: 800px;
        overflow-y: auto;
    }

    .table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #4e73df;
        color: white;
    }

    .table tbody tr:hover {
        background-color: #f8f9fc;
    }

    .table-secondary {
        background-color: #e3e6f0 !important;
    }
</style>
@endpush
@endsection

