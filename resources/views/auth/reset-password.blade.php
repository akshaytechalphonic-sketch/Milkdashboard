<!doctype html>
<html lang="en">


<head>

    <meta charset="utf-8" />
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Codebucks" name="author" />
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
                            <div>
                                <a href="index.php" class="logo-dark">
                                    <img src="{{asset('assets/images/girgovind.png')}}" alt="" height="60" class="auth-logo logo-dark mx-auto">
                                </a>
                                <a href="index.php" class="logo-light">
                                    <img src="{{asset('assets/images/girgovind.png')}}" alt="" height="20" class="auth-logo logo-light mx-auto">
                                </a>
                            </div>

                            <h4 class="font-size-18 mt-4">Reset Password</h4>
                            <!-- <p class="text-muted">Get your free Clivax account now.</p> -->
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
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                <div class="mb-3">
                                    <label>New Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-success">Reset Password</button>
                            </form>

                        </div>
                        <!-- <div class="mt-5 text-center">
                            <p>©
                                <script>document.write(new Date().getFullYear())</script> Clivax. Crafted with <i class="mdi mdi-heart text-danger"></i> by Codubucks
                            </p>
                        </div> -->
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