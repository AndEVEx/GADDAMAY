<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    {{-- Header Banner Ekstrakurikuler --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca);">
        <div class="card-body p-4 position-relative">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-trophy-fill me-1"></i>MODUL EKSTRAKURIKULER
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white px-2.5 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>

                    <h3 class="fw-bold mb-1 text-white">
                        Presensi &amp; Manajemen Ekstrakurikuler
                    </h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 720px;">
                        Pusat pencatatan kehadiran kegiatan ekstrakurikuler berbasis <strong>Scan QR Code Kartu Pelajar Digital</strong> dan <strong>Checklist Manual</strong> untuk Pembina &amp; Pelatih Ekskul.
                    </p>
                </div>

                {{-- Selector Cabang Ekskul --}}
                <div class="bg-black bg-opacity-25 p-3 rounded-4 border border-white border-opacity-15" style="min-width: 280px;">
                    <label class="small text-white-50 fw-semibold mb-1 d-block">PILIH CABANG EKSKUL:</label>
                    <select wire:change="selectEkskul($event.target.value)" class="form-select form-select-sm fw-bold border-0 bg-white text-dark rounded-pill">
                        @foreach($ekskulList as $eks)
                            <option value="{{ $eks->id }}" {{ $selectedEkskulId == $eks->id ? 'selected' : '' }}>
                                {{ $eks->nama_ekskul }} ({{ $eks->kategori }})
                            </option>
                        @endforeach
                    </select>

                    @if($currentEkskul)
                        <div class="mt-2 pt-2 border-top border-white border-opacity-15 small text-white-50" style="font-size: 0.78rem;">
                            <div><i class="bi bi-person-badge me-1 text-warning"></i>Pembina: <strong class="text-white">{{ $currentEkskul->nama_pembina_lengkap }}</strong></div>
                            <div><i class="bi bi-whistle me-1 text-info"></i>Pelatih: <strong class="text-white">{{ $currentEkskul->pelatih_nama }}</strong></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Singkat Ekskul Aktif --}}
    @if($currentEkskul)
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 text-white bg-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-calendar2-week fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Jadwal &amp; Waktu Latihan</div>
                            <div class="fw-bold text-dark">{{ $currentEkskul->jadwal_rutin ?: 'Sesuai Jadwal' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 text-white bg-success d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Lokasi / Tempat Latihan</div>
                            <div class="fw-bold text-dark">{{ $currentEkskul->tempat_latihan ?: 'Kampus SMKN 2 Indramayu' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 text-white bg-warning text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Total Anggota Terdaftar</div>
                            <div class="fw-bold text-dark">{{ count($anggotaList) }} Siswa Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs Navigasi Modul --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2 p-md-3">
            <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto no-scrollbar py-1">
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'presensi')" class="nav-link {{ $activeTab === 'presensi' ? 'active bg-primary text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                        <i class="bi bi-qr-code-scan me-1"></i> Presensi Sesi Latihan (Scan &amp; Manual)
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'riwayat')" class="nav-link {{ $activeTab === 'riwayat' ? 'active bg-dark text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Pertemuan Ekskul
                    </button>
                </li>
                <li class="nav-item">
                    <button wire:click="$set('activeTab', 'anggota')" class="nav-link {{ $activeTab === 'anggota' ? 'active bg-indigo text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small" style="{{ $activeTab === 'anggota' ? 'background: #4338ca !important;' : '' }}">
                        <i class="bi bi-people-fill me-1"></i> Manajemen Anggota ({{ count($anggotaList) }})
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- TAB 1: PRESENSI & SCAN QR --}}
    @if($activeTab === 'presensi')
        <div class="row g-4">
            {{-- Panel Kiri: Scanner QR & Input Barcode --}}
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4 sticky-top" style="top: 80px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-camera-fill text-primary"></i> Scanner QR Kartu Pelajar
                        </h5>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill small">
                            <i class="bi bi-broadcast me-1"></i>Siap Scan
                        </span>
                    </div>

                    <p class="text-muted small mb-3">
                        Arahkan kamera ke QR Code pada <strong>Kartu Siswa Digital</strong> atau gunakan barcode scanner USB untuk presensi instan.
                    </p>

                    {{-- Kotak Scanner Kamera Live --}}
                    <div class="position-relative rounded-4 overflow-hidden border bg-dark mb-3 shadow-inner" style="min-height: 250px;">
                        <div id="qr-reader" style="width: 100%;"></div>
                        <div id="qr-reader-placeholder" class="d-flex flex-column align-items-center justify-content-center p-4 text-white text-center position-absolute top-0 start-0 w-100 h-100">
                            <i class="bi bi-qr-code-scan fs-1 text-warning mb-2 animate-bounce"></i>
                            <div class="fw-bold">Kamera Scanner Aktif</div>
                            <div class="small text-white-50">Izinkan akses kamera jika browser meminta izin</div>
                            <button type="button" onclick="startScanner()" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 mt-3 shadow-sm">
                                <i class="bi bi-camera-video-fill me-1"></i> Mulai Kamera Scanner
                            </button>
                        </div>
                    </div>

                    {{-- Form Input Manual NIS / Barcode Scanner Fisik --}}
                    <form wire:submit.prevent="submitManualNis" class="mb-3">
                        <label class="small fw-bold text-muted mb-1">
                            <i class="bi bi-upc-scan me-1"></i>Input Manual NIS / Barcode Reader:
                        </label>
                        <div class="input-group">
                            <input type="text" wire:model="scannerInput" class="form-control rounded-start-pill bg-light border-0 px-3" placeholder="Ketik NIS lalu tekan Enter..." autofocus>
                            <button type="submit" class="btn btn-primary rounded-end-pill px-3 fw-bold">
                                <i class="bi bi-arrow-return-left me-1"></i> Cek &amp; Hadir
                            </button>
                        </div>
                    </form>

                    {{-- Feedback Alert Scan --}}
                    @if($scanFeedback)
                        <div class="alert {{ $scanStatus === 'success' ? 'alert-success' : ($scanStatus === 'warning' ? 'alert-warning' : 'alert-danger') }} rounded-3 py-2 px-3 mb-0 small d-flex align-items-center gap-2 shadow-sm animate-fade-in">
                            <i class="bi {{ $scanStatus === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} fs-5"></i>
                            <div class="flex-grow-1 fw-semibold">{{ $scanFeedback }}</div>
                        </div>
                    @endif

                    {{-- Parameter Sesi Latihan Hari Ini --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="small text-muted fw-bold mb-0">Pengaturan Sesi Latihan:</label>
                            <button type="button" wire:click="buatPertemuanBaru" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                                <i class="bi bi-plus-circle me-1"></i> Sesi Baru
                            </button>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="bg-light p-2 rounded-3">
                                    <div class="text-muted" style="font-size: 0.70rem;">Tanggal:</div>
                                    <input type="date" wire:model.live="tanggalPertemuan" wire:change="loadOrCreatePertemuanHariIni" class="form-control form-control-sm border-0 bg-transparent fw-bold p-0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-2 rounded-3">
                                    <div class="text-muted" style="font-size: 0.70rem;">Jam Latihan:</div>
                                    <div class="fw-bold small text-dark">{{ $jamMulai }} - {{ $jamSelesai }} WIB</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-light p-2 rounded-3">
                                    <div class="text-muted" style="font-size: 0.70rem;">Materi / Topik Latihan:</div>
                                    <input type="text" wire:model="topikLatihan" class="form-control form-control-sm border-0 bg-transparent fw-semibold p-0 text-dark" placeholder="Masukkan materi latihan...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Kanan: Rekap Statistik & Tabel Presensi Anggota --}}
            <div class="col-12 col-lg-7">
                {{-- Kartu Statistik Kehadiran --}}
                <div class="row g-2 g-md-3 mb-3">
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 p-2.5 text-center">
                            <div class="text-success small fw-bold">HADIR (H)</div>
                            <div class="h3 fw-bold text-success mb-0">{{ $statistik['H'] }}</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 p-2.5 text-center">
                            <div class="text-primary small fw-bold">IZIN (I)</div>
                            <div class="h3 fw-bold text-primary mb-0">{{ $statistik['I'] }}</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 p-2.5 text-center">
                            <div class="text-warning-emphasis small fw-bold">SAKIT (S)</div>
                            <div class="h3 fw-bold text-warning-emphasis mb-0">{{ $statistik['S'] }}</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 p-2.5 text-center">
                            <div class="text-danger small fw-bold">ALPA (A)</div>
                            <div class="h3 fw-bold text-danger mb-0">{{ $statistik['A'] }}</div>
                        </div>
                    </div>
                </div>

                {{-- Toolbar Aksi Cepat --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="fw-bold text-dark">Daftar Kehadiran Anggota</span>
                            <span class="badge bg-secondary rounded-pill ms-1">{{ count($presensiList) }} Siswa</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" wire:click="tandaiSemuaHadir" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm">
                                <i class="bi bi-check-all me-1"></i> Semua Hadir
                            </button>
                            <button type="button" wire:click="resetPresensi" wire:confirm="Yakin ingin mereset kehadiran sesi ini?" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tabel Presensi Interaktif --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                                <tr>
                                    <th class="ps-3" style="width: 45%;">Siswa &amp; Jabatan</th>
                                    <th style="width: 25%;">Metode &amp; Waktu</th>
                                    <th class="text-center pe-3" style="width: 30%;">Status Presensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensiList as $pr)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="rounded-circle overflow-hidden flex-shrink-0 border" style="width: 38px; height: 38px;">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($pr->siswa->nama) }}&size=100&background=4338ca&color=ffffff" alt="Avatar" class="w-100 h-100">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 180px;">{{ $pr->siswa->nama }}</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">
                                                        NIS: {{ $pr->siswa->nis }} • {{ $pr->siswa->rombel->nama_rombel ?? 'X' }}
                                                    </div>
                                                    <span class="badge bg-info-subtle text-info fw-semibold" style="font-size: 0.65rem;">
                                                        {{ $pr->anggota->jabatan_anggota ?? 'Anggota' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($pr->status === 'H')
                                                <div class="d-flex flex-column">
                                                    <span class="badge {{ $pr->metode_presensi === 'scan_qr' ? 'bg-primary' : 'bg-secondary' }} rounded-pill small" style="width: fit-content; font-size: 0.65rem;">
                                                        <i class="bi {{ $pr->metode_presensi === 'scan_qr' ? 'bi-qr-code' : 'bi-pencil' }} me-1"></i>
                                                        {{ $pr->metode_presensi === 'scan_qr' ? 'Scan QR' : 'Manual' }}
                                                    </span>
                                                    <span class="text-muted mt-1" style="font-size: 0.70rem;">
                                                        {{ $pr->waktu_scan ? $pr->waktu_scan->format('H:i') . ' WIB' : '-' }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 0.75rem;">Belum Hadir</span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-center">
                                            <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border shadow-sm" role="group">
                                                {{-- Tombol H (Hadir) --}}
                                                <button type="button" wire:click="updateStatusManual('{{ $pr->id }}', 'H')" class="btn {{ $pr->status === 'H' ? 'btn-success fw-bold text-white' : 'btn-light text-muted' }} rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Tandai Hadir">
                                                    H
                                                </button>
                                                {{-- Tombol S (Sakit) --}}
                                                <button type="button" wire:click="updateStatusManual('{{ $pr->id }}', 'S')" class="btn {{ $pr->status === 'S' ? 'btn-warning fw-bold text-dark' : 'btn-light text-muted' }} rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Tandai Sakit">
                                                    S
                                                </button>
                                                {{-- Tombol I (Izin) --}}
                                                <button type="button" wire:click="updateStatusManual('{{ $pr->id }}', 'I')" class="btn {{ $pr->status === 'I' ? 'btn-primary fw-bold text-white' : 'btn-light text-muted' }} rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Tandai Izin">
                                                    I
                                                </button>
                                                {{-- Tombol A (Alpa) --}}
                                                <button type="button" wire:click="updateStatusManual('{{ $pr->id }}', 'A')" class="btn {{ $pr->status === 'A' ? 'btn-danger fw-bold text-white' : 'btn-light text-muted' }} rounded-pill px-2.5 py-1" style="font-size: 0.75rem;" title="Tandai Alpa">
                                                    A
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">
                                            Belum ada anggota terdaftar di ekskul ini. Buka tab <strong>Manajemen Anggota</strong> untuk menambahkan siswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: RIWAYAT PERTEMUAN EKSKUL --}}
    @if($activeTab === 'riwayat')
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i> Riwayat Sesi Latihan &amp; Rekap Kehadiran
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-3">Tanggal &amp; Waktu</th>
                            <th>Topik Latihan</th>
                            <th>Pencatat / Pembina</th>
                            <th>Kehadiran</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPertemuan as $sesi)
                            @php
                                $hadirCount = $sesi->presensi->where('status', 'H')->count();
                                $totalAnggota = $sesi->presensi->count();
                                $persen = $totalAnggota > 0 ? round(($hadirCount / $totalAnggota) * 100) : 0;
                            @endphp
                            <tr class="{{ $selectedPertemuanId === $sesi->id ? 'table-primary bg-opacity-25' : '' }}">
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $sesi->tanggal_pertemuan->translatedFormat('d F Y') }}</div>
                                    <div class="text-muted small">{{ $sesi->jam_mulai }} - {{ $sesi->jam_selesai }} WIB</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $sesi->topik_latihan }}</div>
                                    <div class="text-muted small">{{ $sesi->deskripsi_kegiatan ?: 'Latihan rutin berkala' }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $sesi->pencatat->name ?? 'Pembina' }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary small">{{ $sesi->status_pertemuan }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                            <div class="progress-bar bg-success" style="width: {{ $persen }}%;"></div>
                                        </div>
                                        <span class="small fw-bold text-dark">{{ $hadirCount }}/{{ $totalAnggota }} ({{ $persen }}%)</span>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" wire:click="gantiPertemuan('{{ $sesi->id }}')" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Buka Presensi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Belum ada sesi pertemuan yang tercatat untuk cabang ekskul ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TAB 3: MANAJEMEN ANGGOTA --}}
    @if($activeTab === 'anggota')
        <div class="row g-4">
            {{-- Form Tambah Anggota --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill text-primary"></i> Daftarkan Anggota Siswa
                    </h5>

                    @if(session('anggota_success'))
                        <div class="alert alert-success rounded-3 py-2 px-3 small mb-3">
                            {{ session('anggota_success') }}
                        </div>
                    @endif
                    @if(session('anggota_error'))
                        <div class="alert alert-danger rounded-3 py-2 px-3 small mb-3">
                            {{ session('anggota_error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="tambahAnggota">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Pilih Siswa SMKN 2:</label>
                            <select wire:model="tambahSiswaId" class="form-select rounded-3 small" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($allSiswaSekolah as $sw)
                                    <option value="{{ $sw->id }}">
                                        {{ $sw->nama }} (NIS: {{ $sw->nis }}) - {{ $sw->rombel->nama_rombel ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Jabatan di Ekskul:</label>
                            <select wire:model="tambahJabatan" class="form-select rounded-3 small">
                                <option value="Ketua">Ketua</option>
                                <option value="Wakil Ketua">Wakil Ketua</option>
                                <option value="Sekretaris">Sekretaris</option>
                                <option value="Bendahara">Bendahara</option>
                                <option value="Komandan Pleton">Komandan Pleton (Danton)</option>
                                <option value="Anggota" selected>Anggota</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Tambahkan ke Ekskul
                        </button>
                    </form>
                </div>
            </div>

            {{-- Tabel Daftar Anggota --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill text-indigo" style="color: #4338ca;"></i> Anggota Resmi Cabang Ekskul
                        </h5>
                        <span class="badge bg-primary rounded-pill">{{ count($anggotaList) }} Siswa</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                                <tr>
                                    <th class="ps-3">Nama Siswa &amp; NIS</th>
                                    <th>Kelas</th>
                                    <th>Jabatan</th>
                                    <th>Kehadiran</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($anggotaList as $agt)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark">{{ $agt->siswa->nama }}</div>
                                            <div class="text-muted small">NIS: {{ $agt->siswa->nis }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $agt->siswa->rombel->nama_rombel ?? 'X' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning-subtle text-dark fw-bold">{{ $agt->jabatan_anggota }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">{{ $agt->persentase_kehadiran }}%</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" wire:click="hapusAnggota('{{ $agt->id }}')" wire:confirm="Yakin ingin mengeluarkan siswa ini dari ekskul?" class="btn btn-outline-danger btn-sm rounded-circle p-1" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Belum ada anggota terdaftar di ekskul ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- HTML5-QRCode Library CDN & Web Audio API Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrScanner = null;
    let isScanning = false;

    // Web Audio API Synthesizer Beep Sound
    function playBeep(freq = 880, duration = 0.15, type = 'sine') {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + duration);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start();
            osc.stop(audioCtx.currentTime + duration);
        } catch (e) {
            console.log('Audio feedback not available', e);
        }
    }

    function startScanner() {
        const placeholder = document.getElementById('qr-reader-placeholder');
        if (placeholder) placeholder.style.display = 'none';

        if (html5QrScanner) {
            html5QrScanner.clear();
        }

        html5QrScanner = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 220, height: 220 } };

        html5QrScanner.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                // Berhasil membaca QR Code
                playBeep(980, 0.18, 'triangle');
                @this.call('processScanQr', decodedText);
            },
            (errorMessage) => {
                // Parsing frame (no qr found in current frame)
            }
        ).catch(err => {
            console.error("Gagal memulai kamera scanner:", err);
            if (placeholder) placeholder.style.display = 'flex';
        });
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('playAudioAlert', (data) => {
            const eventData = data[0] || data;
            if (eventData.type === 'success') {
                playBeep(1046, 0.2, 'sine'); // High C Note Beep
            } else {
                playBeep(260, 0.4, 'sawtooth'); // Low Error Buzz
            }
        });
    });
</script>
