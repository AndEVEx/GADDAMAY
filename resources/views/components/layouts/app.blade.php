<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a56db">
    <meta name="description" content="AgenDamay - Sistem Agenda Guru SMKN 2 Indramayu">

    <title>{{ $title ?? 'AgenDAmay' }} — AgenDAmay SMKN 2 Indramayu</title>

    {{-- PWA Fullscreen Meta Tags --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="AgenDamay">
    <meta name="mobile-web-app-capable" content="yes">
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
            <div class="d-flex align-items-center gap-2">
                {{-- Sidebar Trigger Button --}}
                <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" style="min-height: 38px;">
                    <i class="bi bi-list fs-5"></i>
                    <span class="d-none d-sm-inline fw-semibold small">Menu</span>
                </button>

                <a class="navbar-brand fw-bold d-flex align-items-center gap-2 ms-1" href="/" wire:navigate style="text-decoration: none;">
                    <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" alt="Logo" style="width: 44px; height: 44px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                    <div class="d-flex flex-column text-start">
                        <span class="fw-extrabold text-white" style="font-size: 1.1rem; line-height: 1.1; letter-spacing: 0.3px;">AgenDAmay</span>
                        <span class="text-white-50" style="font-size: 0.65rem; font-weight: 500; line-height: 1.1; margin-top: 2px;">Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari</span>
                    </div>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- User Dropdown Menu --}}
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2 px-2" type="button" data-bs-toggle="dropdown" style="min-height: 40px;">
                        <i class="bi bi-person-circle fs-6"></i>
                        <span class="d-none d-md-inline fw-semibold small">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg py-1" style="min-width: 250px; border-radius: 0.75rem;">
                        <li class="px-3 py-2 bg-light border-bottom mb-1">
                            <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                            <span class="badge bg-primary mt-1" style="font-size: 0.65rem;">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                        </li>
                        <li class="px-2 py-1">
                            @livewire('components.notification-bell')
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <button onclick="toggleFullscreen()" class="dropdown-item py-2 d-flex align-items-center">
                                <i class="bi bi-arrows-fullscreen text-info me-2 fs-6"></i>
                                <span class="small">Layar Penuh (Fullscreen)</span>
                            </button>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('ganti-password') }}" wire:navigate>
                                <i class="bi bi-key-fill text-warning me-2 fs-6"></i>
                                <span class="small">Ganti Password</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right me-2 fs-6"></i>
                                    <span class="small">Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{-- Offcanvas Sidebar Drawer --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel" style="width: 290px;">
        <div class="offcanvas-header text-white" style="background: linear-gradient(135deg, #1a56db, #0d47a1);">
            <div class="d-flex align-items-center gap-2" id="appSidebarLabel">
                <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" alt="Logo" style="width: 32px; height: 32px; border-radius: 6px;">
                <div>
                    <h6 class="mb-0 fw-bold">AgenDamay</h6>
                    <small style="font-size: 0.75rem; opacity: 0.85;">SMKN 2 Indramayu</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            {{-- Tombol Kembali / Back --}}
            <button onclick="history.back()" class="btn btn-outline-secondary btn-sm w-100 mb-3 d-flex align-items-center justify-content-center gap-2 py-2" style="min-height: 42px;">
                <i class="bi bi-arrow-left fs-6"></i>
                <span class="fw-semibold">Kembali ke Halaman Sebelumnya</span>
            </button>

            <div class="text-muted small fw-bold text-uppercase px-2 mb-2">Navigasi Utama</div>
            <div class="list-group list-group-flush mb-3">
                @php $role = Auth::user()->role; @endphp

                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                    </a>
                    <a href="{{ route('admin.jadwal') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar3 me-2"></i>Manajemen Jadwal
                    </a>
                    <a href="{{ route('admin.import') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.import') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-file-earmark-code me-2"></i>Import Jadwal (XML)
                    </a>
                    <a href="{{ route('admin.guru') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.guru') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-people-fill me-2"></i>Manajemen Guru
                    </a>
                    <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.users') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-person-fill-lock me-2"></i>Manajemen User
                    </a>
                    <a href="{{ route('admin.kelas') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.kelas') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-door-open-fill me-2"></i>Manajemen Kelas
                    </a>
                    <a href="{{ route('admin.siswa') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.siswa') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-person-badge-fill me-2"></i>Manajemen Siswa
                    </a>
                    <a href="{{ route('admin.mapel') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.mapel') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-book-fill me-2"></i>Manajemen Mapel
                    </a>
                    <a href="{{ route('admin.import-kktp') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.import-kktp') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-file-earmark-excel me-2"></i>Import KKTP (Excel)
                    </a>
                    <a href="{{ route('admin.motivasi') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.motivasi') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-chat-quote-fill me-2"></i>Motivasi & Pantun
                    </a>
                    <a href="{{ route('monitoring.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-graph-up-arrow me-2"></i>Monitoring Realtime
                    </a>
                    <a href="{{ route('monitoring.progress') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.progress') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-bar-chart-line me-2"></i>Progress TP
                    </a>
                @elseif(in_array($role, ['guru', 'ketua_mgmp']))
                    <a href="{{ route('guru.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-house-fill me-2"></i>Beranda Guru
                    </a>
                    <a href="{{ route('guru.jurnal') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('guru.jurnal*') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-journal-text me-2"></i>Jurnal Mengajar
                    </a>
                    @if($role === 'ketua_mgmp')
                        <a href="{{ route('mgmp.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('mgmp.dashboard') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-diagram-3-fill me-2"></i>Dashboard MGMP
                        </a>
                        <a href="{{ route('mgmp.tp') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('mgmp.tp') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-list-check me-2"></i>Manajemen TP
                        </a>
                    @endif
                @elseif(in_array($role, ['kepsek', 'waka']))
                    <a href="{{ route('monitoring.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-graph-up-arrow me-2"></i>Monitoring Agenda
                    </a>
                    <a href="{{ route('monitoring.progress') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.progress') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-bar-chart-line me-2"></i>Progress TP
                    </a>
                @elseif($role === 'ketua_kelas')
                    <a href="{{ route('ketua.verifikasi') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('ketua.verifikasi') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-qr-code-scan me-2"></i>Verifikasi Token
                    </a>
                @endif
            </div>

            <div class="text-muted small fw-bold text-uppercase px-2 mb-2">Akun Saya</div>
            <div class="list-group list-group-flush">
                <a href="{{ route('ganti-password') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('ganti-password') ? 'active' : '' }}" wire:navigate>
                    <i class="bi bi-key-fill text-warning me-2"></i>Ganti Password
                </a>
            </div>
        </div>
    </div>
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
