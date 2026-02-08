<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ __('invoice.invoice_title', ['number' => $invoice->invoice_number]) }} - SOFA Experience</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/invoice-pricing.css') }}" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');

        body {
            font-family: '{{ $lang === "ar" ? "Cairo" : "Arial, sans-serif" }}', sans-serif;
        }

        .print-button {
            position: fixed;
            top: 20px;
            {{ $lang === 'ar' ? 'left' : 'right' }}: 20px;
            z-index: 1000;
            display: flex !important;
            gap: 0.5rem;
        }

        @media print {
            .print-button, .no-print {
                display: none !important;
            }
        }

        :root {
            --primary: #08203E;
            --input-text: #333;
        }

        .gap-sm-5 { gap: 8px; }
        .gap-sm-6 { gap: 12px; }
        .body-2 { font-size: 14px; }
        .heading-h7 { font-size: 18px; font-weight: 700; }
        .heading-h8 { font-size: 20px; font-weight: 700; }
        .sub-heading-3 { font-size: 16px; font-weight: 600; }
        .text-heading { color: #08203E; }
        .text-subheading { color: #666; }
    </style>
</head>

<body>
    <!-- Print & Language Buttons -->
    <div class="print-button no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print me-2"></i>{{ __('invoice.print') }}
        </button>

        @if($lang === 'ar')
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="btn btn-outline-secondary">
                English
            </a>
        @else
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" class="btn btn-outline-secondary">
                العربية
            </a>
        @endif
    </div>

    <!-- ===== INVOICE CONTAINER ===== -->
    <div class="container invoice-pricing-container" style="max-width: 900px; margin: 40px auto; padding: 40px; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        <!-- ===== Logo & Details ===== -->
        <div class="logo-details" style="display: flex; justify-content: space-between; margin-bottom: 30px;">
            <!-- Logo -->
            <div class="logo">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" style="max-width: 150px;" />
            </div>

            <!-- Details -->
            <div class="details" style="display: flex; gap: 40px;">
                <!-- Provider Company Data -->
                <div class="d-flex flex-column gap-sm-6">
                    <p class="body-2 mb-0">{{ $siteSettings->site_name }}</p>
                    <p class="body-2 mb-0">{{ $siteSettings->address }}</p>
                    <p class="body-2 mb-0">{{ $siteSettings->phone }}</p>
                    <p class="body-2 mb-0">CR: 1010000000</p>
                </div>

                <!-- Invoice Data -->
                <div class="d-flex flex-column gap-sm-6">
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">{{ __('invoice.invoice_number') }}:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;"># {{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">{{ __('invoice.issue_date') }}:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">
                            {{ $lang === 'ar' ? $invoice->created_at->format('d F Y') : $invoice->created_at->format('F d, Y') }}
                        </p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">{{ __('invoice.due_date') }}:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">
                            {{ $invoice->due_date ? ($lang === 'ar' ? $invoice->due_date->format('d F Y') : $invoice->due_date->format('F d, Y')) : '-' }}
                        </p>
                    </div>
                    <div class="d-flex gap-sm-5">
                        <p class="mb-0 body-2">{{ __('invoice.status') }}:</p>
                        <p class="mb-0 body-2" style="color: var(--input-text) !important;">
                            @if($invoice->status == 'paid')
                                {{ __('invoice.paid') }}
                            @elseif($invoice->status == 'partial')
                                {{ __('invoice.partially_paid') }}
                            @else
                                {{ __('invoice.unpaid') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4" />

        <!-- ===== Customer Data =====
        <div class="customer-data d-flex flex-column gap-sm-4 mb-4">
            <h3 class="heading-h8 mb-3">{{ __('invoice.customer_details') }}</h3>
            <div class="customer-data-item-details" style="display: flex; justify-content: space-between;">
                <div class="d-flex flex-column gap-sm-3">
                    <p class="mb-0 body-2">{{ __('invoice.customer_name') }}:</p>
                    <p class="mb-0 body-2">{{ __('invoice.phone') }}:</p>
                    <p class="mb-0 body-2">{{ __('invoice.address') }}:</p>
                    <p class="mb-0 body-2">{{ __('invoice.company') }}:</p>

                </div>
                <div class="d-flex flex-column gap-sm-3" style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">
                    <p class="mb-0 body-2 text-heading">{{ $invoice->customer->name ?? __('invoice.not_specified') }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $invoice->customer->phone ?? __('invoice.not_specified') }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $invoice->customer->address ?? __('invoice.not_specified') }}</p>
                    <p class="mb-0 body-2 text-heading">{{ $invoice->quote->customer_company ?? __('invoice.not_specified') }}</p>



                </div>
            </div>
        </div>-->

        <div class="customer-data d-flex flex-column gap-sm-4 mb-4">
            <h3 class="heading-h8 mb-3">{{ __('invoice.customer_details') }}</h3>

            <div class="customer-data-item-details d-flex justify-content-between">

                {{-- 🔹 العمود الشمال --}}
                <div class="d-flex flex-column gap-sm-3"
                     style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">

                    <p class="mb-0 body-2 text-heading">
                        {{ __('invoice.company') }}:
                        {{ $invoice->quote->customer_company ?? __('invoice.not_specified') }}
                    </p>

                    <p class="mb-0 body-2 text-heading">
                        {{ __('invoice.project_name') }}:
                        {{ $invoice->quote->lead->project_type ?? __('invoice.not_specified') }}
                    </p>

                    <p class="mb-0 body-2 text-heading">
                        {{ __('invoice.description') }}:
                        {{ $invoice->quote->lead->notes ?? __('invoice.not_specified') }}
                    </p>
                </div>

                {{-- 🔹 العمود اليمين --}}
                <div class="d-flex flex-column gap-sm-3"
                     style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">

                    <p class="mb-0 body-2 text-heading">
                        {{ __('invoice.customer_name') }}:
                        {{ $invoice->customer->name ?? __('invoice.not_specified') }}
                    </p>

                    <p class="mb-0 body-2 text-heading">
                        {{ __('invoice.phone') }}:
                        {{ $invoice->customer->phone ?? __('invoice.not_specified') }}
                    </p>

                    <p class="mb-0 body-2 text-heading">
                        {{ __('invoice.address') }}:
                        {{ $invoice->customer->address ?? __('invoice.not_specified') }}
                    </p>
                </div>

            </div>
        </div>

        <!-- ===== Package Details (Grouped by Unit) ===== -->
        @php
        $isQuote = $invoice->quote_id && $invoice->quote && $invoice->quote->quoteItems->isNotEmpty();

        if($isQuote){
            $invoiceItems = $invoice->quote->quoteItems;
            $groupedItems = $invoiceItems->groupBy(function($item) use ($lang) {
                return $lang === 'ar' ? (optional($item->unit)->name_ar ?? 'غير محدد') : (optional($item->unit)->name_en ?? 'Not Specified');
            });
        } elseif($invoice->package && $invoice->package->packageUnitItems->isNotEmpty()) {
            $invoiceItems = $invoice->package->packageUnitItems;
            $groupedItems = $invoiceItems->groupBy(function($item) use ($lang) {
                return $lang === 'ar' ? (optional($item->unit)->name_ar ?? 'غير محدد') : (optional($item->unit)->name_en ?? 'Not Specified');
            });
        } else {
            $groupedItems = collect();
        }
    @endphp

    @if($groupedItems->isNotEmpty())
    <div class="studio-package-details mb-4">
        <h4 class="sub-heading-3 mb-3">{{ __('invoice.components') }}</h4>

        @foreach($groupedItems as $unitName => $items)
            <div class="mb-4">
                <div class="p-2 text-center text-white" style="background-color: var(--primary);">
                    <h5 class="mb-0">{{ $unitName }}</h5>
                </div>
                @php $i = 1; @endphp

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th>#</th>
                                <th>{{ __('invoice.item') }}</th>
                                <th>{{ __('invoice.dimensions') }}</th>
                                <th>{{ __('invoice.material') }}</th>
                                <th>{{ __('invoice.color') }}</th>
                                <th>{{ __('invoice.image') }}</th>
                                <th>{{ __('invoice.quantity') }}</th>
                                @if($isQuote)
                                    <th>{{ __('invoice.cost') }}</th>
                                    <th>{{ __('invoice.description') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>{{ $i++}}</td>

                                <td>{{ $lang === 'ar' ? (optional($item->item)->item_name_ar ?? '-') : (optional($item->item)->item_name_en ?? '-') }}</td>
                                <td>{{ optional($item->item)->dimensions ?? '-' }}</td>
                                <td>{{ $lang === 'ar' ? (optional($item->item)->material_ar ?? '-') : (optional($item->item)->material_en ?? '-') }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ optional($item->item)->background_color ?? '#262626' }}; color: white;">
                                        {{ $lang === 'ar' ? (optional($item->item)->color_ar ?? __('invoice.not_specified')) : (optional($item->item)->color_en ?? __('invoice.not_specified')) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(optional($item->item)->image_path)
                                        <img src="{{ asset('storage/' . $item->item->image_path) }}"
                                             alt="{{ $lang === 'ar' ? optional($item->item)->item_name_ar : optional($item->item)->item_name_en }}"
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    @endif
                                </td>
                                <td>{{ $item->quantity ?? ($item->pivot->quantity ?? 1) }}</td>

                                @if($isQuote)
                                    <td>{{ number_format($item->unit_price ?? $item->price ?? 0, 2) }}</td>
                                    <td>{{ $item->description ?? ($item->pivot->description ?? '-') }}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if(!$loop->last)
                <hr class="my-4" />
            @endif
        @endforeach
    </div>
    @endif



        <!-- ===== Total Summary ===== -->
        <div class="total-summary border rounded mb-4">
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">{{ __('invoice.subtotal') }}:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($invoice->subtotal, 0) }}</strong> {{ __('invoice.currency') }}</p>
            </div>
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">{{ __('invoice.tax', ['rate' => $invoice->tax_rate ?? 15]) }}:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($invoice->tax_amount, 0) }}</strong> {{ __('invoice.currency') }}</p>
            </div>
            <div class="d-flex justify-content-between p-3 border-bottom">
                <p class="body-2 mb-0">{{ __('invoice.shipping') }}:</p>
                <p class="body-2 mb-0"><strong>{{ number_format($invoice->shipping_cost ?? 0, 0) }}</strong> {{ __('invoice.currency') }}</p>
            </div>
            <div class="d-flex justify-content-between p-3 text-white" style="background-color: var(--primary);">
                <p class="body-2 mb-0 fw-bold">{{ __('invoice.total') }}:</p>
                <p class="heading-h7 mb-0">{{ number_format($invoice->total_amount, 0) }} {{ __('invoice.currency') }}</p>
            </div>
        </div>

        <!-- ===== Payment Methods ===== -->
        <div class="payment-methods p-3 bg-light rounded mb-4">
            <div class="d-flex justify-content-between">
                <p class="body-2 mb-0">{{ __('invoice.amount_paid') }}:</p>
                <p class="body-2 mb-0 fw-bold">{{ number_format($invoice->paid_amount ?? 0) }} {{ __('invoice.currency') }}</p>
            </div>
        </div>

        <!-- ===== Policies ===== -->
        <div class="policies border rounded p-3 mb-4">
            <div class="mb-3">
                <p class="fw-bold mb-1">{{ __('invoice.warranty_terms') }}:</p>
                <p class="body-2 mb-0">{{ __('invoice.warranty_policy') }}</p>
            </div>
            <div>
                <p class="fw-bold mb-1">{{ __('invoice.return_policy') }}:</p>
                <p class="body-2 mb-0">{{ __('invoice.return_policy_text') }}</p>
            </div>
        </div>

        <hr class="my-4" />

        <!-- ===== Footer ===== -->
        <div class="invoice-footer text-center">
            <p class="body-2 mb-0">{{ __('invoice.thank_you') }}</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
