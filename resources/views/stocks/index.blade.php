@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-bordered d-flex justify-content-between">
                    <h3 class="card-title">Stocks</h3>
                    <div class="hstack gap-2 justify-content-end">
                        <!-- <button class="btn btn-primary w-max" data-bs-toggle="modal" data-bs-target="#creatertaskModal"> Add delivery</button> -->
                        <a href="{{ route('stock.create') }}" class="btn btn-primary w-max">
                            Add Stock
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center col-12">
                        <!-- Heading -->
                        <div class="col-md-3">
                            <h4 class="card-title mb-0">All Delivery</h4>
                        </div>

                        <!-- Filters -->
                        <div class="col-md-9 ">
                            <form method="GET" action="{{ route('stock.index') }}" class="row g-2 align-items-end">


                                <div class="col-6 col-lg-4">
                                    <label for="from_date" class="form-label">From</label>
                                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}"
                                        class="form-control">
                                </div>

                                <!-- To Date -->
                                <div class="col-6 col-lg-4">
                                    <label for="to_date" class="form-label">To</label>
                                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control">
                                </div>

                                <!-- Buttons -->
                                <div class="col-12 col-lg-2 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="{{ route('stock.index') }}" class="btn btn-secondary w-100">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>



                <div class="card-body">

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
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
                                <th>S.N</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Opening</th>
                                <th>Wastage</th>
                                <th>Received</th>
                                <th>Used</th>
                                <th>Closing</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($stocks as $index => $stock)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stock->date . ' ' . $stock->month . ' ' . $stock->year }}</td>
                                    <td>
                                        {{ optional($stock->category)->name }}

                                    </td>
                                    <td>{{ $stock->opening_stock ?? '' }} {{ $stock->category->store_type ?? '' }}</td>
                                    <td>{{ $stock->wastage ?? '' }}</td>
                                    <td>{{ $stock->received ?? '' }} {{ $stock->category->store_type ?? '' }}</td>
                                    <td>{{ $stock->used ?? '' }} {{ $stock->category->store_type ?? '' }}</td>
                                    <td>{{ $stock->closing_stock ?? '' }} {{ $stock->category->store_type ?? '' }}</td>

                                    <td>
                                        <ul class="d-flex gap-3 list-unstyled justify-content-center align-items-center">
                                            @if ($loop->first || $loop->last)
                                                <li>
                                                    <a href="{{ route('stock.edit', $stock->id) }}"
                                                        class="btn btn-sm btn-primary d-flex align-items-center justify-content-center">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach

                            </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    @endsection
