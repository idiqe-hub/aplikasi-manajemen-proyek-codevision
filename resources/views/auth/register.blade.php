<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Register - PKL Codevision</title>

    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block"
                             style="background: url('{{ asset('sbadmin2/img/undraw_profile.svg') }}') center/contain no-repeat; background-color:#f8f9fc;">
                        </div>

                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <h1 class="h4 text-gray-900 mb-1">Buat Akun</h1>
                                    <small class="text-muted">Sistem PKL Codevision</small>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger small mb-3">
                                        <ul class="mb-0 pl-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register') }}" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <input type="text" name="name" value="{{ old('name') }}"
                                               class="form-control form-control-user" placeholder="Nama" required>
                                    </div>

                                    <div class="form-group">
                                        <input type="email" name="email" value="{{ old('email') }}"
                                               class="form-control form-control-user" placeholder="Email" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password"
                                               class="form-control form-control-user" placeholder="Password" required>
                                    </div>

                                    <div class="form-group">
                                        <input type="password" name="password_confirmation"
                                               class="form-control form-control-user" placeholder="Ulangi Password" required>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-user btn-block">
                                        <i class="fas fa-user-plus mr-1"></i> Register
                                    </button>
                                </form>

                                <hr>
                                <div class="text-center">
                                    <a class="small" href="{{ route('login') }}">Sudah punya akun? Login</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
</body>
</html>