<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-calendar2-day text-primary me-2"></i>Monitoring Harian KBM
            </h4>
            <p class="text-muted small mb-0">
                Pemantauan jadwal, kehadiran guru, foto bukti, dan status KBM hari <strong>{{ $date->translatedFormat('l, d F Y') }}</strong>
            </p>
        </div>

        {{-- Export Buttons (Exporting filtered dataset) --}}
        <div class="d-flex gap-2 flex-wrap">
            <button wire:click="exportExcel" class="btn btn-outline-success d-flex align-items-center gap-1" style="min-height: 42px; border-radius: 10px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportExcel"><i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel</span>
                <span wire:loading wire:target="exportExcel"><span class="spinner-border spinner-border-sm me-1"></span>Menyiapkan...</span>
            </button>
            <button wire:click="exportPdf" class="btn btn-outline-danger d-flex align-items-center gap-1" style="min-height: 42px; border-radius: 10px;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="exportPdf"><i class="bi bi-file-earmark-pdf-fill me-1"></i>Export PDF (Kop Surat)</span>
                <span wire:loading wire:target="exportPdf"><span class="spinner-border spinner-border-sm me-1"></span>Menyiapkan...</span>
            </button>
        </div>
    </div>

    {{-- Sub-Navigation Tabs (Hidden on mobile) --}}
    <div class="d-none d-md-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('monitoring.dashboard') }}" class="btn btn-sm btn-light text-muted fw-semibold px-3 py-1 shadow-sm" style="border-radius: 8px;" wire:navigate>
            <i class="bi bi-speedometer2 me-1"></i>Realtime Live
        </a>
        <a href="{{ route('monitoring.harian') }}" class="btn btn-sm btn-primary fw-bold px-3 py-1 shadow-sm" style="border-radius: 8px;" wire:navigate>
            <i class="bi bi-calendar2-day me-1"></i>Monitoring Harian (Tabel)
        </a>
        <a href="{{ route('monitoring.mingguan') }}" class="btn btn-sm btn-light text-muted fw-semibold px-3 py-1 shadow-sm" style="border-radius: 8px;" wire:navigate>
            <i class="bi bi-calendar-range me-1"></i>Monitoring Mingguan
        </a>
        <a href="{{ route('monitoring.izin-guru') }}" class="btn btn-sm btn-light text-muted fw-semibold px-3 py-1 shadow-sm" style="border-radius: 8px;" wire:navigate>
            <i class="bi bi-patch-check me-1"></i>Tabel Manajemen Izin
        </a>
    </div>

    {{-- 4 Executive Percentage KPI Cards --}}
    <div class="row g-3 mb-3">
        {{-- Card 1: % KBM Terlaksana Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100 text-white animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #10b981, #047857);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-white-50 small fw-semibold" style="font-size: 0.76rem;">KBM Terlaksana Hari Ini</div>
                        <div class="fs-2 fw-extrabold my-1">{{ $pctKbmHariIni }}%</div>
                    </div>
                    <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-25" style="height: 6px; border-radius: 4px;">
                    <div class="progress-bar bg-white" role="progressbar" style="width: {{ min(100, $pctKbmHariIni) }}%;"></div>
                </div>
                <div class="small text-white-50 mt-2" style="font-size: 0.72rem;">
                    {{ $selesaiDaily }} selesai dari total {{ $totalDaily }} sesi
                </div>
            </div>
        </div>

        {{-- Card 2: % Guru Mengajar Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100 text-white animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-white-50 small fw-semibold" style="font-size: 0.76rem;">Guru Aktif Mengajar</div>
                        <div class="fs-2 fw-extrabold my-1">{{ $pctGuruMengajar }}%</div>
                    </div>
                    <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-person-video3 fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-25" style="height: 6px; border-radius: 4px;">
                    <div class="progress-bar bg-white" role="progressbar" style="width: {{ min(100, $pctGuruMengajar) }}%;"></div>
                </div>
                <div class="small text-white-50 mt-2" style="font-size: 0.72rem;">
                    Persentase guru hadir terjadwal hari ini
                </div>
            </div>
        </div>

        {{-- Card 3: % KBM Terlaksana Minggu Ini --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100 text-white animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-white-50 small fw-semibold" style="font-size: 0.76rem;">KBM Minggu Ini</div>
                        <div class="fs-2 fw-extrabold my-1">{{ $pctKbmMingguIni }}%</div>
                    </div>
                    <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-calendar-week fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-25" style="height: 6px; border-radius: 4px;">
                    <div class="progress-bar bg-white" role="progressbar" style="width: {{ min(100, $pctKbmMingguIni) }}%;"></div>
                </div>
                <div class="small text-white-50 mt-2" style="font-size: 0.72rem;">
                    Rentang: {{ \Carbon\Carbon::parse($startOfWeek)->format('d M') }} - {{ \Carbon\Carbon::parse($endOfWeek)->format('d M') }}
                </div>
            </div>
        </div>

        {{-- Card 4: % Tingkat Izin & Dispensasi --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100 text-white animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #f59e0b, #b45309);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="text-white-50 small fw-semibold" style="font-size: 0.76rem;">Tingkat Izin / Cuti</div>
                        <div class="fs-2 fw-extrabold my-1">{{ $pctIzinHariIni }}%</div>
                    </div>
                    <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-25" style="height: 6px; border-radius: 4px;">
                    <div class="progress-bar bg-white" role="progressbar" style="width: {{ min(100, $pctIzinHariIni) }}%;"></div>
                </div>
                <div class="small text-white-50 mt-2" style="font-size: 0.72rem;">
                    {{ $izinDaily }} sesi izin dari {{ $totalDaily }} jadwal
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Status Filter Pills --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'semua' ? 'border-bottom border-primary border-3 bg-primary bg-opacity-10' : 'bg-light' }}"
                 wire:click="setFilterStatus('semua')" style="cursor: pointer; border-radius: 10px;">
                <div class="fs-5 fw-extrabold text-primary">{{ $totalDaily }}</div>
                <div class="text-muted small fw-semibold" style="font-size: 0.75rem;">Semua Jadwal</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'selesai' ? 'border-bottom border-success border-3 bg-success bg-opacity-10' : 'bg-light' }}"
                 wire:click="setFilterStatus('selesai')" style="cursor: pointer; border-radius: 10px;">
                <div class="fs-5 fw-extrabold text-success">{{ $selesaiDaily }}</div>
                <div class="text-muted small fw-semibold" style="font-size: 0.75rem;">Selesai (Hadir)</div>
            </div>
        </div>
        <div class="col-4 col-md">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'berjalan' ? 'border-bottom border-info border-3 bg-info bg-opacity-10' : 'bg-light' }}"
                 wire:click="setFilterStatus('berjalan')" style="cursor: pointer; border-radius: 10px;">
                <div class="fs-5 fw-extrabold text-info">{{ $berjalanDaily }}</div>
                <div class="text-muted small fw-semibold" style="font-size: 0.75rem;">Sedang KBM</div>
            </div>
        </div>
        <div class="col-4 col-md">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'izin' ? 'border-bottom border-warning border-3 bg-warning bg-opacity-10' : 'bg-light' }}"
                 wire:click="setFilterStatus('izin')" style="cursor: pointer; border-radius: 10px;">
                <div class="fs-5 fw-extrabold text-warning">{{ $izinDaily }}</div>
                <div class="text-muted small fw-semibold" style="font-size: 0.75rem;">Izin / Sakit</div>
            </div>
        </div>
        <div class="col-4 col-md">
            <div class="card border-0 shadow-sm text-center p-2 {{ $filterStatus === 'kosong' ? 'border-bottom border-secondary border-3 bg-secondary bg-opacity-10' : 'bg-light' }}"
                 wire:click="setFilterStatus('kosong')" style="cursor: pointer; border-radius: 10px;">
                <div class="fs-5 fw-extrabold text-secondary">{{ $kosongDaily }}</div>
                <div class="text-muted small fw-semibold" style="font-size: 0.75rem;">Belum Mulai / Kosong</div>
            </div>
        </div>
    </div>

    {{-- Filter & Date Selector Bar --}}
    <div class="card border-0 shadow-sm mb-3 animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                {{-- Date Selector & Navigation --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-1">
                        <button wire:click="prevDay" class="btn btn-outline-secondary btn-sm px-2" title="Hari Sebelumnya" style="min-height: 40px; border-radius: 8px;">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <input type="date" wire:model.live="tanggal" class="form-control form-control-sm text-center fw-bold" style="min-height: 40px; border-radius: 8px;">
                        <button wire:click="nextDay" class="btn btn-outline-secondary btn-sm px-2" title="Hari Berikutnya" style="min-height: 40px; border-radius: 8px;">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <button wire:click="setHariIni" class="btn btn-primary btn-sm px-3 fw-semibold flex-shrink-0" style="min-height: 40px; border-radius: 8px;" title="Reset ke Hari Ini">
                            Hari Ini
                        </button>
                    </div>
                </div>

                {{-- Tingkat / Kelas Filter --}}
                <div class="col-6 col-md-2">
                    <select wire:model.live="filterRombel" class="form-select form-select-sm" style="min-height: 40px; border-radius: 8px;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Guru Filter --}}
                <div class="col-6 col-md-3">
                    <select wire:model.live="filterGuru" class="form-select form-select-sm" style="min-height: 40px; border-radius: 8px;">
                        <option value="">Semua Guru</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Box --}}
                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari guru, kelas, mapel..." style="min-height: 40px; border-radius: 0 8px 8px 0;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule & Monitoring Table --}}
    <div class="card border-0 shadow-sm animate-fade-in-up" style="border-radius: 14px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-task text-primary me-2"></i>Daftar Sesi Pembelajaran ({{ count($items) }} Sesi)
            </h6>
            <div class="small text-muted">
                Klik kolom header untuk menyortir &bull; Klik thumbnail untuk melihat foto bukti
            </div>
        </div>

        @if($items->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                <h6 class="fw-bold text-dark">Tidak Ada Sesi KBM Ditemukan</h6>
                <p class="text-muted small mb-0">Tidak ada jadwal atau filter tidak menghasilkan data pada hari ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th wire:click="toggleSort('jam_ke')" style="cursor: pointer; width: 110px;">
                                Jam Ke-
                                @if($sortBy === 'jam_ke') <i class="bi bi-sort-numeric-{{ $sortDirection === 'asc' ? 'down' : 'up' }}"></i> @endif
                            </th>
                            <th wire:click="toggleSort('kelas')" style="cursor: pointer; width: 110px;">
                                Kelas
                                @if($sortBy === 'kelas') <i class="bi bi-sort-alpha-{{ $sortDirection === 'asc' ? 'down' : 'up' }}"></i> @endif
                            </th>
                            <th>Mata Pelajaran / Kegiatan</th>
                            <th wire:click="toggleSort('guru')" style="cursor: pointer;">
                                Guru Pengajar
                                @if($sortBy === 'guru') <i class="bi bi-sort-alpha-{{ $sortDirection === 'asc' ? 'down' : 'up' }}"></i> @endif
                            </th>
                            <th wire:click="toggleSort('status')" class="text-center" style="cursor: pointer; width: 150px;">
                                Status KBM
                                @if($sortBy === 'status') <i class="bi bi-sort-down"></i> @endif
                            </th>
                            <th class="text-center" style="width: 120px;">Waktu Aktual</th>
                            <th class="text-center" style="width: 90px;">Foto Bukti</th>
                            <th>Materi Diajarkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->jam_display }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1" style="border-radius: 6px;">
                                        {{ $item->rombel_nama }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->mapel_nama }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->guru_nama }}</div>
                                    @if($item->guru_pengganti)
                                        <div class="small text-warning fw-semibold mt-1">
                                            <i class="bi bi-person-check me-1"></i>Pengganti: {{ $item->guru_pengganti }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->status_badge }} px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.78rem; border-radius: 6px;">
                                        <i class="bi {{ $item->status_icon }}"></i>
                                        <span>{{ $item->status_label }}</span>
                                    </span>
                                </td>
                                <td class="text-center small">
                                    <span class="text-muted fw-semibold">{{ $item->waktu_aktual }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->foto_bukti)
                                        <button type="button" onclick="window.openPhotoModal('{{ $item->foto_bukti }}', 'Foto Bukti KBM — {{ $item->rombel_nama }} ({{ $item->guru_nama }})')" class="btn btn-sm btn-outline-primary p-1 shadow-sm" style="border-radius: 8px;" title="Lihat Foto Bukti">
                                            <img src="{{ $item->foto_bukti }}" alt="Foto" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px;">
                                        </button>
                                    @else
                                        <span class="text-muted small"><i class="bi bi-camera text-black-50 fs-5"></i></span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-muted" style="max-width: 250px; line-height: 1.3;">
                                        {{ Str::limit($item->materi, 70) }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
