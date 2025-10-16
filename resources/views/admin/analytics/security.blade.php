@extends('admin.layouts.app')

@section('title', 'مراقبة الأمان')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">🔒 مراقبة الأمان</h1>

    <div class="alert alert-info">
        <strong>تنبيه:</strong> هذه الصفحة تعرض محاولات الدخول وأنشطة الأمان في النظام.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">محاولات تسجيل الدخول</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المستخدم</th>
                            <th>IP</th>
                            <th>الموقع</th>
                            <th>الحالة</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginAttempts as $attempt)
                            <tr>
                                <td>{{ $attempt->user->name ?? 'مجهول' }}</td>
                                <td>{{ $attempt->ip_address }}</td>
                                <td>{{ $attempt->location ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $attempt->status == 'success' ? 'success' : 'danger' }}">
                                        {{ $attempt->status == 'success' ? 'نجاح' : 'فشل' }}
                                    </span>
                                </td>
                                <td>{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">لا توجد محاولات مسجلة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <h5>آخر الأنشطة المشبوهة</h5>
    <ul class="list-group">
        @forelse($suspiciousActivities as $activity)
            <li class="list-group-item">
                {{ $activity->description }} — <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
            </li>
        @empty
            <li class="list-group-item text-center">لا توجد أنشطة مشبوهة</li>
        @endforelse
    </ul>
</div>
@endsection
