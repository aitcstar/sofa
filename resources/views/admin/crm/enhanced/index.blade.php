@extends('admin.layouts.app')

@section('title', 'لوحة تحكم CRM')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">لوحة تحكم CRM</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active">CRM</li>
                </ol>
            </nav>
        </div>
        <div>
            @if($user && ($user->hasPermission('crm.leads.create') || $user->role === 'admin'))
            <a href="{{ route('admin.crm.leads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة عميل محتمل
            </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-white-50 mb-1">إجمالي العملاء</h6>
                        <h2 class="mb-0" style="color: #84909f;">{{ $stats['total_leads'] }}</h2>
                        <div class="fs-1 mt-2">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-white-50 mb-1">عملاء جدد</h6>
                        <h2 class="mb-0">{{ $stats['new_leads'] }}</h2>
                        <div class="fs-1 mt-2">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-white-50 mb-1">تم التواصل</h6>
                        <h2 class="mb-0">{{ $stats['contacted_leads'] }}</h2>
                        <div class="fs-1 mt-2">
                            <i class="fas fa-phone"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-white-50 mb-1">مهتمين</h6>
                        <h2 class="mb-0">{{ $stats['interested_leads'] }}</h2>
                        <div class="fs-1 mt-2">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-white-50 mb-1">تم التحويل</h6>
                        <h2 class="mb-0"  style="color: #c6c7c8;">{{ $stats['converted_leads'] }}</h2>
                        <div class="fs-1 mt-2">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card border-success">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-muted mb-1">معدل التحويل</h6>
                        <h2 class="mb-0 text-success">{{ number_format($stats['conversion_rate'], 1) }}%</h2>
                        <div class="fs-1 mt-2 text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #07203e;"><i class="fas fa-chart-pie me-2"></i>توزيع حالة العملاء المحتملين</h5>
                </div>
                <div class="card-body">
                    <canvas id="leadStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"  style="color: #07203e;"><i class="fas fa-chart-bar me-2"></i>مصادر العملاء المحتملين</h5>
                </div>
                <div class="card-body">
                    <canvas id="leadSourceChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Leads Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"  style="color: #07203e;"><i class="fas fa-chart-line me-2"></i>العملاء المحتملين الشهريين</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyLeadsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Leads & Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>أحدث العملاء المحتملين</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>الشركة</th>
                                    <th>الهاتف</th>
                                    <th>المصدر</th>
                                    <th>الحالة</th>
                                    <th>المسؤول</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                <tr>
                                    <td><strong>{{ $lead->name }}</strong></td>
                                    <td>{{ $lead->company ?? '-' }}</td>
                                    <td>{{ $lead->phone }}</td>
                                    <td>
                                        @php
                                            $sources = [
                                                'website' => 'الموقع',
                                                'phone' => 'هاتف',
                                                'email' => 'بريد',
                                                'social_media' => 'سوشيال ميديا',
                                                'referral' => 'إحالة',
                                                'other' => 'أخرى'
                                            ];
                                        @endphp
                                        <span class="badge bg-secondary">{{ $sources[$lead->source] ?? $lead->source }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'new' => 'info',
                                                'contacted' => 'warning',
                                                'interested' => 'success',
                                                'not_interested' => 'danger',
                                                'converted' => 'dark'
                                            ];
                                            $statusTexts = [
                                                'new' => 'جديد',
                                                'contacted' => 'تم التواصل',
                                                'interested' => 'مهتم',
                                                'not_interested' => 'غير مهتم',
                                                'converted' => 'تم التحويل'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                            {{ $statusTexts[$lead->status] ?? $lead->status }}
                                        </span>
                                    </td>
                                    <td>{{ $lead->assignedTo->name ?? '-' }}</td>
                                    <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($user && ($user->hasPermission('crm.leads.view') || $user->role === 'admin'))
                                        <a href="{{ route('admin.crm.leads.show', $lead->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">لا توجد عملاء محتملين</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض جميع العملاء المحتملين <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user && ($user->hasPermission('crm.leads.create') || $user->role === 'admin'))
                        <a href="{{ route('admin.crm.leads.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>إضافة عميل محتمل
                        </a>
                        @endif

                        @if($user && ($user->hasPermission('crm.quotes.create') || $user->role === 'admin'))
                        <a href="{{ route('admin.crm.quotes.create') }}" class="btn btn-success">
                            <i class="fas fa-file-invoice me-2"></i>إنشاء عرض سعر
                        </a>
                        @endif

                        @if($user && ($user->hasPermission('crm.funnel') || $user->role === 'admin'))
                        <a href="{{ route('admin.crm.funnel') }}" class="btn btn-info">
                            <i class="fas fa-filter me-2"></i> المبيعات
                        </a>
                        @endif

                        @if($user && ($user->hasPermission('crm.activities') || $user->role === 'admin'))
                        <a href="{{ route('admin.crm.activities') }}" class="btn btn-warning">
                            <i class="fas fa-history me-2"></i>سجل الأنشطة
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>المهام المعلقة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>متابعة عملاء جدد</span>
                                <span class="badge bg-info">{{ $stats['new_leads'] }}</span>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>عروض أسعار معلقة</span>
                                <span class="badge bg-warning">{{ $stats['pending_quotes'] }}</span>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>عملاء مهتمين</span>
                                <span class="badge bg-success">{{ $stats['interested_leads'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const chartColors = {
        primary: '#0d6efd',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#0dcaf0',
        secondary: '#6c757d',
        dark: '#212529'
    };

    // Lead Status Chart
    const leadStatusCtx = document.getElementById('leadStatusChart').getContext('2d');
    new Chart(leadStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['جديد', 'تم التواصل', 'مهتم', 'غير مهتم', 'تم التحويل'],
            datasets: [{
                data: [
                    {{ $chartData['status']['new'] ?? 0 }},
                    {{ $chartData['status']['contacted'] ?? 0 }},
                    {{ $chartData['status']['interested'] ?? 0 }},
                    {{ $chartData['status']['not_interested'] ?? 0 }},
                    {{ $chartData['status']['converted'] ?? 0 }}
                ],
                backgroundColor: [
                    chartColors.info,
                    chartColors.warning,
                    chartColors.success,
                    chartColors.danger,
                    chartColors.dark
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Lead Source Chart
    const leadSourceCtx = document.getElementById('leadSourceChart').getContext('2d');
    const sourceData = @json($chartData['source'] ?? []);
    const sourceLabels = Object.keys(sourceData);
    const sourceValues = Object.values(sourceData);

    new Chart(leadSourceCtx, {
        type: 'bar',
        data: {
            labels: sourceLabels,
            datasets: [{
                label: 'عدد العملاء',
                data: sourceValues,
                backgroundColor: chartColors.primary
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Monthly Leads Chart
    const monthlyLeadsCtx = document.getElementById('monthlyLeadsChart').getContext('2d');
    new Chart(monthlyLeadsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['monthly']['labels'] ?? []) !!},
            datasets: [{
                label: 'عدد العملاء المحتملين',
                data: {!! json_encode($chartData['monthly']['leads'] ?? []) !!},
                borderColor: chartColors.primary,
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush

