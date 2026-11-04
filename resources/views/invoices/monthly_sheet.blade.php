@extends('layouts.admin')
@section('content')

    <style>
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute !important;
            right: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            cursor: pointer !important;
            z-index: 10 !important;
            font-size: 16px !important;
            color: #999 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #333 !important;
        }
        .select2-container--default .select2-selection--single {
            position: relative !important;
            padding-right: 40px !important;
        }

        /* Sticky Columns Solid Background & Hover Support */
        #monthly-sheet-table th[style*="sticky"] {
            background-color: #eff2f7 !important;
            z-index: 3 !important;
        }
        #monthly-sheet-table td[style*="sticky"] {
            background-color: #ffffff !important;
            z-index: 2 !important;
        }
        #monthly-sheet-table tr:nth-of-type(odd) td[style*="sticky"] {
            background-color: #f8f9fa !important;
        }
        #monthly-sheet-table tr:hover td[style*="sticky"] {
            background-color: #f2f4f8 !important;
        }
    </style>

    <h2 class="mb-3">Monthly Delivery Sheet - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h2>

    <div class="row mb-4 align-items-center">
        <div class="col-md-8 col-sm-12">
            <form method="GET" class="d-flex gap-2 align-items-center mb-md-0 mb-3 flex-wrap">
                <input type="month" name="month" class="form-control w-auto" value="{{ $month }}">

                <select name="customer_id" class="form-control w-auto select2" style="min-width: 50px;">
                    <option value="">-- All Customers --</option>
                    @foreach($allCustomers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->area ?? '' }})
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-sm btn-primary">View</button>
            </form>
        </div>
        <div class="col-md-4 col-sm-12 d-flex justify-content-md-end justify-content-start">
            <div class="d-flex align-items-center">
                <input type="text" id="customerSearch" class="form-control" placeholder="Search Customer..."
                    style="width: 280px;">
            </div>
        </div>
    </div>
    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $selectedCarbon = \Carbon\Carbon::parse($month);
        $isEditable = true;
    @endphp

    <form method="POST" action="{{ route('admin.deliveries.updateMonthly') }}">
        @csrf
        <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
        <input type="hidden" name="month" value="{{ $month }}">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-0">

                        <div class="card_body table-responsive"
                            style="height: 100%;max-height:calc(100vh - 130px); overflow:scroll; ">
                            <table id="monthly-sheet-table"
                                class="table table-hover table-bordered table-striped dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="table-light sticky-top" style="font-size: 11px;">
                                    <tr style="line-height: 1;">
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">S.N</th>
                                        <th scope="col" class="text-start" style="position: sticky; left: 0; padding: 6px 4px; vertical-align: middle; background-color: #eff2f7 !important; z-index: 2; min-width: 150px; max-width: 150px; width: 150px;">
                                            Customer</th>
                                        <th scope="col" class="text-start" style="position: sticky; left: 150px; padding: 6px 4px; vertical-align: middle; background-color: #eff2f7 !important; z-index: 2; min-width: 180px; max-width: 180px; width: 180px;">Address</th>
                                        @for ($i = 1; $i <= $daysInMonth; $i++)
                                            <th scope="col" style="padding: 6px 2px; vertical-align: middle;">{{ $i }}</th>
                                        @endfor
                                        @foreach ($categories as $category)
                                            <th style="padding: 6px 4px; vertical-align: middle;">{{ ucfirst($category->name) }}</th>
                                        @endforeach
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Amount</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Old remaining amount</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Grand Total</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Received</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Pending</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Advance Payment</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Mode</th>
                                        <th scope="col" style="padding: 6px 4px; vertical-align: middle;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $index => $customer)
                                                                    @php
                                                                        $invoice = $customer->invoices->first(); // Only one invoice per month-year per customer expected
                                                                        $oldBalance = $invoice->old_balance ?? '0';
                                                                        $totalAmount = $invoice->total_amount ?? 0;
                                                                        $received = $invoice->received_amount ?? 0;
                                                                        $grandTotal = $totalAmount + $oldBalance; // fallback if not set
                                                                        // Get advance amount if applicable
                                                                        $customerAdvance =
                                                                            $customer->amount_type == 2 ? $customer->old_balance ?? 0 : 0;

                                                                        // Deduct advance first
                                                                        $usedAdvance = min($customerAdvance, $grandTotal);
                                                                        $adjustedTotal = $grandTotal - $usedAdvance;

                                                                        // Final due amount
                                                                        $pending = max(0, $adjustedTotal - $received);

                                                                        $advancePayment = $received > $grandTotal ? $received - $grandTotal : 0;
                                                                    @endphp
                                                                    @php
                                                                        $unitDetailsByDay = array_fill(1, $daysInMonth, []);
                                                                        $dayCategoryUnits = [];
                                                                        $totalUnits = 0;
                                                                        $totalAmount = 0;
                                                                        $customerCategories = is_string($customer->category_rates)
                                                                            ? json_decode($customer->category_rates, true)
                                                                            : ($customer->category_rates ?? []);
                                                                    @endphp

                                                                    @foreach ($customer->deliveries as $delivery)
                                                                        @php

                                                                            $monthNumber = is_numeric($delivery->month)
                                                                                ? $delivery->month
                                                                                : \Carbon\Carbon::parse("1 {$delivery->month}")->month;

                                                                            $fullDate = \Carbon\Carbon::createFromDate(
                                                                                $delivery->year,
                                                                                $monthNumber,
                                                                                $delivery->date,
                                                                            );
                                                                            $day = $fullDate->day;

                                                                            $rates = is_string($delivery->customer_rates)
                                                                                ? json_decode($delivery->customer_rates, true)
                                                                                : $delivery->customer_rates;

                                                                            foreach ($rates as $rateItem) {
                                                                                $unit = floatval($rateItem['unit'] ?? 0);
                                                                                $rate = floatval($rateItem['rate'] ?? 0);
                                                                                $categoryId = $rateItem['category_id'] ?? null;
                                                                                $categoryName = strtolower($rateItem['categoryname'] ?? 'unknown');
                                                                                $storeType = $rateItem['category_type'] ?? $rateItem['store_type'] ?? '';

                                                                                $unitDetailsByDay[$day][] = "{$categoryName} - {$unit} {$storeType}";

                                                                                if ($categoryId) {
                                                                                    $dayCategoryUnits[$day][$categoryId] = [
                                                                                        'unit' => $unit,
                                                                                        'rate' => $rate,
                                                                                        'categoryname' => $rateItem['categoryname'] ?? '',
                                                                                        'store_type' => $storeType
                                                                                    ];
                                                                                }

                                                                                if ($categoryName === 'milk') {
                                                                                    $totalUnits += $unit;
                                                                                }

                                                                                $totalAmount += $unit * $rate;
                                                                            }
                                                                        @endphp
                                                                    @endforeach
                                                                    @php
                                                                        $categoryUnits = [];

                                                                        foreach ($customer->deliveries as $delivery) {
                                                                            $rates = is_string($delivery->customer_rates)
                                                                                ? json_decode($delivery->customer_rates, true)
                                                                                : $delivery->customer_rates;

                                                                            foreach ($rates as $rateItem) {
                                                                                $categoryId = $rateItem['category_id'] ?? null;
                                                                                $unit = floatval($rateItem['unit'] ?? 0);

                                                                                if ($categoryId) {
                                                                                    $categoryUnits[$categoryId] =
                                                                                        ($categoryUnits[$categoryId] ?? 0) + $unit;
                                                                                }
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td class="text-start fw-semibold" style="position: sticky; left: 0; min-width: 150px; max-width: 150px; width: 150px; background-color: inherit; z-index: 1;">
                                                                            {{ $customer->name }} ({{ $customer->area ?? '' }})
                                                                        </td>
                                                                        <td style="position: sticky; left: 150px; min-width: 180px; max-width: 180px; width: 180px; background-color: inherit; z-index: 1;">{{ $customer->address ?? '' }}</td>
                                                                        @php
                                                                            $isRowEditable = $isEditable && empty($customer->invoices->first());
                                                                        @endphp
                                                                        @for ($i = 1; $i <= $daysInMonth; $i++)
                                                                            <td>
                                                                                @if ($isRowEditable)
                                                                                    @foreach ($customerCategories as $cat)
                                                                                        @php
                                                                                            $catId = $cat['category_id'];
                                                                                            $existingData = $dayCategoryUnits[$i][$catId] ?? null;
                                                                                            $unitValue = $existingData ? $existingData['unit'] : '';
                                                                                            $rateValue = $existingData ? $existingData['rate'] : ($cat['rate'] ?? 0);
                                                                                            $catName = $existingData ? $existingData['categoryname'] : ($cat['categoryname'] ?? '');
                                                                                            $storeType = $existingData ? $existingData['store_type'] : ($cat['store_type'] ?? '');
                                                                                        @endphp
                                                                                        <div class="mb-2 text-start delivery-input-container">
                                                                                            <span class="text-capitalize fw-bold"
                                                                                                style="font-size: 9px; display: block;">{{ $catName }}:</span>
                                                                                            <input type="number"
                                                                                                data-name="deliveries[{{ $customer->id }}][{{ $i }}][{{ $catId }}][unit]"
                                                                                                value="{{ $unitValue }}"
                                                                                                class="form-control form-control-sm text-center p-1 quantity-input"
                                                                                                style="width: 70px; font-size: 10px;" step="0.01" min="0"
                                                                                                placeholder="0">

                                                                                            <input type="hidden"
                                                                                                data-name="deliveries[{{ $customer->id }}][{{ $i }}][{{ $catId }}][rate]"
                                                                                                value="{{ $rateValue }}">
                                                                                            <input type="hidden"
                                                                                                data-name="deliveries[{{ $customer->id }}][{{ $i }}][{{ $catId }}][categoryname]"
                                                                                                value="{{ $catName }}">
                                                                                            <input type="hidden"
                                                                                                data-name="deliveries[{{ $customer->id }}][{{ $i }}][{{ $catId }}][store_type]"
                                                                                                value="{{ $storeType }}">
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                
                                                                                    @foreach ($unitDetailsByDay[$i] as $item)
                                                                                        <p class="mb-1">
                                                                                            <span class="badge badge-info text-nowrap p-1 text-capitalize"
                                                                                                style="font-size: 10px;">
                                                                                                {{ $item }}
                                                                                            </span>
                                                                                        </p>
                                                                                    @endforeach
                                                                                @endif
                                                                            </td>
                                                                        @endfor


                                                                        @foreach ($categories as $cat)
                                                                            <td class="fw-bold">{{ $categoryUnits[$cat->id] ?? 0 }}</td>
                                                                        @endforeach
                                                                        <td class="text-success">₹{{ number_format($totalAmount, 2) }}</td>

                                                                        @php

                                                                            $selectedMonth = request('month') ?? now()->format('Y-m');

                                                                            // Convert to Carbon
                                                                            $selectedCarbon = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);

                                                                            // Get previous month from selected month
                                                                            $previousMonth = $selectedCarbon->copy()->subMonth();
                                                                            $lastMonthName = $previousMonth->format('F');
                                                                            $lastMonthYear = $previousMonth->year;

                                                                            // Fetch previous invoice based on selected month - 1
                                                                            $previousInvoice = \App\Models\Invoice::where('customer_id', $customer->id)
                                                                                ->where('month', $lastMonthName)
                                                                                ->where('year', $lastMonthYear)
                                                                                ->first();

                                                                            // Determine old balance and advance amount
                                                                            $oldBalance = isset($previousInvoice->due_amount)
                                                                                ? (float) $previousInvoice->due_amount
                                                                                : ($customer->amount_type == 1
                                                                                    ? (float) ($customer->old_balance ?? 0)
                                                                                    : 0);

                                                                            $advanceAmount = isset($previousInvoice->advance_amount)
                                                                                ? (float) $previousInvoice->advance_amount
                                                                                : ($customer->amount_type == 2
                                                                                    ? (float) ($customer->old_balance ?? 0)
                                                                                    : 0);

                                                                            // Calculate Grand Total:
                                                                            $grandTotal = $totalAmount;

                                                                            if ($customer->amount_type == 1) {
                                                                                $grandTotal += $oldBalance;
                                                                            } elseif ($customer->amount_type == 2) {
                                                                                $grandTotal -= $advanceAmount;
                                                                                if ($grandTotal < 0) {
                                                                                    $grandTotal = $totalAmount; // Prevent negative total
                                                                                }
                                                                            }

                                                                        @endphp
                                                                        <td>{{ number_format($oldBalance, 2) }}</td>

                                                                        <td>{{ number_format($grandTotal) }}</td>
                                                                        <td>{{ number_format($received) ?? 0 }}</td>
                                                                        <td class="text-danger fw-semibold">
                                                                            ₹{{ $invoice->due_amount ?? '0' }}
                                                                        </td>
                                                                        <td>{{ $advanceAmount ?? 0 }}</td>
                                                                        <td>
                                                                            <span
                                                                                class="badge bg-primary text-light">{{ $invoice->payment_mode ?? '-' }}</span>
                                                                        </td>

                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                                                data-bs-target="#paymentModal{{ $customer->id }}">
                                                                                Update
                                                                            </button>

                                                                            <a href="{{ route('customers.show', [
                                                                                'customer' => $customer->id,
                                                                                'month' => $selectedCarbon->month,
                                                                                'year' => $selectedCarbon->year,
                                                                            ]) }}" class="btn btn-sm btn-outline-info" title="Payment History">
                                                                                History
                                                                            </a>

                                                                            <a href="{{ route('invoices.generate', [
                                            'customer' => $customer->id,
                                            'year' => $selectedCarbon->year,
                                            'month' => $selectedCarbon->month,
                                        ]) }}" class="btn btn-sm btn-warning">
                                                                                Generate Invoice
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($isEditable)
                    <div class="text-end mt-3 mb-4">
                        <button type="submit" class="btn btn-success">Save Deliveries</button>
                    </div>
                @endif
            </div>
        </div>
    </form>



    @foreach ($customers as $customer)
        @php
            $modalInvoice = $customer->invoices->first();
            $modalExistingPayment = $modalInvoice ? $modalInvoice->payments()->latest()->first() : null;
            $modalCurrentReceived = $modalInvoice && $modalInvoice->received_amount > 0
                ? $modalInvoice->received_amount
                : ($modalExistingPayment ? $modalExistingPayment->amount : '');
            $modalCurrentMode = $modalInvoice && $modalInvoice->payment_mode
                ? $modalInvoice->payment_mode
                : ($modalExistingPayment ? $modalExistingPayment->payment_mode : ($customer->payment_mode ?? ''));
            $modalCurrentDate = $modalExistingPayment && $modalExistingPayment->date
                ? \Carbon\Carbon::parse($modalExistingPayment->date)->format('Y-m-d')
                : ($selectedCarbon->year == now()->year && $selectedCarbon->month == now()->month ? now()->format('Y-m-d') : $selectedCarbon->copy()->endOfMonth()->format('Y-m-d'));
            $modalCurrentNote = $modalExistingPayment ? $modalExistingPayment->note : '';
        @endphp
        <!-- Modal -->
        <div class="modal fade" id="paymentModal{{ $customer->id }}" tabindex="-1"
            aria-labelledby="modalLabel{{ $customer->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.customer.updatePayment', $customer->id) }}">
                    @csrf
                    @method('POST')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel{{ $customer->id }}">Update Payment -
                                {{ $customer->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="year" value="{{ $selectedCarbon->year }}">
                            <input type="hidden" name="month" value="{{ $selectedCarbon->format('F') }}">
                            @if($modalExistingPayment)
                            <input type="hidden" name="payment_id" value="{{ $modalExistingPayment->id }}">
                            @endif

                            <div class="mb-3">
                                <label for="received_amount" class="form-label">Received Payment (₹)</label> <span
                                    class="text-danger">*</span>
                                <input type="number" step="0.01" name="received_amount" class="form-control" placeholder="0.00"
                                    value="{{ $modalCurrentReceived }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="payment_mode" class="form-label">Payment Mode</label> <span
                                    class="text-danger">*</span>
                                <select name="payment_mode" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="paytm" {{ $modalCurrentMode == 'paytm' ? 'selected' : '' }}>
                                        Paytm</option>
                                    <option value="cash" {{ $modalCurrentMode == 'cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="phonepe" {{ $modalCurrentMode == 'phonepe' ? 'selected' : '' }}>
                                        PhonePe</option>
                                    <option value="gpay" {{ $modalCurrentMode == 'gpay' ? 'selected' : '' }}>GPay
                                    </option>
                                    <option value="bank" {{ $modalCurrentMode == 'bank' ? 'selected' : '' }}>Bank
                                        Transfer</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <span class="text-danger">*</span>
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ $modalCurrentDate }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="note" class="form-label">Note (Optional)</label>
                                <textarea name="note" rows="3" class="form-control">{{ $modalCurrentNote }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.jQuery && window.jQuery.fn.select2) {
                    window.jQuery('.select2').select2({
                        placeholder: {
                            id: "",
                            text: "-- All Customers --"
                        },
                        allowClear: true
                    });
                }

                // Handle dynamically setting name attributes to prevent max_input_vars limit
                var qInputs = document.querySelectorAll('.quantity-input');
                qInputs.forEach(function (input) {
                    input.addEventListener('change', function () {
                        this.setAttribute('name', this.getAttribute('data-name'));
                        var container = this.closest('.delivery-input-container');
                        if (container) {
                            container.querySelectorAll('input[type="hidden"]').forEach(function (hidden) {
                                hidden.setAttribute('name', hidden.getAttribute('data-name'));
                            });
                        }
                    });
                });

                var searchInput = document.getElementById('customerSearch');
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        var value = this.value.toLowerCase().trim();
                        console.log('Search value:', value); // Debugging line

                        var rows = document.querySelectorAll('#monthly-sheet-table tbody tr');
                        rows.forEach(function (row) {
                            var nameCell = row.cells[1];
                            var addressCell = row.cells[2];

                            var nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
                            var addressText = addressCell ? addressCell.textContent.toLowerCase() : '';

                            if (nameText.indexOf(value) > -1 || addressText.indexOf(value) > -1) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
    @endpush

@endsection