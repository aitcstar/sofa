<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>رقم الدفعة</th>
            <th>العميل</th>
            <th>المبلغ</th>
            <th>طريقة الدفع</th>
            <th>تاريخ الدفع</th>
            <th>الحالة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td>{{ $payment->payment_number }}</td>
            <td>{{ $payment->customer->name ?? '-' }}</td>
            <td>{{ $payment->amount }}</td>
            <td>{{ $payment->payment_method ?? '-' }}</td>
            <td>{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '-' }}</td>
            <td>{{ ucfirst($payment->status) }}</td>
            <td>
                <a href="{{ route('admin.financial.payments.show', $payment) }}" class="btn btn-sm btn-primary">عرض</a>
                <!--<a href="{{ route('admin.financial.payments.edit', $payment) }}" class="btn btn-sm btn-warning">تعديل</a>
                <form action="{{ route('admin.financial.payments.destroy', $payment) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                </form>-->
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
