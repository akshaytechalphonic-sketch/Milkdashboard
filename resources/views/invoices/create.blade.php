@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-bordered d-flex justify-content-between">
                <h3 class="card-title">Delivery</h3>
                <div class="hstack gap-2 justify-content-end">
                    <!-- <button class="btn btn-primary w-max" data-bs-toggle="modal" data-bs-target="#creatertaskModal"> Add delivery</button> -->
                    <a href="http://127.0.0.1:8000/deliveries/create" class="btn btn-primary w-max">
                         Add delivery
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Add Delivery</h4>
            <!-- <button type="" class="btn btn-success">Back</button> -->
            <a href="{{ route('deliveries.list') }}" class="btn btn-success w-max">
                Back
            </a>
        </div>

        <div class="card-body">

            {{-- Success Message --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('deliveries.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label">Delivery Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="litres" class="form-label">Litres</label>
                        <input type="number" step="0.01" name="litres" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="rate" class="form-label">Rate per Litre</label>
                        <input type="number" step="0.01" name="rate" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="product_type" class="form-label">Product Type</label>
                        <select name="product_type" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id}}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="remarks" class="form-label">Remarks (Optional)</label>
                        <input type="text" name="remarks" class="form-control">
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Save Delivery</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection