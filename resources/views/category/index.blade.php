@extends('layouts.admin')
@section('content')


<!-- start page title -->

<!-- end page title -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-bordered d-flex justify-content-between">
                <h3 class="card-title">Category</h3>
                <div class="hstack gap-2 justify-content-end">
                    <button class="btn btn-primary w-max" data-bs-toggle="modal" data-bs-target="#creatertaskModal"> Add Category</button>
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
                <h4 class="card-title">All Category</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <li>{{ session('error') }}</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

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
                <table id="datatable-buttons" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>Name</th>
                            <th>Store Unit</th>
                            <th>Default Rate (₹)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($categories as $index =>$category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                <span class="badge bg-soft-info text-info">
                                    {{ $category->store_type == 'L' ? 'Liter' : ($category->store_type == 'kg' ? 'KG' : $category->store_type) }}
                                </span>
                            </td>
                            <td><strong class="text-success">₹{{ number_format($category->rate ?? 0, 2) }}</strong></td>
                            <td>
                                <ul class="d-flex gap-3 list-unstyled justify-content-center align-items-center mb-0">
                                    <!-- Edit Category Button -->
                                    <li>
                                        <a href="#!"
                                            class="btn btn-sm btn-primary edit-category-btn d-flex align-items-center justify-content-center"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-type="{{ $category->store_type }}"
                                            data-rate="{{ $category->rate }}"
                                            data-route="{{ url('category/update') }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBlogModal"
                                            title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </li>

                                    <!-- Delete Category Button -->
                                    <li>
                                        <form action="{{ route('category.destroy', $category->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </li>
                                </ul>

                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
</div>
</div> <!-- container-fluid -->

<!-- END layout-wrapper -->


<!-- add Category modal  -->

<div class="modal fade" id="creatertaskModal" tabindex="-1" aria-labelledby="creatertaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title" id="creatertaskModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('category.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label for="categoryName" class="form-label">Category Name</label> <span class="text-danger">*</span>
                            <input type="text" name="name" class="form-control" id="categoryName" placeholder="Category Name" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="categoryRate" class="form-label">Default Rate (₹)</label> <span class="text-danger">*</span>
                            <input type="number" step="0.01" name="rate" class="form-control" id="categoryRate" placeholder="Enter Rate (e.g. 82)" required>
                        </div>
                        <div class="col-lg-12 mt-3">
                            <label class="form-label d-block">Store Type <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="store_type" id="createStoreKg" value="kg">
                                <label class="form-check-label" for="createStoreKg">KG</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="store_type" id="createStoreLitre" value="L" checked>
                                <label class="form-check-label" for="createStoreLitre">Liter</label>
                            </div>
                        </div>

                        <!--end col-->
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add Category</button>
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
<!-- add modal end    -->

<!-- Edit Category Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-soft-info">
                <h5 class="modal-title" id="editBlogModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <label for="editProjectName" class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" id="editProjectName" placeholder="Category Name" required>
                            <input type="hidden" name="id" id="editCategoryId">
                        </div>

                        <div class="col-lg-12">
                            <label for="editCategoryRate" class="form-label">Default Rate (₹)</label> <span class="text-danger">*</span>
                            <input type="number" step="0.01" name="rate" class="form-control" id="editCategoryRate" placeholder="Enter Rate" required>
                        </div>

                        <div class="col-lg-12 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="update_all_customers" id="updateAllCustomers" value="1" checked>
                                <label class="form-check-label text-primary fw-semibold" for="updateAllCustomers">
                                    Update this rate for all existing customers who use this category
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <label class="form-label d-block">Store Type</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="store_type" id="storeKg" value="kg">
                                <label class="form-check-label" for="storeKg">KG</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="store_type" id="storeLitre" value="L">
                                <label class="form-check-label" for="storeLitre">Liter</label>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Update Category</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- edit modal end    -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-category-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                const categoryName = this.getAttribute('data-name');
                const storeType = this.getAttribute('data-type');
                const rate = this.getAttribute('data-rate');
                const updateRoute = this.getAttribute('data-route');

                // Fill the modal fields
                document.getElementById('editCategoryId').value = categoryId;
                document.getElementById('editProjectName').value = categoryName;
                document.getElementById('editCategoryRate').value = rate || '0';

                // Set the radio button based on storeType
                if (storeType === 'kg') {
                    document.getElementById('storeKg').checked = true;
                } else {
                    document.getElementById('storeLitre').checked = true;
                }

                // Set form action
                const form = document.querySelector('#editBlogModal form');
                form.action = `${updateRoute}/${categoryId}`;
            });
        });
    });
</script>

@endsection
