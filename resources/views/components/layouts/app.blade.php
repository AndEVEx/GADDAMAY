<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a56db">
    <meta name="description" content="AgenDamay - Sistem Agenda Guru SMKN 2 Indramayu">

    <title>{{ $title ?? 'AgenDamay' }} — AgenDamay SMKN 2 Indramayu</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getBase64() }}">
    <link rel="apple-touch-icon" href="{{ \App\Helpers\LogoHelper::getBase64() }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    {{-- Top Navigation Bar --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1a56db, #0d47a1);">
        <div class="container-fluid px-3">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/" wire:navigate>
                <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" alt="Logo" style="width: 32px; height: 32px; border-radius: 6px;">
                <span class="d-none d-sm-inline">AgenDamay</span>
            </a>

            <div class="d-flex align-items-center gap-2">
                {{-- Notification Bell --}}
                @livewire('components.notification-bell')

                {{-- User Menu --}}
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="min-height: 40px;">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 animate-fade-in-up" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3 animate-fade-in-up" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="container-fluid px-3 py-3">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="text-center py-3 text-muted small" style="margin-bottom: 80px;">
        Made with every kind of <i class="bi bi-heart-fill text-danger"></i> &copy; 2026 SMKN 2 Indramayu
    </footer>

    {{-- Bottom Navigation (Mobile) --}}
    @auth
    @php $role = Auth::user()->role; @endphp
    <nav class="bottom-nav d-lg-none">
        @if(in_array($role, ['guru', 'ketua_mgmp']))
            <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-house-fill"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('guru.jurnal') }}" class="nav-item {{ request()->routeIs('guru.jurnal*') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-journal-text"></i>
                <span>Jurnal</span>
            </a>
        @elseif($role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-house-fill"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('admin.jadwal') }}" class="nav-item {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-calendar3"></i>
                <span>Jadwal</span>
            </a>
            <a href="{{ route('monitoring.dashboard') }}" class="nav-item {{ request()->routeIs('monitoring.*') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-graph-up"></i>
                <span>Monitor</span>
            </a>
        @elseif(in_array($role, ['kepsek', 'waka']))
            <a href="{{ route('monitoring.dashboard') }}" class="nav-item {{ request()->routeIs('monitoring.dashboard') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-house-fill"></i>
                <span>Monitor</span>
            </a>
            <a href="{{ route('monitoring.progress') }}" class="nav-item {{ request()->routeIs('monitoring.progress') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-graph-up"></i>
                <span>Progress TP</span>
            </a>
        @elseif($role === 'ketua_kelas')
            <a href="{{ route('ketua.verifikasi') }}" class="nav-item {{ request()->routeIs('ketua.*') ? 'active' : '' }}" wire:navigate>
                <i class="bi bi-qr-code-scan"></i>
                <span>Verifikasi</span>
            </a>
        @endif
    </nav>
    @endauth

    @livewireScripts

    <script>
        // Prevent zoom on double-tap
        document.addEventListener('touchend', function(e) {
            const now = Date.now();
            if (now - (this.lastTouchEnd || 0) < 300) { e.preventDefault(); }
            this.lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>
