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

    {{-- Login History Logs Section --}}
    <div class="card border-0 shadow-sm mb-4 animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-radius: 14px 14px 0 0;">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Log Riwayat Login Pengguna</h6>
                <span class="text-muted small">Rekapitulasi aktivitas autentikasi siapa saja yang telah masuk ke sistem</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="searchLogin" class="form-control bg-light border-start-0" placeholder="Cari nama/email/IP...">
                </div>
                <select wire:model.live="filterRole" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="ketua_mgmp">Ketua MGMP</option>
                    <option value="waka">Waka</option>
                    <option value="kepsek">Kepsek</option>
                    <option value="ketua_kelas">Ketua Kelas</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            @if($loginLogs->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-journal-text fs-3 d-block mb-1 text-muted opacity-50"></i>
                    <span class="small">Belum ada catatan log login yang sesuai filter.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-3 py-2">Waktu Login (WIB)</th>
                                <th class="py-2">Nama Pengguna</th>
                                <th class="py-2">Role</th>
                                <th class="py-2">IP Address</th>
                                <th class="py-2 pe-3">Perangkat / User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loginLogs as $log)
                            @php
                                $vals = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values ?? '[]', true);
                                $userName = $log->user?->name ?? ($vals['name'] ?? 'User Dihapus');
                                $userRole = $log->user?->role ?? ($vals['role'] ?? 'guru');
                                $userAgent = $vals['user_agent'] ?? '-';
                                $badgeClass = match($userRole) {
                                    'admin' => 'bg-danger',
                                    'kepsek' => 'bg-primary',
                                    'waka' => 'bg-info text-dark',
                                    'ketua_mgmp' => 'bg-purple text-white',
                                    'ketua_kelas' => 'bg-warning text-dark',
                                    default => 'bg-success',
                                };
                            @endphp
                            <tr>
                                <td class="ps-3 py-2 text-nowrap">
                                    <span class="fw-semibold text-dark d-block">
                                        {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <span class="fw-bold text-dark">{{ $userName }}</span>
                                    <span class="text-muted d-block small" style="font-size: 0.75rem;">{{ $log->user?->email ?? '-' }}</span>
                                </td>
                                <td class="py-2">
                                    <span class="badge {{ $badgeClass }} text-uppercase fw-semibold" style="font-size: 0.72rem;">
                                        {{ str_replace('_', ' ', $userRole) }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <span class="badge bg-light text-dark border">{{ $log->ip_address ?? '-' }}</span>
                                </td>
                                <td class="py-2 pe-3 text-muted small" style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $userAgent }}">
                                    @if(str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone'))
                                        <i class="bi bi-phone me-1 text-primary"></i>
                                    @else
                                        <i class="bi bi-laptop me-1 text-secondary"></i>
                                    @endif
                                    {{ Str::limit($userAgent, 40) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">Menampilkan {{ $loginLogs->firstItem() ?? 0 }} - {{ $loginLogs->lastItem() ?? 0 }} dari {{ $loginLogs->total() }} log</span>
                    <div>{{ $loginLogs->links() }}</div>
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
