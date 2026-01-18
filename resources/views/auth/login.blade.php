<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login - PKL Codevision</title>

    {{-- SB Admin 2 CSS --}}
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        body.sidebar-solid {
            min-height: 100vh;
            background:
                radial-gradient(1000px 600px at 20% 10%, rgba(78, 115, 223, .22), transparent 55%),
                radial-gradient(800px 500px at 90% 40%, rgba(28, 200, 138, .18), transparent 55%),
                linear-gradient(135deg, #0b2a3a, #0f3c55);
        }

        .card.o-hidden {
            border-radius: 18px !important;
            overflow: hidden;
            box-shadow: 0 22px 55px rgba(0, 0, 0, .30) !important;
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, .10) !important;
        }

        .bg-login-image {
            position: relative;
            background:
                linear-gradient(180deg, #0b2a3a, #0f3c55);
            overflow: hidden;
        }

        .bg-login-image::before {
            content: "";
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            filter: blur(40px);
        }

        .bg-login-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: url('/sbadmin2/img/logo-pkl.png') center/60% no-repeat;
        }


        .login-title {
            font-weight: 800;
            letter-spacing: .2px;
        }

        .login-sub {
            color: #7a8a9a;
        }

        .input-group-text {
            border-radius: 14px 0 0 14px !important;
            border: 1px solid #e7eef6 !important;
            background: #f6f9fc !important;
            color: #6c7a89 !important;
        }

        .form-control-login {
            height: 46px;
            border-radius: 0 14px 14px 0 !important;
            border: 1px solid #e7eef6 !important;
            background: #f6f9fc !important;
        }

        .form-control-login:focus {
            background: #fff !important;
            border-color: #bcd0ff !important;
            box-shadow: 0 0 0 .2rem rgba(78, 115, 223, .18) !important;
        }

        .btn-login {
            height: 46px;
            border-radius: 14px !important;
            font-weight: 700;
            box-shadow: 0 14px 26px rgba(78, 115, 223, .20);
            transition: .15s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(78, 115, 223, .26);
        }

        .alert {
            border-radius: 14px;
        }
    </style>

</head>

<body class="sidebar-solid">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">

                <div class="card border-0 shadow-lg">
                    <div class="card-body p-0">
                        <div class="p-5">

                            {{-- HEADER --}}
                            <div class="text-center mb-4">
                                <img src="{{ asset('sbadmin2/img/logo-pkl.png') }}" alt="Logo"
                                    style="height:60px" class="mb-2">
                                <h1 class="h4 text-gray-900 login-title mb-1">Login</h1>
                                <small class="login-sub text-muted">Codevision Project Management</small>
                            </div>

                            {{-- STATUS / ERROR --}}
                            @if (session('status'))
                                <div class="alert alert-success small">{{ session('status') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger small mb-3">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- FORM LOGIN --}}
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                {{-- Email --}}
                                <div class="form-group">
                                    <label class="small font-weight-bold text-gray-700">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email"
                                            class="form-control"
                                            value="{{ old('email') }}"
                                            required autofocus>
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="form-group">
                                    <label class="small font-weight-bold text-gray-700">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-lock text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="password" name="password"
                                            class="form-control"
                                            required>
                                    </div>
                                </div>

                                {{-- Remember + Lupa Password --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label" for="remember">Remember me</label>
                                    </div>

                                    @if (Route::has('password.request'))
                                        <a class="small" href="{{ route('password.request') }}">Lupa Password?</a>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary btn-block py-2">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SHOW / HIDE PASSWORD --}}
    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
        }
    </script>

    {{-- SB ADMIN 2 JS --}}
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

</body>

</html>
