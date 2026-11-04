@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-xxl-12">

        <div class="row">
            <div class="col-xl-3">
                <div class="card bg-danger-subtle" style="background: url('assets/images/dashboard/dashboard-shape-1.png'); background-repeat: no-repeat; background-position: bottom center; ">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar avatar-sm avatar-label-danger">
                                <i class="mdi mdi-account-group"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-danger mb-1">Total Customers</p>
                                <h4 class="mb-0"> {{$customerCount}}</h4>
                            </div>
                        </div>
                        <div class="hstack gap-2 mt-3">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card bg-success-subtle" style="background: url('assets/images/dashboard/dashboard-shape-2.png'); background-repeat: no-repeat; background-position: bottom center; ">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar avatar-sm avatar-label-success">
                                <i class="mdi mdi-currency-inr"></i>

                            </div>
                            <div class="ms-3">
                                <p class="text-success mb-1">Total Amount</p>
                                <h4 class="mb-0">₹ {{$totalAmount}}</h4>
                            </div>
                        </div>
                        <div class="mt-3 mb-2">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card bg-info-subtle" style="background: url('assets/images/dashboard/dashboard-shape-3.png'); background-repeat: no-repeat; background-position: bottom center; ">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar avatar-sm avatar-label-info">
                                <i class="mdi mdi-currency-inr"></i>

                            </div>
                            <div class="ms-3">
                                <p class="text-info mb-1">Received Amount.</p>
                                <h4 class="mb-0">
                                    <h4 class="mb-0">₹ {{$recievedAmount}}</h4>
                                </h4>
                            </div>
                        </div>
                        <div class="mt-3 mb-2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card bg-info-subtle" style="background: url('assets/images/dashboard/dashboard-shape-3.png'); background-repeat: no-repeat; background-position: bottom center; ">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="avatar avatar-sm avatar-label-info">
                                <i class="mdi mdi-currency-inr"></i>

                            </div>
                            <div class="ms-3">
                                <p class="text-info mb-1">Upcoming Amount.</p>
                                <h4 class="mb-0">
                                    <h4 class="mb-0">₹ {{$UpcomingPayment}}</h4>
                                </h4>
                            </div>
                        </div>
                        <div class="mt-3 mb-2">

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <!-- <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-hockey-puck fs-14 text-muted"></i>
                            </div>
                            <h4 class="card-title mb-0">Sales by product category</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row mb-2">
                                        @php
                                        use Illuminate\Support\Carbon;

                                        $categoryTotals = [];

                                        // Convert categories collection to key-value array using lowercase name
                                        $categoriesByName = $categories->keyBy(fn($cat) => strtolower($cat->name));

                                        // Get current month deliveries only
                                        $currentMonth = now()->format('Y-m');
                                        $monthlyDeliveries = $deliveries->filter(function ($delivery) use ($currentMonth) {
                                        return \Carbon\Carbon::parse($delivery->date)->format('Y-m') === $currentMonth;
                                        });

                                        // Tally quantities per category
                                        foreach ($monthlyDeliveries as $delivery) {
                                        $rates = is_string($delivery->customer_rates)
                                        ? json_decode($delivery->customer_rates, true)
                                        : $delivery->customer_rates;

                                        foreach ($rates as $rate) {
                                        $categoryName = strtolower($rate['categoryname'] ?? '');
                                        $quantity = $rate['unit'] ?? 0;

                                        if ($categoryName) {
                                        $categoryTotals[$categoryName] = ($categoryTotals[$categoryName] ?? 0) + $quantity;
                                        }
                                        }
                                        }
                                        @endphp

                                        @foreach ($categoryTotals as $categoryName => $totalQty)
                                        @php
                                        $category = $categoriesByName[$categoryName] ?? null;
                                        @endphp
                                        @php
                                        $categoryLabels = array_keys($categoryTotals);
                                        $categorySeries = array_values($categoryTotals);
                                        @endphp

                                        @if ($category)
                                        <div class="col-6 mb-2">
                                            <div>
                                                <p>
                                                    <i class="mdi mdi-food-variant text-primary me-2"></i>
                                                    {{ ucfirst($category->name) }}
                                                    <span class="text-muted fs-14">({{ $category->store_type }})</span> —
                                                    <strong>{{ $totalQty }}</strong> Units
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>


                                </div>
                                <div class="col-sm-6">
                                    <div>
                                        <div id="gradient_chart" class="apex-charts" dir="ltr"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> -->

         <div class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Stock Summary (Category Wise)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Category</th>
                            <th>Total Stock<br>(Received)</th>
                            <th>Used Stock</th>
                            <th>Pending Stock</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($categoryWiseStock as $stock)
                            <tr>
                                <td>{{ $stock['name'] }}</td>
                                <td>{{ $stock['received'] }}</td>
                                <td>{{ $stock['used'] }}</td>
                                <td class="{{ $stock['pending'] <= 5 ? 'text-danger fw-bold' : '' }}">
                                    {{ $stock['pending'] }}
                                </td>
                                <td>{{ $stock['store_type'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">No stock data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


        </div>
        {{--<tfoot>
            <tr>
                <th>Total</th>
                <th>{{ $categoryWiseStock->sum('received') }}</th>
                <th>{{ $categoryWiseStock->sum('used') }}</th>
                <th>{{ $categoryWiseStock->sum('pending') }}</th>
                <th></th>
            </tr>
        </tfoot>--}}

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                chart: {
                    type: 'donut',
                    height: 320
                },
                series: @json($categorySeries ?? []),
                labels: @json($categoryLabels ?? []),
                colors: ['#AEDFF7', '#FAD4D4', '#FFF1B0', '#D3F8E2', '#B5EAD7', '#C7CEEA'], // Add more if needed
                dataLabels: {
                    style: {
                        colors: ['#000']
                    }
                },
                tooltip: {
                    theme: 'light',
                    style: {
                        fontSize: '14px',
                        color: '#000'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: ['#000'],
                        useSeriesColors: false
                    }
                },
                stroke: {
                    show: false
                }
            };

            var chart = new ApexCharts(document.querySelector("#gradient_chart"), options);
            chart.render();
        });
    </script>

    @endsection