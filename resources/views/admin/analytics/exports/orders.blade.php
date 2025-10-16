<table>
    <thead>
        <tr>
            <th>رقم الطلب</th>
            <th>رقم العميل</th>
            <th>الحالة</th>
            <th>إجمالي السعر</th>
            <th>تاريخ الإنشاء</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->user_id }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ number_format($order->total_amount, 2) }}</td>
                <td>{{ $order->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
