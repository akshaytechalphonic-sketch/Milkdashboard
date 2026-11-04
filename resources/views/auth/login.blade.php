<!doctype html>
<html lang="en">


<head>

    <meta charset="utf-8" />
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">


    <!-- dark layout js -->
    <script src="{{asset('assets/js/pages/layout.js')}}"></script>

    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- simplebar css -->
    <link href="{{asset('assets/libs/simplebar/simplebar.min.css')}}" rel="stylesheet">
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body>
    <div class="container-fluid authentication-bg overflow-hidden">
        <div class="bg-overlay"></div>
        <div class="row align-items-center justify-content-center min-vh-100">
            <div class="col-10 col-md-6 col-lg-4 col-xxl-3">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center">
                            <a href="/" class="logo-dark">
                                <img src="{{asset('assets/images/girgovind.png')}}" alt="" height="60" class="auth-logo logo-dark mx-auto">
                            </a>
                            <a href="index.php" class="logo-dark">
                                <img src="{{asset('assets/images/logo-light.png')}}" alt="" height="20" class="auth-logo logo-light mx-auto">
                            </a>


                            <h4 class="mt-4">Welcome Back !</h4>
                            <!-- <p class="text-muted">Sign in to continue to Clivax.</p> -->
                        </div>
                        @if ($errors->any())
                        <div style="color: red;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="p-2 mt-5">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="input-group auth-form-group-custom mb-3">
                                    <span class="input-group-text bg-primary bg-opacity-10 fs-16 " id="basic-addon3"><i class="mdi mdi-email-outline auti-custom-input-icon"></i></span>
                                    <input type="email" name="email" class="form-control" id="useremail" placeholder="Enter email" aria-label="email" aria-describedby="basic-addon3">
                                </div>

                                <div class="input-group auth-form-group-custom mb-3">
                                    <span class="input-group-text bg-primary bg-opacity-10 fs-16" id="basic-addon2"><i class="mdi mdi-lock-outline auti-custom-input-icon"></i></span>
                                    <input type="password" name="password" class="form-control" id="userpassword" placeholder="Enter password" aria-label="Username" aria-describedby="basic-addon1">
                                </div>

                                <div class="mb-sm-5">
                                    <div class="form-check float-sm-start">
                                        <input type="checkbox" class="form-check-input" id="customControlInline" name="remember">
                                        <label class="form-check-label" for="customControlInline">Remember me</label>
                                    </div>
                                    <div class="float-sm-end">
                                        <a href="{{route('password.request')}}" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                    </div>
                                </div>

                                <div class="pt-3 text-center">
                                    <button class="btn btn-primary w-xl waves-effect waves-light" type="submit">Log In</button>
                                </div>

                                <!-- <div class="mt-3 text-center">
                                    <p class="mb-0">Don't have an account ? <a href="{{route('register')}}" class="fw-medium text-primary"> Register </a> </p>
                                </div> -->


                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

    <script src="{{asset('assets/js/app.js')}}"></script>

</body>


</html>