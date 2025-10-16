@extends('admin.layouts.app')

@section('title', 'سجل الأنشطة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">سجل الأنشطة</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                    <li class="breadcrumb-item active">الأنشطة</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">إجمالي الأنشطة</h6>
                            <h2 style="color: #84909f;">{{ $stats['total_activities'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-tasks"></i>
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
                            <h6 class="text-white-50">مكالمات</h6>
                            <h2>{{ $stats['calls'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-phone"></i>
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
                            <h6 class="text-white-50">اجتماعات</h6>
                            <h2>{{ $stats['meetings'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-users"></i>
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
                            <h6 class="text-white-50">رسائل بريد</h6>
                            <h2>{{ $stats['emails'] ?? 0 }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities List -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>آخر الأنشطة</h5>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.crm.activities') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <select name="activity_type" class="form-select" onchange="this.form.submit()">
                            <option value="">جميع الأنواع</option>
                            <option value="call" {{ request('activity_type') == 'call' ? 'selected' : '' }}>مكالمة</option>
                            <option value="email" {{ request('activity_type') == 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                            <option value="meeting" {{ request('activity_type') == 'meeting' ? 'selected' : '' }}>اجتماع</option>
                            <option value="note" {{ request('activity_type') == 'note' ? 'selected' : '' }}>ملاحظة</option>
                            <option value="other" {{ request('activity_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control" placeholder="من تاريخ"
                               value="{{ request('date_from') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ"
                               value="{{ request('date_to') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.crm.activities') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>

            <!-- Activities Timeline -->
            <div class="timeline">
                @forelse($activities ?? [] as $activity)
                <div class="timeline-item mb-4">
                    <div class="timeline-marker">
                        <i class="fas fa-{{ $activity->activity_icon ?? 'circle' }} text-primary"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge bg-{{ $activity->type_color ?? 'secondary' }}">
                                                {{ $activity->activity_type_text ?? $activity->activity_type }}
                                            </span>
                                            @if($activity->lead)
                                            <a href="{{ route('admin.crm.leads.show', $activity->lead_id) }}" class="text-decoration-none">
                                                {{ $activity->lead->name }}
                                            </a>
                                            @endif
                                        </h6>
                                        <p class="mb-0">{{ $activity->description }}</p>
                                    </div>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                @if($activity->user)
                                <div class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <small>{{ $activity->user->name }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>لا توجد أنشطة مسجلة</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if(isset($activities) && $activities->hasPages())
            <div class="mt-4">
                {{ $activities->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        position: relative;
        padding-right: 40px;
    }

    .timeline-marker {
        position: absolute;
        right: 0;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #4e73df;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        right: 14px;
        top: 30px;
        bottom: -20px;
        width: 2px;
        background-color: #e3e6f0;
    }

    .timeline-content {
        padding-right: 20px;
    }
</style>
@endpush
@endsection

