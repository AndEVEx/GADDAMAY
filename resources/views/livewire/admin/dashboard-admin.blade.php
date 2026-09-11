<div>
    <div class="page-header">
        <h1><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h1>
        <p class="subtitle mb-0">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 animate-fade-in-up">
                <i class="bi bi-people-fill text-primary" style="font-size: 1.5rem;"></i>
                <div class="fw-bold fs-4 mt-1">{{ $totalGuru }}</div>
                <div class="text-muted small">Guru</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 animate-fade-in-up" style="animation-delay: 0.05s">
                <i class="bi bi-building text-success" style="font-size: 1.5rem;"></i>
                <div class="fw-bold fs-4 mt-1">{{ $totalKelas }}</div>
                <div class="text-muted small">Kelas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 animate-fade-in-up" style="animation-delay: 0.1s">
                <i class="bi bi-book text-info" style="font-size: 1.5rem;"></i>
                <div class="fw-bold fs-4 mt-1">{{ $totalMapel }}</div>
                <div class="text-muted small">Mata Pelajaran</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center py-3 animate-fade-in-up" style="animation-delay: 0.15s">
                <i class="bi bi-calendar3 text-warning" style="font-size: 1.5rem;"></i>
                <div class="fw-bold fs-4 mt-1">{{ $totalJadwal }}</div>
                <div class="text-muted small">Jadwal</div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="card mb-3 animate-fade-in-up" style="animation-delay: 0.2s">
        <div class="card-header"><i class="bi bi-activity me-2"></i>Aktivitas Hari Ini</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-4 text-center">
                    <div class="fw-bold fs-4 text-primary">{{ $agendaHariIni }}</div>
                    <div class="small text-muted">Total Agenda</div>
                </div>
                <div class="col-4 text-center">
                    <div class="fw-bold fs-4 text-success">{{ $agendaSelesai }}</div>
                    <div class="small text-muted">Selesai</div>
                </div>
                <div class="col-4 text-center">
                    <div class="fw-bold fs-4 text-warning">{{ $agendaBerjalan }}</div>
                    <div class="small text-muted">Berjalan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Block Schedule Swap Banner --}}
    <div class="card border-0 shadow-sm mb-4 animate-fade-in-up" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 14px; color: white;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="bi bi-arrow-left-right me-1"></i>Sistem Blok Rolling</span>
                        <span class="badge bg-white bg-opacity-25 text-white">TP • APHPi • NKPI</span>
                    </div>
                    <h5 class="fw-bold mb-1 text-white">Tukar Jadwal Blok Mingguan (Teori ↔ Produktif)</h5>
                    <p class="mb-0 text-white-50 small">
                        Tukar seluruh sesi jadwal pelajaran antara Rombel 1 dan Rombel 2 untuk jurusan TP, APHP/APHPi, dan NKPI di semua tingkat (X, XI, XII).
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="tukarJadwalBlok" wire:confirm="Yakin ingin menukar jadwal blok jurusan TP, APHPi, dan NKPI (Rombel 1 ↔ Rombel 2) untuk semua tingkat?" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="tukarJadwalBlok"><i class="bi bi-arrow-repeat fs-5"></i></span>
                        <span wire:loading wire:target="tukarJadwalBlok" class="spinner-border spinner-border-sm"></span>
                        <span>Tukar Jadwal Blok Sekarang</span>
                    </button>
                    <a href="{{ route('admin.jadwal') }}" class="btn btn-light fw-bold px-3 py-2 shadow-sm" style="border-radius: 10px;" wire:navigate>
                        <i class="bi bi-gear-fill me-1"></i>Kelola Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Online Users Section --}}
    <div class="card border-0 shadow-sm mb-4 animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-radius: 14px 14px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span class="position-relative d-inline-flex align-items-center justify-content-center" style="width: 12px; height: 12px;">
                    <span class="position-absolute w-100 h-100 bg-success rounded-circle animate-ping opacity-75"></span>
                    <span class="position-relative d-inline-block rounded-circle bg-success" style="width: 10px; height: 10px;"></span>
                </span>
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-check-fill text-success me-2"></i>Pengguna Sedang Online Saat Ini</h6>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1 rounded-pill">{{ count($onlineUsers) }} Online</span>
            </div>
            <span class="text-muted small"><i class="bi bi-clock-history me-1"></i>Aktif dalam 10 menit terakhir</span>
        </div>
        <div class="card-body p-0">
            @if(empty($onlineUsers))
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-3 d-block mb-1 text-muted opacity-50"></i>
                    <span class="small">Belum ada aktivitas pengguna terdeteksi dalam 10 menit terakhir.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-3 py-2">Nama Pengguna</th>
                                <th class="py-2">Role</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">IP Address</th>
                                <th class="py-2 text-end pe-3">Terakhir Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($onlineUsers as $user)
                            <tr>
                                <td class="ps-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2">
                                    @php
                                        $badgeClass = match($user->role ?? 'guru') {
                                            'admin' => 'bg-danger',
                                            'kepsek' => 'bg-primary',
                                            'waka' => 'bg-info text-dark',
                                            'ketua_mgmp' => 'bg-purple text-white',
                                            'ketua_kelas' => 'bg-warning text-dark',
                                            default => 'bg-success',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                        {{ str_replace('_', ' ', $user->role ?? 'guru') }}
                                    </span>
                                </td>
                                <td class="py-2 text-muted small">{{ $user->email ?? '-' }}</td>
                                <td class="py-2 text-muted small">
                                    <span class="badge bg-light text-dark border">{{ $user->ip_address ?? '127.0.0.1' }}</span>
                                </td>
                                <td class="py-2 text-end pe-3">
                                    <span class="text-success fw-semibold small">
                                        <i class="bi bi-dot fs-5 align-middle"></i>{{ \Carbon\Carbon::parse($user->last_seen_at)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Global Data Export Section (Item 4) --}}
    <div class="card border-0 shadow-sm mb-4 animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #1e293b, #0f172a); color: white;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1 text-white d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-arrow-down-fill text-warning"></i>
                        Export Global Data Sekolah (Excel)
                    </h5>
                    <p class="mb-0 text-white-50 small">
                        Unduh rekapan global seluruh guru, absensi siswa per mapel, dan capaian KKTP kurikulum merdeka.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 px-3 py-1 rounded-3">
                        <span class="text-white-50 small">Bulan:</span>
                        <input type="month" wire:model.live="exportBulan" class="form-control form-control-sm bg-white text-dark border-0 fw-semibold" style="width: 150px;">
                    </div>
                </div>
            </div>
            <div class="row g-2 mt-3">
                <div class="col-12 col-md-3">
                    <button wire:click="exportRekapGuruBulanan" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <i class="bi bi-person-check-fill text-warning"></i>
                        <span>Kehadiran Guru (.xlsx)</span>
                    </button>
                </div>
                <div class="col-12 col-md-3">
                    <button wire:click="exportRekapPresensiSiswa" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <i class="bi bi-people-fill text-info"></i>
                        <span>Absensi Siswa (.xlsx)</span>
                    </button>
                </div>
                <div class="col-12 col-md-3">
                    <button wire:click="exportRekapKktpGlobal" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <i class="bi bi-clipboard2-check-fill text-success"></i>
                        <span>Capaian KKTP (.xlsx)</span>
                    </button>
                </div>
                <div class="col-12 col-md-3">
                    <button wire:click="exportSemuaSiswa" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border-radius: 10px;" wire:loading.attr="disabled">
                        <i class="bi bi-mortarboard-fill text-primary"></i>
                        <span>Seluruh Siswa (.xlsx)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- User Login Status & Last Login Table (Item 1) --}}
    <div class="card border-0 shadow-sm mb-4 animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-radius: 14px 14px 0 0;">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Status & Riwayat Login Guru / Pengguna</h6>
                <span class="text-muted small">Daftar akun pengguna dengan catatan waktu login terakhir (1 baris per pengguna)</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="searchLogin" class="form-control bg-light border-start-0" placeholder="Cari nama/email/IP...">
                </div>
                <select wire:model.live="filterRole" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">Semua Role</option>
                    <option value="guru">Guru Saja</option>
                    <option value="ketua_mgmp">Ketua MGMP</option>
                    <option value="waka">Waka</option>
                    <option value="kepsek">Kepsek</option>
                    <option value="admin">Admin</option>
                    <option value="ketua_kelas">Ketua Kelas</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            @if($userLogins->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-3 d-block mb-1 text-muted opacity-50"></i>
                    <span class="small">Belum ada data pengguna yang sesuai filter.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-3 py-2">Nama Pengguna</th>
                                <th class="py-2">Role</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Login Terakhir (WIB)</th>
                                <th class="py-2">IP Address</th>
                                <th class="py-2 pe-3">Perangkat Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userLogins as $user)
                            @php
                                $badgeClass = match($user->role) {
                                    'admin' => 'bg-danger',
                                    'kepsek' => 'bg-primary',
                                    'waka' => 'bg-info text-dark',
                                    'ketua_mgmp' => 'bg-purple text-white',
                                    'ketua_kelas' => 'bg-warning text-dark',
                                    default => 'bg-success',
                                };
                                $isOnline = !empty(Cache::get('user_online_' . $user->id));
                                $dev = $user->last_login_device ?? '';
                            @endphp
                            <tr>
                                <td class="ps-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="position-relative">
                                            <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.82rem;">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            @if($isOnline)
                                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" title="Sedang Online"></span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                            @if($isOnline)
                                                <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.65rem;">Online Sekarang</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2">
                                    <span class="badge {{ $badgeClass }} text-uppercase fw-semibold" style="font-size: 0.72rem;">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                                <td class="py-2 text-muted small">{{ $user->email }}</td>
                                <td class="py-2 text-nowrap">
                                    @if($user->last_login_at)
                                        <span class="fw-semibold text-dark d-block">
                                            {{ \Carbon\Carbon::parse($user->last_login_at)->translatedFormat('d M Y, H:i') }}
                                        </span>
                                        <span class="text-muted small" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">Belum Pernah Login</span>
                                    @endif
                                </td>
                                <td class="py-2">
                                    @if($user->last_login_ip)
                                        <span class="badge bg-light text-dark border">{{ $user->last_login_ip }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="py-2 pe-3 text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $dev }}">
                                    @if($dev)
                                        @if(str_contains($dev, 'Mobile') || str_contains($dev, 'Android') || str_contains($dev, 'iPhone'))
                                            <i class="bi bi-phone me-1 text-primary"></i>
                                        @else
                                            <i class="bi bi-laptop me-1 text-secondary"></i>
                                        @endif
                                        {{ Str::limit($dev, 35) }}
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">Menampilkan {{ $userLogins->firstItem() ?? 0 }} - {{ $userLogins->lastItem() ?? 0 }} dari {{ $userLogins->total() }} pengguna</span>
                    <div>{{ $userLogins->links() }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Links --}}
    <h5 class="fw-bold mb-3"><i class="bi bi-grid me-2"></i>Menu Cepat</h5>
    <div class="row g-3">
        @php
        $menus = [
            ['route' => 'admin.import', 'icon' => 'bi-cloud-upload', 'color' => 'primary', 'label' => 'Import Jadwal'],
            ['route' => 'admin.import-kktp', 'icon' => 'bi-file-earmark-excel', 'color' => 'success', 'label' => 'Import KKTP'],
            ['route' => 'admin.jadwal', 'icon' => 'bi-calendar3', 'color' => 'success', 'label' => 'Kelola Jadwal'],
            ['route' => 'admin.guru', 'icon' => 'bi-people', 'color' => 'info', 'label' => 'Kelola Guru'],
            ['route' => 'admin.kelas', 'icon' => 'bi-building', 'color' => 'warning', 'label' => 'Kelola Kelas'],
            ['route' => 'admin.mapel', 'icon' => 'bi-book', 'color' => 'danger', 'label' => 'Kelola Mapel'],
            ['route' => 'admin.siswa', 'icon' => 'bi-mortarboard', 'color' => 'secondary', 'label' => 'Kelola Siswa'],
            ['route' => 'admin.users', 'icon' => 'bi-person-gear', 'color' => 'dark', 'label' => 'Kelola User'],
            ['route' => 'admin.koreksi', 'icon' => 'bi-pencil-square', 'color' => 'primary', 'label' => 'Koreksi Agenda'],
            ['route' => 'admin.hari-libur', 'icon' => 'bi-calendar-check', 'color' => 'danger', 'label' => 'Hari Libur'],
            ['route' => 'monitoring.dashboard', 'icon' => 'bi-graph-up', 'color' => 'success', 'label' => 'Monitoring'],
            ['route' => 'admin.motivasi', 'icon' => 'bi-emoji-smile', 'color' => 'info', 'label' => 'Motivasi/Pantun'],
        ];
        @endphp
        @foreach($menus as $menu)
        <div class="col-6 col-md-4">
            <a href="{{ route($menu['route']) }}" class="card text-decoration-none h-100 shadow-sm" wire:navigate>
                <div class="card-body text-center py-3">
                    <i class="bi {{ $menu['icon'] }} text-{{ $menu['color'] }}" style="font-size: 1.5rem;"></i>
                    <div class="fw-semibold small mt-2 text-dark">{{ $menu['label'] }}</div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
