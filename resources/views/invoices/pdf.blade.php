<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }

        .invoice {
            width: 100%;
            max-width: 700px;
            margin: auto;
            border: 2px solid #000;
            background-color: #ffe69946;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 12px;
        }

        .no-border {
            border: none;
        }

        .header-table td {
            text-align: center;
            font-weight: bold;
            background-color: #f9cb9c;
        }

        .section-title {
            background-color: #f9cb9c;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            margin-top: 10px;
        }

        .final-row {
            background-color: #ffe599;
            font-weight: bold;
        }

        .footer-table td {
            text-align: center;
            font-size: 12px;
        }

        img {
            display: block;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td colspan="2">
                    <h3>Hare Krishna</h3>
                    <h4>Gir Govind A2 Milk Bill</h4>
                </td>
                <td>
                    <p>SR.No</p>
                    <h4>36</h4>
                </td>
            </tr>
        </table>

        <!-- Customer and Bill Info -->
        <table style="margin-top: 10px;">
            <tr>
                <td style="width:50%; text-align:left;">
                    <strong>Customer Name:</strong> {{ $invoice->customer->name }}<br>
                    <strong>Address:</strong> {{ $invoice->customer->address ?? 'NA' }}
                </td>
                <td style="width:50%; text-align:left;">
                    <strong>Bill Date:</strong> {{ now()->format('d-m-Y') }}<br>
                    <strong>Bill Month:</strong> {{ \Carbon\Carbon::parse($invoice->month)->format('F Y') }}
                </td>
            </tr>
        </table>

        <!-- Billing Title -->
        <div class="section-title">Billing Details {Liters As Per Date}</div>

        <!-- Billing Tables -->
        <table style="margin-top: 5px;">
            <tr>
                <!-- Delivery Data -->
                <td style="width:60%; vertical-align: top;">
                    <table>

                        @php
                            $firstDelivery = $invoice->deliveries->first();

                            // Fallback to current month if no deliveries exist
                            $month = $firstDelivery->month ?? now()->month;
                            $year = $firstDelivery->year ?? now()->year;

                            // Convert month name to number if needed
                            $month = is_numeric($month) ? $month : \Carbon\Carbon::parse("1 $month")->month;

                            $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

                            // Initialize daily units and total
                            $dateUnits = array_fill(1, $daysInMonth, 0);
                            $totalCategoryType1Units = 0;

                            // Loop through deliveries
                            foreach ($invoice->deliveries as $delivery) {
                                $day = intval($delivery->date); // Day of the month

                                $rates = is_string($delivery->customer_rates)
                                    ? json_decode($delivery->customer_rates, true)
                                    : $delivery->customer_rates;

                                $dailyUnits = 0;

                                // foreach ($rates as $rate) {
                                //     if (isset($rate['category_type']) && $rate['category_type'] === '1') {
                                //         $unit = floatval($rate['unit'] ?? 0);
                                //         $dailyUnits += $unit;
                                //         $totalCategoryType1Units += $unit;
                                //     }
                                // }
                                foreach ($rates as $rate) {
                                    if (isset($rate['category_type']) && in_array($rate['category_type'], ['1', 'l'])) {
                                        $unit = floatval($rate['unit'] ?? 0);
                                        $dailyUnits += $unit;
                                        $totalCategoryType1Units += $unit;
                                    }
                                }

                                $dateUnits[$day] = $dailyUnits;
                            }
                        @endphp

                        <!-- Show in 3 columns layout like your screenshot -->
                        <thead style="background-color:#fff3cd;">
                            <tr>
                                <th colspan="6">Delivery Data</th>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>Lts</th>
                                <th>Date</th>
                                <th>Lts</th>
                                <th>Date</th>
                                <th>Lts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= ceil($daysInMonth / 3); $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dateUnits[$i] ?? 0 }}</td>

                                    @php $second = $i + 10; @endphp
                                    <td>{{ $second <= $daysInMonth ? $second : '' }}</td>
                                    <td>{{ $second <= $daysInMonth ? $dateUnits[$second] ?? 0 : '' }}</td>

                                    @php $third = $i + 20; @endphp
                                    <td>{{ $third <= $daysInMonth ? $third : '' }}</td>
                                    <td>{{ $third <= $daysInMonth ? $dateUnits[$third] ?? 0 : '' }}</td>
                                </tr>
                            @endfor
                        </tbody>
                        {{-- <tbody>
                            @for ($i = 1; $i <= $daysInMonth; $i += 3)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $dateUnits[$i] ?? 0 }}</td>

                                    <td>{{ $i + 1 <= $daysInMonth ? $i + 1 : '' }}</td>
                                    <td>{{ $i + 1 <= $daysInMonth ? $dateUnits[$i + 1] ?? 0 : '' }}</td>

                                    <td>{{ $i + 2 <= $daysInMonth ? $i + 2 : '' }}</td>
                                    <td>{{ $i + 2 <= $daysInMonth ? $dateUnits[$i + 2] ?? 0 : '' }}</td>
                                </tr>
                            @endfor
                        </tbody> --}}

                    </table>
                </td>

                <!-- Billing Data -->
                <td style="width:40%; vertical-align: top;">
                    <table>
                        <thead style="background-color:#fff3cd;">
                            <tr>
                                <th colspan="4">Billing Data</th>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        @php

                            $oldAmount = $invoice->old_balance ?? 0;
                            $totalAmount = $invoice->total_amount ?? 0;
                            $received = $invoice->received_amount ?? 0;
                            $previousInvoiceAmount = $previousInvoice->total_amount ?? 0;
                            $grandTotal = $totalAmount - $previousInvoiceAmount + $oldAmount;

                            $firstDelivery = $invoice->deliveries->first();
                            $rates = $firstDelivery ? $firstDelivery->customer_rates : [];

                        @endphp

                        <tbody>


                            @php
                                $categoryTotals = [];
                                $totalDaysQty = 0;

                                foreach ($invoice->deliveries as $delivery) {
                                    $month = is_numeric($delivery->month)
                                        ? $delivery->month
                                        : \Carbon\Carbon::parse("1 {$delivery->month}")->month;

                                    $year = $delivery->year ?? now()->year;
                                    $day = $delivery->date ?? 1;

                                    $deliveryDate = \Carbon\Carbon::createFromDate($year, $month, $day);

                                    // ✅ Match invoice month/year instead of current month
                                    $invoiceMonth = is_numeric($invoice->month)
                                        ? $invoice->month
                                        : \Carbon\Carbon::parse("1 {$invoice->month}")->month;
                                    $invoiceYear = $invoice->year;

                                    if ($deliveryDate->month == $invoiceMonth && $deliveryDate->year == $invoiceYear) {
                                        $rates = is_string($delivery->customer_rates)
                                            ? json_decode($delivery->customer_rates, true)
                                            : $delivery->customer_rates;

                                        foreach ($rates as $rate) {
                                            $category = ucfirst($rate['categoryname']);
                                            $unit = floatval($rate['unit'] ?? 0);
                                            $rateValue = floatval($rate['rate'] ?? 0);
                                            $amount = $unit * $rateValue;

                                            if (!isset($categoryTotals[$category])) {
                                                $categoryTotals[$category] = [
                                                    'qty' => 0,
                                                    'rate' => $rateValue,
                                                    'total' => 0,
                                                ];
                                            }

                                            // ✅ This already accumulates category-wise total for whole month
                                            $categoryTotals[$category]['qty'] += $unit;
                                            $categoryTotals[$category]['total'] += $amount;
                                            $totalDaysQty += $unit;
                                        }
                                    }
                                }
                            @endphp

                            @foreach ($categoryTotals as $category => $data)
                                <tr>
                                    <td>{{ $category }}</td>
                                    <td>{{ $data['qty'] }}</td>
                                    <td>{{ $data['rate'] }}</td>
                                    <td>{{ $data['total'] }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td>Old Balance</td>
                                <td colspan="3">{{ $invoice->old_balance ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Advance Paid</td>
                                <td colspan="3">{{ $invoice->advance_amount ?? '' }}</td>
                            </tr>
                            <tr class="final-row">
                                <td colspan="3">Final Payable Amount</td>
                                <td>{{ $invoice->due_amount ?? '' }}</td>
                            </tr>
                        </tbody>

                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <table class="footer-table" style="margin-top: 15px; border-top: 1px solid black;">
            <tr>
                <!-- Payment QR -->
                <td style="width:50%; border-right: 1px solid black;">
                    <img src="{{ public_path('assets/images/qr.png') }}" width="120">
                    <p><strong>GPAY/PHONEPAY/PAYTM:</strong> 8779698589</p>
                    <p><strong>AC NO:</strong> 35774137515</p>
                    <p><strong>IFSC CODE:</strong> SBI0005800</p>
                </td>

                <!-- Brand Info -->
                <td style="width:50%;">
                    <img src="{{ public_path('assets/images/girgovind.png') }}" width="120">
                    <p><strong>Mohan Gothai</strong></p>
                    <p>8779698589</p>
                    <p>Thank you for using Gir Govind A2 Milk!</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
