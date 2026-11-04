@extends('layouts.admin')

@section('content')
    <div class="row">


    </div>
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Stock Management</h4>
                <!-- <button type="" class="btn btn-success">Back</button> -->
                <a href="{{ route('stock.index') }}" class="btn btn-success w-max">
                    Back
                </a>
            </div>

            <div class="card-body">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('stockupdate', $stock->id) }}">
                    @csrf
                    @method('PUT') <!-- Required for Laravel to handle update -->

                    <div class="row g-3">
                        @php
                            $today = \Carbon\Carbon::now()->format('Y-m-d');
                            $selectedDate = \Carbon\Carbon::parse($stock->date)->format('Y-m-d');
                        @endphp

                        <!-- Date Field -->
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date</label> <span class="text-danger">*</span>
                            <input type="date" name="date" class="form-control" required max="{{ $today }}"
                                value="{{ $selectedDate }}" readonly>
                        </div>

                        <!-- Product Type Field -->
                        <div class="col-md-6">
                            <label for="product_type" class="form-label">Product Type</label> <span
                                class="text-danger">*</span>
                            <select name="product_type" class="form-select" id="product_type" required disabled>
                                <option value="">-- Select Product --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $stock->product_type == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Hidden to submit value of disabled dropdown -->
                            <input type="hidden" name="product_type" value="{{ $stock->product_type }}">
                        </div>

                        <!-- Received Field -->
                        <div class="col-md-6">
                            <label for="received" class="form-label">Received</label> <span class="text-danger">*</span>
                            <input type="number" step="0.01" name="received" class="form-control" placeholder="Received"
                                required value="{{ $stock->received }}">
                        </div>

                        <!-- Wastage Field -->
                        <div class="col-md-6">
                            <label for="wastage" class="form-label">Wastage (optional)</label>
                            <input type="number" step="0.01" name="wastage" class="form-control" placeholder="Wastage"
                                value="{{ $stock->wastage }}">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Update Stock</button>
                        </div>
                    </div>
                </form>




            </div>
        </div>
    </div>
@endsection
