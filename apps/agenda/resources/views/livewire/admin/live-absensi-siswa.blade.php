<div class="container-fluid py-3 px-2 px-md-4">
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-broadcast text-danger"></i> Live Absensi Siswa
            </h4>
            <p class="text-muted small mb-0">
                Monitoring presensi siswa real-time per jam pelajaran hari ini di SMKN 2 Indramayu
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="max-width: 200px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-primary"></i></span>
                <input type="date" wire:model.live="tanggal" class="form-control form-control-sm border-start-0 fw-semibold">
            </div>
            <button wire:click="$refresh" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 shadow-sm" title="Segarkan Data">
                <i class="bi bi-arrow-clockwise" wire:loading.class="spin"></i>
                <span class="d-none d-sm-inline">Refresh</span>
            </button>
        </div>
    </div>

    {{-- Quick Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-primary bg-opacity-10 border-start border-primary border-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Tingkat Hadir</div>
                        <div class="fs-4 fw-extrabold text-primary">{{ $stats['persenHadir'] }}%</div>
                    </div>
                    <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-person-check-fill fs-5"></i>
                    </div>
                </div>
                <div class="text-muted small mt-2" style="font-size: 0.75rem;">
                    <strong>{{ number_format($stats['hadir']) }}</strong> data presensi hadir
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-success bg-opacity-10 border-start border-success border-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Hadir Sesi</div>
                        <div class="fs-4 fw-extrabold text-success">{{ number_format($stats['hadir']) }}</div>
                    </div>
                    <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                </div>
                <div class="text-muted small mt-2" style="font-size: 0.75rem;">
                    Dari total {{ number_format($stats['totalPresensi']) }} presensi tercatat
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-warning bg-opacity-10 border-start border-warning border-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Sakit & Izin</div>
                        <div class="fs-4 fw-extrabold text-warning">{{ number_format($stats['sakit'] + $stats['izin']) }}</div>
                    </div>
                    <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-envelope-paper-heart-fill fs-5"></i>
                    </div>
                </div>
                <div class="text-muted small mt-2" style="font-size: 0.75rem;">
                    Sakit: <strong>{{ $stats['sakit'] }}</strong> | Izin: <strong>{{ $stats['izin'] }}</strong>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-danger bg-opacity-10 border-start border-danger border-4 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Alpa / Bolos</div>
                        <div class="fs-4 fw-extrabold text-danger">{{ number_format($stats['alpa']) }}</div>
                    </div>
                    <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <div class="text-muted small mt-2" style="font-size: 0.75rem;">
                    Perlu penanganan Kesiswaan/BK
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Kelas & Rombel Section --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold small text-secondary text-uppercase"><i class="bi bi-filter me-1"></i>Pilih Tingkat:</span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" wire:click="$set('filterTingkat', 'all')" class="btn {{ $filterTingkat === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</button>
                        <button type="button" wire:click="$set('filterTingkat', '10')" class="btn {{ $filterTingkat === '10' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas X</button>
                        <button type="button" wire:click="$set('filterTingkat', '11')" class="btn {{ $filterTingkat === '11' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas XI</button>
                        <button type="button" wire:click="$set('filterTingkat', '12')" class="btn {{ $filterTingkat === '12' ? 'btn-primary' : 'btn-outline-secondary' }}">Kelas XII</button>
                    </div>
                </div>
                <div class="text-muted small">
                    Total Kelas: <strong>{{ $allRombels->count() }}</strong>
                </div>
            </div>

            {{-- Grid Tombol Pilihan Kelas --}}
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-2" style="max-height: 200px; overflow-y: auto;">
                @forelse($allRombels as $rombel)
                    <div class="col">
                        <button type="button" 
                                wire:click="selectRombel('{{ $rombel->id }}')"
                                class="btn w-100 text-truncate text-start py-2 px-2 rounded-3 transition-all d-flex align-items-center justify-content-between {{ $selectedRombelId === $rombel->id ? 'btn-primary shadow-sm fw-bold' : 'btn-outline-secondary text-dark' }}"
                                style="min-height: 40px; font-size: 0.82rem;">
                            <span>{{ $rombel->nama_kelas }}</span>
                            @if($selectedRombelId === $rombel->id)
                                <i class="bi bi-check-circle-fill ms-1"></i>
                            @endif
                        </button>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3 small">
                        Tidak ada rombel pada filter ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Main Attendance Table Section --}}
    @if($selectedRombel)
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        {{-- Card Header with Filter & Search --}}
        <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">{{ $selectedRombel->nama_kelas }}</span>
                    <span class="badge bg-light text-dark border px-2 py-1 small">
                        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1 small">
                        {{ $agendas->count() }} Sesi Pelajaran
                    </span>
                </div>
            </div>

            {{-- Filter Status & Pencarian --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Quick Filter Status --}}
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" wire:click="$set('filterStatus', 'all')" class="btn {{ $filterStatus === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
                        Semua Siswa
                    </button>
                    <button type="button" wire:click="$set('filterStatus', 'bermasalah')" class="btn {{ $filterStatus === 'bermasalah' ? 'btn-danger text-white fw-bold' : 'btn-outline-danger' }}" title="Siswa Bolos Jam / Alpa">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Bermasalah
                    </button>
                    <button type="button" wire:click="$set('filterStatus', 'alpa')" class="btn {{ $filterStatus === 'alpa' ? 'btn-danger' : 'btn-outline-secondary' }}">Alpa</button>
                    <button type="button" wire:click="$set('filterStatus', 'izin')" class="btn {{ $filterStatus === 'izin' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">Izin</button>
                    <button type="button" wire:click="$set('filterStatus', 'sakit')" class="btn {{ $filterStatus === 'sakit' ? 'btn-primary' : 'btn-outline-secondary' }}">Sakit</button>
                </div>

                {{-- Search Box --}}
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Cari nama/NIS...">
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-hover table-bordered align-middle mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="table-light sticky-top shadow-sm" style="z-index: 2;">
                        <tr>
                            <th class="py-3" style="width: 45px;">No</th>
                            <th class="py-3 text-start" style="min-width: 180px;">Nama Siswa</th>
                            <th class="py-3" style="width: 90px;">NIS</th>

                            {{-- Kolom Dinamis per Sesi Jam Pelajaran Hari Ini --}}
                            @forelse($agendas as $idx => $agenda)
                                @php
                                    $mapel = $agenda->jadwalPelajaran?->mataPelajaran;
                                    $guru = $agenda->guruPengganti ?? $agenda->guru;
                                    $jamMulai = $agenda->jadwalPelajaran?->jamMulai?->jam_ke ?? '?';
                                    $jamSelesai = $agenda->jadwalPelajaran?->jamSelesai?->jam_ke ?? '?';
                                @endphp
                                <th class="py-2 px-2 bg-light border-start border-end" style="min-width: 140px; max-width: 180px;">
                                    <div class="fw-bold text-primary text-truncate" title="{{ $mapel?->nama_mapel }}">
                                        {{ $mapel?->kode_mapel ?? $mapel?->nama_mapel ?? 'Mapel' }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        Jam Ke {{ $jamMulai == $jamSelesai ? $jamMulai : "$jamMulai - $jamSelesai" }}
                                    </div>
                                    <div class="text-secondary small text-truncate" style="font-size: 0.70rem;" title="{{ $guru?->name }}">
                                        <i class="bi bi-person me-1"></i>{{ $guru?->name ?? 'Guru' }}
                                    </div>
                                </th>
                            @empty
                                <th class="py-3 text-muted" style="min-width: 250px;">
                                    <i class="bi bi-info-circle me-1"></i>Belum Ada Sesi Pelajaran Berlangsung
                                </th>
                            @endforelse

                            <th class="py-3 bg-light" style="width: 160px;">Kesimpulan Hari Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaMatrix as $index => $item)
                            <tr class="{{ $item->isProblematic ? 'table-danger table-opacity-10' : '' }}">
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="text-start fw-semibold">
                                    {{ $item->siswa->nama }}
                                    @if($item->summaryStatus === 'cabut')
                                        <span class="badge bg-danger ms-1 small" style="font-size: 0.68rem;">Bolos Sesi</span>
                                    @endif
                                </td>
                                <td class="text-muted small font-monospace">{{ $item->siswa->nis ?? '-' }}</td>

                                {{-- Status per Sesi --}}
                                @forelse($agendas as $agenda)
                                    @php
                                        $st = $item->sessions[$agenda->id] ?? 'belum';
                                    @endphp
                                    <td class="py-2">
                                        @if($st === 'hadir')
                                            <span class="badge bg-success py-1 px-2 fw-bold" style="font-size: 0.8rem;" title="Hadir">H</span>
                                        @elseif($st === 'sakit')
                                            <span class="badge bg-primary py-1 px-2 fw-bold" style="font-size: 0.8rem;" title="Sakit">S</span>
                                        @elseif($st === 'izin')
                                            <span class="badge bg-warning text-dark py-1 px-2 fw-bold" style="font-size: 0.8rem;" title="Izin">I</span>
                                        @elseif($st === 'alpa')
                                            <span class="badge bg-danger py-1 px-2 fw-bold" style="font-size: 0.8rem;" title="Alpa / Bolos">A</span>
                                        @else
                                            <span class="text-muted small" title="Belum Absen">-</span>
                                        @endif
                                    </td>
                                @empty
                                    <td class="text-muted small">-</td>
                                @endforelse

                                {{-- Kesimpulan Status Harian --}}
                                <td>
                                    <span class="badge {{ $item->summaryBadgeClass }} py-1 px-2" style="font-size: 0.78rem;">
                                        {{ $item->summaryLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + max(1, $agendas->count()) }}" class="text-center text-muted py-4">
                                    <i class="bi bi-people fs-4 d-block mb-2"></i>
                                    Tidak ada data siswa yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card Footer: Legend --}}
        <div class="card-footer bg-light py-2 px-3 border-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small">
                <div class="d-flex align-items-center gap-3 text-muted">
                    <span class="fw-bold text-dark">Keterangan:</span>
                    <span><span class="badge bg-success px-2 py-1 me-1">H</span> Hadir</span>
                    <span><span class="badge bg-primary px-2 py-1 me-1">S</span> Sakit</span>
                    <span><span class="badge bg-warning text-dark px-2 py-1 me-1">I</span> Izin</span>
                    <span><span class="badge bg-danger px-2 py-1 me-1">A</span> Alpa / Bolos</span>
                    <span><span class="text-muted fw-bold me-1">-</span> Belum Absen</span>
                </div>
                <div class="text-muted">
                    Total Siswa: <strong>{{ $siswaMatrix->count() }}</strong> orang
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
