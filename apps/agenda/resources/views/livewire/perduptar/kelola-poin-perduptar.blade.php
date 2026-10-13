<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    {{-- Header Banner PERDUPTAR 2026 --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e1b4b, #312e81, #1e3a8a);">
        <div class="card-body p-4 position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-shield-fill-check me-1"></i>PERDUPTAR 2026 RESMI
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Buku Saku &amp; Sistem Poin Kedisiplinan Taruna</h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 680px;">
                        Implementasi digital Buku Peraturan Kehidupan Taruna (PERDUPTAR 2026). Penilaian kedisiplinan berbasis formula <strong>Nk = Npr - Npl</strong> (Prestasi dikurangi Pelanggaran) dengan kontrol pembinaan fisik edukatif dan terintegrasi ke buku saku siswa.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button wire:click="$set('activeTab', 'input_poin')" class="btn btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                        <i class="bi bi-plus-circle-fill me-1"></i> Catat Poin Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 Kartu Metrik Ringkasan Kedisiplinan Sekolah --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Total Pelanggaran</span>
                    <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $totalPelanggaran }}</h3>
                <span class="small text-muted" style="font-size: 0.72rem;">Tercatat di Buku Saku</span>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Total Prestasi Taruna</span>
                    <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-trophy-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $totalPrestasi }}</h3>
                <span class="small text-muted" style="font-size: 0.72rem;">Poin Plus Diberikan</span>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Sedang Dibina Fisik</span>
                    <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-walking fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-warning mb-0">{{ $totalTarunaDibina }}</h3>
                <span class="small text-muted" style="font-size: 0.72rem;">Sanksi Push-Up / Lari</span>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small text-muted fw-semibold">Surat Peringatan (SP)</span>
                    <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-envelope-exclamation-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalSpAktif }}</h3>
                <span class="small text-muted" style="font-size: 0.72rem;">SP / Panggilan Ortu</span>
            </div>
        </div>
    </div>

    {{-- Tabs Navigasi PERDUPTAR --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2 p-md-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto no-scrollbar py-1">
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'buku_saku')" class="nav-link {{ $activeTab === 'buku_saku' ? 'active bg-primary fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                            <i class="bi bi-journal-bookmark-fill me-1"></i> Buku Saku Taruna (Rekap Nilai)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'input_poin')" class="nav-link {{ $activeTab === 'input_poin' ? 'active bg-warning text-dark fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                            <i class="bi bi-pencil-square me-1"></i> Input Pencatatan Poin
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'katalog_aturan')" class="nav-link {{ $activeTab === 'katalog_aturan' ? 'active bg-indigo text-white fw-bold' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small" style="{{ $activeTab === 'katalog_aturan' ? 'background: #4338ca !important;' : '' }}">
                            <i class="bi bi-book-half me-1"></i> Katalog Pasal PERDUPTAR 2026
                        </button>
                    </li>
                    @if(Auth::user()?->role === 'siswa' || Auth::user()?->role === 'ketua_kelas')
                        <li class="nav-item">
                            <button wire:click="$set('activeTab', 'buku_saya')" class="nav-link {{ $activeTab === 'buku_saya' ? 'active bg-success fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                                <i class="bi bi-person-badge-fill me-1"></i> Buku Saku Saya
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- TAB 1: BUKU SAKU TARUNA (REKAP KEDISIPLINAN Nk = Npr - Npl) --}}
    @if($activeTab === 'buku_saku')
        {{-- Filter & Pencarian --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
            <div class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="searchSiswa" class="form-control bg-light border-0" placeholder="Cari nama taruna atau NIS...">
                    </div>
                </div>
                <div class="col-md-5">
                    <select wire:model.live="filterRombel" class="form-select bg-light border-0">
                        <option value="">Semua Rombel / Kelas</option>
                        @foreach($rombelList as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Grid Kartu Buku Saku Taruna --}}
        <div class="row g-3 g-md-4">
            @forelse($siswaList as $s)
                @php
                    $npr = $s->getTotalPoinPrestasi();
                    $npl = $s->getTotalPoinPelanggaran();
                    $nk = $s->getNilaiKedisiplinan();
                    $kategori = $s->getKategoriKedisiplinan();
                    $isDanger = $nk <= -20;
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 d-flex flex-column justify-content-between transition-all hover-shadow {{ $isDanger ? 'border border-2 border-danger' : '' }}">
                        <div>
                            {{-- Header Kartu Siswa --}}
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 overflow-hidden shadow-sm flex-shrink-0" style="width: 54px; height: 68px; background: #e2e8f0;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($s->nama) }}&size=150&background=1e1b4b&color=ffffff&bold=true" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="overflow-hidden flex-grow-1">
                                    <span class="badge bg-light text-secondary border small mb-1" style="font-size: 0.68rem;">
                                        {{ $s->rombel->nama_rombel ?? 'X Vokasi' }}
                                    </span>
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" title="{{ $s->nama }}">{{ $s->nama }}</h6>
                                    <div class="text-muted small" style="font-size: 0.72rem;">NIS: {{ $s->nis }}</div>
                                </div>
                            </div>

                            {{-- Poin Rumus: Nk = Npr - Npl --}}
                            <div class="p-2.5 rounded-3 bg-light mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small" style="font-size: 0.72rem;">Rumus Disiplin (Nk):</span>
                                    <span class="badge {{ $kategori['badge'] }} px-2 py-0.5 small">{{ $kategori['grade'] }} - {{ $kategori['label'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small">
                                        <span class="text-success fw-bold">+{{ $npr }} (Prestasi)</span> - 
                                        <span class="text-danger fw-bold">{{ $npl }} (Pelanggaran)</span>
                                    </div>
                                    <div class="h5 fw-bold mb-0 {{ $nk >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $nk > 0 ? '+' . $nk : $nk }}
                                    </div>
                                </div>
                            </div>

                            @if($isDanger)
                                <div class="alert alert-danger p-2 mb-2 rounded-3 small d-flex align-items-center gap-2" style="font-size: 0.72rem;">
                                    <i class="bi bi-exclamation-octagon-fill fs-6 text-danger"></i>
                                    <div><strong>PERINGATAN BERAT:</strong> Kumulatif minus &ge; 20 poin. Wajib sidang disiplin &amp; pemanggilan orang tua!</div>
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Buka Buku Saku Lengkap --}}
                        <div class="pt-2 border-top">
                            <button wire:click="lihatDetailSiswa('{{ $s->id }}')" class="btn btn-outline-primary btn-sm rounded-pill w-100 py-1.5 fw-semibold">
                                <i class="bi bi-book me-1"></i> Buka Buku Saku Digital ({{ $s->perduptarPoin->count() }} Catatan)
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                        <i class="bi bi-person-x display-4 text-muted mb-2"></i>
                        <h6 class="fw-bold text-dark">Tidak Ada Data Taruna</h6>
                        <p class="text-muted small">Coba cari dengan kata kunci lain atau pilih rombel yang berbeda.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $siswaList->links() }}
        </div>
    @endif

    {{-- TAB 2: INPUT PENCATATAN POIN BARU --}}
    @if($activeTab === 'input_poin')
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center gap-2.5 mb-3 pb-3 border-bottom">
                        <div class="rounded-3 p-2 bg-warning text-dark">
                            <i class="bi bi-pencil-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Formulir Pencatatan Buku Saku Taruna</h5>
                            <span class="text-muted small">Mencatat prestasi atau pelanggaran resmi mengacu PERDUPTAR 2026</span>
                        </div>
                    </div>

                    <form wire:submit.prevent="simpanPencatatanPoin">
                        <div class="row g-3">
                            {{-- Pilih Siswa --}}
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Pilih Taruna / Siswa <span class="text-danger">*</span></label>
                                <select wire:model="siswaId" class="form-select @error('siswaId') is-invalid @enderror">
                                    <option value="">-- Pilih Siswa / Taruna --</option>
                                    @foreach($allSiswaSelect as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel->nama_rombel ?? '-' }} - NIS: {{ $s->nis }})</option>
                                    @endforeach
                                </select>
                                @error('siswaId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Pilih Tindakan: Prestasi vs Pelanggaran --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Jenis Tindakan <span class="text-danger">*</span></label>
                                <select wire:model.live="tipe" class="form-select">
                                    <option value="pelanggaran">Pelanggaran Kedisiplinan (-)</option>
                                    <option value="prestasi">Prestasi / Penghargaan (+)</option>
                                </select>
                            </div>

                            {{-- Pilih Dari Katalog PERDUPTAR 2026 --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Katalog Aturan PERDUPTAR 2026</label>
                                <select wire:model.live="selectedKatalogId" class="form-select">
                                    <option value="">-- Pilih dari Butir Peraturan --</option>
                                    @if($tipe === 'pelanggaran')
                                        @foreach($katalogPelanggaran as $kat)
                                            <option value="{{ $kat->id }}">[{{ $kat->bobot_poin }} Poin] {{ $kat->pasal_rujukan }} - {{ $kat->nama_butir }}</option>
                                        @endforeach
                                    @else
                                        @foreach($katalogPrestasi as $kat)
                                            <option value="{{ $kat->id }}">[+{{ $kat->bobot_poin }} Poin] {{ $kat->pasal_rujukan }} - {{ $kat->nama_butir }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- Bobot Poin & Pasal Rujukan --}}
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Bobot Poin <span class="text-danger">*</span></label>
                                <input type="number" wire:model="bobotPoin" class="form-control @error('bobotPoin') is-invalid @enderror" placeholder="-5 atau +5">
                                @error('bobotPoin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-dark">Pasal Rujukan PERDUPTAR</label>
                                <input type="text" wire:model="pasalRujukan" class="form-control" placeholder="misal: Pasal 34 Ayat 7 Huruf a">
                            </div>

                            {{-- Nama Butir Tindakan --}}
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Nama Butir / Judul Tindakan <span class="text-danger">*</span></label>
                                <input type="text" wire:model="namaButir" class="form-control @error('namaButir') is-invalid @enderror" placeholder="misal: Terlambat Masuk Sekolah Lebih Dari 15 Menit">
                                @error('namaButir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Tanggal & Lokasi --}}
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Tanggal Kejadian <span class="text-danger">*</span></label>
                                <input type="date" wire:model="tanggalKejadian" class="form-control @error('tanggalKejadian') is-invalid @enderror">
                                @error('tanggalKejadian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Lokasi Kejadian</label>
                                <input type="text" wire:model="lokasi" class="form-control" placeholder="Pintu Gerbang / Ruang Kelas / Lapangan">
                            </div>

                            {{-- Sanksi Pembinaan Diberikan --}}
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-dark">Sanksi Edukatif Diberikan (Pasal 35)</label>
                                <input type="text" wire:model="sanksiDiberikan" class="form-control" placeholder="misal: Push up 20x dan membersihkan pos penjagaan">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark">Status Surat Peringatan</label>
                                <select wire:model="suratPeringatan" class="form-select">
                                    <option value="tidak_ada">Tidak Ada SP</option>
                                    <option value="sp1">Surat Peringatan 1 (SP 1)</option>
                                    <option value="sp2">Surat Peringatan 2 (SP 2)</option>
                                    <option value="sp3">Surat Peringatan 3 (SP 3)</option>
                                    <option value="panggilan_ortu">Pemanggilan Orang Tua</option>
                                    <option value="skorsing">Skorsing</option>
                                </select>
                            </div>

                            {{-- Kronologi / Catatan --}}
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Kronologi Kejadian &amp; Catatan Petugas</label>
                                <textarea wire:model="kronologi" rows="3" class="form-control" placeholder="Jelaskan kronologi kejadian secara objektif..."></textarea>
                            </div>

                            {{-- Unggah Bukti Foto --}}
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Lampiran Foto Bukti (Opsional)</label>
                                <input type="file" wire:model="fotoBukti" class="form-control">
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="col-12 pt-3 border-top d-flex justify-content-end gap-2">
                                <button type="button" wire:click="$set('activeTab', 'buku_saku')" class="btn btn-outline-secondary rounded-pill px-4">
                                    Batal
                                </button>
                                <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Simpan Catatan Buku Saku
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 3: KATALOG PASAL & ATURAN PERDUPTAR 2026 --}}
    @if($activeTab === 'katalog_aturan')
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-3 p-3 text-white" style="background: #4338ca;">
                    <i class="bi bi-book-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Buku Pedoman PERDUPTAR 2026 (Pasal 33 &amp; 34)</h5>
                    <p class="text-muted small mb-0">Rujukan resmi bobot nilai prestasi dan sanksi pelanggaran kedisiplinan Taruna SMKN 2 Indramayu.</p>
                </div>
            </div>

            <div class="row g-4">
                {{-- Bagian Prestasi --}}
                <div class="col-12 col-lg-6">
                    <div class="border rounded-4 p-3 bg-light h-100">
                        <h6 class="fw-bold text-success mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-award-fill"></i> PASAL 33: PENILAIAN PRESTASI (+1 s/d +20)
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($katalogPrestasi as $p)
                                <div class="list-group-item bg-transparent px-0 py-2.5">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <span class="badge bg-success-subtle text-success small mb-1">{{ $p->pasal_rujukan }} • {{ $p->kategori_tingkat }}</span>
                                            <h6 class="fw-bold text-dark small mb-1">{{ $p->nama_butir }}</h6>
                                            <p class="text-muted mb-0" style="font-size: 0.72rem;">{{ $p->deskripsi_aturan }}</p>
                                        </div>
                                        <span class="badge bg-success rounded-pill px-2.5 py-1 text-nowrap">+{{ $p->bobot_poin }} Poin</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Bagian Pelanggaran --}}
                <div class="col-12 col-lg-6">
                    <div class="border rounded-4 p-3 bg-light h-100">
                        <h6 class="fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-x"></i> PASAL 34: PENILAIAN PELANGGARAN (-1 s/d -20 &amp; Berat)
                        </h6>
                        <div class="list-group list-group-flush">
                            @foreach($katalogPelanggaran as $pl)
                                <div class="list-group-item bg-transparent px-0 py-2.5">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <span class="badge bg-danger-subtle text-danger small mb-1">{{ $pl->pasal_rujukan }} • {{ $pl->kategori_tingkat }}</span>
                                            <h6 class="fw-bold text-dark small mb-1">{{ $pl->nama_butir }}</h6>
                                            <p class="text-muted mb-1" style="font-size: 0.72rem;">{{ $pl->deskripsi_aturan }}</p>
                                            @if($pl->sanksi_default)
                                                <div class="text-danger small" style="font-size: 0.70rem;">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Sanksi: {{ $pl->sanksi_default }}
                                                </div>
                                            @endif
                                        </div>
                                        <span class="badge bg-danger rounded-pill px-2.5 py-1 text-nowrap">{{ $pl->bobot_poin }} Poin</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 4: BUKU SAKU SAYA (KHUSUS SISWA YANG SEDANG LOGIN) --}}
    @if($activeTab === 'buku_saya' && $siswaSaya)
        @php
            $nprSaya = $siswaSaya->getTotalPoinPrestasi();
            $nplSaya = $siswaSaya->getTotalPoinPelanggaran();
            $nkSaya = $siswaSaya->getNilaiKedisiplinan();
            $kategoriSaya = $siswaSaya->getKategoriKedisiplinan();
        @endphp
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 overflow-hidden shadow-sm flex-shrink-0" style="width: 64px; height: 80px; background: #e2e8f0;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($siswaSaya->nama) }}&size=200&background=1e1b4b&color=ffffff&bold=true" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div>
                        <span class="badge bg-primary rounded-pill px-2.5 py-1 small mb-1">{{ $siswaSaya->rombel->nama_rombel ?? 'X Vokasi' }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $siswaSaya->nama }}</h4>
                        <div class="text-muted small">Nomor Induk Siswa: <strong>{{ $siswaSaya->nis }}</strong></div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 bg-light p-3 rounded-4">
                    <div>
                        <div class="small text-muted">Nilai Kedisiplinan Taruna:</div>
                        <div class="h4 fw-bold mb-0 {{ $nkSaya >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $nkSaya > 0 ? '+' . $nkSaya : $nkSaya }}
                        </div>
                    </div>
                    <span class="badge {{ $kategoriSaya['badge'] }} px-3 py-2 rounded-pill fs-6">
                        Grade {{ $kategoriSaya['grade'] }} ({{ $kategoriSaya['label'] }})
                    </span>
                </div>
            </div>

            <h6 class="fw-bold text-dark mb-3">Histori Transaksi Catatan Buku Saku Taruna</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Tanggal</th>
                            <th>Tindakan / Prestasi / Pelanggaran</th>
                            <th>Pasal Rujukan</th>
                            <th>Poin</th>
                            <th>Sanksi / Apresiasi</th>
                            <th>Petugas Pencatat</th>
                            <th>Status Pembinaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaSaya->perduptarPoin as $catatan)
                            <tr>
                                <td class="small text-muted">{{ $catatan->tanggal_kejadian->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $catatan->nama_butir }}</div>
                                    @if($catatan->kronologi_kejadian)
                                        <div class="text-muted small" style="font-size: 0.72rem;">{{ $catatan->kronologi_kejadian }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-secondary border small">{{ $catatan->pasal_rujukan ?? '-' }}</span></td>
                                <td>
                                    <span class="badge {{ $catatan->tipe === 'prestasi' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1">
                                        {{ $catatan->bobot_poin > 0 ? '+' . $catatan->bobot_poin : $catatan->bobot_poin }}
                                    </span>
                                </td>
                                <td class="small">{{ $catatan->sanksi_diberikan ?? '-' }}</td>
                                <td class="small text-muted">{{ $catatan->petugas->name ?? $catatan->peran_petugas }}</td>
                                <td>
                                    @if($catatan->status_tindakan === 'selesai_pembinaan')
                                        <span class="badge bg-success-subtle text-success small"><i class="bi bi-check2 me-1"></i>Selesai</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark small"><i class="bi bi-clock me-1"></i>Dalam Proses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted small">
                                    <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                                    Selamat! Buku saku Anda bersih, belum ada catatan pelanggaran yang tercatat. Pertahankan disiplin korps taruna!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- MODAL DETAIL BUKU SAKU SISWA (BAGI GURU / ADMIN) --}}
    @if($selectedSiswaDetail)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white p-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-journal-bookmark-fill text-warning fs-5"></i>
                            <div>
                                <h6 class="modal-title fw-bold mb-0">Buku Saku Digital: {{ $selectedSiswaDetail->nama }}</h6>
                                <div class="text-white-50 small" style="font-size: 0.72rem;">Kelas: {{ $selectedSiswaDetail->rombel->nama_rombel ?? '-' }} | NIS: {{ $selectedSiswaDetail->nis }}</div>
                            </div>
                        </div>
                        <button type="button" wire:click="tutupDetailSiswa" class="btn-close btn-close-white"></button>
                    </div>

                    <div class="modal-body p-4">
                        @php
                            $detNpr = $selectedSiswaDetail->getTotalPoinPrestasi();
                            $detNpl = $selectedSiswaDetail->getTotalPoinPelanggaran();
                            $detNk = $selectedSiswaDetail->getNilaiKedisiplinan();
                            $detKat = $selectedSiswaDetail->getKategoriKedisiplinan();
                        @endphp
                        {{-- Ringkasan Rumus Nk --}}
                        <div class="p-3 bg-light rounded-4 mb-4 border d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Rumus Evaluasi Kedisiplinan (Pasal 32):</div>
                                <h5 class="fw-bold text-dark mb-0">Nk = {{ $detNpr }} (Npr) - {{ $detNpl }} (Npl) = <span class="{{ $detNk >= 0 ? 'text-success' : 'text-danger' }}">{{ $detNk }}</span></h5>
                            </div>
                            <span class="badge {{ $detKat['badge'] }} px-3 py-2 rounded-pill fs-6">
                                Kategori {{ $detKat['grade'] }} ({{ $detKat['label'] }})
                            </span>
                        </div>

                        {{-- Daftar Riwayat Catatan --}}
                        <h6 class="fw-bold text-dark mb-2">Riwayat Catatan Buku Saku:</h6>
                        <div class="list-group list-group-flush mb-3">
                            @forelse($selectedSiswaDetail->perduptarPoin as $cp)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="badge {{ $cp->tipe === 'prestasi' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-0.5 small me-1">
                                                {{ $cp->bobot_poin > 0 ? '+' . $cp->bobot_poin : $cp->bobot_poin }} Poin
                                            </span>
                                            <span class="badge bg-light text-secondary border small">{{ $cp->pasal_rujukan ?? 'PERDUPTAR' }}</span>
                                            <h6 class="fw-bold text-dark mt-1 mb-0">{{ $cp->nama_butir }}</h6>
                                        </div>
                                        <div class="text-end small text-muted">
                                            {{ $cp->tanggal_kejadian->format('d M Y') }}
                                        </div>
                                    </div>

                                    @if($cp->kronologi_kejadian)
                                        <p class="text-muted small mb-1" style="font-size: 0.78rem;">{{ $cp->kronologi_kejadian }}</p>
                                    @endif

                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top mt-2 small">
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $cp->lokasi_kejadian ?? 'Sekolah' }} • Pencatat: <strong>{{ $cp->petugas->name ?? $cp->peran_petugas }}</strong>
                                        </div>

                                        <div class="d-flex align-items-center gap-1.5">
                                            @if($cp->status_tindakan === 'selesai_pembinaan')
                                                <span class="badge bg-success-subtle text-success small">Telah Selesai</span>
                                            @else
                                                <button type="button" wire:click="ubahStatusTindakan('{{ $cp->id }}', 'selesai_pembinaan')" class="btn btn-outline-success btn-sm rounded-pill py-0.5 px-2" style="font-size: 0.70rem;">
                                                    <i class="bi bi-check-lg me-1"></i>Tandai Selesai Dibina
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted small bg-light rounded-3">
                                    Belum ada catatan pelanggaran atau prestasi untuk taruna ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="modal-footer p-3 bg-light">
                        <button type="button" onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Catatan Buku Saku
                        </button>
                        <button type="button" wire:click="tutupDetailSiswa" class="btn btn-secondary btn-sm rounded-pill px-3">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
