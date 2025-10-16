<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>رقم الفاتورة</th>
            <th>العميل</th>
            <th>المبلغ</th>
            <th>تاريخ الإنشاء</th>
            <th>الحالة</th>
            <th>إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->customer->name ?? '-' }}</td>
            <td>{{ $invoice->total_amount }}</td>
            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
            <td>{{ ucfirst($invoice->status) }}</td>
            <td>
                <a href="{{ route('admin.financial.invoices.show', $invoice) }}" class="btn btn-sm btn-primary">عرض</a>
                <a href="{{ route('admin.financial.invoices.edit', $invoice) }}" class="btn btn-sm btn-warning">تعديل</a>
                <form action="{{ route('admin.financial.invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
