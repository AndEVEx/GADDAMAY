<div class="container-fluid px-3 py-3" style="max-width: 1200px;">
    {{-- Breadcrumb & Title --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 fw-bold rounded-pill small">
                    <i class="bi bi-speedometer me-1"></i> Performa Saya
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-warning"></i> Realisasi Jam Masuk Kelas Bulanan
            </h4>
            <p class="text-muted small mb-0">Perbandingan total jam mengajar yang terealisasi dibanding jam semestinya dalam satu bulan.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white small"><i class="bi bi-calendar3"></i></span>
                <input type="month" wire:model.live="bulan" class="form-control form-control-sm fw-semibold" style="min-height: 38px; width: 150px;">
            </div>
            <a href="{{ route('guru.performa.export-iki', ['bulan' => $bulan]) }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 px-3 py-2 fw-semibold text-nowrap" style="border-radius: 10px;" wire:navigate>
                <i class="bi bi-file-earmark-pdf-fill"></i> Export IKI (PDF)
            </a>
        </div>
    </div>

    {{-- Stats Cards Overview --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Target Jam Semestinya</span>
                    <h3 class="fw-extrabold text-secondary mb-0 mt-1">{{ $targetJpTotal }} <span class="fs-6 fw-normal text-muted">JP</span></h3>
                    <small class="text-muted">Target {{ $targetPertemuanTotal }} sesi di {{ $namaBulan }}</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-success border-4">
                <div class="card-body p-3">
                    <span class="text-success text-uppercase fw-bold" style="font-size: 0.68rem;">Realisasi Masuk Kelas</span>
                    <h3 class="fw-extrabold text-success mb-0 mt-1">{{ $realisasiJpTotal }} <span class="fs-6 fw-normal text-muted">JP</span></h3>
                    <small class="text-muted">{{ $realisasiPertemuanTotal }} pertemuan terlaksana</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #1a56db, #0d47a1); color: white;">
                <div class="card-body p-3">
                    <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.68rem;">Ketercapaian Kehadiran</span>
                    <h3 class="fw-extrabold text-white mb-0 mt-1">{{ $persentaseKehadiran }}%</h3>
                    <div class="progress mt-2" style="height: 6px; background-color: rgba(255,255,255,0.2);">
                        <div class="progress-bar bg-white" style="width: {{ min(100, $persentaseKehadiran) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Izin / Sakit / Tidak Masuk</span>
                    <h3 class="fw-extrabold text-danger mb-0 mt-1">{{ $totalIzinJp + $totalSakitJp }} <span class="fs-6 fw-normal text-muted">JP</span></h3>
                    <small class="text-muted">Izin: {{ $totalIzinJp }} JP &bull; Sakit: {{ $totalSakitJp }} JP</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap Target vs Realisasi Per Kelas & Mapel --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-steps text-primary"></i> Rekap Realisasi Mengajar Per Kelas (Bulan {{ $namaBulan }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Kelas / Rombel</th>
                            <th class="py-3">Mata Pelajaran</th>
                            <th class="text-center py-3">Target JP</th>
                            <th class="text-center py-3">Realisasi JP</th>
                            <th class="text-center py-3">Izin / Sakit</th>
                            <th class="text-center py-3">Selisih JP</th>
                            <th class="pe-4 text-center py-3" style="width: 140px;">Ketercapaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapKelas as $rk)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $rk->rombel_nama }}</td>
                                <td class="fw-semibold text-dark">{{ $rk->mapel_nama }}</td>
                                <td class="text-center fw-semibold text-muted">{{ $rk->target_jp }} JP</td>
                                <td class="text-center fw-bold text-success">{{ $rk->realisasi_jp }} JP</td>
                                <td class="text-center text-muted small">
                                    @if($rk->izin_jp > 0 || $rk->sakit_jp > 0)
                                        <span class="badge bg-warning bg-opacity-10 text-dark">{{ $rk->izin_jp + $rk->sakit_jp }} JP</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center fw-bold {{ $rk->selisih_jp >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $rk->selisih_jp >= 0 ? '+' . $rk->selisih_jp : $rk->selisih_jp }} JP
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                        <div class="progress flex-fill" style="height: 8px;">
                                            <div class="progress-bar {{ $rk->persentase >= 100 ? 'bg-success' : ($rk->persentase >= 75 ? 'bg-primary' : 'bg-warning') }}" style="width: {{ min(100, $rk->persentase) }}%;"></div>
                                        </div>
                                        <span class="small fw-bold">{{ $rk->persentase }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada data jadwal untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($rekapKelas->isNotEmpty())
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="2" class="ps-4 py-3 text-end">TOTAL REKAPITULASI BULAN {{ strtoupper($namaBulan) }}:</td>
                                <td class="text-center py-3 text-secondary">{{ $targetJpTotal }} JP</td>
                                <td class="text-center py-3 text-success">{{ $realisasiJpTotal }} JP</td>
                                <td class="text-center py-3 text-warning">{{ $totalIzinJp + $totalSakitJp }} JP</td>
                                <td class="text-center py-3 {{ ($realisasiJpTotal - $targetJpTotal) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ ($realisasiJpTotal - $targetJpTotal) >= 0 ? '+' . ($realisasiJpTotal - $targetJpTotal) : ($realisasiJpTotal - $targetJpTotal) }} JP
                                </td>
                                <td class="pe-4 text-center py-3">
                                    <span class="badge bg-primary px-3 py-1.5 rounded-pill">{{ $persentaseKehadiran }}%</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Log Agenda Harian Bulan Ini --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-secondary"></i> Riwayat Agenda Masuk Kelas (Bulan {{ $namaBulan }})
            </h6>
            <span class="badge bg-light text-muted border px-2.5 py-1">{{ count($agendas) }} Catatan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-2.5">Tanggal</th>
                            <th class="py-2.5">Kelas</th>
                            <th class="py-2.5">Mata Pelajaran</th>
                            <th class="py-2.5">Materi Diajarkan</th>
                            <th class="text-center py-2.5">Durasi</th>
                            <th class="text-center py-2.5">Kehadiran Guru</th>
                            <th class="pe-4 text-center py-2.5">Status Agenda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $ag)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $ag->tanggal?->translatedFormat('d M Y') ?? '-' }}</div>
                                    <small class="text-muted">{{ $ag->tanggal?->translatedFormat('l') }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $ag->jadwalPelajaran?->rombel?->nama_kelas ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $ag->jadwalPelajaran?->mataPelajaran?->nama_mapel ?? '-' }}</span>
                                </td>
                                <td class="small text-muted" style="max-width: 250px;">
                                    {{ Str::limit($ag->materi_diajarkan ?? 'Belum ada catatan materi', 60) }}
                                </td>
                                <td class="text-center fw-bold text-dark">
                                    {{ $ag->durasi_jp }} JP
                                </td>
                                <td class="text-center">
                                    @if($ag->status_kehadiran_guru === 'hadir')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">Hadir</span>
                                    @elseif(in_array($ag->status_kehadiran_guru, ['izin', 'cuti', 'dinas', 'tugas_luar']))
                                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-2.5 py-1">Izin</span>
                                    @elseif($ag->status_kehadiran_guru === 'sakit')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1">Sakit</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-2.5 py-1">{{ $ag->status_kehadiran_guru }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    @if($ag->status === 'selesai')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                                    @elseif($ag->status === 'berjalan')
                                        <span class="badge bg-primary"><i class="bi bi-play-circle me-1"></i> Berjalan</span>
                                    @elseif($ag->status === 'dibatalkan')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $ag->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada catatan agenda masuk kelas di bulan {{ $namaBulan }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>