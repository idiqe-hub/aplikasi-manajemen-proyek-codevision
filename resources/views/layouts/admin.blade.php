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

    {{-- ── CSS Transition & Animation Global ── --}}
    <style>
        /* ── Smooth sidebar link transition ── */
        .sidebar-solid .nav-item .nav-link {
            transition: background 0.18s ease, color 0.18s ease !important;
        }

        /* ── Card hover shadow elevation ── */
        .card {
            transition: box-shadow 0.22s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.12) !important;
        }

        /* ── Modal fade + slight scale (Bootstrap 4 compatible) ── */
        .modal.fade .modal-dialog {
            transform: scale(0.96) translateY(-8px);
            transition: transform 0.22s cubic-bezier(.4,0,.2,1), opacity 0.22s ease;
        }
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }

        /* ── Global Toast Container ── */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .cv-toast {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 280px;
            max-width: 380px;
            padding: 14px 18px;
            border-radius: 8px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.18);
            font-size: 0.88rem;
            font-weight: 500;
            pointer-events: all;
            opacity: 0;
            transform: translateX(30px);
            transition: opacity 0.28s ease, transform 0.28s cubic-bezier(.4,0,.2,1);
            color: #fff;
        }
        .cv-toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        .cv-toast.cv-toast-success { background: #1cc88a; }
        .cv-toast.cv-toast-error   { background: #e74a3b; }
        .cv-toast.cv-toast-info    { background: #36b9cc; }
        .cv-toast.cv-toast-warning { background: #f6c23e; color: #333; }
        .cv-toast .cv-toast-icon   { font-size: 1.1rem; flex-shrink: 0; }
        .cv-toast .cv-toast-close  {
            margin-left: auto;
            background: none;
            border: none;
            color: inherit;
            font-size: 1rem;
            cursor: pointer;
            opacity: 0.8;
            padding: 0;
            line-height: 1;
        }

        /* ── Page loading bar ── */
        #cv-page-loader {
            position: fixed;
            top: 0; left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #4e73df, #36b9cc);
            z-index: 99999;
            transition: width 0.3s ease;
            border-radius: 0 2px 2px 0;
        }
    </style>

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
            <li class="nav-item {{ request()->routeIs('dashboard', 'developer.dashboard', 'client.dashboard') ? 'active' : '' }}">
                @auth
                    @if(auth()->user()->role === 'client')
                        <a class="nav-link" href="{{ route('client.dashboard') }}">
                    @elseif(auth()->user()->role === 'developer')
                        <a class="nav-link" href="{{ route('developer.dashboard') }}">
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

                    <li class="nav-item {{ request()->routeIs('kpi.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('kpi.index') }}">
                            <i class="fas fa-fw fa-star"></i>
                            <span>KPI & Performa</span>
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

                                @if(auth()->user()->role === 'admin')
                                <a class="dropdown-item" href="{{ route('account.email.edit') }}">
                                    <i class="fas fa-envelope fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Ubah Email
                                </a>
                                @endif

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

    {{-- ── Toast Container ── --}}
    <div id="toast-container"></div>

    {{-- ── Page loading bar ── --}}
    <div id="cv-page-loader"></div>

    {{-- SB Admin 2 JS --}}
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    {{-- Stack untuk scripts tambahan dari halaman child (misal: Kanban SortableJS) --}}
    @stack('scripts')

    {{-- ══════════════════════════════════════════════════
         GLOBAL TOAST NOTIFICATION SYSTEM
         Panggil: window.showToast('Pesan', 'success|error|info|warning')
         ═══════════════════════════════════════════════ --}}
    <script>
    (function () {
        'use strict';

        var iconMap = {
            success : 'fa-check-circle',
            error   : 'fa-times-circle',
            info    : 'fa-info-circle',
            warning : 'fa-exclamation-triangle'
        };

        window.showToast = function (message, type) {
            type = type || 'success';
            var container = document.getElementById('toast-container');
            if (!container) return;

            var toast = document.createElement('div');
            toast.className = 'cv-toast cv-toast-' + type;
            toast.innerHTML =
                '<i class="fas ' + (iconMap[type] || 'fa-info-circle') + ' cv-toast-icon"></i>' +
                '<span>' + message + '</span>' +
                '<button class="cv-toast-close" onclick="this.closest(\'.cv-toast\').remove()" title="Tutup">&#x2715;</button>';

            container.appendChild(toast);

            // Trigger masuk (delay kecil agar transition jalan)
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    toast.classList.add('show');
                });
            });

            // Auto dismiss setelah 4 detik
            setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';
                setTimeout(function () {
                    if (toast.parentNode) toast.parentNode.removeChild(toast);
                }, 300);
            }, 4000);
        };

        // ── Page loader bar: aktif saat navigasi link biasa ──
        var loader = document.getElementById('cv-page-loader');
        if (loader) {
            document.addEventListener('click', function (e) {
                var link = e.target.closest('a[href]');
                if (!link) return;
                var href = link.getAttribute('href');
                // Abaikan: anchor, javascript:, target=_blank, tombol AJAX
                if (!href || href.startsWith('#') || href.startsWith('javascript') ||
                    link.target === '_blank' || link.dataset.noLoader) return;
                loader.style.width = '70%';
            });
            window.addEventListener('pageshow', function () {
                loader.style.width = '0%';
            });
        }

        // ── Auto-trigger Toast dari session flash (injected via Blade) ──
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                window.showToast({{ Js::from(session('success')) }}, 'success');
            });
        @endif

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function () {
                window.showToast({{ Js::from(session('error')) }}, 'error');
            });
        @endif

        // ── Auto-trigger Toast dari sessionStorage (setelah AJAX redirect) ──
        document.addEventListener('DOMContentLoaded', function () {
            var flash = sessionStorage.getItem('flash_toast');
            if (flash) {
                try {
                    var data = JSON.parse(flash);
                    window.showToast(data.message, data.type || 'success');
                } catch (e) {}
                sessionStorage.removeItem('flash_toast');
            }
        });

    }());
    </script>

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
