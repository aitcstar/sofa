@extends("admin.layouts.app")

@section("title", "Orders")

@section("content")
    <div class="container-fluid">
        <h1>Orders</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>User</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->final_amount }}</td>
                        <td>{{ $order->order_status }}</td>
                        <td>{{ $order->created_at->format("Y-m-d") }}</td>
                        <td>
                            <a href="{{ route("admin.orders.show", $order) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
@endsection
