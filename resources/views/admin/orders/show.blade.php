@extends("admin.layouts.app")

@section("title", "تفاصيل الطلب")

@section("content")
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">تفاصيل الطلب #{{ $order->order_number }}</h1>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للطلبات
                </a>
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> تعديل
                </a>
            </div>
        </div>

        <div class="row">
            <!-- معلومات الطلب الأساسية -->
            <div class="col-lg-8">
                <!-- بيانات العميل -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> بيانات العميل</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">الاسم</label>
                                <p class="mb-0 fw-bold">{{ $order->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">البريد الإلكتروني</label>
                                <p class="mb-0 fw-bold">{{ $order->email }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">رقم الهاتف</label>
                                <p class="mb-0 fw-bold">{{ $order->country_code }} {{ $order->phone }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">نوع العميل</label>
                                <p class="mb-0 fw-bold">{{ $order->client_type == 'individual' ? 'فرد' : 'شركة' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تفاصيل المشروع -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-project-diagram"></i> تفاصيل المشروع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">نوع المشروع</label>
                                <p class="mb-0 fw-bold">
                                    @if($order->project_type == 'small') صغير
                                    @elseif($order->project_type == 'medium') متوسط
                                    @else كبير
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">المرحلة الحالية</label>
                                <p class="mb-0 fw-bold">
                                    @if($order->current_stage == 'design') تصميم
                                    @elseif($order->current_stage == 'execution') تنفيذ
                                    @else تشغيل
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">عدد الوحدات</label>
                                <p class="mb-0 fw-bold">{{ $order->units_count }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">لديه تصميم داخلي</label>
                                <p class="mb-0">
                                    @if($order->has_interior_design)
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-secondary">لا</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">يحتاج مساعدة في التشطيب</label>
                                <p class="mb-0">
                                    @if($order->needs_finishing_help)
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-secondary">لا</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">يحتاج مساعدة في الألوان</label>
                                <p class="mb-0">
                                    @if($order->needs_color_help)
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-secondary">لا</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($order->internal_notes)
                        <div class="mt-3">
                            <label class="text-muted small">ملاحظات العميل</label>
                            <p class="mb-0 p-3 bg-light rounded">{{ $order->internal_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- عناصر الطلب -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-box"></i> عناصر الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>الصورة</th>
                                        <th>اسم الباكج</th>
                                        <th>السعر</th>
                                        <th>الكمية</th>
                                        <th>المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                @if($item->package->image)
                                                <img src="{{ asset('storage/' . $item->package->image) }}" 
                                                     alt="{{ $item->package->name_ar }}" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->package->name_ar }}</strong><br>
                                                <small class="text-muted">{{ $item->package->name_en }}</small>
                                            </td>
                                            <td>{{ number_format($item->price, 2) }} ريال</td>
                                            <td><span class="badge bg-primary">{{ $item->quantity }}</span></td>
                                            <td><strong>{{ number_format($item->price * $item->quantity, 2) }} ريال</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الملخص المالي وحالة الطلب -->
            <div class="col-lg-4">
                <!-- حالة الطلب -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> حالة الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">حالة الطلب</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $order->status_color }} fs-6">
                                    {{ $order->status_text }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">حالة الدفع</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $order->payment_status_color }} fs-6">
                                    {{ $order->payment_status_text }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">الأولوية</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $order->priority_color }} fs-6">
                                    {{ $order->priority_text }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">تاريخ الإنشاء</label>
                            <p class="mb-0 fw-bold">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($order->expected_delivery_date)
                        <div class="mb-3">
                            <label class="text-muted small">تاريخ التسليم المتوقع</label>
                            <p class="mb-0 fw-bold">{{ $order->expected_delivery_date->format('Y-m-d') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- الملخص المالي -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> الملخص المالي</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>المجموع الفرعي:</span>
                            <strong>{{ number_format($order->base_amount ?? 0, 2) }} ريال</strong>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>الخصم:</span>
                            <strong>-{{ number_format($order->discount_amount, 2) }} ريال</strong>
                        </div>
                        @if($order->coupon)
                        <div class="mb-2">
                            <small class="text-muted">كود الخصم: <strong>{{ $order->coupon->code }}</strong></small>
                        </div>
                        @endif
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>الضريبة (15%):</span>
                            <strong>{{ number_format($order->tax_amount ?? 0, 2) }} ريال</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5">المجموع النهائي:</span>
                            <strong class="fs-4 text-primary">{{ number_format($order->total_amount, 2) }} ريال</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>المبلغ المدفوع:</span>
                            <strong class="text-success">{{ number_format($order->paid_amount, 2) }} ريال</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>المبلغ المتبقي:</span>
                            <strong class="text-danger">{{ number_format($order->remaining_amount, 2) }} ريال</strong>
                        </div>
                    </div>
                </div>

                <!-- معلومات إضافية -->
                @if($order->user)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-circle"></i> حساب المستخدم</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>الاسم:</strong> {{ $order->user->name }}</p>
                        <p class="mb-1"><strong>البريد:</strong> {{ $order->user->email }}</p>
                        <p class="mb-0"><strong>الهاتف:</strong> {{ $order->user->phone }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
