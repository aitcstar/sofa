@extends('admin.layouts.app')

@section('title', 'تفاصيل العميل المحتمل - ' . $lead->name)

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تفاصيل العميل المحتمل</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.leads.index') }}">العملاء المحتملون</a></li>
                    <li class="breadcrumb-item active">{{ $lead->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
            @if($user && ($user->hasPermission('crm.leads.edit') || $user->role === 'admin'))
            <a href="{{ route('admin.crm.leads.edit', $lead->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>تعديل
            </a>
            @endif
        </div>
    </div>

    <!-- Status Badges -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-{{ $lead->status_color ?? 'secondary' }} fs-6 me-2">
                                <i class="fas fa-{{ $lead->status_icon ?? 'circle' }} me-1"></i>
                                {{ $lead->status_text ?? $lead->status }}
                            </span>
                            @if($lead->priority)
                            <span class="badge bg-{{ $lead->priority_color ?? 'secondary' }} fs-6 me-2">
                                <i class="fas fa-flag me-1"></i>
                                {{ $lead->priority_text ?? $lead->priority }}
                            </span>
                            @endif
                        </div>
                        <div class="text-end">
                            <small class="text-muted">تاريخ الإنشاء:</small>
                            <strong>{{ $lead->created_at->format('Y-m-d H:i A') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lead Information -->
        <div class="col-lg-8">
            <!-- Basic Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>معلومات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-user me-2"></i>الاسم:</td>
                                    <td><strong>{{ $lead->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-envelope me-2"></i>البريد:</td>
                                    <td><strong>{{ $lead->email }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-phone me-2"></i>الهاتف:</td>
                                    <td><strong>{{ $lead->phone }}</strong></td>
                                </tr>
                                @if($lead->company)
                                <tr>
                                    <td class="text-muted"><i class="fas fa-building me-2"></i>الشركة:</td>
                                    <td><strong>{{ $lead->company }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-source me-2"></i>المصدر:</td>
                                    <td><strong>{{ $lead->source_text ?? $lead->source }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-project-diagram me-2"></i>نوع المشروع:</td>
                                    <td><strong>{{ $lead->project_type_text ?? $lead->project_type ?? '-' }}</strong></td>
                                </tr>
                                @if($lead->budget_range)
                                <tr>
                                    <td class="text-muted"><i class="fas fa-money-bill me-2"></i>الميزانية:</td>
                                    <td><strong>{{ $lead->budget_range }}</strong></td>
                                </tr>
                                @endif
                                @if($lead->assignedTo)
                                <tr>
                                    <td class="text-muted"><i class="fas fa-user-tie me-2"></i>المسؤول:</td>
                                    <td><strong>{{ $lead->assignedTo->name }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($lead->notes)
                    <div class="mt-3">
                        <h6 class="text-muted"><i class="fas fa-sticky-note me-2"></i>ملاحظات:</h6>
                        <p class="mb-0">{{ $lead->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Activities Timeline -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>سجل الأنشطة</h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                        <i class="fas fa-plus"></i> إضافة نشاط
                    </button>
                </div>
                <div class="card-body">
                    @forelse($lead->activities as $activity)
                    <div class="activity-item mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="fas fa-{{ $activity->activity_icon ?? 'circle' }} text-primary me-2"></i>
                                <strong>{{ $activity->activity_type_text ?? $activity->activity_type }}</strong>
                            </div>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 ms-4">{{ $activity->description }}</p>
                        @if($activity->user)
                        <small class="text-muted ms-4">بواسطة: {{ $activity->user->name }}</small>
                        @endif
                    </div>
                    @empty
                    <p class="text-center text-muted">لا توجد أنشطة مسجلة</p>
                    @endforelse
                </div>
            </div>

            <!-- Quotes -->
            @if($lead->quotes && $lead->quotes->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>عروض الأسعار</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم العرض</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lead->quotes as $quote)
                                <tr>
                                    <td>#{{ $quote->quote_number }}</td>
                                    <td>{{ number_format($quote->total_amount, 2) }} ريال</td>
                                    <td>
                                        <span class="badge bg-{{ $quote->status_color ?? 'secondary' }}">
                                            {{ $quote->status_text ?? $quote->status }}
                                        </span>
                                    </td>
                                    <td>{{ $quote->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($user && ($user->hasPermission('crm.leads.view') || $user->role === 'admin'))
                                        <a href="{{ route('admin.crm.quotes.show', $quote->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user && ($user->hasPermission('crm.activities') || $user->role === 'admin'))
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addActivityModal">
                            <i class="fas fa-plus me-2"></i>إضافة نشاط
                        </button>
                        @endif


                        @if($user && ($user->hasPermission('crm.quotes.create') || $user->role === 'admin'))
                        <a href="{{ route('admin.crm.quotes.create', ['lead_id' => $lead->id]) }}" class="btn btn-outline-success">
                            <i class="fas fa-file-invoice me-2"></i>إنشاء عرض سعر
                        </a>
                        @endif

                        @if($user && ($user->hasPermission('crm.leads.convert') || $user->role === 'admin'))
                        @if($lead->status !== 'converted')
                        <form action="{{ route('admin.crm.leads.convert', $lead->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-info w-100" onclick="return confirm('هل تريد تحويل هذا العميل إلى طلب؟')">
                                <i class="fas fa-exchange-alt me-2"></i>تحويل إلى طلب
                            </button>
                        </form>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">عدد الأنشطة</small>
                        <h4>{{ $lead->activities->count() }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">عدد عروض الأسعار</small>
                        <h4>{{ $lead->quotes->count() }}</h4>
                    </div>
                    @if($lead->last_contact_at)
                    <div class="mb-3">
                        <small class="text-muted">آخر تواصل</small>
                        <p>{{ $lead->last_contact_at->diffForHumans() }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Activity Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addActivityModalLabel">إضافة نشاط جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.crm.leads.add-activity', $lead->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="activity_type" class="form-label">نوع النشاط</label>
                        <select class="form-select" id="activity_type" name="activity_type" required>
                            <option value="call">مكالمة هاتفية</option>
                            <option value="email">بريد إلكتروني</option>
                            <option value="meeting">اجتماع</option>
                            <option value="note">ملاحظة</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

