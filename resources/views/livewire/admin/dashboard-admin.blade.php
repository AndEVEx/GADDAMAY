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
            <a href="{{ route($menu['route']) }}" class="card text-decoration-none h-100" wire:navigate>
                <div class="card-body text-center py-3">
                    <i class="bi {{ $menu['icon'] }} text-{{ $menu['color'] }}" style="font-size: 1.5rem;"></i>
                    <div class="fw-semibold small mt-2 text-dark">{{ $menu['label'] }}</div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
