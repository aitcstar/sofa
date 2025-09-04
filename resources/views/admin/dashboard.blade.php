@extends('admin.layouts.app')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">لوحة التحكم الرئيسية</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <span class="badge bg-primary">مرحباً {{ auth()->user()->name }}</span>
        </div>
    </div>
</div>

<div class="row">
    <!-- عدد الطلبات -->
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h5 class="text-primary">طلبات اليوم</h5>
                <h2 class="text-dark">{{ $today_orders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h5 class="text-primary">طلبات الشهر</h5>
                <h2 class="text-dark">{{ $month_orders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <h5 class="text-primary">طلبات السنة</h5>
                <h2 class="text-dark">{{ $year_orders }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- أكثر الباكجات مبيعاً -->
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-header bg-primary text-white">أكثر الباكجات مبيعاً</div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($top_packages as $pkg)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $pkg->name }}
                    <span class="badge bg-secondary rounded-pill">{{ $pkg->orders_count }} طلب</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- العملاء الجدد -->
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-body text-center">
        <h5 class="text-primary">عملاء جدد (آخر 7 أيام)</h5>
        <h2 class="text-dark">{{ $new_users }}</h2>
    </div>
</div>

<!-- نسبة الإنجاز -->
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-body text-center">
        <h5 class="text-primary">نسبة الإنجاز</h5>
        <h2 class="text-dark">{{ $completion_rate }}%</h2>
    </div>
</div>

<br>
<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                    <p class="mb-0">إجمالي العملاء</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                    <p class="mb-0">إجمالي المنتجات</p>
                </div>
                <div class="icon">
                    <i class="fas fa-couch"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                    <p class="mb-0">إجمالي الطلبات</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="mb-0">{{ number_format($stats['total_sales'], 2) }} ر.س</h3>
                    <p class="mb-0">إجمالي المبيعات</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">الطلبات المعلقة</h5>
                <h2 class="text-warning">0</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm">عرض الطلبات</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">التصنيفات</h5>
                <h2 class="text-dark">{{ $stats['total_categories'] }}</h2>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary btn-sm">إدارة التصنيفات</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">رسائل جديدة</h5>
                <h2 class="text-dark">{{ $stats['new_contacts'] }}</h2>
                <a href="#" class="btn btn-outline-primary btn-sm">عرض الرسائل</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders and Users -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">أحدث الطلبات</h5>
            </div>
            <div class="card-body">
                @if($recent_orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? '-' }}</td>
                                <td>{{ number_format($order->total_amount, 2) }} ر.س</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center">لا توجد طلبات حتى الآن</p>
            @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">أحدث العملاء</h5>
            </div>
            <div class="card-body">
                @if($recent_users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>تاريخ التسجيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">لا يوجد عملاء حتى الآن</p>
                @endif
            </div>
        </div>
    </div>
</div>

<br>
<!-- Charts -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">المبيعات (آخر 6 شهور)</div>
            <div class="card-body">
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">حالات الطلبات</div>
            <div class="card-body">
                <canvas id="ordersStatusChart" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    :root {
        --primary-dark: #08203e;
        --primary-light: #33415c;
        --secondary: #ad996f;
        --accent: #979dac;
        --light-bg: #f8f9fa;
    }
    .h1, h2, h3, h4, h5, h6 {
        color: #ffffff;
        font-weight: 700;
    }
    .stats-card {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-card .icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .badge.bg-primary {
        background-color: var(--primary-dark) !important;
    }

    .border-bottom {
        border-bottom: 2px solid var(--secondary) !important;
    }

    .btn-outline-primary {
        color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    .text-primary {
        color: var(--primary-dark) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart المبيعات
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($salesMonths) !!},
            datasets: [{
                label: 'المبيعات',
                data: {!! json_encode($salesData) !!},
                backgroundColor: 'rgba(8, 32, 62, 0.7)',
                borderColor: 'rgba(8, 32, 62, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value + " ر.س"; }
                    }
                }
            }
        }
    });

    // Chart حالات الطلبات
    const ordersCtx = document.getElementById('ordersStatusChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['جديد', 'مؤكد', 'قيد التنفيذ', 'تم الشحن', 'تم التسليم', 'مؤرشف'],
            datasets: [{
                data: {!! json_encode(array_values($ordersStatusData)) !!},
                backgroundColor: [
                    '#08203e', // جديد
                    '#33415c', // مؤكد
                    '#ad996f', // قيد التنفيذ
                    '#979dac', // تم الشحن
                    '#5c7c9e', // تم التسليم
                    '#3d5066'  // مؤرشف
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush
