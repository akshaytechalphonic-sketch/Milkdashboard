<?php

use App\Models\User;

$admin = User::where('user_type', 'admin')->first();

?>


<div id="layout-wrapper">

    <!-- Start topbar -->
    <header id="page-topbar">
        <div class="navbar-header">

            <!-- Logo -->

            <!-- Start Navbar-Brand -->
            <div class="navbar-logo-box">
                <a href="{{route('dashboard')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-sm.png')}}" alt="logo-sm-dark" height="20">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/girgovind.png')}}" alt="logo-dark" height="50">
                    </span>
                </a>

                <a href="index.php" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-sm.png')}}" alt="logo-sm-light" height="20">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo-light.png')}}" alt="logo-light" height="18">
                    </span>
                </a>

                <button type="button" class="btn btn-sm top-icon sidebar-btn" id="sidebar-btn">
                    <i class="mdi mdi-menu-open align-middle fs-19"></i>
                </button>
            </div>
            <!-- End navbar brand -->

            <!-- Start menu -->
            <div class="d-flex justify-content-between menu-sm px-3 ms-auto">
                <div class="d-flex align-items-center gap-2">

                </div>

                <div class="d-flex align-items-center gap-2">

                    <!-- Start Profile -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-sm top-icon p-0" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded avatar-2xs p-0" src="{{asset('assets/images/users/avatar-6.png')}}" alt="Header Avatar">
                        </button>
                        <div class="dropdown-menu dropdown-menu-wide dropdown-menu-end dropdown-menu-animated overflow-hidden py-0">
                            <div class="card border-0">
                                <div class="card-header bg-primary rounded-0">
                                    <div class="rich-list-item w-100 p-0">
                                        <div class="rich-list-prepend">
                                            <div class="avatar avatar-label-light avatar-circle">
                                                <div class="avatar-display"><i class="fa fa-user-alt"></i></div>
                                            </div>
                                        </div>
                                        <div class="rich-list-content">
                                            <h3 class="rich-list-title text-white">{{$user->name ?? ''}}</h3>
                                            <span class="rich-list-subtitle text-white">{{$user->email ?? ''}}</span>
                                        </div>

                                    </div>
                                </div>

                                <div class="card-footer card-footer-bordered rounded-0">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>

                                    <!-- Visible logout link -->
                                    <a href="#" class="btn btn-label-danger"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Sign out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Profile -->
                </div>
            </div>
            <!-- End menu -->
        </div>
    </header>
    <!-- End topbar -->

    <!-- ========== Left Sidebar Start ========== -->
    <div class="sidebar-left">

        <div data-simplebar class="h-100">

            <!--- Sidebar-menu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="left-menu list-unstyled" id="side-menu">
                    @if(auth()->user()->user_type === 'admin')
                    <li>
                        <a href="{{route('dashboard')}}" class="">
                            <i class="fas fa-desktop"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @endif
                    <li class="menu-title">Pages</li>
                  @if(auth()->user()->user_type === 'admin')
                    <li>
                        <a href="{{route('category.index')}}"><i class="fas fa-tags"></i> <span>Category</span></a>
                    </li>
                    <li>
                        <a href="{{route('user.index')}}"> <i class="fas fa-user-cog"></i> <span>User Management</span></a>
                    </li>
                    @endif
                    <li>
                        <a href="{{route('customers.index')}}"> <i class="fas fa-users"></i> <span>Customers</span></a>
                    </li>

                    <!-- <li>
                        <a href="{{--route('faq.index')--}}"> <i class="fas fa-users"></i> <span>FAQ'S</span></a>
                    </li> -->

                    @auth
                    @if(auth()->user()->user_type === 'admin')
                    <li>
                        <a href="{{route('deliveries.list')}}"><i class="fas fa-truck"></i> <span>Deliveries</span></a>
                    </li>
                    @endif
                    @endauth
                    @auth
                    @if(auth()->user()->user_type === 'user')
                    <li>
                        <a href="{{route('Delivery.sheet')}}"><i class="fas fa-clipboard-list"></i> <span>Delivery Sheet</span></a>
                    </li>
                    @endif
                    @endauth
                    
                     @if(auth()->user()->user_type === 'admin')
                    <li>
                        <a href="{{route('monthly.sheet')}}"><i class="fas fa-boxes"></i> <span>Monthly Sheet</span></a>
                    </li>
                   

                    <li>
                        <a href="{{route('invoices.index')}}"><i class="fas fa-book"></i> <span>Invoice</span></a>
                    </li>
                    @endif
                    @auth
                    @if(auth()->user()->user_type === 'admin')
                    <li>
                        <a href="{{route('stock.index')}}"><i class="fas fa-box"></i> <span>Stock Management</span></a>
                    </li>
                    @endif
                    @endauth
                </ul>
            </div>
            <!-- Sidebar -->
        </div>
    </div>
    <!-- Left Sidebar End -->