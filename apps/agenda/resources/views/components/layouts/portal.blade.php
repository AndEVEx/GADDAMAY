<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a56db">
    <title>{{ $title ?? 'GADDAMAY' }} — Portal Sistem Terpadu SMKN 2 Indramayu</title>
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getBase64() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .hero-pattern {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.6;
        }
        .module-card {
            transition: all 0.25s ease-in-out;
            border-radius: 16px;
            background: white;
            border: 1px solid #e2e8f0;
        }
        .module-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #93c5fd;
        }
        .badge-pulse {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }
    </style>
    @livewireStyles
</head>
<body class="min-vh-100 d-flex flex-column">

    {{-- Top Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom border-dark-subtle py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ url('/') }}">
                <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" width="36" height="36" alt="Logo" class="rounded">
                <span>GADDAMAY</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPortal">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navPortal">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold" href="#modul-layanan"><i class="bi bi-grid-fill me-1"></i> Layanan Sistem</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="{{ route('perizinan.index') }}"><i class="bi bi-shield-check me-1"></i> Perizinan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="#tentang"><i class="bi bi-info-circle me-1"></i> Tentang</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <span class="dropdown-item-text small text-muted">Role: <strong>{{ strtoupper(auth()->user()->role) }}</strong></span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                                @elseif(auth()->user()->isGuru())
                                    <li><a class="dropdown-item" href="{{ route('guru.dashboard') }}"><i class="bi bi-house-fill me-2"></i>Beranda Guru</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('perizinan.index') }}"><i class="bi bi-shield-check me-2"></i>Perizinan Siswa</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-dark text-white-50 py-4 mt-5 border-top border-dark-subtle" id="tentang">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" width="28" height="28" alt="Logo">
                        <h6 class="text-white fw-bold mb-0">GADDAMAY — SMKN 2 Indramayu</h6>
                    </div>
                    <p class="small text-muted mb-0">
                        Gerbang, Agenda, Disiplin, DUDI/Industri, Akademik & Manajemen Terpadu.<br>
                        Jl. Raya Pabean No. 15, Kec. Indramayu, Kabupaten Indramayu, Jawa Barat 45218.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-success bg-opacity-25 text-success border border-success-subtle px-3 py-2">
                        <i class="bi bi-hdd-network-fill me-1"></i> Single Server & Tunnel VPS Online
                    </span>
                    <div class="small text-muted mt-2">© 2026 SMKN 2 Indramayu. All Rights Reserved.</div>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>