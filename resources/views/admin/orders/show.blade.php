@extends("admin.layouts.app")

@section("title", "Order Details")

@section("content")
    <div class="container-fluid">
        <h1>Order #{{ $order->order_number }}</h1>

        <div class="card">
            <div class="card-header">
                Order Details
            </div>
            <div class="card-body">
                <p><strong>User:</strong> {{ $order->user->name }}</p>
                <p><strong>Total Amount:</strong> {{ $order->total_amount }}</p>
                <p><strong>Discount:</strong> {{ $order->discount_amount }}</p>
                <p><strong>Final Amount:</strong> {{ $order->final_amount }}</p>
                <p><strong>Payment Status:</strong> {{ $order->payment_status }}</p>
                <p><strong>Order Status:</strong> {{ $order->order_status }}</p>

                <h5>Items</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->package->name_en }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->price * $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
