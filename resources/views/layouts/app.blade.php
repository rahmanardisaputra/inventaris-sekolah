<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Inventaris Sekolah') }}</title>

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6.4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts: Poppins untuk kesan modern -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        footer {
            background-color: #ffffff;
            border-top: 1px solid #eaeaea;
        }
        .text-primary-custom {
            color: #4e73df !important;
        }
        .bg-primary-custom {
            background-color: #4e73df !important;
        }
    </style>
</head>
<body>
    <!-- Navbar Modern -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-school me-2 fs-4"></i>
                <span>Inventaris SD Muhammadiyah</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    
                    @guest
                        <li class="nav-item"><a class="nav-link px-3" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
                        <li class="nav-item"><a class="nav-link px-3" href="{{ route('register') }}"><i class="fas fa-user-plus me-1"></i>Register</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link px-3" href="{{ route('barang.index') }}"><i class="fas fa-boxes me-1"></i>Barang</a></li>
                        <li class="nav-item"><a class="nav-link px-3" href="{{ route('pemeliharaan.index') }}"><i class="fas fa-tools me-1"></i>Pemeliharaan</a></li>
                        <li class="nav-item"><a class="nav-link px-3" href="{{ route('lokasi.index') }}"><i class="fas fa-door-open me-1"></i>Ruangan</a></li>
                        
                        <!-- Dropdown Laporan -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3" href="#" id="dropdownLaporan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt me-1"></i>Laporan
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownLaporan">
                                <li><a class="dropdown-item" href="{{ route('laporan.barang') }}"><i class="fas fa-list me-2 text-muted"></i>Daftar Barang</a></li>
                                <li><a class="dropdown-item" href="{{ route('laporan.pemeliharaan') }}"><i class="fas fa-history me-2 text-muted"></i>Riwayat Perbaikan</a></li>
                            </ul>
                        </li>

                        @if(Auth::user()->role === 'waka')
                            <li class="nav-item">
                                <a class="nav-link px-3 text-warning fw-semibold" href="{{ route('barang.pending.validation') }}">
                                    <i class="fas fa-check-circle me-1"></i>Validasi
                                </a>
                            </li>
                        @endif

                        <!-- Dropdown Profil User -->
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-pill bg-white text-dark border" href="#" id="dropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff&size=32" 
                                     alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="dropdownUser">
                                <li><span class="dropdown-item-text small text-muted"><i class="fas fa-id-badge me-2"></i>Role: {{ ucfirst(Auth::user()->role) }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container">
        <!-- Alert Success -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Alert Error -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Modern -->
    <footer class="text-center py-4 mt-5">
        <div class="container">
            <small class="text-muted">
                &copy; {{ date('Y') }} <strong>SD Muhammadiyah Metro Pusat</strong><br>
                Sistem Inventaris Barang v1.0 • Dibuat dengan ❤️ untuk Skripsi
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>