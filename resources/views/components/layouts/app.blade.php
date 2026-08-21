<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a56db">
    <meta name="description" content="AgenDAmay - Agenda Digital SMKN 2 Indramayu">
    @auth
    <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth
    <title>{{ $title ?? 'AgenDAmay' }} — AgenDAmay SMKN 2 Indramayu</title>

    {{-- PWA Assets (Diperbarui ke /pwa-icons/) --}}
    {{-- PWA Assets (Bersih dari crossorigin) --}}
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/pwa-icons/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/pwa-icons/icon-512.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/pwa-icons/icon-192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/pwa-icons/icon-512.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="AgenDAmay">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="AgenDAmay">
    <meta name="msapplication-TileColor" content="#1a56db">
    <meta name="msapplication-TileImage" content="/pwa-icons/icon-192.png">
    
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
                    <a href="{{ route('admin.hari-libur') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.hari-libur') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar-check-fill me-2 text-danger"></i>Kelola Hari Libur
                    </a>
                    <a href="{{ route('admin.verifikasi-izin') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('admin.verifikasi-izin') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-patch-check-fill me-2 text-warning"></i>Verifikasi Izin Guru
                    </a>
                    <a href="{{ route('monitoring.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-graph-up-arrow me-2"></i>Monitoring Realtime
                    </a>
                    <a href="{{ route('monitoring.harian') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.harian') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar2-day me-2 text-primary"></i>Monitoring Harian
                    </a>
                    <a href="{{ route('monitoring.mingguan') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.mingguan') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar-range me-2 text-info"></i>Monitoring Mingguan
                    </a>
                    <a href="{{ route('monitoring.progress') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.progress') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-bar-chart-line me-2"></i>Progress KKTP (Analytic)
                    </a>
                @elseif(in_array($role, ['guru', 'ketua_mgmp']))
                    <a href="{{ route('guru.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-house-fill me-2"></i>Beranda Guru
                    </a>
                    <a href="{{ route('guru.jurnal') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('guru.jurnal*') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-journal-text me-2"></i>Jurnal Mengajar
                    </a>

                    {{-- Menu KKTP Section --}}
                    <a href="{{ route('guru.kktp-hub') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('guru.kktp-hub') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-list-check me-2 text-primary"></i>Menu KKTP (Utama)
                    </a>
                    <div class="ps-3 mb-1">
                        <a href="{{ route('guru.import-kktp') }}" class="list-group-item list-group-item-action border-0 rounded py-1 mb-1 small {{ request()->routeIs('guru.import-kktp') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-file-earmark-excel me-2 text-success"></i>Import KKTP (Excel)
                        </a>
                        <a href="{{ route('guru.kktp-setting') }}" class="list-group-item list-group-item-action border-0 rounded py-1 mb-1 small {{ request()->routeIs('guru.kktp-setting') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-gear-fill me-2 text-secondary"></i>Setting KKTP / TP
                        </a>
                        <a href="{{ route('guru.kktp-nilai') }}" class="list-group-item list-group-item-action border-0 rounded py-1 mb-1 small {{ request()->routeIs('guru.kktp-nilai*') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-clipboard2-check-fill me-2 text-success"></i>Nilai KKTP Murid
                        </a>
                    </div>

                    {{-- Pengajuan Izin Guru --}}
                    <a href="{{ route('guru.izin') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('guru.izin*') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar-x me-2 text-warning"></i>Pengajuan Izin Guru
                    </a>

                    @if($role === 'ketua_mgmp')
                        <a href="{{ route('mgmp.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('mgmp.dashboard') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-diagram-3-fill me-2"></i>Dashboard MGMP
                        </a>
                        <a href="{{ route('mgmp.tp') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('mgmp.tp') ? 'active' : '' }}" wire:navigate>
                            <i class="bi bi-card-checklist me-2"></i>Manajemen TP MGMP
                        </a>
                    @endif
                @elseif(in_array($role, ['kepsek', 'waka']))
                    <a href="{{ route('monitoring.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.dashboard') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-speedometer2 me-2"></i>Monitoring Realtime
                    </a>
                    <a href="{{ route('monitoring.harian') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.harian') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar2-day me-2 text-primary"></i>Monitoring Harian
                    </a>
                    <a href="{{ route('monitoring.mingguan') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.mingguan') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-calendar-range me-2 text-info"></i>Monitoring Mingguan
                    </a>
                    <a href="{{ route('monitoring.progress') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.progress') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-bar-chart-line me-2"></i>Progress KKTP (Analytic)
                    </a>
                    <a href="{{ route('monitoring.izin-guru') }}" class="list-group-item list-group-item-action border-0 rounded mb-1 {{ request()->routeIs('monitoring.izin-guru') || request()->routeIs('waka.verifikasi-izin') ? 'active' : '' }}" wire:navigate>
                        <i class="bi bi-patch-check-fill me-2 text-warning"></i>Tabel Manajemen Izin
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

    {{-- Register PWA Service Worker + Teaching Schedule Notification System --}}
    <script>
    // ============================================================
    // 1. PWA Service Worker Registration & Installation Handler
    // ============================================================
    let deferredPrompt = null;

    // Tangkap event instalasi PWA dari browser Chrome/Android
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        console.log('PWA Install Prompt berhasil ditangkap.');
    });

    // Fungsi trigger saat tombol "Install Aplikasi (PWA)" di Sidebar diklik
    window.installPWA = function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User menerima instalasi PWA');
                } else {
                    console.log('User menolak instalasi PWA');
                }
                deferredPrompt = null;
            });
        } else {
            alert('Aplikasi sudah terinstall di HP/Komputer Anda atau browser ini belum mendukung instalasi PWA otomatis.');
        }
    };

    // Fungsi Fullscreen
    window.toggleFullscreen = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.warn('Gagal mode layar penuh:', err);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    };

    // Helper untuk konversi VAPID Public Key base64 string ke Uint8Array
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Fungsi Registrasi Notifikasi Push HP (VAPID + Service Worker)
    window.subscribeToWebPush = async function() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Browser atau perangkat ini belum mendukung fitur Web Push Notification.');
            return;
        }

        try {
            const btnText = document.getElementById('webpush-btn-text');
            if (btnText) btnText.innerText = 'Menghubungkan...';

            const perm = await Notification.requestPermission();
            if (perm !== 'granted') {
                alert('Izin notifikasi ditolak oleh browser. Silakan aktifkan izin notifikasi di pengaturan browser Anda.');
                if (btnText) btnText.innerText = 'Aktifkan Notifikasi HP (Web Push)';
                return;
            }

            const reg = await navigator.serviceWorker.ready;

            // Ambil VAPID Public Key dari Server
            const keyResp = await fetch('/api/push/vapid-public-key');
            const { publicKey } = await keyResp.json();

            if (!publicKey) {
                throw new Error('VAPID Public Key tidak ditemukan di server.');
            }

            const convertedVapidKey = urlBase64ToUint8Array(publicKey);

            // Berlangganan ke Push Service
            let subscription = await reg.pushManager.getSubscription();
            if (!subscription) {
                subscription = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedVapidKey
                });
            }

            // Kirim data subscription ke server Laravel
            const subResp = await fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(subscription.toJSON())
            });

            const subResult = await subResp.json();

            if (btnText) btnText.innerText = 'Notifikasi HP Aktif ✅';
            if (window.Livewire) {
                window.Livewire.dispatch('show-toast', {
                    message: '🔔 Notifikasi Push HP Berhasil Diaktifkan! Anda akan menerima pengingat KBM & notifikasi izin.',
                    type: 'success'
                });
            } else {
                alert('Notifikasi Push HP Berhasil Diaktifkan!');
            }
        } catch (err) {
            console.error('Gagal subscribe Web Push:', err);
            alert('Gagal mengaktifkan notifikasi push: ' + err.message);
            const btnText = document.getElementById('webpush-btn-text');
            if (btnText) btnText.innerText = 'Aktifkan Notifikasi HP (Web Push)';
        }
    };

    // Fungsi Uji Coba Notifikasi HP
    window.testWebPushNotification = async function() {
        try {
            const resp = await fetch('/api/push/test-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            const data = await resp.json();

            if (window.Livewire) {
                window.Livewire.dispatch('show-toast', {
                    message: data.message,
                    type: data.sent_count > 0 ? 'success' : 'warning'
                });
            } else {
                alert(data.message);
            }
        } catch (e) {
            alert('Gagal mengirim test notifikasi: ' + e.message);
        }
    };

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(reg) {
                console.log('PWA ServiceWorker registered with scope:', reg.scope);

                // Check initial push subscription state
                reg.pushManager.getSubscription().then(function(sub) {
                    const btnText = document.getElementById('webpush-btn-text');
                    if (sub && btnText) {
                        btnText.innerText = 'Notifikasi HP Aktif ✅';
                    }
                });

                // Auto-reload saat Service Worker versi baru terdeteksi
                reg.onupdatefound = function() {
                    const installingWorker = reg.installing;
                    if (installingWorker) {
                        installingWorker.onstatechange = function() {
                            if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                console.log('Versi baru AgenDamay diunduh. Memuat ulang...');
                                window.location.reload();
                            }
                        };
                    }
                };
            }).catch(function(err) {
                console.warn('PWA ServiceWorker registration failed:', err);
            });
        });
    }

    // ============================================================
    // 2. Teaching Schedule Push Notification System (Guru Only)
    // ============================================================
    (function() {
        'use strict';

        const NOTIF_STORAGE_KEY = 'agendamay_notified_schedule';
        const NOTIF_CHECK_INTERVAL = 30000; // Check every 30 seconds
        const NOTIF_MINUTES_BEFORE = 15;    // Notify 15 minutes before class

        let notifIntervalId = null;
        let cachedSchedule = null;
        let lastFetchDate = null;

        // Request Notification permission on first interaction
        function requestNotifPermission() {
            if (!('Notification' in window)) return;
            if (Notification.permission === 'default') {
                Notification.requestPermission().then(function(perm) {
                    console.log('Notification permission:', perm);
                });
            }
        }

        // Get today's date string for localStorage key scoping
        function todayStr() {
            const d = new Date();
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        // Check if a specific notification has already been sent today
        function alreadyNotified(key) {
            try {
                const data = JSON.parse(localStorage.getItem(NOTIF_STORAGE_KEY) || '{}');
                return data[todayStr()] && data[todayStr()][key] === true;
            } catch (e) { return false; }
        }

        // Mark a notification as sent for today
        function markNotified(key) {
            try {
                const data = JSON.parse(localStorage.getItem(NOTIF_STORAGE_KEY) || '{}');
                const today = todayStr();

                // Clean up old dates (only keep today)
                const cleaned = {};
                cleaned[today] = data[today] || {};
                cleaned[today][key] = true;

                localStorage.setItem(NOTIF_STORAGE_KEY, JSON.stringify(cleaned));
            } catch (e) { }
        }

        let globalAudioCtx = null;
        function getAudioContext() {
            if (!globalAudioCtx) {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) globalAudioCtx = new AudioCtx();
            }
            if (globalAudioCtx && globalAudioCtx.state === 'suspended') {
                globalAudioCtx.resume().catch(function() {});
            }
            return globalAudioCtx;
        }

        // Unlock Web Audio on first user interaction
        ['click', 'touchstart', 'keydown'].forEach(function(evt) {
            document.addEventListener(evt, function() {
                getAudioContext();
            }, { once: false, passive: true });
        });

        // Play notification sound using Web Audio API
        function playNotifSound() {
            try {
                const ctx = getAudioContext();
                if (!ctx) return;

                function playTone(freq, startTime, duration) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, ctx.currentTime + startTime);
                    gain.gain.setValueAtTime(0.4, ctx.currentTime + startTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + startTime + duration);
                    osc.start(ctx.currentTime + startTime);
                    osc.stop(ctx.currentTime + startTime + duration);
                }

                // 3-tone ascending chime: C5 (523Hz) → E5 (659Hz) → G5 (784Hz)
                playTone(523, 0, 0.25);
                playTone(659, 0.2, 0.25);
                playTone(784, 0.4, 0.45);
            } catch (e) {
                console.warn('Audio notification failed:', e);
            }
        }

        // Show browser notification
        function showTeachingNotification(block, type) {
            const is15Min = type === '15min';
            const title = is15Min ? '⏰ 15 Menit Lagi Mengajar!' : '🔔 Waktu Mengajar Dimulai!';
            const body = block.mapel + ' — ' + block.kelas + '\nPukul ' + block.waktu_mulai + ' WIB';

            // Play sound chime
            playNotifSound();

            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const notif = new Notification(title, {
                        body: body,
                        icon: '/pwa-icons/icon-192.png',
                        badge: '/pwa-icons/icon-192.png',
                        tag: 'teaching-' + type + '-' + block.waktu_mulai,
                        renotify: false,
                        requireInteraction: true,
                        vibrate: [300, 150, 300, 150, 300],
                    });

                    notif.onclick = function() {
                        window.focus();
                        notif.close();
                    };

                    setTimeout(function() { notif.close(); }, 30000);
                } catch (e) {
                    console.warn('Notification display failed:', e);
                }
            }

            // Also show in-app toast banner
            if (window.Livewire) {
                window.Livewire.dispatch('show-toast', {
                    message: (is15Min ? '⏰ 15 Menit Lagi: ' : '🔔 Waktu Mengajar: ') + block.mapel + ' — ' + block.kelas + ' (pkl ' + block.waktu_mulai + ' WIB)',
                    type: is15Min ? 'warning' : 'info'
                });
            }
        }

        // Fetch schedule from API
        async function fetchSchedule() {
            const today = todayStr();
            if (cachedSchedule && lastFetchDate === today) {
                return cachedSchedule;
            }

            try {
                const resp = await fetch('/api/guru/jadwal-hari-ini', {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!resp.ok) {
                    cachedSchedule = null;
                    return null;
                }

                const data = await resp.json();
                cachedSchedule = data;
                lastFetchDate = today;
                return data;
            } catch (e) {
                return cachedSchedule;
            }
        }

        function timeToMinutes(timeStr) {
            const parts = timeStr.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        async function checkScheduleNotifications() {
            const data = await fetchSchedule();
            if (!data || !data.jadwal || data.jadwal.length === 0) return;

            const now = new Date();
            const nowMinutes = now.getHours() * 60 + now.getMinutes();

            data.jadwal.forEach(function(block) {
                const startMinutes = timeToMinutes(block.waktu_mulai);
                const diff = startMinutes - nowMinutes;

                // 1. Notifikasi 15 Menit Sebelum (jika selisih antara 0 dan 15 menit)
                if (diff >= 0 && diff <= NOTIF_MINUTES_BEFORE) {
                    const notifKey = '15min_' + block.waktu_mulai + '_' + block.kelas + '_' + block.mapel;

                    if (!alreadyNotified(notifKey)) {
                        markNotified(notifKey);
                        showTeachingNotification(block, '15min');
                    }
                }

                // 2. Notifikasi Saat Jam Masuk Mulai (0 s/d 10 menit setelah jam mulai)
                if (nowMinutes >= startMinutes && nowMinutes <= (startMinutes + 10)) {
                    const notifKey = 'start_' + block.waktu_mulai + '_' + block.kelas + '_' + block.mapel;

                    if (!alreadyNotified(notifKey)) {
                        markNotified(notifKey);
                        showTeachingNotification(block, 'start');
                    }
                }
            });
        }

        function initNotifSystem() {
            requestNotifPermission();

            if (notifIntervalId) {
                clearInterval(notifIntervalId);
                notifIntervalId = null;
            }

            setTimeout(function() {
                checkScheduleNotifications();
            }, 2000);

            notifIntervalId = setInterval(checkScheduleNotifications, NOTIF_CHECK_INTERVAL);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNotifSystem);
        } else {
            initNotifSystem();
        }

        document.addEventListener('livewire:navigated', function() {
            if (!notifIntervalId) {
                initNotifSystem();
            }
        });

        window.addEventListener('beforeunload', function() {
            if (notifIntervalId) {
                clearInterval(notifIntervalId);
                notifIntervalId = null;
            }
        });
    })();
    </script>
</body>
</html>
