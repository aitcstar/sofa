@extends('admin.layouts.app')

@section('title', 'لوحة التسويق')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">لوحة التسويق</h2>
        <div>
            <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-bullhorn"></i> حملة جديدة
            </a>
            <a href="{{ route('admin.marketing.coupons.create') }}" class="btn btn-success">
                <i class="fas fa-ticket-alt"></i> كوبون جديد
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
                            <h6 class="text-white-50 mb-1">إجمالي الحملات</h6>
                            <h2 class="mb-0">{{ $stats['total_campaigns'] }}</h2>
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
                            <h6 class="text-white-50 mb-1">حملات نشطة</h6>
                            <h2 class="mb-0">{{ $stats['active_campaigns'] }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-chart-line"></i>
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
                            <h6 class="text-white-50 mb-1">إجمالي الكوبونات</h6>
                            <h2 class="mb-0">{{ $stats['total_coupons'] }}</h2>
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
                            <h6 class="text-white-50 mb-1">كوبونات نشطة</h6>
                            <h2 class="mb-0">{{ $stats['active_coupons'] }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Campaigns -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أحدث الحملات</h5>
                    <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>اسم الحملة</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>تاريخ البدء</th>
                                    <th>الأداء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCampaigns as $campaign)
                                <tr>
                                    <td>
                                        <strong>{{ $campaign->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $campaign->type }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'active' => 'success',
                                                'paused' => 'warning',
                                                'completed' => 'info',
                                            ];
                                            $statusLabels = [
                                                'draft' => 'مسودة',
                                                'active' => 'نشط',
                                                'paused' => 'متوقف',
                                                'completed' => 'مكتمل',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$campaign->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$campaign->status] ?? $campaign->status }}
                                        </span>
                                    </td>
                                    <td>{{ $campaign->start_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($campaign->tracking->count() > 0)
                                            @php
                                                $clicks = $campaign->tracking->sum('clicks');
                                                $impressions = $campaign->tracking->sum('impressions');
                                                $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;
                                            @endphp
                                            <small>CTR: {{ $ctr }}%</small>
                                        @else
                                            <small class="text-muted">لا توجد بيانات</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">لا توجد حملات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Coupons -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أكثر الكوبونات استخداماً</h5>
                    <a href="{{ route('admin.marketing.coupons.index') }}" class="btn btn-sm btn-outline-success">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($topCoupons as $coupon)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $coupon->code }}</strong>
                                    <small class="text-muted d-block">
                                        @if($coupon->type === 'percentage')
                                            {{ $coupon->value }}% خصم
                                        @else
                                            {{ $coupon->value }} ريال
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-primary">
                                    {{ $coupon->usages_count }} استخدام
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center mb-0">لا توجد كوبونات</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">روابط سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-bullhorn"></i> إدارة الحملات
                        </a>
                        <a href="{{ route('admin.marketing.coupons.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-ticket-alt"></i> إدارة الكوبونات
                        </a>
                        <a href="{{ route('admin.marketing.analytics') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-bar"></i> تحليلات التسويق
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

