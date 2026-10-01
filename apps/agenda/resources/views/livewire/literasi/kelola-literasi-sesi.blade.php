<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Banner Literasi Lapangan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a, #0284c7);">
        <div class="card-body p-4 text-white position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-broadcast me-1"></i>LITERASI RABU PAGI
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Pusat Literasi Lapangan & Presentasi Terpandu</h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 720px;">
                        Materi bacaan dikontrol langsung oleh Pemateri dan tersinkronisasi otomatis ke ponsel seluruh siswa di lapangan. Dilengkapi evaluasi kuis pilihan ganda & esai pendek reflektif ketercapaian materi.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button wire:click="openCreateSesi" class="btn btn-warning text-dark fw-bold shadow-sm rounded-3 px-3 py-2">
                        <i class="bi bi-plus-circle-fill me-1"></i> Buat Sesi Literasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari judul sesi, tema, atau nama pemateri...">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <select wire:model.live="filterStatus" class="form-select bg-light border-0">
                        <option value="">Semua Status Sesi</option>
                        <option value="draft">Draft (Persiapan)</option>
                        <option value="live">🔴 Live (Sedang Berlangsung)</option>
                        <option value="selesai">Selesai (Arsip)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Sesi Literasi --}}
    <div class="row g-4">
        @forelse($sesiList as $sesi)
            <div class="col-12 col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden transition-all hover-shadow">
                    {{-- Status Indicator Bar --}}
                    <div style="height: 5px;" class="
                        @if($sesi->status === 'live') bg-danger animate-pulse
                        @elseif($sesi->status === 'selesai') bg-success
                        @else bg-secondary @endif">
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            {{-- Header Card --}}
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <span class="badge bg-light text-primary border small fw-semibold">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $sesi->tema }}
                                </span>
                                @if($sesi->status === 'live')
                                    <span class="badge bg-danger text-white px-2 py-1 rounded-pill small d-flex align-items-center gap-1 shadow-sm">
                                        <span class="spinner-grow spinner-grow-sm" role="status" style="width: 0.5rem; height: 0.5rem;"></span>
                                        LIVE LAPANGAN
                                    </span>
                                @elseif($sesi->status === 'selesai')
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small">
                                        <i class="bi bi-check-circle-fill me-1"></i>Selesai
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">
                                        <i class="bi bi-pencil-fill me-1"></i>Draft
                                    </span>
                                @endif
                            </div>

                            <h5 class="fw-bold text-dark mb-2 line-clamp-2">{{ $sesi->judul }}</h5>
                            <div class="text-muted small mb-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                    <span>{{ $sesi->tanggal->isoFormat('dddd, D MMMM Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-video3 text-info"></i>
                                    <span class="fw-semibold text-dark">{{ $sesi->nama_pemateri }}</span>
                                    @if($sesi->jabatan_pemateri)
                                        <span class="text-muted">({{ $sesi->jabatan_pemateri }})</span>
                                    @endif
                                </div>
                            </div>

                            @if($sesi->deskripsi)
                                <p class="text-muted small mb-3 line-clamp-2 bg-light p-2 rounded-3">
                                    {{ $sesi->deskripsi }}
                                </p>
                            @endif

                            {{-- Statistik Ringkas Sesi --}}
                            <div class="row g-2 mb-3 text-center">
                                <div class="col-4">
                                    <div class="bg-light p-2 rounded-3">
                                        <div class="fw-bold text-primary fs-6">{{ $sesi->slides_count }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Halaman Slide</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light p-2 rounded-3">
                                        <div class="fw-bold text-warning fs-6">{{ $sesi->soal_count }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Soal Evaluasi</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light p-2 rounded-3">
                                        <div class="fw-bold text-success fs-6">{{ $sesi->presensi_siswa_count }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Siswa Merespon</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Toggle Evaluasi & Live --}}
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-white mb-3">
                                <span class="small fw-semibold text-secondary">Akses Soal / Kuis:</span>
                                <button wire:click="toggleAllowQuiz('{{ $sesi->id }}')" class="btn btn-sm {{ $sesi->allow_quiz ? 'btn-success' : 'btn-outline-secondary' }} py-0 px-2 rounded-pill" style="font-size: 0.75rem;">
                                    <i class="bi {{ $sesi->allow_quiz ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                                    {{ $sesi->allow_quiz ? 'Terbuka Untuk Siswa' : 'Terkunci' }}
                                </button>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-3 border-top">
                            <div class="d-grid gap-2 mb-2">
                                {{-- Tombol Masuk Remote Presenter --}}
                                <a href="{{ route('literasi.presenter', $sesi->id) }}" class="btn btn-primary fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-tv-fill fs-6"></i>
                                    <span>Buka Remote Presenter (Siarkan)</span>
                                </a>
                            </div>

                            <div class="d-flex flex-wrap gap-1">
                                <button wire:click="openSlidesModal('{{ $sesi->id }}')" class="btn btn-outline-info btn-sm flex-fill fw-semibold">
                                    <i class="bi bi-file-slides me-1"></i>Slides ({{ $sesi->slides_count }})
                                </button>
                                <button wire:click="openSoalModal('{{ $sesi->id }}')" class="btn btn-outline-warning btn-sm flex-fill fw-semibold">
                                    <i class="bi bi-patch-question me-1"></i>Soal ({{ $sesi->soal_count }})
                                </button>
                                <button wire:click="openRekapModal('{{ $sesi->id }}')" class="btn btn-outline-success btn-sm flex-fill fw-semibold">
                                    <i class="bi bi-people me-1"></i>Rekap ({{ $sesi->presensi_siswa_count }})
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm text-muted px-2" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <a class="dropdown-item small" href="javascript:void(0)" wire:click="editSesi('{{ $sesi->id }}')">
                                                <i class="bi bi-pencil me-2 text-primary"></i>Edit Info Sesi
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item small" href="javascript:void(0)" wire:click="toggleLiveStatus('{{ $sesi->id }}')">
                                                <i class="bi bi-arrow-repeat me-2 text-warning"></i>Ganti Status ({{ $sesi->status }})
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item small text-danger" href="javascript:void(0)" wire:click="deleteSesi('{{ $sesi->id }}')" wire:confirm="Yakin ingin menghapus sesi literasi ini?">
                                                <i class="bi bi-trash me-2"></i>Hapus Sesi
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                    <i class="bi bi-book-half text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold text-dark">Belum Ada Sesi Literasi Lapangan</h5>
                    <p class="text-muted small mx-auto" style="max-width: 500px;">
                        Klik tombol <strong>"Buat Sesi Literasi"</strong> untuk menjadwalkan materi literasi Rabu pagi berikutnya.
                    </p>
                    <div>
                        <button wire:click="openCreateSesi" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Buat Sesi Baru Sekarang
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $sesiList->links() }}
    </div>

    {{-- MODAL 1: FORM SESI LITERASI --}}
    @if($showModalSesi)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            {{ $isEditSesi ? 'Edit Sesi Literasi' : 'Jadwalkan Sesi Literasi Rabu Pagi' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModalSesi', false)"></button>
                    </div>
                    <form wire:submit.prevent="saveSesi">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Judul Sesi Literasi <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="judul" class="form-control" placeholder="Contoh: Membangun Etos Kerja Vokasi & Disiplin Positif" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tema / Kategori <span class="text-danger">*</span></label>
                                    <select wire:model="tema" class="form-select" required>
                                        <option value="Budaya Kerja & Karakter">Budaya Kerja & Karakter</option>
                                        <option value="Anti Perundungan & Bullying">Anti Perundungan & Bullying</option>
                                        <option value="Literasi Digital & AI">Literasi Digital & AI</option>
                                        <option value="Literasi Finansial">Literasi Finansial</option>
                                        <option value="Kesehatan & Pola Hidup">Kesehatan & Pola Hidup</option>
                                        <option value="Kewirausahaan">Kewirausahaan</option>
                                        <option value="Agama & Budi Pekerti">Agama & Budi Pekerti</option>
                                        <option value="Literasi Umum">Literasi Umum</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="tanggal" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Pilih Guru Pemateri</label>
                                    <select wire:model.live="pemateri_id" class="form-select">
                                        <option value="">-- Narasumber Luar / Input Manual --</option>
                                        @foreach($guruList as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Nama Pemateri <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="nama_pemateri" class="form-control" placeholder="Nama pemateri / narasumber" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Jabatan / Instansi Pemateri</label>
                                    <input type="text" wire:model="jabatan_pemateri" class="form-control" placeholder="Contoh: Guru PPLG / Kepala Sekolah / HRD Industri">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Kode Akses Sesi</label>
                                    <input type="text" wire:model="kode_sesi" class="form-control text-uppercase font-monospace" placeholder="KODE UNIK (Otomatis)">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Deskripsi / Pengantar Sesi</label>
                                    <textarea wire:model="deskripsi" class="form-control" rows="2" placeholder="Ringkasan topik atau instruksi bagi peserta literasi lapangan..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" wire:click="$set('showModalSesi', false)">Batal</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-save me-1"></i> Simpan Sesi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 2: KELOLA SLIDES PRESENTASI --}}
    @if($showModalSlide && $activeSesiForSlide)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 bg-primary text-white p-3">
                        <div>
                            <h5 class="modal-title fw-bold mb-0">
                                <i class="bi bi-file-slides me-2"></i>Kelola Slide Presentasi: {{ $activeSesiForSlide->judul }}
                            </h5>
                            <small class="text-white-50">Konten bacaan ini akan disiarkan ke ponsel siswa saat Pemateri berpindah slide.</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showModalSlide', false)"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            {{-- Form Tambah/Edit Slide (Kiri) --}}
                            <div class="col-lg-5 border-end pe-lg-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    {{ $isEditSlide ? 'Edit Halaman Slide' : 'Tambah Slide Baru' }}
                                </h6>
                                <form wire:submit.prevent="saveSlide">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Judul / Topik Slide <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="slideJudul" class="form-control" placeholder="Contoh: Slide 1: Pengertian 5R di Tempat Kerja" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Konten / Naskah Bacaan Materi <span class="text-danger">*</span></label>
                                        <textarea wire:model="slideKonten" class="form-control font-monospace" rows="8" placeholder="Tuliskan naskah/teks bacaan lengkap yang ingin dibaca siswa di HP masing-masing..." required></textarea>
                                        <small class="text-muted" style="font-size: 0.72rem;">Siswa di lapangan akan membaca teks ini dengan jelas di layar HP mereka.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Poin Kunci / Sorotan Penting (Bullet Points)</label>
                                        <textarea wire:model="slidePoinKunci" class="form-control" rows="3" placeholder="Contoh:&#10;• Ringkas: Pisahkan yang perlu dan tidak&#10;• Rapi: Tempatkan barang pada tempatnya"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Catatan Presenter (Speaker Notes)</label>
                                        <textarea wire:model="slideCatatanPemateri" class="form-control bg-light" rows="2" placeholder="Catatan panduan berbicara bagi pemateri (hanya terlihat di layar presenter)..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($isEditSlide)
                                            <button type="button" wire:click="resetSlideForm" class="btn btn-outline-secondary btn-sm">Batal Edit</button>
                                        @endif
                                        <button type="submit" class="btn btn-primary btn-sm fw-bold flex-fill">
                                            <i class="bi bi-save me-1"></i> {{ $isEditSlide ? 'Perbarui Slide' : 'Tambah ke Urutan' }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Daftar Slide yang Ada (Kanan) --}}
                            <div class="col-lg-7 ps-lg-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-dark mb-0">
                                        Daftar Halaman Slide ({{ $activeSesiForSlide->slides->count() }})
                                    </h6>
                                    <span class="badge bg-light text-secondary border small">Urutan Otomatis</span>
                                </div>

                                <div class="overflow-auto" style="max-height: 550px;">
                                    @forelse($activeSesiForSlide->slides as $slide)
                                        <div class="card border rounded-3 mb-3 shadow-none {{ $slideId === $slide->id ? 'border-primary bg-primary bg-opacity-10' : 'bg-light' }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-primary rounded-pill px-2">Halaman {{ $slide->urutan }}</span>
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $slide->judul_slide }}</h6>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button wire:click="editSlide('{{ $slide->id }}')" class="btn btn-outline-primary btn-sm py-0 px-2" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button wire:click="deleteSlide('{{ $slide->id }}')" wire:confirm="Hapus slide ini?" class="btn btn-outline-danger btn-sm py-0 px-2" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <p class="small text-muted mb-2 line-clamp-3" style="white-space: pre-line;">
                                                    {{ $slide->konten }}
                                                </p>

                                                @if($slide->poin_kunci)
                                                    <div class="p-2 bg-white rounded-2 border mb-2 small text-dark">
                                                        <strong class="text-primary d-block mb-1"><i class="bi bi-stars me-1"></i>Poin Kunci:</strong>
                                                        <div style="white-space: pre-line;">{{ $slide->poin_kunci }}</div>
                                                    </div>
                                                @endif

                                                @if($slide->catatan_pemateri)
                                                    <div class="small text-muted fst-italic">
                                                        <i class="bi bi-info-circle me-1"></i>Speaker note: {{ $slide->catatan_pemateri }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-muted bg-white border rounded-3">
                                            <i class="bi bi-file-earmark-text fs-2 d-block mb-2"></i>
                                            Belum ada slide materi. Tambahkan slide melalui formulir di samping kiri.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 3: KELOLA SOAL EVALUASI & KUIS --}}
    @if($showModalSoal && $activeSesiForSoal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 bg-warning text-dark p-3">
                        <div>
                            <h5 class="modal-title fw-bold mb-0">
                                <i class="bi bi-patch-question-fill me-2"></i>Bank Soal Evaluasi: {{ $activeSesiForSoal->judul }}
                            </h5>
                            <small>Butir pertanyaan pilihan ganda dan esai pendek untuk menguji pemahaman siswa.</small>
                        </div>
                        <button type="button" class="btn-close" wire:click="$set('showModalSoal', false)"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            {{-- Form Tambah/Edit Soal (Kiri) --}}
                            <div class="col-lg-5 border-end pe-lg-4">
                                <h6 class="fw-bold text-dark mb-3">
                                    {{ $isEditSoal ? 'Edit Butir Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                                </h6>
                                <form wire:submit.prevent="saveSoal">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Jenis Pertanyaan <span class="text-danger">*</span></label>
                                        <select wire:model.live="jenisSoal" class="form-select" required>
                                            <option value="pilihan_ganda">Pilihan Ganda (A - E)</option>
                                            <option value="esai_pendek">Esai Pendek / Refleksi Tertulis</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Teks Pertanyaan <span class="text-danger">*</span></label>
                                        <textarea wire:model="pertanyaan" class="form-control" rows="4" placeholder="Tuliskan butir pertanyaan atau perintah refleksi materi..." required></textarea>
                                    </div>

                                    @if($jenisSoal === 'pilihan_ganda')
                                        <div class="row g-2 mb-3">
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text fw-bold">A</span>
                                                    <input type="text" wire:model="opsi_a" class="form-control" placeholder="Pilihan A" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text fw-bold">B</span>
                                                    <input type="text" wire:model="opsi_b" class="form-control" placeholder="Pilihan B" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text fw-bold">C</span>
                                                    <input type="text" wire:model="opsi_c" class="form-control" placeholder="Pilihan C">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text fw-bold">D</span>
                                                    <input type="text" wire:model="opsi_d" class="form-control" placeholder="Pilihan D">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text fw-bold">E</span>
                                                    <input type="text" wire:model="opsi_e" class="form-control" placeholder="Pilihan E">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Kunci Jawaban <span class="text-danger">*</span></label>
                                                <select wire:model="kunci_jawaban" class="form-select form-select-sm" required>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                    <option value="E">E</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Bobot Skor</label>
                                                <input type="number" wire:model="bobot_skor" class="form-control form-select-sm" min="1" max="100">
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Bobot Skor Esai</label>
                                            <input type="number" wire:model="bobot_skor" class="form-control form-select-sm" min="1" max="100">
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Pembahasan / Catatan Kunci</label>
                                        <textarea wire:model="pembahasan" class="form-control bg-light" rows="2" placeholder="Penjelasan atau kriteria jawaban benar..."></textarea>
                                    </div>

                                    <div class="d-flex gap-2">
                                        @if($isEditSoal)
                                            <button type="button" wire:click="resetSoalForm" class="btn btn-outline-secondary btn-sm">Batal Edit</button>
                                        @endif
                                        <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold flex-fill">
                                            <i class="bi bi-save me-1"></i> {{ $isEditSoal ? 'Perbarui Soal' : 'Simpan Pertanyaan' }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Daftar Soal yang Ada (Kanan) --}}
                            <div class="col-lg-7 ps-lg-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-dark mb-0">
                                        Daftar Butir Soal ({{ $activeSesiForSoal->soal->count() }})
                                    </h6>
                                </div>

                                <div class="overflow-auto" style="max-height: 550px;">
                                    @forelse($activeSesiForSoal->soal as $soal)
                                        <div class="card border rounded-3 mb-3 shadow-none bg-light">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-dark rounded-pill px-2">No. {{ $soal->urutan }}</span>
                                                        <span class="badge {{ $soal->jenis_soal === 'pilihan_ganda' ? 'bg-info text-dark' : 'bg-success text-white' }} small">
                                                            {{ $soal->jenis_soal === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Esai Pendek' }}
                                                        </span>
                                                        <span class="badge bg-secondary-subtle text-secondary small">Bobot: {{ $soal->bobot_skor }}</span>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button wire:click="editSoal('{{ $soal->id }}')" class="btn btn-outline-primary btn-sm py-0 px-2" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button wire:click="deleteSoal('{{ $soal->id }}')" wire:confirm="Hapus butir soal ini?" class="btn btn-outline-danger btn-sm py-0 px-2" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <p class="small text-dark fw-semibold mb-2">{{ $soal->pertanyaan }}</p>

                                                @if($soal->jenis_soal === 'pilihan_ganda')
                                                    <div class="row g-1 small text-muted mb-2">
                                                        <div class="col-6 {{ $soal->kunci_jawaban === 'A' ? 'fw-bold text-success' : '' }}">A. {{ $soal->opsi_a }}</div>
                                                        <div class="col-6 {{ $soal->kunci_jawaban === 'B' ? 'fw-bold text-success' : '' }}">B. {{ $soal->opsi_b }}</div>
                                                        @if($soal->opsi_c)<div class="col-6 {{ $soal->kunci_jawaban === 'C' ? 'fw-bold text-success' : '' }}">C. {{ $soal->opsi_c }}</div>@endif
                                                        @if($soal->opsi_d)<div class="col-6 {{ $soal->kunci_jawaban === 'D' ? 'fw-bold text-success' : '' }}">D. {{ $soal->opsi_d }}</div>@endif
                                                        @if($soal->opsi_e)<div class="col-6 {{ $soal->kunci_jawaban === 'E' ? 'fw-bold text-success' : '' }}">E. {{ $soal->opsi_e }}</div>@endif
                                                    </div>
                                                    <div class="small text-success fw-bold">
                                                        <i class="bi bi-check2-circle me-1"></i>Kunci: {{ $soal->kunci_jawaban }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-muted bg-white border rounded-3">
                                            <i class="bi bi-question-circle fs-2 d-block mb-2"></i>
                                            Belum ada soal evaluasi. Buat butir soal pilihan ganda atau esai di sebelah kiri.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL 4: REKAP KETERCAPAIAN & JAWABAN SISWA --}}
    @if($showModalRekap && $activeSesiForRekap)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 bg-success text-white p-3">
                        <div>
                            <h5 class="modal-title fw-bold mb-0">
                                <i class="bi bi-people-fill me-2"></i>Rekap Respon & Ketercapaian Siswa: {{ $activeSesiForRekap->judul }}
                            </h5>
                            <small class="text-white-50">Daftar kehadiran siswa di lapangan, skor kuis PG, dan rangkuman esai.</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showModalRekap', false)"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small fw-semibold text-muted mb-0">Filter Kelas:</label>
                                <select wire:model.live="filterRombel" class="form-select form-select-sm" style="min-width: 180px;">
                                    <option value="">Semua Rombel</option>
                                    @foreach($rombelList as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button wire:click="exportExcelRekap" class="btn btn-outline-success btn-sm fw-bold">
                                    <i class="bi bi-file-earmark-excel me-1"></i> Ekspor CSV / Excel
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-3 overflow-hidden">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Waktu Hadir</th>
                                        <th>Waktu Selesai</th>
                                        <th class="text-center">Skor PG</th>
                                        <th>Rangkuman Refleksi / Esai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapSiswa as $idx => $r)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $r->siswa->nama ?? '-' }}</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">NISN: {{ $r->siswa->nisn ?? '-' }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $r->rombel->nama_rombel ?? '-' }}</span></td>
                                            <td>{{ $r->waktu_gabung ? $r->waktu_gabung->format('H:i') : '-' }}</td>
                                            <td>
                                                @if($r->waktu_submit)
                                                    <span class="badge bg-success-subtle text-success">{{ $r->waktu_submit->format('H:i') }}</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Menyimak</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $r->skor_pg }}</span>
                                            </td>
                                            <td>
                                                @if($r->rangkuman_siswa)
                                                    <div class="text-muted line-clamp-2" title="{{ $r->rangkuman_siswa }}">
                                                        "{{ $r->rangkuman_siswa }}"
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                Belum ada data siswa yang merespon sesi ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
