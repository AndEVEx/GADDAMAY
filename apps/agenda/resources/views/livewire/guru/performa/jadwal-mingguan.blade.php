<div class="container-fluid px-3 py-3" style="max-width: 1200px;">
    {{-- Breadcrumb & Title --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 fw-bold rounded-pill small">
                    <i class="bi bi-speedometer me-1"></i> Performa Saya
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-week text-primary"></i> Jam Mengajar Guru Dalam Seminggu
            </h4>
            <p class="text-muted small mb-0">Rincian beban mengajar mingguan dan distribusi jam pelajaran (JP).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.performa.export-iki') }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 px-3 py-2 fw-semibold" style="border-radius: 10px;" wire:navigate>
                <i class="bi bi-file-earmark-pdf-fill"></i> Export IKI (PDF)
            </a>
        </div>
    </div>

    {{-- Stats Overview Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #1a56db, #0d47a1); color: white;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Beban Mengajar</span>
                        <h2 class="display-6 fw-extrabold mb-0 mt-1 text-white">{{ $totalJpSeminggu }} <span class="fs-5 fw-normal text-white-50">JP / Minggu</span></h2>
                        <small class="text-white-50 mt-1 d-block">Jam Pelajaran (45 mnt / JP)</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                        <i class="bi bi-clock-history fs-3 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Kelas Diajar</span>
                        <h2 class="display-6 fw-bold mb-0 mt-1 text-primary">{{ $totalRombelUnik }} <span class="fs-5 fw-normal text-muted">Kelas</span></h2>
                        <small class="text-muted mt-1 d-block">Rombongan Belajar Aktif</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                        <i class="bi bi-door-open-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Mata Pelajaran Diampu</span>
                        <h2 class="display-6 fw-bold mb-0 mt-1 text-success">{{ $totalMapelUnik }} <span class="fs-5 fw-normal text-muted">Mapel</span></h2>
                        <small class="text-muted mt-1 d-block">Mata Pelajaran Resmi</small>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                        <i class="bi bi-journal-bookmark-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Schedule Table --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-table text-primary"></i> Tabel Jadwal Mengajar Mingguan
            </h6>
            <span class="badge bg-primary rounded-pill px-3 py-2 fw-semibold">
                {{ $totalJpSeminggu }} Total JP
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="width: 50px;">No</th>
                            <th class="py-3" style="width: 110px;">Hari</th>
                            <th class="py-3" style="width: 140px;">Jam Ke</th>
                            <th class="py-3" style="width: 130px;">Waktu</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Mata Pelajaran</th>
                            <th class="text-center py-3" style="width: 90px;">Beban JP</th>
                            <th class="pe-4 py-3" style="width: 150px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse($jadwalList as $item)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">{{ $no++ }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                        {{ $item->hari_label }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-primary">
                                    {{ $item->jam_range }}
                                </td>
                                <td class="small text-muted font-monospace">
                                    <i class="bi bi-clock me-1"></i>{{ $item->waktu_range }}
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $item->rombel }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->mapel }}</div>
                                    @if($item->kode_mapel !== '-')
                                        <small class="text-muted">Kode: {{ $item->kode_mapel }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1.5 fw-bold">
                                        {{ $item->total_jp }} JP
                                    </span>
                                </td>
                                <td class="pe-4 text-muted small">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada data jadwal mengajar untuk akun guru ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($jadwalList->isNotEmpty())
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-end py-3 ps-4">TOTAL BEBAN MENGAJAR DALAM SEMINGGU:</td>
                                <td class="text-center py-3">
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-2 fs-6">
                                        {{ $totalJpSeminggu }} JP
                                    </span>
                                </td>
                                <td class="pe-4 py-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Breakdown per Mapel & per Kelas --}}
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart text-success"></i> Rincian JP Berdasarkan Mata Pelajaran
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="text-muted small">
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Jumlah Kelas</th>
                                    <th class="text-end">Total JP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapPerMapel as $rm)
                                    <tr>
                                        <td class="fw-semibold">{{ $rm->mapel }}</td>
                                        <td class="text-center">{{ $rm->kelas_count }} Kelas</td>
                                        <td class="text-end fw-bold text-success">{{ $rm->total_jp }} JP</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-building text-info"></i> Rincian JP Berdasarkan Kelas
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="text-muted small">
                                <tr>
                                    <th>Kelas / Rombel</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-end">Total JP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapPerKelas as $rk)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $rk->kelas }}</td>
                                        <td class="small text-muted">{{ $rk->mapel_list }}</td>
                                        <td class="text-end fw-bold text-primary">{{ $rk->total_jp }} JP</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>