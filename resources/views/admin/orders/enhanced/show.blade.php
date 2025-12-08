@extends('admin.layouts.app')

@section('title', 'تفاصيل الطلب - ' . $order->order_number)

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تفاصيل الطلب #{{ $order->order_number }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.enhanced.index') }}">الطلبات</a></li>
                    <li class="breadcrumb-item active">تفاصيل الطلب</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.orders.enhanced.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>

        </div>
    </div>

    <!-- Status & Priority Badges -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-{{ $order->status_color }} fs-6 me-2">
                                <i class="fas fa-{{ $order->status_icon }} me-1"></i>
                                {{ $order->status_text }}
                            </span>
                            <span class="badge bg-{{ $order->priority_color }} fs-6 me-2">
                                <i class="fas fa-flag me-1"></i>
                                {{ $order->priority_text }}
                            </span>
                            <span class="badge bg-{{ $order->payment_status_color }} fs-6">
                                <i class="fas fa-{{ $order->payment_status_icon }} me-1"></i>
                                {{ $order->payment_status_text }}
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">تاريخ الإنشاء:</small>
                            <strong>{{ $order->created_at->format('Y-m-d H:i A') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>بيانات العميل</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-user me-2"></i>الاسم:</td>
                                    <td><strong>{{ $order->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-phone me-2"></i>الهاتف:</td>
                                    <td><strong>{{ $order->country_code }} {{ $order->phone }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-envelope me-2"></i>البريد:</td>
                                    <td><strong>{{ $order->email }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-building me-2"></i>نوع العميل:</td>
                                    <td><strong>
                                    @if($order->client_type =="individual")
                                        فرد
                                    @else
                                        شركه
                                    @endif
                                    </strong></td>
                                </tr>
                                @if($order->client_type === 'commercial')
                                <tr>
                                    <td class="text-muted"><i class="fas fa-file-alt me-2"></i>السجل التجاري:</td>
                                    <td><strong>{{ $order->commercial_register ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-receipt me-2"></i>الرقم الضريبي:</td>
                                    <td><strong>{{ $order->tax_number ?? '-' }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>تفاصيل الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- بيانات عامة -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-cube me-2"></i>الباكجات:</td>
                                    <td>
                                        @forelse($order->packages as $package)
                                            <strong>- {{ $package->name_ar ?? $package->name ?? '-' }}</strong><br>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-home me-2"></i>عدد الوحدات:</td>
                                    <td><strong>{{ $order->units_count }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-project-diagram me-2"></i>نوع المشروع:</td>
                                    <td><strong>
                                        @if($order->project_type == "large") مشروع كبير
                                        @elseif ($order->project_type == "medium") مشروع متوسط
                                        @else مشروع صغير
                                        @endif
                                    </strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-layer-group me-2"></i>المرحلة الحالية:</td>
                                    <td><strong>
                                        @if($order->current_stage == "design") مرحلة التصميم
                                        @elseif($order->current_stage == "execution") مرحلة التنفيذ
                                        @else مرحلة التشغيل
                                        @endif
                                    </strong></td>
                                </tr>
                            </table>
                        </div>

                        <!-- المساعدة والألوان -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%"><i class="fas fa-paint-brush me-2"></i>تصميم داخلي:</td>
                                    <td><strong>{{ $order->has_interior_design ? 'نعم' : 'لا' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-tools me-2"></i>مساعدة تشطيب:</td>
                                    <td><strong>{{ $order->needs_finishing_help ? 'نعم' : 'لا' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-palette me-2"></i>مساعدة ألوان:</td>
                                    <td><strong>{{ $order->needs_color_help ? 'نعم' : 'لا' }}</strong></td>
                                </tr>
                                @if($order->colors && count($order->colors) > 0)
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-fill-drip me-2"></i>الألوان المختارة:</td>
                                        <td>
                                            @foreach($order->colors as $color)
                                                <span class="badge" style="background-color: {{ $color }}; color: #fff;">{{ $color }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- المخططات -->
                    @if($order->diagrams_path)
                        <div class="mt-3">
                            <h6 class="text-muted"><i class="fas fa-file-image me-2"></i>المخططات المرفقة:</h6>
                            <a href="{{ asset('storage/' . $order->diagrams_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>تحميل المخططات
                            </a>
                        </div>
                    @endif

                    <!-- الباكجات والوحدات والعناصر -->
                    <div class="mt-4">
                        <h6>الباكجات والوحدات:</h6>
                        @forelse($order->packages as $package)
                            <div class="mb-3">
                                <strong>{{ $package->name_ar ?? $package->name ?? '-' }}</strong>

                                @if($package->packageUnitItems->count() > 0)
                                    @foreach($package->packageUnitItems->groupBy('unit_id') as $unitId => $items)
                                        @php $unit = $items->first()->unit; @endphp
                                        <div class="ms-3 mt-2">
                                            <strong>{{ $unit->name_ar ?? $unit->name ?? '-' }}</strong>
                                            <ul>
                                                @foreach($items as $item)
                                                    <li>
                                                        {{ $item->item->item_name_ar ?? $item->item->name ?? '-' }}
                                                        ({{ $item->item->quantity ?? '-' }}) - لون: {{ $item->item->color_ar ?? '-' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="ms-3 text-muted">لا توجد وحدات لهذا الباكج</div>
                                @endif
                            </div>
                        @empty
                            <div class="text-muted">لا توجد باكجات مرتبطة بالطلب</div>
                        @endforelse
                    </div>
                </div>

            </div>


            <!-- Financial Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>المعلومات المالية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">المبلغ الأساسي:</td>
                                    <td><strong>{{ number_format($order->base_amount ?? 0, 2) }} ريال</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">الضريبة (15%):</td>
                                    <td><strong>{{ number_format($order->tax_amount ?? 0, 2) }} ريال</strong></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-muted"><strong>الإجمالي:</strong></td>
                                    <td><h5 class="text-success mb-0">{{ number_format($order->total_amount, 2) }} ريال</h5></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">المبلغ المدفوع:</td>
                                    <td><strong class="text-success">{{ number_format($order->paid_amount, 2) }} ريال</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">المبلغ المتبقي:</td>
                                    <td><strong class="text-danger">{{ number_format($order->remaining_amount, 2) }} ريال</strong></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-muted"><strong>حالة الدفع:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->payment_status_color }} fs-6">
                                            {{ $order->payment_status_text }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0"><i class="fas fa-calendar-alt me-2"></i>جدول الدفعات:</h6>
                            @if($user && ($user->hasPermission('financial.payments.create') || $user->role === 'admin'))
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentScheduleModal">
                                    <i class="fas fa-plus me-1"></i>إضافة دفعة
                                </button>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="paymentSchedulesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->paymentSchedules as $index => $schedule)
                                    <tr data-schedule-id="{{ $schedule->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ number_format($schedule->amount, 2) }} ريال</td>
                                        <td>{{ $schedule->due_date ? \Carbon\Carbon::parse($schedule->due_date)->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $schedule->status === 'paid' ? 'success' : 'warning' }}">
                                                {{ $schedule->status === 'paid' ? 'مدفوعة' : 'غير مدفوعة' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user && ($user->hasPermission('financial.payments.edit') || $user->role === 'admin'))
                                                @if($schedule->status !== 'paid')
                                                <button type="button" class="btn btn-sm btn-success mark-paid-btn" data-schedule-id="{{ $schedule->id }}">
                                                    <i class="fas fa-check"></i> تحديد كمدفوعة
                                                </button>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger delete-schedule-btn" data-schedule-id="{{ $schedule->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد دفعات مضافة</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>مراحل تنفيذ الطلب</h5>
                </div>
                <div class="card-body">
                    @php
                        // استرجاع المراحل الأساسية مع المراحل الفرعية
                        $stages = \App\Models\OrderStage::whereNull('parent_id')
                                    ->orderBy('order_number')
                                    ->with(['children' => function($q) {
                                        $q->orderBy('order_number');
                                    }])
                                    ->get();

                        // حساب حالة الطلب العامة
                        $allCompleted = $order->stageStatuses()->count() > 0 &&
                                        $order->stageStatuses()->where('status', 'not_started')->count() === 0;
                        $orderStatusLabel = $allCompleted ? 'مكتمل' : 'قيد التنفيذ';
                        $orderStatusBadge = $allCompleted ? 'success' : 'warning';
                    @endphp

                    {{-- عرض حالة الطلب بشكل عام --}}
                    <div class="mb-3">
                        <strong>حالة الطلب:</strong>
                        <span class="badge bg-{{ $orderStatusBadge }}">{{ $orderStatusLabel }}</span>
                    </div>

                    @if($stages->count() > 0)
                    <div class="timeline">
                        @foreach($stages as $stage)
                            @php
                                $status = $order->stageStatuses()->where('order_stage_id', $stage->id)->first();
                                $isCompleted = $status?->status === 'completed';
                                $completedAt = $status?->completed_at;
                            @endphp

                            {{-- المرحلة الأساسية --}}
                            <div class="timeline-item {{ $isCompleted ? 'completed' : 'not_started' }}">
                                <div class="timeline-marker">
                                    <i class="fas fa-{{ $isCompleted ? 'check-circle' : 'circle' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $stage->title_ar }}</h6>
                                        </div>
                                        <div class="text-end">
                                            @if($isCompleted)
                                                <span class="badge bg-success">مكتمل</span>
                                                <br>
                                                <small class="text-muted">{{ optional($completedAt)->format('Y-m-d') }}</small>
                                            @else
                                                <span class="badge bg-warning">قيد التنفيذ</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- المراحل الفرعية --}}
                            @foreach($stage->children as $child)
                                @php
                                    $childStatus = $order->stageStatuses()->where('order_stage_id', $child->id)->first();
                                    $childCompleted = $childStatus?->status === 'completed';
                                    $childCompletedAt = $childStatus?->completed_at;
                                @endphp

                                <div class="timeline-item sub-stage {{ $childCompleted ? 'completed' : 'not_started' }}">
                                    <div class="timeline-marker">
                                        <i class="fas fa-{{ $childCompleted ? 'check-circle' : 'circle' }}"></i>
                                    </div>
                                    <div class="timeline-content ps-4">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $child->title_ar }}</h6>
                                            </div>
                                            <div class="text-end">
                                                @if($childCompleted)
                                                    <span class="badge bg-success">مكتمل</span>
                                                    <br>
                                                    <small class="text-muted">{{ optional($childCompletedAt)->format('Y-m-d') }}</small>
                                                @else
                                                    <span class="badge bg-warning">قيد التنفيذ</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>لم يتم تحديد مراحل لهذا الطلب بعد
                    </div>
                    @endif
                </div>
            </div>

            {{-- CSS بسيط للمراحل الفرعية --}}
            <style>
                .timeline-item.sub-stage .timeline-content {
                    padding-left: 2rem; /* مسافة للمراحل الفرعية */
                    border-left: 2px dashed #ccc; /* خط فرعي */
                    margin-bottom: 1rem;
                }
            </style>




            <!-- Activity Log -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>سجل النشاطات</h5>
                </div>
                <div class="card-body">
                    @if($order->logs && count($order->logs) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="20%">التاريخ</th>
                                    <th width="20%">المستخدم</th>
                                    <th width="20%">النشاط</th>
                                    <th width="40%">التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'النظام' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->action_color }}">{{ $log->action }}</span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>لا توجد نشاطات مسجلة
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user && ($user->hasPermission('orders.status') || $user->role === 'admin'))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                            <i class="fas fa-sync me-2"></i>تحديث الحالة
                        </button>
                        @endif

                        @if($user && ($user->hasPermission('orders.assign') || $user->role === 'admin'))
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignEmployeeModal">
                            <i class="fas fa-user-plus me-2"></i>تعيين موظف
                        </button>
                        @endif

                        @if($user && ($user->hasPermission('orders.note') || $user->role === 'admin'))
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="fas fa-sticky-note me-2"></i>إضافة ملاحظة
                        </button>
                        @endif

                        @if($user && ($user->hasPermission('orders.updatetimeline') || $user->role === 'admin'))
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateTimelineModal">
                            <i class="fas fa-tasks me-2"></i>تحديث المراحل
                        </button>
                        @endif

                        @if($user && ($user->hasPermission('orders.invoice') || $user->role === 'admin'))
                        <a href="{{ route('admin.financial.invoices.create', ['order_id' => $order->id]) }}" class="btn btn-secondary">
                            <i class="fas fa-file-invoice me-2"></i>إنشاء فاتورة
                        </a>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Assigned Employees -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>الموظفين المعينين</h5>
                </div>
                <div class="card-body">
                    @if($order->activeAssignments && count($order->activeAssignments) > 0)
                    <div class="list-group">
                        @foreach($order->activeAssignments as $assignment)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $assignment->user->name }}</h6>
                                    <small class="text-muted">{{ $assignment->user->job_title }}</small>
                                </div>

                                @if($user && ($user->hasPermission('orders.unassignment') || $user->role === 'admin'))
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeAssignment({{ $assignment->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>لم يتم تعيين موظفين بعد
                    </div>
                    @endif
                </div>
            </div>

            <!-- Internal Notes -->

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>الملاحظات الداخلية</h5>
                </div>
                <div class="card-body">
                    @if($order->internal_notes)
                    <div class="alert alert-light">
                        {{ $order->internal_notes }}
                    </div>
                    @else
                    <p class="text-muted mb-0">لا توجد ملاحظات</p>
                    @endif
                </div>
            </div>

            <!-- Related Documents -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>المستندات المرتبطة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @if($order->invoices && count($order->invoices) > 0)
                            @foreach($order->invoices as $invoice)
                            @if($user && ($user->hasPermission('financial.invoices.view') || $user->role === 'admin'))
                                <a href="{{ route('admin.financial.invoices.show', $invoice->id) }}" class="list-group-item list-group-item-action">
                                    <i class="fas fa-file-invoice me-2"></i>فاتورة #{{ $invoice->invoice_number }}
                                </a>
                            @endif
                            @endforeach
                        @else
                            <div class="list-group-item text-muted">لا توجد مستندات</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.enhanced.update-status', $order->id) }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">تحديث حالة الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">

                        <label class="form-label">الحالة الجديدة</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>جديد</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}> تم التاكيد</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}> بالشحن</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}> تم التوصيل</option>
                            <option value="archived" {{ $order->status === 'archived' ? 'selected' : '' }}>مؤرشف</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
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

<!-- Assign Employee Modal -->
<div class="modal fade" id="assignEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.enhanced.assign', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تعيين موظف للطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الموظف</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر موظف</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الدور</label>
                        <select name="role" class="form-select" required>
                            <option value="designer">مصمم</option>
                            <option value="supervisor">مشرف</option>
                            <option value="technician">فني</option>
                            <option value="coordinator">منسق</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تعيين</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.enhanced.add-note', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة ملاحظة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الملاحظة</label>
                        <textarea name="note" class="form-control" rows="4" required></textarea>
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

<!-- Update Timeline Modal -->

<div class="modal fade" id="updateTimelineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('admin.orders.enhanced.update-timeline', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div id="timeline-stages">
                    {{-- جلب المراحل الرئيسية فقط --}}
                    @foreach(\App\Models\OrderStage::whereNull('parent_id')->orderBy('order_number')->get() as $stage)
                        @php
                            $status = $order->stageStatuses->firstWhere('order_stage_id', $stage->id);
                            $subStages = $stage->children()->orderBy('order_number')->get();
                        @endphp
                        <div class="card mb-2">
                            <div class="card-body row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control mb-2" value="{{ $stage->title_ar }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="stages[{{ $stage->id }}][completed]" class="form-check-input"
                                            {{ $status && $status->status == 'completed' ? 'checked' : '' }}>
                                        <label class="form-check-label">مكتملة</label>
                                    </div>
                                </div>
                            </div>

                            {{-- عرض المراحل الفرعية إن وجدت --}}
                            @if($subStages->count())
                                <div class="ms-4 mt-2">
                                    @foreach($subStages as $sub)
                                        @php
                                            $subStatus = $order->stageStatuses->firstWhere('order_stage_id', $sub->id);
                                        @endphp
                                        <div class="row align-items-center mb-1">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control form-control-sm" value="{{ $sub->title_ar }}" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input type="checkbox" name="stages[{{ $sub->id }}][completed]" class="form-check-input"
                                                        {{ $subStatus && $subStatus->status == 'completed' ? 'checked' : '' }}>
                                                    <label class="form-check-label">مكتملة</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- Add Payment Schedule Modal -->
<div class="modal fade" id="addPaymentScheduleModal" tabindex="-1" aria-labelledby="addPaymentScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentScheduleModalLabel">إضافة دفعة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentScheduleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ (ريال)</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="unpaid">غير مدفوعة</option>
                            <option value="paid">مدفوعة</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function addTimelineStage() {
    let container = document.getElementById('timeline-stages');
    let index = container.children.length;
    let html = `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <input type="text" name="stages[new_${index}][name]" class="form-control mb-2" placeholder="اسم المرحلة" required>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="stages[new_${index}][completed]" class="form-check-input">
                            <label class="form-check-label">مكتملة</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
}
</script>


@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding: 0;
        list-style: none;
    }

    .timeline-item {
        position: relative;
        padding-left: 50px;
        padding-bottom: 30px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 25px;
        bottom: -30px;
        width: 2px;
        background-color: #dee2e6;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-item.completed .timeline-marker {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
    }

    .timeline-item.pending .timeline-marker {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #fff;
    }

    .timeline-content {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #dee2e6;
    }

    .timeline-item.completed .timeline-content {
        border-left-color: #198754;
    }

    .timeline-item.pending .timeline-content {
        border-left-color: #ffc107;
    }

    @media print {
        .btn, .modal, nav, .card-header .btn {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let stageCounter = {{ $order->timeline ? count($order->timeline) : 0 }};

    function addTimelineStage() {
        const container = document.getElementById('timeline-stages');
        const newStage = `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="stages[${stageCounter}][name]" class="form-control mb-2" placeholder="اسم المرحلة" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="stages[${stageCounter}][completed]" class="form-check-input">
                                <label class="form-check-label">مكتملة</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newStage);
        stageCounter++;
    }

    function removeAssignment(assignmentId) {
        if (confirm('هل أنت متأكد من إلغاء تعيين هذا الموظف؟')) {
            fetch(`/admin/orders/enhanced/assignments/${assignmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('حدث خطأ أثناء الحذف');
                }
            });
        }
    }

    // Payment Schedule Functions
document.getElementById('addPaymentScheduleForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        amount: formData.get('amount'),
        due_date: formData.get('due_date'),
        status: formData.get('status')
    };

    const addUrl = `{{ route("admin.orders.enhanced.payment-schedules.add", ["order" => $order->id]) }}`;

    fetch(addUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة الدفعة');
    });
});


    // Mark payment as paid
    document.querySelectorAll('.mark-paid-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const scheduleId = this.dataset.scheduleId;

        if (confirm('هل أنت متأكد من تحديد هذه الدفعة كمدفوعة؟')) {
            const urlTemplate = `{{ route("admin.orders.enhanced.payment-schedules.update", ["order" => $order->id, "schedule" => ":scheduleId"]) }}`;
            const finalUrl = urlTemplate.replace(':scheduleId', scheduleId);

            fetch(finalUrl, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'paid' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحديث الدفعة');
            });
        }
    });
});


   // Delete payment schedule
document.querySelectorAll('.delete-schedule-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const scheduleId = this.dataset.scheduleId;

        if (confirm('هل أنت متأكد من حذف هذه الدفعة؟')) {
            const urlTemplate = `{{ route("admin.orders.enhanced.payment-schedules.delete", ["order" => $order->id, "schedule" => ":scheduleId"]) }}`;
            const finalUrl = urlTemplate.replace(':scheduleId', scheduleId);

            fetch(finalUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء حذف الدفعة');
            });
        }
    });
});

</script>


@endpush

