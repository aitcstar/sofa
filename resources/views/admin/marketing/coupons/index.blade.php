@extends('admin.layouts.app')

@section('title', 'إدارة الكوبونات')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">إدارة الكوبونات</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">التسويق</a></li>
                    <li class="breadcrumb-item active">الكوبونات</li>
                </ol>
            </nav>
        </div>
        <div>
            @if($user && ($user->hasPermission('marketing.coupons.create') || $user->role === 'admin'))
            <a href="{{ route('admin.marketing.coupons.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة كوبون جديد
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marketing.coupons.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="بحث بالكود أو الاسم" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="is_active" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
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

    <!-- Coupons Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>قائمة الكوبونات</h5>
        </div>
        <div class="card-body">
            @if(isset($coupons) && $coupons->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>الكود</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>القيمة</th>
                            <th>الاستخدامات</th>
                            <th>صالح من</th>
                            <th>صالح حتى</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong></td>
                            <td>{{ $coupon->name ?? '-' }}</td>
                            <td>
                                @if($coupon->type == 'percentage')
                                    <span class="badge bg-info">نسبة مئوية</span>
                                @else
                                    <span class="badge bg-success">مبلغ ثابت</span>
                                @endif
                            </td>
                            <td>
                                @if($coupon->type == 'percentage')
                                    {{ $coupon->value }}%
                                @else
                                    {{ number_format($coupon->value, 2) }} ريال
                                @endif
                            </td>
                            <td>
                                @if(isset($coupon->usages_count))
                                    {{ $coupon->usages_count }} / {{ $coupon->max_uses ?? '∞' }}
                                @else
                                    0 / {{ $coupon->max_uses ?? '∞' }}
                                @endif
                            </td>
                            <td>{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '-' }}</td>
                            <td>{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                @if($user && ($user->hasPermission('marketing.coupons.edit') || $user->role === 'admin'))
                                <a href="{{ route('admin.marketing.coupons.edit', $coupon) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                @if($user && ($user->hasPermission('marketing.coupons.delete') || $user->role === 'admin'))
                                <form action="{{ route('admin.marketing.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد كوبونات حالياً</p>
                <a href="{{ route('admin.marketing.coupons.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة كوبون جديد
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

