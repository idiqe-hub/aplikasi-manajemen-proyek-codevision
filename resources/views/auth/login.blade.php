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
            .sidebar-solid {
            background: #1b3c53 !important;
            background-image: none !important;
        }
    </style>
</head>

<body class="sidebar-solid">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        {{-- Kiri: ilustrasi --}}
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"
                             style="background: url('{{ asset('sbadmin2/img/undraw_posting_photo.svg') }}') center/contain no-repeat; background-color:#f8f9fc;">
                        </div>

                        {{-- Kanan: form --}}
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <img src="{{ asset('sbadmin2/img/logo-pkl.png') }}" alt="Logo" style="height:60px" class="mb-2">
                                    <h1 class="h4 text-gray-900 mb-1">Login</h1>
                                    <small class="text-muted">Codevision Project Management</small>
                                </div>

                                {{-- status / error --}}
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

                                <form method="POST" action="{{ route('login') }}" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               class="form-control form-control-user"
                                               placeholder="Email"
                                               required autofocus>
                                    </div>

                                    <div class="form-group">
                                        <input type="password"
                                               name="password"
                                               class="form-control form-control-user"
                                               placeholder="Password"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                            <label class="custom-control-label" for="remember_me">Remember me</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                                    </button>
                                </form>

                                <hr>

                                <div class="text-center">
                                    @if (Route::has('password.request'))
                                        <a class="small" href="{{ route('password.request') }}">Lupa Password?</a>
                                    @endif
                                </div>
{{--
                                <div class="text-center mt-2">
                                    <a class="small" href="{{ route('register') }}">
                                        Belum punya akun? Register
                                    </a>
                                </div> --}}

                            </div>
                        </div>
                        {{-- end kanan --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SB Admin 2 JS --}}
<script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
</body>
</html>
