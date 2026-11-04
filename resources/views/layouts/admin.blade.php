 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Codebucks" name="author" />

    <!-- CSS Framework (e.g., Bootstrap or Tailwind) -->
    @include('partials.style')
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('partials.navbar')
    
    <div class="main-content">
        <!-- Main Content -->
         <div class="page-content">
            <div class="container-fluid">
       
            @yield('content')
         </div>
        </div>


        @include('partials.footer')
    </div>
    
    <!-- Scripts -->
    @include('partials.js')
    @stack('scripts')
</body>
</html>
