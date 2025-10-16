@extends('admin.layouts.app')

@section('title', 'لوحة التسويق')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">لوحة التسويق</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active">التسويق</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i>حملة جديدة
            </a>
            <a href="{{ route('admin.marketing.coupons.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>كوبون جديد
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">إجمالي الحملات</h6>
                            <h2 style="color: #84909f;">{{ $stats['total_campaigns'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">الحملات النشطة</h6>
                            <h2>{{ $stats['active_campaigns'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">إجمالي الكوبونات</h6>
                            <h2>{{ $stats['total_coupons'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">الكوبونات النشطة</h6>
                            <h2>{{ $stats['active_coupons'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Campaigns -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>آخر الحملات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الحملة</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCampaigns ?? [] as $campaign)
                                <tr>
                                    <td>{{ $campaign->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $campaign->type_text ?? $campaign->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $campaign->status_color ?? 'secondary' }}">
                                            {{ $campaign->status_text ?? $campaign->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.marketing.campaigns.show', $campaign->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد حملات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-outline-primary">
                            عرض جميع الحملات <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Coupons -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>الكوبونات الأكثر استخداماً</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الكود</th>
                                    <th>النوع</th>
                                    <th>القيمة</th>
                                    <th>الاستخدامات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCoupons ?? [] as $coupon)
                                <tr>
                                    <td><strong>{{ $coupon->code }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->type == 'percentage' ? 'warning' : 'info' }}">
                                            {{ $coupon->type == 'percentage' ? 'نسبة مئوية' : 'قيمة ثابتة' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($coupon->type == 'percentage')
                                            {{ $coupon->value }}%
                                        @else
                                            {{ number_format($coupon->value, 2) }} ريال
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $coupon->usages_count ?? 0 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد كوبونات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.marketing.coupons.index') }}" class="btn btn-outline-success">
                            عرض جميع الكوبونات <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-list fa-2x mb-2"></i><br>
                                <strong>إدارة الحملات</strong>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.marketing.coupons.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-tags fa-2x mb-2"></i><br>
                                <strong>إدارة الكوبونات</strong>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.marketing.analytics') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-chart-line fa-2x mb-2"></i><br>
                                <strong>التحليلات والتقارير</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

