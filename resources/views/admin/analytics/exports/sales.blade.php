<table>
    <thead>
        <tr>
            <th>رقم الطلب</th>
            <th>رقم العميل</th>
            <th>إجمالي السعر</th>
            <th>تاريخ التسليم</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $sale)
            <tr>
                <td>{{ $sale->order_number }}</td>
                <td>{{ $sale->user_id }}</td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
                <td>{{ $sale->delivered_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
