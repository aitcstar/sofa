@extends('admin.layouts.app')

@section('title', 'تحليلات الأمان')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تحليلات الأمان</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.security.index') }}">الأمان والحماية</a></li>
                    <li class="breadcrumb-item active">التحليلات</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h6>إجمالي السجلات</h6>
                <h3 class="fw-bold text-primary">{{ $summary['total_logs'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h6 class="text-danger">الأحداث الحرجة</h6>
                <h3 class="fw-bold text-danger">{{ $summary['critical_alerts'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h6 class="text-warning">التحذيرات</h6>
                <h3 class="fw-bold text-warning">{{ $summary['warnings'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
                <h6 class="text-success">الأحداث الآمنة</h6>
                <h3 class="fw-bold text-success">{{ $summary['safe_events'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Security Report -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>نظرة عامة على الأمان</h5>
        </div>
        <div class="card-body">
            @if(!empty($securityReport['event_distribution']))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>نوع الحدث</th>
                            <th>عدد السجلات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($securityReport['event_distribution'] as $event)
                            <tr>
                                <td>{{ $event->event_type ?? '-' }}</td>
                                <td>{{ $event->count ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted mb-0">لا توجد بيانات تحليلية متاحة حاليًا.</p>
            @endif
        </div>
    </div>

    <!-- Failed Login Attempts -->
    <div class="card shadow mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-user-lock me-2"></i>آخر محاولات تسجيل الدخول الفاشلة</h5>
        </div>
        <div class="card-body">
            @if($failedAttempts->count())
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>عنوان IP</th>
                            <th>الموقع التقريبي</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($failedAttempts as $attempt)
                            <tr>
                                <td>{{ $attempt->username ?? 'غير معروف' }}</td>
                                <td>{{ $attempt->ip_address ?? '-' }}</td>
                                <td>{{ $attempt->location ?? '-' }}</td>
                                <td>{{ $attempt->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">لا توجد محاولات فاشلة مؤخرًا.</p>
            @endif
        </div>
    </div>

    <!-- IP Analysis -->
    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-network-wired me-2"></i>تحليل عناوين IP</h5>
        </div>
        <div class="card-body">
            @if(!empty($ipAnalysis) && count($ipAnalysis))
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>عنوان IP</th>
                            <th>عدد المحاولات</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ipAnalysis as $ipAddress => $data)
    <tr>
        <td>{{ $ipAddress }}</td>
        <td>{{ $data['attempts'] ?? 0 }}</td>
        <td>
            @if(($data['status'] ?? '') === 'critical')
                <span class="badge bg-danger">حرج</span>
            @elseif(($data['status'] ?? '') === 'warning')
                <span class="badge bg-warning text-dark">تحذير</span>
            @else
                <span class="badge bg-success">آمن</span>
            @endif
        </td>
    </tr>
@endforeach


                    </tbody>
                </table>
            @else
                <p class="text-muted">لا توجد بيانات تحليل لعناوين IP خلال الفترة المحددة.</p>
            @endif
        </div>
    </div>

    <!-- Critical Events -->
    <div class="card shadow mb-5">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>الأحداث الحرجة الأخيرة</h5>
        </div>
        <div class="card-body">
            @if($criticalEvents->count())
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>نوع الحدث</th>
                            <th>المستخدم</th>
                            <th>المستوى</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criticalEvents as $event)
                            <tr>
                                <td>{{ $event->event_type ?? '-' }}</td>
                                <td>{{ $event->user->name ?? 'غير معروف' }}</td>
                                <td><span class="badge bg-danger">{{ $event->risk_level ?? 'غير محدد' }}</span></td>
                                <td>{{ $event->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">لا توجد أحداث حرجة حديثة.</p>
            @endif
        </div>
    </div>
</div>
@endsection
