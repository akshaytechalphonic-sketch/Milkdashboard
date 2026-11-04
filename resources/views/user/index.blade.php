@extends('layouts.admin')
@section('content')


    <!-- start page title -->
    <!-- end page title -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-bordered d-flex justify-content-between">
                    <h3 class="card-title">Users</h3>
                    <div class="hstack gap-2 justify-content-end">
                        <!-- <button class="btn btn-primary w-max" data-bs-toggle="modal" data-bs-target="#creatertaskModal"> Add delivery</button> -->
                        <a href="{{ route('user.create') }}" class="btn btn-primary w-max">
                            Add User
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
                    <h4 class="card-title">All Users</h4>
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
                                <th>Email</th>
                                  <th>Total Supplied (by Category)</th>
                                <th>Status</th>
                                <th>Customers</th>
                              
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name ?? '' }}</td>
                                    <td>{{ $user->email ?? '' }}</td>
                                     <td>
                                        @if (!empty($categoryQuantities[$user->id]))
                                            @foreach ($categoryQuantities[$user->id] as $category)
                                                <span class="badge bg-success mb-1">
                                                    {{ $category['categoryname'] }}: {{ $category['total_unit'] }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No data</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('users.toggleStatus', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $user->status ? 'btn-success' : 'btn-warning' }}">
                                                {{ $user->status ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>

                                    <td>{{ $user->customers_count ?? '' }}</td>
                                   
                                    <td>

                                        <ul class="d-flex gap-3 list-unstyled justify-content-center align-items-center">
                                            <!-- Edit Button -->
                                            <li>
                                                <a href="{{ route('user.edit', $user->id) }}"
                                                    class="btn btn-sm btn-primary d-flex align-items-center justify-content-center">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </li>

                                            <!-- Delete Button -->
                                            <!-- <li>

                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </li> -->
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


@endsection
