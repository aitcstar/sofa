@extends('admin.layouts.app')

@section('title', 'لوحة التحكم المحسنة')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt"></i>
            لوحة التحكم المحسنة
        </h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> تحديث
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                        id="periodDropdown" data-toggle="dropdown">
                    <i class="fas fa-calendar"></i> آخر 30 يوم
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="changePeriod(7)">آخر 7 أيام</a>
                    <a class="dropdown-item" href="#" onclick="changePeriod(30)">آخر 30 يوم</a>
                    <a class="dropdown-item" href="#" onclick="changePeriod(90)">آخر 90 يوم</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row mb-4">
        <!-- Total Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الطلبات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['orders']['total']) }}</div>
                            <div class="text-xs text-muted">
                                اليوم: {{ $stats['orders']['today'] }} | 
                                الشهر: {{ $stats['orders']['month'] }}
                                @if($stats['orders']['growth'] != 0)
                                    <span class="text-{{ $stats['orders']['growth'] > 0 ? 'success' : 'danger' }}">
                                        ({{ $stats['orders']['growth'] > 0 ? '+' : '' }}{{ $stats['orders']['growth'] }}%)
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                إجمالي الإيرادات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['revenue']['total'], 2) }} ر.س</div>
                            <div class="text-xs text-muted">
                                اليوم: {{ number_format($stats['revenue']['today'], 2) }} ر.س | 
                                الشهر: {{ number_format($stats['revenue']['month'], 2) }} ر.س
                                @if($stats['revenue']['growth'] != 0)
                                    <span class="text-{{ $stats['revenue']['growth'] > 0 ? 'success' : 'danger' }}">
                                        ({{ $stats['revenue']['growth'] > 0 ? '+' : '' }}{{ $stats['revenue']['growth'] }}%)
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                طلبات في الانتظار
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['orders']['pending'] }}</div>
                            <div class="text-xs text-muted">
                                مؤكدة: {{ $stats['orders']['confirmed'] }} | 
                                قيد التنفيذ: {{ $stats['orders']['processing'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                طلبات متأخرة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['orders']['overdue'] }}</div>
                            <div class="text-xs text-muted">
                                تحتاج متابعة فورية
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                معدل الإنجاز
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $stats['performance']['completion_rate'] }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: {{ $stats['performance']['completion_rate'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                التسليم في الوقت المحدد
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $stats['performance']['on_time_delivery'] }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $stats['performance']['on_time_delivery'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                متوسط قيمة الطلب
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['revenue']['average_order'], 2) }} ر.س</div>
                            <div class="text-xs text-muted">
                                العملاء: {{ $stats['customers']['total'] }} | 
                                جدد: {{ $stats['customers']['new_this_month'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">الإيرادات اليومية</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="exportChart('revenue')">تصدير البيانات</a>
                            <a class="dropdown-item" href="#" onclick="printChart('revenue')">طباعة</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع حالات الطلبات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($statusDistribution as $status => $count)
                            <span class="mr-2">
                                <i class="fas fa-circle text-{{ 
                                    $status === 'pending' ? 'warning' : 
                                    ($status === 'confirmed' ? 'info' : 
                                    ($status === 'processing' ? 'primary' : 
                                    ($status === 'shipped' ? 'secondary' : 
                                    ($status === 'delivered' ? 'success' : 'danger')))) 
                                }}"></i> {{ ucfirst($status) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث الطلبات</h6>
                    <a href="{{ route('admin.orders.enhanced.index') }}" class="btn btn-primary btn-sm">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.enhanced.show', $order) }}" 
                                               class="text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->status_color }}">
                                                {{ $order->status_text }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($order->total_amount, 2) }} ر.س</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد طلبات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Orders -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">الطلبات المتأخرة</h6>
                    <span class="badge badge-danger">{{ $overdueOrders->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>المسؤول</th>
                                    <th>أيام التأخير</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overdueOrders as $order)
                                    <tr class="table-warning">
                                        <td>
                                            <a href="{{ route('admin.orders.enhanced.show', $order) }}" 
                                               class="text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ $order->assignedEmployee->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="badge badge-danger">
                                                {{ abs($order->getDaysUntilDelivery()) }} يوم
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="quickAction({{ $order->id }}, 'follow_up')">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-success">
                                            <i class="fas fa-check-circle"></i> لا توجد طلبات متأخرة
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Row -->
    <div class="row">
        <!-- Top Packages -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل الباكجات</h6>
                </div>
                <div class="card-body">
                    @forelse($topPackages as $package)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ $package['name'] }}</strong><br>
                                <small class="text-muted">{{ $package['orders_count'] }} طلب</small>
                            </div>
                            <div class="text-right">
                                <strong>{{ number_format($package['revenue'], 2) }} ر.س</strong>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @empty
                        <p class="text-muted text-center">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Employee Performance -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أداء الموظفين</h6>
                </div>
                <div class="card-body">
                    @forelse($employeePerformance as $employee)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>{{ $employee['name'] }}</strong>
                                <span class="badge badge-{{ $employee['completion_rate'] >= 80 ? 'success' : ($employee['completion_rate'] >= 60 ? 'warning' : 'danger') }}">
                                    {{ $employee['completion_rate'] }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-{{ $employee['completion_rate'] >= 80 ? 'success' : ($employee['completion_rate'] >= 60 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $employee['completion_rate'] }}%"></div>
                            </div>
                            <small class="text-muted">
                                {{ $employee['assigned_orders'] }} طلب | 
                                {{ $employee['completed_orders'] }} مكتمل
                                @if($employee['overdue_orders'] > 0)
                                    | <span class="text-danger">{{ $employee['overdue_orders'] }} متأخر</span>
                                @endif
                            </small>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @empty
                        <p class="text-muted text-center">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث الإشعارات</h6>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary btn-sm">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @forelse($recentNotifications->take(5) as $notification)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="{{ $notification->channel_icon }} text-{{ $notification->priority_color }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong>{{ $notification->title }}</strong><br>
                                <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small><br>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @empty
                        <p class="text-muted text-center">لا توجد إشعارات</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($revenueData, 'date')),
        datasets: [{
            label: 'الإيرادات (ر.س)',
            data: @json(array_column($revenueData, 'revenue')),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3
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
                    callback: function(value) {
                        return value.toLocaleString() + ' ر.س';
                    }
                }
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(@json($statusDistribution)),
        datasets: [{
            data: Object.values(@json($statusDistribution)),
            backgroundColor: [
                '#f6c23e', // pending - warning
                '#36b9cc', // confirmed - info
                '#4e73df', // processing - primary
                '#6c757d', // shipped - secondary
                '#1cc88a', // delivered - success
                '#e74a3b'  // cancelled - danger
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Dashboard Functions
function refreshDashboard() {
    location.reload();
}

function changePeriod(days) {
    // Update charts with new period
    fetch(`{{ route('admin.dashboard.charts') }}?period=${days}`)
        .then(response => response.json())
        .then(data => {
            // Update charts with new data
            updateCharts(data);
        });
}

function updateCharts(data) {
    // Update revenue chart
    revenueChart.data.labels = data.revenue.map(item => item.date);
    revenueChart.data.datasets[0].data = data.revenue.map(item => item.revenue);
    revenueChart.update();

    // Update status chart
    statusChart.data.labels = Object.keys(data.status_distribution);
    statusChart.data.datasets[0].data = Object.values(data.status_distribution);
    statusChart.update();
}

function quickAction(orderId, action) {
    // Handle quick actions on orders
    switch(action) {
        case 'follow_up':
            // Open follow-up modal or redirect
            window.open(`{{ route('admin.orders.enhanced.show', '') }}/${orderId}`, '_blank');
            break;
        default:
            console.log('Unknown action:', action);
    }
}

function exportChart(chartType) {
    // Export chart data
    console.log('Exporting chart:', chartType);
}

function printChart(chartType) {
    // Print chart
    window.print();
}

// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    fetch('{{ route("admin.dashboard.widgets") }}')
        .then(response => response.json())
        .then(data => {
            // Update widget counters
            updateWidgets(data);
        });
}, 300000); // 5 minutes

function updateWidgets(data) {
    // Update statistics without full page reload
    // This would update the numbers in the cards
}

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
