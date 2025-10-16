<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.invoice') }} #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            color: #333;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        .logo img {
            height: 60px;
        }
        .company-info {
            text-align: right;
            font-size: 12px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 5px;
            vertical-align: top;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .customer-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .customer-info td {
            padding: 5px;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-section table {
            width: 300px;
            border-collapse: collapse;
        }
        .total-section td {
            padding: 8px;
            text-align: right;
        }
        .total-section .label {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ asset('assets/images/logos/logo-black.svg') }}" alt="SOFA Experience">
            </div>
            <div class="company-info">
                <p>SOFA Furnishing Co</p>
                <p>الرياض، المملكة العربية السعودية</p>
                <p>+966500000000</p>
                <p>CR: 101000000</p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <table>
                <tr>
                    <td><strong>{{ __('site.invoice_number') }}:</strong></td>
                    <td>#{{ $order->order_number }}</td>
                    <td><strong>{{ __('site.invoice_date') }}:</strong></td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('site.due_date') }}:</strong></td>
                    <td>{{ $order->created_at->addDays(7)->format('Y-m-d') }}</td>
                    <td><strong>{{ __('site.status') }}:</strong></td>
                    <td>{{ __('site.' . $order->status) }}</td>
                </tr>
            </table>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <h4>{{ __('site.customer_info') }}</h4>
            <table>
                <tr>
                    <td><strong>{{ __('site.name') }}:</strong></td>
                    <td>{{ $order->name ?? $order->user?->name }}</td>
                    <td><strong>{{ __('site.phone') }}:</strong></td>
                    <td>{{ $order->phone }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('site.email') }}:</strong></td>
                    <td>{{ $order->email }}</td>
                    <td><strong>{{ __('site.country_code') }}:</strong></td>
                    <td>{{ $order->country_code }}</td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <div class="items-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('site.quantity') }}</th>
                        <th>{{ __('site.image') }}</th>
                        <th>{{ __('site.item_name') }}</th>
                        <th>{{ __('site.color') }}</th>
                        <th>{{ __('site.material') }}</th>
                        <th>{{ __('site.dimensions') }}</th>
                        <th>{{ __('site.price') }}</th>
                        <th>{{ __('site.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @foreach($order->package->packageUnitItems as $item)
                        @php
                            $subtotal = $item->item->price * $item->item->quantity;
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item->item->quantity }}</td>
                            <td>
                                @if($item->item->image_path)
                                    <img src="{{ asset('storage/' . $item->item->image_path) }}" alt="Item" style="height: 50px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('assets/images/no-image.png') }}" alt="No Image" style="height: 50px; object-fit: cover;">
                                @endif
                            </td>
                            <td>{{ $item->item->{'item_name_'.app()->getLocale()} }}</td>
                            <td>
                                <span style="display: inline-block; width: 16px; height: 16px; background-color: {{ $item->item->background_color }}; border-radius: 50%;"></span>
                                {{ $item->item->{'color_'.app()->getLocale()} }}
                            </td>
                            <td>{{ $item->item->{'material_'.app()->getLocale()} }}</td>
                            <td>{{ $item->item->dimensions }}</td>
                            <td>{{ number_format($item->item->price) }} SAR</td>
                            <td>{{ number_format($subtotal) }} SAR</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <table>
                <tr>
                    <td class="label">{{ __('site.subtotal') }}:</td>
                    <td>{{ number_format($total) }} SAR</td>
                </tr>
                <tr>
                    <td class="label">{{ __('site.tax') }} (15%):</td>
                    <td>{{ number_format($total * 0.15) }} SAR</td>
                </tr>
                <tr>
                    <td class="label">{{ __('site.total') }}:</td>
                    <td><strong>{{ number_format($total * 1.15) }} SAR</strong></td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('site.footer_invoice_text') }}</p>
            <p>{{ __('site.contact_info') }}: info@sofa.com | www.sofa.com</p>
        </div>
    </div>

    <script>
        // طباعة الصفحة تلقائيًا عند التحميل
        window.onload = function() {
            window.print();
        };

        // منع الطباعة عند النقر على روابط أو أزرار
        document.addEventListener('click', function(e) {
            e.preventDefault();
        }, true);

        // إعادة توجيه إلى صفحة تفاصيل الطلب بعد الإغلاق
        window.onafterprint = function() {
            window.close();
            window.location.href = "{{ route('order.details', $order) }}";
        };
    </script>
</body>
</html>
