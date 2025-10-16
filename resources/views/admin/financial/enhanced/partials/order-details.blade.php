<div class="mb-4 p-3 border rounded bg-light">
    <h6>تفاصيل الطلب</h6>
    <p><strong>رقم الطلب:</strong> {{ $order->order_number }}</p>
    <p><strong>المستخدم:</strong> {{ $order->user->name }}</p>
    <p><strong>المبلغ الأساسي:</strong> {{ number_format($order->custom_fields['base_price'] ?? 0, 2) }} ريال</p>
    <p><strong>الضريبة:</strong> {{ number_format($order->custom_fields['tax'] ?? 0, 2) }} ريال</p>
    <p><strong>الإجمالي:</strong> {{ number_format($order->total_amount, 2) }} ريال</p>
    <p><strong>المدفوع:</strong> {{ number_format($order->paid_amount, 2) }} ريال</p>
    <p><strong>المتبقي:</strong> {{ number_format($order->remaining_amount, 2) }} ريال</p>
</div>
