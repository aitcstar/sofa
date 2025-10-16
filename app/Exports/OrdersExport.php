<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Return collection for export.
     */
    public function collection()
    {
        // هنا حدد الأعمدة اللي تحب تظهرها
        return $this->orders->map(function($order) {
            return [
                'Order ID' => $order->id,
                'Order Number' => $order->order_number,
                'Customer' => $order->user->name ?? '-',
                'Package' => $order->package->name ?? '-',
                'Total Amount' => $order->total_amount,
                'Status' => $order->status,
                'Created At' => $order->created_at->format('Y-m-d'),
            ];
        });
    }

    /**
     * Column headings
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Customer',
            'Package',
            'Total Amount',
            'Status',
            'Created At',
        ];
    }
}
