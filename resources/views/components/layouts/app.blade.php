<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a56db">
    <meta name="description" content="AgenDAmay - Agenda Digital SMKN 2 Indramayu">
    
    <title>{{ $title ?? 'AgenDAmay' }} — AgenDAmay SMKN 2 Indramayu</title>

    {{-- PWA Assets --}}
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="{{ \App\Helpers\LogoHelper::getBase64() }}">
    <link rel="apple-touch-icon" href="{{ \App\Helpers\LogoHelper::getBase64() }}">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Vite Assets (CSS & JS) --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles
</head>
<body>
    {{-- Top Navigation Bar --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1a56db, #0d47a1);">
        <div class="container-fluid px-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                {{-- Sidebar Trigger Button --}}
                <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" style="min-height: 38px;">
                    <i class="bi bi-list fs-5"></i>
                    <span class="d-none d-sm-inline fw-semibold small">Menu</span>
                </button>

                {{-- Brand Link with School Logo --}}
                <a class="navbar-brand fw-bold d-flex align-items-center gap-2 ms-1" href="/" wire:navigate style="text-decoration: none;">
                    <div class="bg-white p-1 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                        <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" alt="Logo" style="width: 30px; height: 30px; object-fit: contain;">
                    </div>
                    <div class="d-flex flex-column text-start">
                        <span class="fw-extrabold text-white" style="font-size: 1.1rem; line-height: 1.1; letter-spacing: 0.3px;">AgenDAmay</span>
                        <span class="text-white-50" style="font-size: 0.62rem; font-weight: 500; line-height: 1.1; margin-top: 2px;">Menginspirasi Tanpa Henti, Terdata Rapi Setiap Hari</span>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    {{-- Offcanvas Sidebar Drawer --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel" style="width: 290px;">
        <div class="offcanvas-header text-white" style="background: linear-gradient(135deg, #1a56db, #0d47a1);">
            <div class="d-flex align-items-center gap-2" id="appSidebarLabel">
                <div class="bg-white p-1 rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                    <img src="{{ \App\Helpers\LogoHelper::getBase64() }}" alt="Logo" style="width: 34px; height: 34px; object-fit: contain;">
                </div>
                <div>
                    <h6 class="mb-0 fw-extrabold text-white" style="letter-spacing: 0.3px;">AgenDAmay</h6>
                    <small class="fw-bold" style="color: #ffeb3b; text-shadow: 0 1px 2px rgba(0,0,0,0.5); font-size: 0.78rem;">SMKN 2 Indramayu</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3" style="padding-bottom: 130px !important; overflow-y: auto;">
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

            {{-- Akun Saya Profile Section inside Sidebar --}}
            <div class="text-muted small fw-bold text-uppercase px-2 mb-2">Akun Saya</div>
            <div class="card mb-3 border-0 bg-light shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="bi bi-person-circle fs-3"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark text-truncate small">{{ Auth::user()->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;">{{ Auth::user()->email }}</div>
                            <span class="badge bg-primary mt-1" style="font-size: 0.65rem;">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                        </div>
                    </div>

                    <div class="py-1 border-top border-bottom my-2">
                        @livewire('components.notification-bell')
                    </div>

                    <div class="list-group list-group-flush">
                        <button type="button" onclick="window.installPWA()" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex align-items-center py-2 text-primary fw-semibold pwa-install-btn">
                            <i class="bi bi-download text-primary me-2 fs-6"></i>
                            <span class="small">Install Aplikasi (PWA)</span>
                        </button>
                        <button type="button" onclick="window.toggleFullscreen()" data-bs-dismiss="offcanvas" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex align-items-center py-2">
                            <i class="bi bi-arrows-fullscreen text-info me-2 fs-6"></i>
                            <span class="small">Layar Penuh (Fullscreen)</span>
                        </button>
                        <a href="{{ route('ganti-password') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 d-flex align-items-center py-2 {{ request()->routeIs('ganti-password') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-key-fill text-warning me-2 fs-6"></i>
                            <span class="small">Ganti Password</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action border-0 rounded text-danger d-flex align-items-center py-2 w-100">
                                <i class="bi bi-box-arrow-right me-2 fs-6"></i>
                                <span class="small fw-bold">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
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

    {{-- Toast Notification --}}
    <div id="toast-container" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1080;"></div>

    {{-- Bottom Navigation --}}
    @auth
        @include('components.bottom-nav')
    @endauth

    @livewireScripts

    {{-- Register PWA Service Worker --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(reg) {
                console.log('PWA ServiceWorker registered with scope:', reg.scope);
            }).catch(function(err) {
                console.warn('PWA ServiceWorker registration failed:', err);
            });
        });
    }
    </script>
</body>
</html>
