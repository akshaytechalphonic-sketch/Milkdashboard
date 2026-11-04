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

                <form method="POST" action="{{ route('deliveries.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" class="form-select select2" id="customerSelect" required>
                                <option value="">-- Select Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-rates='@json($customer->category_rates)'>
                                        {{ $customer->name }}{{ $customer->area ? ' (' . $customer->area . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label">Delivery Date</label>
                            <input type="date" name="date" class="form-control"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                        </div>


                        <!-- Container for Category/Rate Fields -->
                        <div id="category-rate-wrapper" class="row g-3"></div>

                        <!-- Add More Button -->
                        <div class="col-lg-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success" id="add-more-fields">+</button>
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

   {{-- @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: "Search Customer...",

                });
            });
        </script>
    @endpush --}}
{{-- @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const categoryRateWrapper = document.getElementById("category-rate-wrapper");
            const customerSelect = document.getElementById("customerSelect");
            const addMoreBtn = document.getElementById("add-more-fields");

            const categories = @json($categories); // make sure this is available globally

            // Add row dynamically
            function addCategoryRateRow(categoryId = '', unitValue = '', rateValue = '') {
                const row = document.createElement('div');
                row.className = "row g-3 align-items-end mb-2 category-rate-clone";

                let optionsHtml = `<option value="">-- Select Category --</option>`;
                categories.forEach(cat => {
                    const selected = String(cat.id) === String(categoryId) ? 'selected' : '';
                    optionsHtml +=
                        `<option value="${cat.id}" data-store_type="${cat.store_type}" ${selected}>${cat.name}</option>`;
                });

                const rawStoreType = categoryId ?
                    (categories.find(c => String(c.id) === String(categoryId))?.store_type || '--') :
                    '--';
                const storeType = rawStoreType.trim().toLowerCase().startsWith('l') ? '1' : '2';

                const categoryName = categoryId ?
                    (categories.find(c => String(c.id) === String(categoryId))?.name || '') :
                    '';

                row.innerHTML = `
        <div class="col-lg-3">
                <label class="form-label">Select Category</label>
                <select name="category_id[]" class="form-select category-select" required>
                    ${optionsHtml}
                </select>
               <input type="hidden" name="category_name[]" value="${categoryName}" class="category-name-hidden">
               <input type="hidden" name="store_type[]" value="${storeType}" class="store-type-hidden">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <div class="input-group">
                    <span class="input-group-text unit-prefix">${storeType}</span>
                    <input type="number" class="form-control" name="unit[]" value="${unitValue}" step="0.01" placeholder="Enter Value" required>
                </div>
            </div>
           
            <div class="col-lg-4">
                <label class="form-label">Rate</label>
                <input type="number" name="rate[]" class="form-control" value="${rateValue}" placeholder="Rate" required>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-field">-</button>
            </div>
        `;

                categoryRateWrapper.appendChild(row);
            }

            // On customer select
            customerSelect.addEventListener("change", function() {
                const selectedOption = this.options[this.selectedIndex];
                const ratesJson = selectedOption.getAttribute("data-rates");

                categoryRateWrapper.innerHTML = ""; // clear old fields

                if (ratesJson) {
                    try {
                        const rates = JSON.parse(ratesJson);
                        rates.forEach(rate => {
                            addCategoryRateRow(rate.category_id, rate.unit, rate.rate);
                        });
                    } catch (e) {
                        console.error("Invalid JSON in data-rates:", e);
                    }
                }
            });

            // Add button handler
            addMoreBtn.addEventListener("click", function() {
                addCategoryRateRow();
            });

            // Remove field handler
            categoryRateWrapper.addEventListener("click", function(e) {
                if (e.target && e.target.classList.contains("remove-field")) {
                    e.target.closest(".row").remove();
                }
            });

            // Update store_type + category name on category change
            categoryRateWrapper.addEventListener("change", function(e) {
                if (e.target && e.target.classList.contains("category-select")) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const storeType = selectedOption.getAttribute("data-store_type") || "--";
                    const unitPrefix = e.target.closest(".row").querySelector(".unit-prefix");
                    const hiddenInput = e.target.closest(".row").querySelector(".category-name-hidden");

                    if (unitPrefix) unitPrefix.textContent = storeType;
                    if (hiddenInput) hiddenInput.value = selectedOption.textContent.trim();
                    if (storeTypeHidden) storeTypeHidden.value = storeType;
                }
            });
        });
    </script>
@endpush --}}


@push('scripts')
<script>
$(document).ready(function () {
    // Initialize Select2
    $('#customerSelect').select2({
        placeholder: "Search Customer..."
    });

    const categoryRateWrapper = document.getElementById("category-rate-wrapper");
    const addMoreBtn = document.getElementById("add-more-fields");

    const categories = @json($categories); // available globally

    // Add row dynamically
    function addCategoryRateRow(categoryId = '', unitValue = '', rateValue = '') {
        const row = document.createElement('div');
        row.className = "row g-3 align-items-end mb-2 category-rate-clone";

        let optionsHtml = `<option value="">-- Select Category --</option>`;
        categories.forEach(cat => {
            const selected = String(cat.id) === String(categoryId) ? 'selected' : '';
            optionsHtml += `<option value="${cat.id}" data-store_type="${cat.store_type}" ${selected}>${cat.name}</option>`;
        });

        const rawStoreType = categoryId ?
            (categories.find(c => String(c.id) === String(categoryId))?.store_type || '--') :
            '--';
        const storeType = rawStoreType.trim().toLowerCase().startsWith('l') ? '1' : '2';

        const categoryName = categoryId ?
            (categories.find(c => String(c.id) === String(categoryId))?.name || '') :
            '';

        row.innerHTML = `
            <div class="col-lg-3">
                <label class="form-label">Select Category</label>
                <select name="category_id[]" class="form-select category-select" required>
                    ${optionsHtml}
                </select>
                <input type="hidden" name="category_name[]" value="${categoryName}" class="category-name-hidden">
                <input type="hidden" name="store_type[]" value="${storeType}" class="store-type-hidden">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <div class="input-group">
                    <span class="input-group-text unit-prefix">${storeType}</span>
                    <input type="number" class="form-control" name="unit[]" value="${unitValue}" step="0.01" placeholder="Enter Value" required>
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Rate</label>
                <input type="number" name="rate[]" class="form-control" value="${rateValue}" placeholder="Rate" required>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-field">-</button>
            </div>
        `;

        categoryRateWrapper.appendChild(row);
    }

    // Handle customer select change (MUST use jQuery for Select2)
    $('#customerSelect').on("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const ratesJson = selectedOption.getAttribute("data-rates");

        categoryRateWrapper.innerHTML = ""; // clear old fields

        if (ratesJson) {
            try {
                const rates = JSON.parse(ratesJson);
                rates.forEach(rate => {
                    addCategoryRateRow(rate.category_id, rate.unit, rate.rate);
                });
            } catch (e) {
                console.error("Invalid JSON in data-rates:", e);
            }
        }
    });

    // Add button handler
    addMoreBtn.addEventListener("click", function () {
        addCategoryRateRow();
    });

    // Remove field handler
    categoryRateWrapper.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("remove-field")) {
            e.target.closest(".row").remove();
        }
    });

    // Update store_type + category name on category change
    categoryRateWrapper.addEventListener("change", function (e) {
        if (e.target && e.target.classList.contains("category-select")) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const storeType = selectedOption.getAttribute("data-store_type") || "--";
            const unitPrefix = e.target.closest(".row").querySelector(".unit-prefix");
            const hiddenInput = e.target.closest(".row").querySelector(".category-name-hidden");
            const storeTypeHidden = e.target.closest(".row").querySelector(".store-type-hidden");

            if (unitPrefix) unitPrefix.textContent = storeType;
            if (hiddenInput) hiddenInput.value = selectedOption.textContent.trim();
            if (storeTypeHidden) storeTypeHidden.value = storeType;
        }
    });
});
</script>
@endpush

