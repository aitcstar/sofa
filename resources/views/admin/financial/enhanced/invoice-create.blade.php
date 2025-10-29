@extends('admin.layouts.app')

@section('title', 'إنشاء فاتورة')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>إنشاء فاتورة جديدة</h5>
        </div>
        <div class="card-body">
            {{-- اختيار الطلب --}}
            <form method="POST" action="{{ route('admin.financial.invoices.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="order_id" class="form-label">اختر الطلب</label>
                    <select name="order_id" id="order_id" class="form-control" >
                        <option value="">-- اختر الطلب --</option>
                        @foreach($orders as $order)
                                <option value="{{ $order->id }}"
                                    data-total="{{ $order->total_amount }}"
                                    {{ $selectedOrder && $selectedOrder->id == $order->id ? 'selected' : '' }}>
                                {{ $order->order_number }} - {{ number_format($order->total_amount, 2) }} ريال
                            </option>
                        @endforeach
                    </select>

                </div>

                <div id="order-details" class="mb-4 p-3 border rounded bg-light">

                </div>

{{-- مبلغ الدفع --}}
<div class="mb-3">
    <label for="paid_amount" class="form-label">المبلغ المراد دفعه</label>
    <input type="number" step="0.01" min="0"
           name="paid_amount" id="paid_amount" class="form-control"
           placeholder="أدخل المبلغ المدفوع (يمكن أن يكون جزئي)">
</div>

                {{-- حالة الدفع --}}
                <div class="mb-3">
                    <label for="status" class="form-label">حالة الدفع</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="unpaid">غير مدفوع</option>
                        <option value="partial">مدفوع جزئياً</option>
                        <option value="paid">مدفوع بالكامل</option>
                    </select>
                </div>

                {{-- الملاحظات --}}
                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-plus me-2"></i>إنشاء الفاتورة</button>
            </form>
        </div>
    </div>
</div>
<script>
   document.addEventListener('DOMContentLoaded', function() {
        let orderSelect = document.getElementById('order_id');
        let paidAmountInput = document.getElementById('paid_amount');
        let detailsDiv = document.getElementById('order-details');

        function loadOrderDetails(orderId) {
            if (!orderId) {
                detailsDiv.innerHTML = '';
                paidAmountInput.max = ''; // إزالة الحد الأقصى
                return;
            }

            let url = "{{ route('admin.orders.enhanced.details', ':order') }}";
            url = url.replace(':order', orderId);

            fetch(url)
                .then(res => res.text())
                .then(html => {
                    detailsDiv.innerHTML = html;
                });
        }

        function updateMaxAmount() {
            let selectedOption = orderSelect.options[orderSelect.selectedIndex];
            let total = selectedOption ? selectedOption.getAttribute('data-total') : null;
            if (total) {
                paidAmountInput.max = parseFloat(total);
            } else {
                paidAmountInput.max = '';
            }
        }

        // عند تغيير الطلب
        orderSelect.addEventListener('change', function() {
            loadOrderDetails(this.value);
            updateMaxAmount();
        });

        // عند التحميل الأولي (إذا كان هناك طلب محدد)
        updateMaxAmount();
    });

    </script>


@endsection
