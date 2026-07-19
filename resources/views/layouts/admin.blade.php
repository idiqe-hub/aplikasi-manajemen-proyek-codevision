<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'PKL Codevision')</title>

    {{-- SB Admin 2 CSS --}}
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/custom.css') }}" rel="stylesheet">

    {{-- Wireframe Mode CSS (dimuat selalu; aktif hanya jika body punya class wireframe-mode) --}}
    <link href="{{ asset('sbadmin2/css/wireframe.css') }}" rel="stylesheet" id="wireframeCss">

    {{-- CSRF Token untuk AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Sidebar solid */
        .sidebar-solid {
            background: #1b3c53 !important;
            background-image: none !important;
        }

        /* Link warna */
        .sidebar-solid .nav-item .nav-link {
            color: rgba(255, 255, 255, .9) !important;
        }

        .sidebar-solid .nav-item .nav-link i {
            color: rgba(255, 255, 255, .8) !important;
        }

        /* Hover & Active */
        .sidebar-solid .nav-item .nav-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, .10);
        }

        .sidebar-solid .nav-item.active .nav-link {
            color: #fff !important;
            background: rgba(0, 0, 0, .18);
        }

        .sidebar-solid hr.sidebar-divider {
            border-color: rgba(255, 255, 255, .12);
        }

        .brand-stack {
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 18px 12px !important;
            height: auto !important;
            text-decoration: none;
        }

        .brand-logo {
            background: #fff;
            padding: 4px 5px;
            border-radius: 12px;
            line-height: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .18);
        }

        .brand-logo img {
            height: 36px;
            width: auto;
            display: block;
        }

        .brand-text {
            text-align: center;
            line-height: 1.1;
        }

        .brand-title {
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, .95);
        }

        .brand-subtitle {
            margin-top: 4px;
            font-size: 11px;
            letter-spacing: .6px;
            color: rgba(255, 255, 255, .75);
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">

        {{-- Sidebar --}}

        <ul class="navbar-nav sidebar sidebar-dark accordion sidebar-solid sidebar-fixed" id="accordionSidebar">


            <a class="sidebar-brand brand-stack" href="{{ route('dashboard') }}">

                {{-- Logo PKL --}}
                <div class="brand-logo">
                    <img src="{{ asset('sbadmin2/img/logo-pkl.png') }}" alt="Logo PKL">
                </div>

                {{-- Text --}}
                <div class="brand-text">
                    <div class="brand-title">CODEVISION</div>
                </div>

            </a>



            <hr class="sidebar-divider my-0">

            {{-- Menu Dashboard --}}
            <li class="nav-item {{ request()->routeIs('dashboard', 'client.dashboard') ? 'active' : '' }}">
                @auth
                    @if(auth()->user()->role === 'client')
                        <a class="nav-link" href="{{ route('client.dashboard') }}">
                    @else
                        <a class="nav-link" href="{{ route('dashboard') }}">
                    @endif
                @endauth
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                    </a>
            </li>

            @auth
                @php $role = auth()->user()->role; @endphp

                {{-- ============================================
                     MENU ADMIN & DEVELOPER
                     ============================================ --}}
                @if($role === 'admin' || $role === 'developer')
                    <li class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('tasks.index') }}">
                            <i class="fas fa-fw fa-tasks"></i>
                            <span>Data Tugas</span>
                        </a>
                    </li>
                @endif

                {{-- ============================================
                     MENU ADMIN ONLY
                     ============================================ --}}
                @if($role === 'admin')
                    <li class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('projects.index') }}">
                            <i class="fas fa-fw fa-folder"></i>
                            <span>Data Proyek</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('clients.index') }}">
                            <i class="fas fa-fw fa-user-tie"></i>
                            <span>Data Client</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('developers.index', 'developers.show', 'developers.create', 'developers.edit') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('developers.index') }}">
                            <i class="fas fa-fw fa-users"></i>
                            <span>Data Developer</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('developers.capacity') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('developers.capacity') }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Kapasitas Developer</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('reports.index') }}">
                            <i class="fas fa-fw fa-chart-bar"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                @endif

                {{-- Menu Notifikasi: Admin dan Developer saja --}}
                @if(in_array($role, ['admin', 'developer']))
                    <li class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('notifications.index') }}">
                            <i class="fas fa-fw fa-bell"></i>
                            <span>Notifikasi</span>
                            @php $nb = auth()->user()->unreadNotifications->count(); @endphp
                            @if($nb > 0)
                                <span class="badge badge-danger ml-1">{{ $nb > 9 ? '9+' : $nb }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                {{-- ============================================
                     MENU CLIENT: hanya Dashboard
                     (tidak ada akses ke fitur admin/developer)
                     ============================================ --}}
                {{-- Menu client tidak perlu ditambahkan di sini
                     karena proteksi sudah di middleware route --}}

            @endauth


            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>


        {{-- End Sidebar --}}

        {{-- Content Wrapper --}}
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                {{-- Topbar --}}
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    {{-- Wireframe Mode Toggle --}}
                    <button id="wireframeToggle"
                        class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center mr-2"
                        title="Toggle Wireframe Mode"
                        style="border:1.5px solid #bbb; border-radius:4px; padding:3px 10px; gap:5px;">
                        <i class="fas fa-drafting-compass fa-sm mr-1"></i>
                        <span id="wireframeLabel">Wireframe</span>
                    </button>

                    <div class="d-none d-sm-inline-block font-weight-bold text-gray-700">
                        @yield('page_title', 'Dashboard')
                    </div>

                    <ul class="navbar-nav ml-auto">

                        {{-- Bell Icon Notifikasi (admin & developer) --}}
                        @auth
                            @if(in_array(auth()->user()->role, ['admin', 'developer']))
                                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                                <li class="nav-item mr-2">
                                    <a class="nav-link position-relative" href="{{ route('notifications.index') }}" title="Notifikasi">
                                        <i class="fas fa-bell fa-lg text-gray-500"></i>
                                        @if($unreadCount > 0)
                                            <span class="badge badge-danger badge-counter" style="position:absolute;top:5px;right:0;font-size:10px;">
                                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endauth

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ auth()->user()->name }}
                                </span>
                                <i class="fas fa-user-circle fa-lg text-gray-600"></i>
                            </a>

                            {{-- Dropdown - User Information --}}
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

                                <a class="dropdown-item" href="{{ route('account.profile') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                                <a class="dropdown-item" href="{{ route('account.password.edit') }}">
                                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Ganti Password
                                </a>

                                <div class="dropdown-divider"></div>

                                {{-- Logout harus POST --}}
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                {{-- End Topbar --}}

                <div class="container-fluid">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div class="font-weight-bold mb-1">Terjadi error:</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>

            </div>

            {{-- Footer --}}
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; Codevision {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    {{-- SB Admin 2 JS --}}
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    {{-- Stack untuk scripts tambahan dari halaman child (misal: Kanban SortableJS) --}}
    @stack('scripts')

    {{-- ═══════════════════════════════════════════════
         WIREFRAME MODE — Toggle Script
         Menyimpan preferensi ke localStorage.
         ═══════════════════════════════════════════════ --}}
    <script>
    (function () {
        var STORAGE_KEY = 'codevision_wireframe_mode';
        var body        = document.body;
        var btn         = document.getElementById('wireframeToggle');
        var label       = document.getElementById('wireframeLabel');

        function applyWireframe(active) {
            if (active) {
                body.classList.add('wireframe-mode');
                if (btn)   btn.classList.add('active');
                if (label) label.textContent = 'Normal Mode';
                localStorage.setItem(STORAGE_KEY, '1');
            } else {
                body.classList.remove('wireframe-mode');
                if (btn)   btn.classList.remove('active');
                if (label) label.textContent = 'Wireframe';
                localStorage.removeItem(STORAGE_KEY);
            }
        }

        // Restore dari localStorage saat halaman load
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            applyWireframe(true);
        }

        // Klik toggle
        if (btn) {
            btn.addEventListener('click', function () {
                var isActive = body.classList.contains('wireframe-mode');
                applyWireframe(!isActive);
            });
        }
    })();
    </script>

</body>

</html>
