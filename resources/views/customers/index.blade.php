@extends('layouts.admin')
@section('content')



    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-bordered d-flex justify-content-between">
                    <h3 class="card-title">Customer</h3>
                    <div class="hstack gap-2 justify-content-end">
                        @auth
                            @if (auth()->user()->user_type === 'admin')
                                <button class="btn btn-primary w-max" data-bs-toggle="modal" data-bs-target="#creatertaskModal">
                                    Add Customer</button>
                            @endif
                        @endauth

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
                    <h4 class="card-title">All Customers</h4>
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
                                <th>Name</th>
                                <th>number</th>
                                <th>email</th>
                                <th>Address</th>
                                @auth
                                    @if (auth()->user()->user_type === 'admin')
                                        <th>Status</th>

                                        <th>Action</th>
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
                                    @auth
                                        @if (auth()->user()->user_type === 'admin')
                                            <td>
                                                <form action="{{ route('customers.togglePause', $customer->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $customer->is_paused ? 'btn-danger' : 'btn-success' }}">
                                                        {{ $customer->is_paused ? 'Inactive' : 'Active' }}
                                                    </button>
                                                </form>
                                            </td>

                                            <td>
                                                <ul
                                                    class="d-flex gap-3 list-unstyled justify-content-center align-items-center">
                                                    <!-- Edit Button -->
                                                    <li>
                                                        <a class=" btn-sm btn-info btn"
                                                            href="{{ route('customers.show', ['customer' => $customer->id]) }}">
                                                            <i class="fa fa-credit-card"></i></a>
                                                    </li>
                                                    <li>
                                                        <a href="#!" class="btn btn-sm btn-primary edit-customer-btn"
                                                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                                            data-address="{{ $customer->address }}"
                                                            data-email="{{ $customer->email }}"
                                                            data-number="{{ $customer->number }}"
                                                            data-area="{{ $customer->area }}"
                                                            data-amount_type="{{ $customer->amount_type }}"
                                                            data-old_balance="{{ $customer->old_balance }}"
                                                            data-rates='@json($customer->category_rates ?? [])'
                                                            data-route="{{ route('customers.update', ['customer' => $customer->id]) }}"
                                                            data-bs-toggle="modal" data-bs-target="#editCustomerModal">
                                                            <i class="fa fa-edit"></i>
                                                        </a>

                                                        <!-- Delete Button -->
                                                    <li>
                                                        @auth
                                                            @if (auth()->user()->user_type === 'admin')
                                                                <form action="{{ route('customers.destroy', $customer->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger d-flex align-items-center justify-content-center"
                                                                        title="Delete">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endauth
                                                    </li>
                                                    </li>

                                                    {{-- <li>
                                                        <a href="{{ route('invoices.generate', ['customer_id' => $customer->id, 'year' => now()->year, 'month' => now()->format('F')]) }}"
                                                            class="btn btn-sm btn-warning">
                                                            Generate Invoice
                                                        </a>
                                                    </li> --}}

                                                </ul>

                                            </td>
                                        @endif
                                    @endauth
                                </tr>
                            @endforeach

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    </div> <!-- container-fluid -->

    <!-- END layout-wrapper -->


    <!-- add Blog modal  -->

    <div class="modal fade" id="creatertaskModal" tabindex="-1" aria-labelledby="creatertaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-info">
                    @can('admin')
                        <h5 class="modal-title" id="creatertaskModalLabel">Add Customer</h5>
                    @endcan
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="user" class="form-label">Delivery Man</label> <span
                                    class="text-danger">*</span>
                                <select name="user_id" class="form-select" id="user_id" required>
                                    <option value="">-- Please Select --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Customer Name</label> <span
                                    class="text-danger">*</span>
                                <!-- <input type="text" name="name" class="form-control" id="projectName" placeholder="Category Name"> -->
                                <input type="text" name="name" class="form-control" id=""
                                    placeholder="Customer Name" required>

                            </div>

                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Address</label> <span
                                    class="text-danger">*</span>
                                <input type="text" name="address" value="" class="form-control" id=""
                                    placeholder="Address" required>

                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Email</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="email" value="" class="form-control" id=""
                                    placeholder="Email" required>

                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Phone</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="number" value="" class="form-control" id=""
                                    placeholder="Phone" required>

                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Area</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="area" value="" class="form-control" id=""
                                    placeholder="Area" required>

                            </div>

                            <!-- <div class="row g-3" id="category-rate-group"> -->
                            <div class="row g-3 mt-2 category-rate-clone">
                                <div class="col-lg-3">
                                    <label class="form-label">Select Category <span class="text-danger">*</span></label>
                                    <select name="category_id[]" class="form-select category-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                data-store_type="{{ $category->store_type }}"
                                                data-rate="{{ $category->rate }}">{{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="category_name[]" class="category-name">
                                    <input type="hidden" name="store_type[]" class="store-type">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Unit</label>
                                    <div class="input-group">
                                        <span class="input-group-text unit-prefix">--</span>
                                        <input type="number" class="form-control" name="unit[]" step="0.01"
                                            placeholder="Enter Value" required>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Rate <span class="text-danger">*</span></label>
                                    <input type="text" name="rate[]" class="form-control" placeholder="Rate"
                                        required>
                                </div>

                                <div class="col-lg-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-success" id="add-more-fields">+</button>
                                </div>
                            </div>

                            <!-- </div> -->

                            <!-- Container to append new fields -->
                            <div id="extra-category-rate-fields"></div>
                            <div class="col-lg-3">
                                <input class="form-check-input" type="checkbox" name="store_type" id="remaining_amount"
                                    value="liter">
                                <label class="form-check-label" for="remaining_amount">Remaining Amount</label>
                            </div>
                            <div class="col-md-4 d-none select_type" id="select_type">
                                <label for="amount_type" class="form-label">Select Type</label> <span
                                    class="text-danger">*</span>
                                <select name="amount_type" class="form-select" id="amount_type">
                                    <option value="">-- Please Select --</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Advance</option>
                                </select>
                            </div>
                            <div class="col-lg-4 d-none select_type" id="old_balance_wrapper">
                                <label for="old_balance" class="form-label">Old Balance</label>
                                <input type="text" name="old_balance" class="form-control" id="old_balance"
                                    placeholder="Old Balance">
                            </div>


                            <!--end col-->
                            <div class="mt-4">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Add Customer</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- blog modal end    -->

    <!-- edit Blog modal  -->

    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-soft-info">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="user" class="form-label">Delivery Man</label> <span
                                    class="text-danger">*</span>
                                <select name="user_id" class="form-select" id="user_id" required>
                                    <option value="">-- Please Select --</option>
                                    @foreach ($users as $user)
                                      
                                          <option value="{{ $user->id }}"
                                            {{ $customer->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                          </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Customer Name</label>
                                <input type="text" name="name" class="form-control" id="projectName"
                                    placeholder="Customer Name" required>
                                <input type="hidden" name="id" id="editCustomerId">
                            </div>

                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Phone</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="number" value="" class="form-control" id="number"
                                    placeholder="Phone" required>

                            </div>

                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Address</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="address" value="" class="form-control" id="address"
                                    placeholder="Address" required>

                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Email</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="email" value="" class="form-control" id="email"
                                    placeholder="Email" required>

                            </div>
                            <div class="col-lg-6">
                                <label for="projectName" class="form-label">Area</label><span
                                    class="text-danger">*</span>
                                <input type="text" name="area" value="" class="form-control" id="area"
                                    placeholder="area" required>

                            </div>
                            <div id="edit-category-rate-fields" class="w-100"></div>

                            <div class="col-lg-12">
                                <button type="button" class="btn btn-success mt-2" id="add-more-field">+ Add
                                    More</button>
                            </div>
                            <div class="col-lg-3">
                                <input class="form-check-input" type="checkbox" name="store_type"
                                    id="edit_remaining_amount" value="liter">
                                <label class="form-check-label" for="edit_remaining_amount">Remaining Amount</label>
                            </div>
                            <div class="col-md-4 d-none edit_select_type" id="edit_select_type">
                                <label for="edit_amount_type" class="form-label">Select Type</label> <span
                                    class="text-danger">*</span>
                                <select name="amount_type" class="form-select" id="edit_amount_type">
                                    <option value="">-- Please Select --</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Advance</option>
                                </select>
                            </div>
                            <div class="col-lg-4 d-none edit_select_type" id="edit_old_balance_wrapper">
                                <label for="edit_old_balance" class="form-label">Old Balance</label>
                                <input type="text" name="old_balance" class="form-control" id="edit_old_balance"
                                    placeholder="Old Balance">
                            </div>

                            <div class="mt-4">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Update Customer</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- edit blog modal end    -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Add more fields
            $('#add-more-fields').on('click', function() {
                let html = `
                <div class="row g-3 mt-2 category-rate-clone">
                    <div class="col-lg-3">
                        <label class="form-label">Select Category</label>
                        <select name="category_id[]" class="form-select category-select" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-store_type="{{ $category->store_type }}" data-rate="{{ $category->rate }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="category_name[]" class="category-name">
                        <input type="hidden" name="store_type[]" class="store-type">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit</label>
                        <div class="input-group">
                            <span class="input-group-text unit-prefix">--</span>
                            <input type="number" class="form-control" name="unit[]" step="0.01" placeholder="Enter Value" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Rate</label>
                        <input type="text" name="rate[]" class="form-control" placeholder="Rate" required>
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-field">-</button>
                    </div>
                </div>
            `;
                const $newField = $(html);
                $('#extra-category-rate-fields').append($newField);

                // Update hidden category name
                const selectedOption = $newField.find('.category-select option:selected');
                $newField.find('.category-name').val(selectedOption.text().trim());
            });

            // Remove field
            $(document).on('click', '.remove-field', function() {
                $(this).closest('.category-rate-clone').remove();
            });

            // Handle category change to update unit, category name and auto-fill rate
            $(document).on('change', '.category-select', function() {
                const selectedOption = $(this).find(':selected');
                const rawStoreType = (selectedOption.data('store_type') || '').toString().trim().toLowerCase();
                const storeType = rawStoreType === "l" ? 1 : 2;
                const categoryName = selectedOption.text().trim();
                const rate = selectedOption.data('rate');

                const wrapper = $(this).closest('.category-rate-clone');
                wrapper.find('.unit-prefix').text(rawStoreType.toUpperCase() || '--');
                wrapper.find('.category-name').val(categoryName);
                wrapper.find('.store-type').val(storeType);

                if (rate !== undefined && rate !== null && rate !== '') {
                    wrapper.find('input[name="rate[]"]').val(rate);
                }
            });

            // ✅ Trigger .change() on all existing .category-select to initialize unit & category name
            $('.category-select').each(function() {
                $(this).trigger('change');
            });
        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {


            const editButtons = document.querySelectorAll('.edit-customer-btn');
            const editFieldsContainer = document.getElementById('edit-category-rate-fields');
            const categories = @json($categories); // Pass categories to JS

            const remainingAmountCheckbox = document.getElementById('remaining_amount');
            const selectTypeDiv = document.getElementById('select_type');
            const oldBalanceDiv = document.getElementById('old_balance_wrapper');
            const amountTypeSelect = document.getElementById('amount_type');
            const oldBalanceInput = document.getElementById('old_balance');

            function toggleRemainingFields(isChecked) {
                selectTypeDiv.classList.toggle('d-none', !isChecked);
                oldBalanceDiv.classList.toggle('d-none', !isChecked);

                amountTypeSelect.required = isChecked;
                oldBalanceInput.required = isChecked;

                if (!isChecked) {
                    amountTypeSelect.value = '';
                    oldBalanceInput.value = '';
                }
            }

            if (remainingAmountCheckbox) {
                remainingAmountCheckbox.addEventListener('change', function() {
                    toggleRemainingFields(this.checked);
                });
            }

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const customerId = this.getAttribute('data-id');
                    const customerName = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const number = this.getAttribute('data-number');
                    const area = this.getAttribute('data-area');
                    const old_balance = this.getAttribute('data-old_balance');
                    const address = this.getAttribute('data-address');
                    const amountType = this.getAttribute('data-amount_type');
                    const updateRoute = this.getAttribute('data-route');
                    const ratesData = JSON.parse(this.getAttribute('data-rates') || '[]');

                    document.getElementById('editCustomerId').value = customerId;
                    document.getElementById('projectName').value = customerName;
                    document.getElementById('email').value = email;
                    document.getElementById('number').value = number;
                    document.getElementById('address').value = address;
                    document.getElementById('area').value = area;
                    document.getElementById('old_balance').value = old_balance;
                    document.getElementById('editCustomerForm').action = updateRoute;

                    editFieldsContainer.innerHTML = '';

                    if (ratesData.length === 0) {
                        addCategoryRateRow();
                    } else {
                        ratesData.forEach(item => {
                            addCategoryRateRow(item.category_id, item.rate, item.unit);
                        });
                    }
                });
            });

            document.getElementById('add-more-field').addEventListener('click', function() {
                addCategoryRateRow();
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-field')) {
                    e.target.closest('.category-rate-clone').remove();
                }
            });

            function addCategoryRateRow(selectedCategory = '', rateValue = '', unitValue = '') {
                let selectHtml = `<select name="category_id[]" class="form-select category-select" required>
                <option value="">-- Select Category --</option>`;

                categories.forEach(cat => {
                    const isSelected = String(cat.id) === String(selectedCategory) ? 'selected' : '';
                    selectHtml +=
                        `<option value="${cat.id}" data-store_type="${cat.store_type}" data-rate="${cat.rate || ''}" ${isSelected}>${cat.name}</option>`;
                });

                selectHtml += `</select>`;

                const selectedCategoryObj = categories.find(cat => String(cat.id) === String(selectedCategory));
                const rawStoreType = selectedCategoryObj ? selectedCategoryObj.store_type : '--';
                const storeType = rawStoreType && rawStoreType.toString().toLowerCase() === 'l' ? 1 : 2;
                const categoryName = selectedCategoryObj ? selectedCategoryObj.name : '';
                const finalRate = (rateValue !== '' && rateValue !== null && rateValue !== undefined)
                    ? rateValue
                    : (selectedCategoryObj && selectedCategoryObj.rate !== undefined ? selectedCategoryObj.rate : '');

                const html = `
            <div class="row g-3 mt-2 category-rate-clone">
                <div class="col-lg-3">
                    <label class="form-label">Select Category</label>
                    ${selectHtml}
                    <input type="hidden" name="category_name[]" class="category-name" value="${categoryName}">
                    <input type="hidden" name="store_type[]" class="store-type" value="${storeType}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit</label>
                    <div class="input-group">
                        <span class="input-group-text unit-prefix">${(rawStoreType || '--').toUpperCase()}</span>
                        <input type="number" class="form-control" name="unit[]" step="0.01" placeholder="Enter Value" required value="${unitValue}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Rate</label>
                    <input type="text" name="rate[]" class="form-control" placeholder="Rate" required value="${finalRate}">
                </div>
                <div class="col-lg-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-field">-</button>
                </div>
            </div>
        `;

                editFieldsContainer.insertAdjacentHTML('beforeend', html);
            }
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('remaining_amount');
            const selectWrapper = document.getElementById('select_type');
            const oldBalanceWrapper = document.getElementById('old_balance_wrapper');
            const amountType = document.getElementById('amount_type');
            const oldBalance = document.getElementById('old_balance');

            checkbox.addEventListener('change', function() {
                const isChecked = this.checked;

                // Toggle visibility
                selectWrapper.classList.toggle('d-none', !isChecked);
                oldBalanceWrapper.classList.toggle('d-none', !isChecked);

                // Toggle required attributes
                amountType.required = isChecked;
                oldBalance.required = isChecked;

                // Clear values if hidden
                if (!isChecked) {
                    amountType.value = '';
                    oldBalance.value = '';
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editCheckbox = document.getElementById('edit_remaining_amount');
            const editSelectType = document.getElementById('edit_select_type');
            const editOldBalanceWrapper = document.getElementById('edit_old_balance_wrapper');
            const editAmountType = document.getElementById('edit_amount_type');
            const editOldBalance = document.getElementById('edit_old_balance');

            if (editCheckbox) {
                editCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;

                    editSelectType.classList.toggle('d-none', !isChecked);
                    editOldBalanceWrapper.classList.toggle('d-none', !isChecked);

                    editAmountType.required = isChecked;
                    editOldBalance.required = isChecked;

                    if (!isChecked) {
                        editAmountType.value = '';
                        editOldBalance.value = '';
                    }
                });
            }

            // Prefill checkbox and related fields when edit button is clicked
            const editButtons = document.querySelectorAll('.edit-customer-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const old_balance = this.getAttribute('data-old_balance');
                    const amount_type = this.getAttribute('data-amount_type');

                    // Reset checkbox and related fields
                    if (old_balance || amount_type) {
                        editCheckbox.checked = true;
                        editAmountType.value = amount_type || '';
                        editOldBalance.value = old_balance || '';

                        editSelectType.classList.remove('d-none');
                        editOldBalanceWrapper.classList.remove('d-none');
                        editAmountType.required = true;
                        editOldBalance.required = true;
                    } else {
                        editCheckbox.checked = false;
                        editSelectType.classList.add('d-none');
                        editOldBalanceWrapper.classList.add('d-none');
                        editAmountType.required = false;
                        editOldBalance.required = false;
                        editAmountType.value = '';
                        editOldBalance.value = '';
                    }
                });
            });
        });
    </script>
@endpush
