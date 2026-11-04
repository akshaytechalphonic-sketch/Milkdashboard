@extends('layouts.admin')
@section('content')



    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-bordered d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Total Supplled (by Category)</h3>
                    <div>
                        @if (!empty($categoryQuantities))
                            @foreach ($categoryQuantities as $category)
                                <span class="badge bg-success me-1 mb-1">
                                    {{ $category['categoryname'] }}: {{ $category['total_unit'] }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">No data</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('customer.delivery') }}" method="POST">
                    @csrf
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Customers Delivery Sheet</h4>
                        <div>
                            <input type="date" name="date" class="form-control"
                                value="{{ \Carbon\Carbon::now()->toDateString() }}">
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif


                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        {{-- <form action="{{ route('customer.delivery') }}" method="POST">
                    @csrf --}}
                    <div class="table-responsive">
                            <table id="" class="table table-hover table-bordered table-striped dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Name</th>
                                        <th>number</th>
                                        <th>email</th>
                                        <th>Address</th>
                                        <th>Quantity</th>
                                        @auth
                                            @if (auth()->user()->user_type === 'admin')
                                                <th>Action</th>
                                            @else
                                                <th>
                                                    <input type="checkbox" id="select-all">
                                                    Action
                                                </th>
                                            @endif
    
                                        @endauth
                                    </tr>
                                </thead>
                                <tbody>
    
                                    @foreach ($customers as $index => $customer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->number }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->address }}</td>
    
                                            {{-- <td>
                                                @foreach ($customer->category_rates as $rate)
                                                    <div>
                                                        {{ $rate['categoryname'] ?? 'Unknown Category' }} —
                                                        Unit: {{ $rate['unit'] ?? '--' }}
                                                    </div>
                                                @endforeach
                                            </td> --}}
    
                                            <td>
                                                @foreach ($customer->category_rates as $rateIndex => $rate)
                                                    <div class="mb-2">
                                                        <label
                                                            class="fw-bold">{{ $rate['categoryname'] ?? 'Unknown Category' }}</label>
    
                                                        <input type="number"
                                                            name="category_rates[{{ $customer->id }}][{{ $rateIndex }}][unit]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $rate['unit'] ?? '' }}" placeholder="Enter Unit"
                                                            step="0.01" min="0" required>
    
                                                        <input type="hidden"
                                                            name="category_rates[{{ $customer->id }}][{{ $rateIndex }}][category_id]"
                                                            value="{{ $rate['category_id'] ?? '' }}">
    
                                                        <input type="hidden"
                                                            name="category_rates[{{ $customer->id }}][{{ $rateIndex }}][categoryname]"
                                                            value="{{ $rate['categoryname'] ?? '' }}">
    
                                                        <input type="hidden"
                                                            name="category_rates[{{ $customer->id }}][{{ $rateIndex }}][rate]"
                                                            value="{{ $rate['rate'] ?? '' }}">
    
                                                        <input type="hidden"
                                                            name="category_rates[{{ $customer->id }}][{{ $rateIndex }}][store_type]"
                                                            value="{{ $rate['store_type'] ?? '' }}">
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td> <input type="checkbox" class="row-checkbox" name="deliveries[]"
                                                    value="{{ $customer->id }}">
                                                {{-- <input type="hidden" name="category_rates[{{ $customer->id }}]"
                                                    value='@json($customer->category_rates)'> --}}
                                            </td>
    
                                        </tr>
                                    @endforeach
    
                                    </tr>
    
                                </tbody>
                            </table>

                        </div>
                        <div class="text-end m-3">
                            <button type="submit" class="btn btn-primary">Submit Deliveries</button>
                        </div>
                        {{-- </form> --}}

                    </div>
                </form>
            </div>
        </div> <!-- end col -->
    </div>
    </div> <!-- container-fluid -->

    <!-- END layout-wrapper -->


@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.row-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });

            // Optional: sync select-all if one is unchecked
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else if (document.querySelectorAll('.row-checkbox:checked').length ===
                        checkboxes.length) {
                        selectAll.checked = true;
                    }
                });
            });
        });
    </script>
@endpush
