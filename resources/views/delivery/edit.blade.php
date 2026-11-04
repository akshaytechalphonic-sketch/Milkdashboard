@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Delivery</h4>
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

                <form method="POST" action="{{ route('deliveries.update', $delivery->id) }}">
                    @csrf


                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" class="form-select" id="customerSelect" required>
                                <option value="">-- Select Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-rates='@json($customer->category_rates)'
                                        {{ $delivery->customer_id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label">Delivery Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $fullDate }}" required>
                        </div>

                        <!-- Category Rate Wrapper -->
                        <div id="category-rate-wrapper" class="row g-3">
                            @foreach ($customerRates as $index => $rateData)
                                @php
                                    $category = $categories->firstWhere('id', $rateData['category_id']);
                                    $storeType = $category?->store_type ?? 'kg';
                                    $storeTypeVal = strtolower($storeType) === 'l' ? 1 : 2;
                                @endphp
                                <div class="row g-3 align-items-end mb-2">
                                   <div class="col-md-3">
    <label class="form-label">Category</label>
    <select name="category_id[]" class="form-select category-select" required>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" data-store_type="{{ $cat->store_type }}"
                {{ $cat->id == $rateData['category_id'] ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <input type="hidden" name="category_name[]" value="{{ $category->name ?? '' }}" class="category-name-hidden">
    <input type="hidden" name="store_type[]" value="{{ $storeTypeVal }}" class="store-type-hidden">
</div>


                                    <div class="col-md-3">
                                        <label class="form-label">Unit</label>
                                        <div class="input-group">
                                            <span class="input-group-text unit-prefix">{{ $storeType }}</span>
                                            <input type="number" class="form-control" name="unit[]"
                                                value="{{ $rateData['unit'] ?? '' }}" step="0.01" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Rate</label>
                                        <input type="number" name="rate[]" class="form-control"
                                            value="{{ $rateData['rate'] ?? '' }}" step="0.01" required>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-field">-</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Add More Button -->
                        <div class="col-lg-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success" id="add-more-fields">+</button>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">Update Delivery</button>
                        </div>
                    </div>
                </form>



            </div>
        </div>
    </div>


@endsection
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const wrapper = document.getElementById("category-rate-wrapper");
    const addBtn = document.getElementById("add-more-fields");
    const customerSelect = document.getElementById("customerSelect");
    const categories = @json($categories);

    function createRow(rate = {}) {
        const row = document.createElement('div');
        row.className = "row g-3 align-items-end mb-2";

        let categoryOptions = `<option value="">-- Select Category --</option>`;
        categories.forEach(cat => {
            const selected = rate.category_id == cat.id ? 'selected' : '';
            categoryOptions += `<option value="${cat.id}" data-store_type="${cat.store_type}" ${selected}>${cat.name}</option>`;
        });

        const selectedCat = categories.find(c => c.id == rate.category_id);
        const storeType = selectedCat ? selectedCat.store_type : "--";
        const storeTypeVal = storeType.trim().toUpperCase() === "L" ? 1 : 2;
        const categoryName = selectedCat ? selectedCat.name : "";

        row.innerHTML = `
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id[]" class="form-select category-select" required>
                    ${categoryOptions}
                </select>
                <input type="hidden" name="category_name[]" value="${categoryName}" class="category-name-hidden">
                <input type="hidden" name="store_type[]" value="${storeTypeVal}" class="store-type-hidden">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <div class="input-group">
                    <span class="input-group-text unit-prefix">${storeType}</span>
                    <input type="number" name="unit[]" class="form-control" value="${rate.unit || ''}" step="0.01" required>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rate</label>
                <input type="number" name="rate[]" class="form-control" value="${rate.rate || ''}" step="0.01" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-field">-</button>
            </div>
        `;

        wrapper.appendChild(row);
    }

    // Category change
    wrapper.addEventListener("change", function (e) {
        if (e.target.classList.contains("category-select")) {
            const selected = e.target.options[e.target.selectedIndex];
            const storeType = selected.getAttribute("data-store_type") || "--";
            const storeTypeVal = storeType.trim().toUpperCase() === "L" ? 1 : 2;

            const row = e.target.closest(".row");
            row.querySelector(".unit-prefix").textContent = storeType;
            row.querySelector(".category-name-hidden").value = selected.textContent.trim();
            row.querySelector(".store-type-hidden").value = storeTypeVal;
        }
    });

    // Add new row
    addBtn.addEventListener("click", function () {
        createRow(); // empty row
    });

    // Remove row
    wrapper.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-field")) {
            e.target.closest(".row").remove();
        }
    });

    // On customer change
    customerSelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const ratesJson = selectedOption.getAttribute("data-rates");

        wrapper.innerHTML = ""; // Clear

        if (ratesJson) {
            try {
                const rates = JSON.parse(ratesJson);
                rates.forEach(rate => createRow(rate));
            } catch (err) {
                console.error("Invalid rates JSON", err);
            }
        }
    });
});
</script>
@endpush
