@extends('layouts.admin')

@section('content')

<div class="row">


</div>
<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Add User</h4>
            <!-- <button type="" class="btn btn-success">Back</button> -->
            <a href="{{ route('user.index') }}" class="btn btn-success w-max">
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

            <form method="POST" action="{{ route('user.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="useremail" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group auth-form-group-custom">
                        <span class="input-group-text bg-primary bg-opacity-10 fs-16" id="basic-addon3">
                            <i class="mdi mdi-email-outline auti-custom-input-icon"></i>
                        </span>
                        <input type="email" class="form-control" name="email" id="useremail"
                            placeholder="Enter email" aria-label="Email" aria-describedby="basic-addon3"
                            value="{{-- $user->email ?? '' --}}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group auth-form-group-custom">
                        <span class="input-group-text bg-primary bg-opacity-10 fs-16" id="basic-addon1">
                            <i class="mdi mdi-account-outline auti-custom-input-icon"></i>
                        </span>
                        <input type="text" class="form-control" name="name" id="username"
                            placeholder="Enter username" aria-label="Username" aria-describedby="basic-addon1"
                            value="{{-- $user->name ?? '' --}}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="userpassword" class="form-label">Password</label>
                    <div class="input-group auth-form-group-custom">
                        <span class="input-group-text bg-primary bg-opacity-10 fs-16" id="basic-addon2">
                            <i class="mdi mdi-lock-outline auti-custom-input-icon"></i>
                        </span>
                        <input type="password" class="form-control" name="password" id="userpassword"
                            placeholder="Enter password "
                            aria-label="Password" aria-describedby="basic-addon2">
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Save User</button>
                </div>

            </form>


        </div>
    </div>
</div>





@endsection