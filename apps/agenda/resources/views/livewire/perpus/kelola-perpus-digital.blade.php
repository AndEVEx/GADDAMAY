<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Banner Kepala Perpustakaan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f766e, #0e7490);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-book-half me-1"></i>PERPUSTAKAAN DIGITAL
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Pusat Kelola &amp; Monitoring Perpus Digital</h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 720px;">
                        Ruang kendali Kepala Perpustakaan untuk mengunggah koleksi e-book PDF, memantau riwayat durasi baca siswa, penanda halaman terakhir, serta mengunduh rekap literasi digital sekolah.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button wire:click="openCreateBuku" class="btn btn-warning text-dark fw-bold shadow-sm rounded-3 px-3 py-2">
                        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Koleksi Buku
                    </button>
                    <a href="{{ route('perpus.katalog') }}" class="btn btn-outline-light fw-bold rounded-3 px-3 py-2">
                        <i class="bi bi-eye-fill me-1"></i> Lihat Katalog Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Metrik Perpustakaan --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-teal bg-opacity-10 text-teal rounded-3 p-3 text-success fs-3" style="background: #ccfbf1; color: #0f766e;">
                        <i class="bi bi-journals"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Judul Buku</div>
                        <div class="fs-4 fw-black text-dark">{{ $totalBuku }}</div>
                        <div class="small text-success fw-semibold">Koleksi Digital</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 fs-3">
                        <i class="bi bi-book-reader"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Aktivitas Membaca</div>
                        <div class="fs-4 fw-black text-dark">{{ $totalAktivitasBaca }}</div>
                        <div class="small text-primary fw-semibold">Sesi Tercatat</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 fs-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Jam Membaca</div>
                        <div class="fs-4 fw-black text-dark">{{ $totalJamBaca }} <span class="fs-6 fw-normal">Jam</span></div>
                        <div class="small text-warning-emphasis fw-semibold">Akumulasi Waktu</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-indigo bg-opacity-10 text-indigo rounded-3 p-3 fs-3" style="background: #e0e7ff; color: #4338ca;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Siswa Aktif Baca</div>
                        <div class="fs-4 fw-black text-dark">{{ $totalSiswaMembaca }}</div>
                        <div class="small text-indigo fw-semibold">Partisipasi Literasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigasi: Katalog vs Monitoring --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <ul class="nav nav-pills gap-2">
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'katalog')" class="nav-link {{ $activeTab === 'katalog' ? 'active bg-teal fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-4" style="{{ $activeTab === 'katalog' ? 'background: #0f766e !important;' : '' }}">
                            <i class="bi bi-grid-fill me-1"></i> Katalog Koleksi Buku ({{ $totalBuku }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'monitoring')" class="nav-link {{ $activeTab === 'monitoring' ? 'active bg-primary fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-4">
                            <i class="bi bi-graph-up-arrow me-1"></i> Monitoring Data Pembaca ({{ $totalAktivitasBaca }})
                        </button>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @if($activeTab === 'monitoring')
                        <button wire:click="exportExcelLaporan" class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3">
                            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Laporan CSV
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 1: KATALOG KOLEKSI BUKU --}}
    @if($activeTab === 'katalog')
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari judul buku, penulis, atau nomor ISBN...">
                </div>
            </div>
            <div class="col-md-5">
                <select wire:model.live="filterKategori" class="form-select bg-light border-0">
                    <option value="">Semua Kategori Buku</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }} ({{ $kat->buku_count }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-4">
            @forelse($bukuList as $buku)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column justify-content-between transition-all hover-shadow bg-white">
                        <div>
                            <div class="position-relative" style="height: 200px; background: #f8fafc; overflow: hidden;">
                                <img src="{{ $buku->cover_asset }}" alt="{{ $buku->judul }}" class="w-100 h-100" style="object-fit: cover;">
                                <div class="position-absolute top-0 start-0 p-3">
                                    <span class="badge bg-dark bg-opacity-75 text-white px-2.5 py-1 rounded-pill small">
                                        <i class="bi {{ $buku->kategori->ikon ?? 'bi-book' }} me-1"></i>{{ $buku->kategori->nama_kategori ?? 'Umum' }}
                                    </span>
                                </div>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge {{ $buku->allow_offline_cache ? 'bg-success' : 'bg-secondary' }} rounded-pill small" title="{{ $buku->allow_offline_cache ? 'Mendukung Baca Offline' : 'Khusus Online' }}">
                                        <i class="bi {{ $buku->allow_offline_cache ? 'bi-cloud-check' : 'bi-wifi' }} me-1"></i>
                                        {{ $buku->allow_offline_cache ? 'Offline Ready' : 'Online Only' }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-1 line-clamp-2" title="{{ $buku->judul }}">{{ $buku->judul }}</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-pen me-1"></i>{{ $buku->penulis }}</p>

                                @if($buku->sinopsis)
                                    <p class="text-muted small mb-3 line-clamp-2" style="font-size: 0.78rem;">
                                        {{ $buku->sinopsis }}
                                    </p>
                                @endif

                                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light small mb-2">
                                    <span><i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $buku->jumlah_halaman }} Halaman</span>
                                    <span><i class="bi bi-hdd text-secondary me-1"></i>{{ $buku->file_size_mb }} MB</span>
                                    <span><i class="bi bi-eye text-primary me-1"></i>{{ $buku->riwayat_baca_count }}x Dibaca</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top p-3 d-flex gap-2">
                            <a href="{{ route('perpus.baca', $buku->id) }}" class="btn btn-teal text-white btn-sm flex-fill fw-bold rounded-3" style="background: #0f766e;">
                                <i class="bi bi-book-half me-1"></i> Buka Reader
                            </a>
                            <button wire:click="editBuku('{{ $buku->id }}')" class="btn btn-outline-primary btn-sm rounded-3 px-2.5" title="Edit Metadata">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button wire:click="deleteBuku('{{ $buku->id }}')" wire:confirm="Hapus buku ini dari katalog?" class="btn btn-outline-danger btn-sm rounded-3 px-2.5" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                        <i class="bi bi-journals display-3 text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Belum Ada Buku Dalam Katalog</h5>
                        <p class="text-muted small mx-auto" style="max-width: 460px;">
                            Klik tombol <strong>"Tambah Koleksi Buku"</strong> untuk mengunggah e-book PDF materi pelajaran, modul kejuruan, atau novel literasi.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $bukuList->links() }}
        </div>
    @endif

    {{-- TAB 2: MONITORING DATA PEMBACA & WAKTU BACA --}}
    @if($activeTab === 'monitoring')
        <div class="row g-3 mb-3">
            <div class="col-md-5">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm bg-light" placeholder="Cari nama pembaca atau judul buku...">
            </div>
            <div class="col-md-4">
                <select wire:model.live="filterRombel" class="form-select form-select-sm bg-light">
                    <option value="">Semua Rombel / Kelas</option>
                    @foreach($rombelList as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select wire:model.live="filterKategori" class="form-select form-select-sm bg-light">
                    <option value="">Semua Kategori Buku</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Nama Pembaca</th>
                            <th>Kelas / Rombel</th>
                            <th>Buku Yang Dibaca</th>
                            <th>Kategori</th>
                            <th>Halaman Terakhir</th>
                            <th>Progres</th>
                            <th>Waktu Baca</th>
                            <th>Terakhir Membaca</th>
                            <th class="pe-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatBacaList as $idx => $log)
                            <tr>
                                <td class="ps-3">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $log->user->name ?? '-' }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $log->user->email ?? $log->user->username ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $log->rombel->nama_rombel ?? ucfirst($log->user->role ?? '-') }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary text-truncate" style="max-width: 220px;" title="{{ $log->buku->judul ?? '-' }}">
                                        {{ $log->buku->judul ?? '-' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $log->buku->penulis ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $log->buku->kategori->nama_kategori ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info fw-bold">
                                        Hal. {{ $log->halaman_terakhir }} / {{ $log->buku->jumlah_halaman ?? '-' }}
                                    </span>
                                </td>
                                <td style="min-width: 110px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $log->persentase_baca }}%;"></div>
                                        </div>
                                        <span class="fw-bold" style="font-size: 0.72rem;">{{ $log->persentase_baca }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><i class="bi bi-clock me-1 text-warning"></i>{{ $log->durasi_format }}</div>
                                </td>
                                <td>
                                    {{ $log->waktu_terakhir_baca ? $log->waktu_terakhir_baca->diffForHumans() : '-' }}
                                </td>
                                <td class="pe-3">
                                    @if($log->status_selesai)
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Sedang Membaca</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    Belum ada data aktivitas pembacaan siswa/guru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $riwayatBacaList->links() }}
            </div>
        </div>
    @endif

    {{-- MODAL CRUD BUKU DIGITAL --}}
    @if($showModalBuku)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 bg-teal text-white p-3" style="background: #0f766e;">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-book-plus me-2"></i>{{ $isEditBuku ? 'Edit Koleksi Buku Digital' : 'Unggah Buku Digital Baru (PDF)' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showModalBuku', false)"></button>
                    </div>
                    <form wire:submit.prevent="saveBuku">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="judul" class="form-control" placeholder="Contoh: Pemrograman Web Modern untuk SMK" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Kategori Buku <span class="text-danger">*</span></label>
                                    <select wire:model="kategori_id" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategoriList as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Penulis / Pengarang <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="penulis" class="form-control" placeholder="Nama penulis" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Penerbit</label>
                                    <input type="text" wire:model="penerbit" class="form-control" placeholder="Penerbit / Kementerian Pendidikan">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tahun Terbit</label>
                                    <input type="text" wire:model="tahun_terbit" class="form-control" placeholder="2025">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">ISBN</label>
                                    <input type="text" wire:model="isbn" class="form-control" placeholder="978-602-xxx">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Jumlah Halaman <span class="text-danger">*</span></label>
                                    <input type="number" wire:model="jumlah_halaman" class="form-control" min="1" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Sinopsis / Ringkasan Buku</label>
                                    <textarea wire:model="sinopsis" class="form-control" rows="3" placeholder="Sinopsis atau ringkasan isi buku..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Unggah Berkas PDF</label>
                                    <input type="file" wire:model="uploadPdf" class="form-control" accept=".pdf">
                                    <small class="text-muted" style="font-size: 0.72rem;">Atau masukkan link URL PDF di bawah jika di-host eksternal.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">URL Berkas PDF</label>
                                    <input type="text" wire:model="file_pdf_url" class="form-control" placeholder="https://... atau /storage/perpus/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Unggah Gambar Sampul (Cover)</label>
                                    <input type="file" wire:model="uploadCover" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">URL Cover Buku</label>
                                    <input type="text" wire:model="cover_url" class="form-control" placeholder="https://images...">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" wire:model="allow_offline_cache" id="offlineSwitch">
                                        <label class="form-check-label small fw-semibold" for="offlineSwitch">
                                            Izinkan Siswa Menyimpan Offline di Ponsel (IndexedDB PWA)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" wire:click="$set('showModalBuku', false)">Batal</button>
                            <button type="submit" class="btn btn-teal text-white fw-bold px-4" style="background: #0f766e;">
                                <i class="bi bi-save me-1"></i> Simpan Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
