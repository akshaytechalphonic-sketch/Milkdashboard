@extends('layouts.admin')
@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-bordered d-flex justify-content-between">
                    <h3 class="card-title">Delivery</h3>
                    <div class="hstack gap-2 justify-content-end">
                        <!-- <button class="btn btn-primary w-max" data-bs-toggle="modal" data-bs-target="#creatertaskModal"> Add delivery</button> -->
                        <a href="{{ route('deliveries.create') }}" class="btn btn-primary w-max">
                            Add delivery
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center" style="width: 100%;">
                        <!-- Heading -->
                        <div class="col-md-2">
                            <h4 class="card-title mb-0">All Delivery</h4>
                        </div>

                        <!-- Filters -->
                        <div class="col-md-9">
                            <form method="GET" action="{{ route('deliveries.list') }}" class="row g-1 align-items-end">
                                <!-- Customer -->
                                <div class="col-12 col-lg-4">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select name="customer_id" class="form-select select2">
                                        <option></option> {{-- empty option required for placeholder --}}
                                        @foreach ($customers as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ isset($customerId) && $customerId == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- From Date -->
                                <div class="col-6 col-lg-3">
                                    <label for="from_date" class="form-label">From</label>
                                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}"
                                        class="form-control">
                                </div>

                                <!-- To Date -->
                                <div class="col-6 col-lg-3">
                                    <label for="to_date" class="form-label">To</label>
                                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control">
                                </div>

                                <!-- Buttons -->
                                <div class="col-12 col-lg-2 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="{{ route('deliveries.list') }}" class="btn btn-secondary w-100">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="card-body">

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
                    <table id="datatable-buttons"
                        class="table table-hover table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>Sr.</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($deliveriesData as $index => $delivery)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $delivery->customer->name ?? '' }}</td>
                                    <td>{{ $delivery->customer->address ?? '' }}</td>
                                    <td>{{ $delivery->date . ' ' . $delivery->month . ' ' . $delivery->year }}</td>

                                    <td>
                                        @foreach ($delivery->customer_rates as $rate)
                                            <div>
                                                {{ $rate['categoryname'] ?? 'Unknown Category' }} —
                                                Unit: {{ $rate['unit'] ?? '--' }}
                                            </div>
                                        @endforeach

                                    </td>


                                    <td>

                                        <ul class="d-flex gap-3 list-unstyled justify-content-center align-items-center">
                                            <!-- Edit Button -->
                                            <li>

                                                <a href="{{ route('deliveries.edit', $delivery->id) }}"
                                                    class="btn btn-sm btn-primary d-flex align-items-center justify-content-center">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </li>

                                            <!-- Delete Button -->
                                            <li>
                                                @auth
                                                    @if (auth()->user()->user_type === 'admin')
                                                        <form action="{{ route('deliveries.destroy', $delivery->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                            @csrf

                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger d-flex align-items-center justify-content-center">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div>


    <!-- END layout-wrapper -->
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: "Search Customer...",
                    
                });
            });
        </script>
    @endpush
@endsection
