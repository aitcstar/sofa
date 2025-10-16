@extends('admin.layouts.app')

@section('title', 'تقارير الطلبات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تقارير الطلبات</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.enhanced.index') }}">الطلبات</a></li>
                    <li class="breadcrumb-item active">التقارير</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                <i class="fas fa-file-excel me-2"></i>تصدير Excel
            </button>
            <!--<button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf me-2"></i>تصدير PDF
            </button>-->
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>تصفية التقارير</h5>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" id="dateFrom" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" id="dateTo" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select" id="statusFilter">
                            <option value="">الكل</option>
                            <option value="pending">قيد الانتظار</option>
                            <option value="in_progress">قيد التنفيذ</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">حالة الدفع</label>
                        <select name="payment_status" class="form-select" id="paymentStatusFilter">
                            <option value="">الكل</option>
                            <option value="pending">معلق</option>
                            <option value="partial">دفعة أولى</option>
                            <option value="paid">مدفوع</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-search me-2"></i>تطبيق الفلتر
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">إجمالي الطلبات</h6>
                            <h2 class="text-white-50 mb-0">{{ $stats['total_orders'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-shopping-cart"></i>
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
                            <h6 class="text-white-50 mb-1">الطلبات المكتملة</h6>
                            <h2 class="mb-0">{{ $stats['completed_orders'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-check-circle"></i>
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
                            <h6 class="text-white-50 mb-1">قيد التنفيذ</h6>
                            <h2 class="mb-0">{{ $stats['in_progress_orders'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-spinner"></i>
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
                            <h6 class="text-white-50 mb-1">إجمالي الإيرادات</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</h2>
                            <small>ريال</small>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>توزيع الطلبات حسب الحالة</h5>
                </div>
                <div class="card-body">
                    <canvas id="ordersStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>الطلبات الشهرية</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyOrdersChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>الإيرادات الشهرية</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>حالة الدفع</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>أنواع المشاريع</h5>
                </div>
                <div class="card-body">
                    <canvas id="projectTypesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>الباقات الأكثر طلباً</h5>
                </div>
                <div class="card-body">
                    <canvas id="topPackagesChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>تفاصيل الطلبات</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="ordersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>الباقة</th>
                            <th>الحالة</th>
                            <th>حالة الدفع</th>
                            <th>المبلغ الإجمالي</th>
                            <th>المبلغ المدفوع</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders ?? [] as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->package->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}">
                                    {{ $order->status_text }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status_color }}">
                                    {{ $order->payment_status_text }}
                                </span>
                            </td>
                            <td>{{ number_format($order->total_amount, 2) }} ريال</td>
                            <td>{{ number_format($order->paid_amount, 2) }} ريال</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.enhanced.show', $order->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">لا توجد بيانات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>متوسط وقت التنفيذ</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary">{{ $stats['avg_completion_time'] ?? 0 }}</h2>
                    <p class="text-muted mb-0">يوم</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>معدل الإنجاز</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success">{{ number_format($stats['completion_rate'] ?? 0, 1) }}%</h2>
                    <p class="text-muted mb-0">من إجمالي الطلبات</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>متوسط قيمة الطلب</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-warning">{{ number_format($stats['avg_order_value'] ?? 0, 0) }}</h2>
                    <p class="text-muted mb-0">ريال</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Chart colors
    const chartColors = {
        primary: '#0d6efd',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#0dcaf0',
        secondary: '#6c757d'
    };

    // Orders Status Chart
    const ordersStatusCtx = document.getElementById('ordersStatusChart').getContext('2d');
    new Chart(ordersStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['قيد الانتظار', 'قيد التنفيذ', 'مكتمل', 'ملغي'],
            datasets: [{
                data: [
                    {{ $chartData['status']['pending'] ?? 0 }},
                    {{ $chartData['status']['in_progress'] ?? 0 }},
                    {{ $chartData['status']['completed'] ?? 0 }},
                    {{ $chartData['status']['cancelled'] ?? 0 }}
                ],
                backgroundColor: [
                    chartColors.warning,
                    chartColors.info,
                    chartColors.success,
                    chartColors.danger
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

    // Monthly Orders Chart
    const monthlyOrdersCtx = document.getElementById('monthlyOrdersChart').getContext('2d');
    new Chart(monthlyOrdersCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['monthly']['labels'] ?? []) !!},
            datasets: [{
                label: 'عدد الطلبات',
                data: {!! json_encode($chartData['monthly']['orders'] ?? []) !!},
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

    // Monthly Revenue Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyRevenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['monthly']['labels'] ?? []) !!},
            datasets: [{
                label: 'الإيرادات (ريال)',
                data: {!! json_encode($chartData['monthly']['revenue'] ?? []) !!},
                borderColor: chartColors.success,
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
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
                    beginAtZero: true
                }
            }
        }
    });

    // Payment Status Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentStatusCtx, {
        type: 'pie',
        data: {
            labels: ['معلق', 'دفعة أولى', 'مدفوع'],
            datasets: [{
                data: [
                    {{ $chartData['payment_status']['pending'] ?? 0 }},
                    {{ $chartData['payment_status']['partial'] ?? 0 }},
                    {{ $chartData['payment_status']['paid'] ?? 0 }}
                ],
                backgroundColor: [
                    chartColors.warning,
                    chartColors.info,
                    chartColors.success
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

    // Project Types Chart
    const projectTypesCtx = document.getElementById('projectTypesChart').getContext('2d');
    new Chart(projectTypesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['project_types']['labels'] ?? []) !!},
            datasets: [{
                label: 'عدد المشاريع',
                data: {!! json_encode($chartData['project_types']['data'] ?? []) !!},
                backgroundColor: chartColors.info
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Top Packages Chart
    const topPackagesCtx = document.getElementById('topPackagesChart').getContext('2d');
    new Chart(topPackagesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['top_packages']['labels'] ?? []) !!},
            datasets: [{
                label: 'عدد الطلبات',
                data: {!! json_encode($chartData['top_packages']['data'] ?? []) !!},
                backgroundColor: chartColors.success
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Filter Functions
    function applyFilters() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route("admin.orders.enhanced.reports") }}?' + params.toString();
    }

    function resetFilters() {
        window.location.href = '{{ route("admin.orders.enhanced.reports") }}';
    }

    // Export Functions
    function exportReport(format) {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.append('export', format);

    // استخدم الاسم الصحيح للـ route
    window.location.href = '{{ route("admin.orders.enhanced.export") }}?' + params.toString();
}

</script>
@endpush

