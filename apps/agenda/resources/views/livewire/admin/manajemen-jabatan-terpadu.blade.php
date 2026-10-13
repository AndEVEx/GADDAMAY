<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    {{-- Header Banner Manajemen Jabatan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #1e3a8a, #0369a1, #0f766e);">
        <div class="card-body p-4 position-relative">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill shadow-sm">
                            <i class="bi bi-person-workspace me-1"></i>TATA KELOLA &amp; DELEGASI PERAN
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white px-2 py-1 rounded-pill small">
                            SMKN 2 Indramayu
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">Manajemen Jabatan Guru &amp; Siswa</h3>
                    <p class="mb-0 text-white-50 small" style="max-width: 720px;">
                        Pusat penugasan resmi panitia, pengurus, dan delegasi penanggung jawab (PIC) menu modul aplikasi. Dilengkapi blanko Excel resmi dan matriks acuan tanggung jawab fitur.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button wire:click="downloadTemplateExcel" class="btn btn-light btn-sm fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                        <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Blanko Excel
                    </button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalImportExcel" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 text-nowrap">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Import Excel
                    </button>
                    <button wire:click="create('{{ $activeTab === 'siswa' ? 'siswa' : 'guru' }}')" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Penugasan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigasi: Jabatan Guru vs Jabatan Siswa vs Kamus Master --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2 p-md-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto no-scrollbar py-1">
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'guru')" class="nav-link {{ $activeTab === 'guru' ? 'active bg-primary fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                            <i class="bi bi-person-video3 me-1"></i> Jabatan Guru / Pendidik
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'siswa')" class="nav-link {{ $activeTab === 'siswa' ? 'active bg-teal fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small" style="{{ $activeTab === 'siswa' ? 'background: #0d9488 !important;' : '' }}">
                            <i class="bi bi-mortarboard-fill me-1"></i> Jabatan Siswa / Taruna
                        </button>
                    </li>
                    <li class="nav-item">
                        <button wire:click="$set('activeTab', 'kamus_jabatan')" class="nav-link {{ $activeTab === 'kamus_jabatan' ? 'active bg-dark fw-bold text-white' : 'bg-light text-dark' }} rounded-pill px-3 px-md-4 py-2 text-nowrap small">
                            <i class="bi bi-diagram-3-fill me-1"></i> Kamus Master &amp; Acuan Fitur Baru
                        </button>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <select wire:model.live="filterTahun" class="form-select form-select-sm rounded-pill bg-light border-0">
                        <option value="2026/2027">Tahun Ajaran 2026/2027</option>
                        <option value="2025/2026">Tahun Ajaran 2025/2026</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 1 & 2: DAFTAR PENUGASAN JABATAN (GURU ATAU SISWA) --}}
    @if(in_array($activeTab, ['guru', 'siswa']))
        {{-- Filter & Pencarian --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
            <div class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Cari personil {{ $activeTab === 'guru' ? 'guru...' : 'siswa / NIS...' }}">
                    </div>
                </div>
                <div class="col-md-5">
                    <select wire:model.live="filterJabatan" class="form-select bg-light border-0">
                        <option value="">Semua Jabatan</option>
                        @foreach($kamusList->where('kategori', $activeTab) as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Tabel Penugasan --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-3">Personil Terdaftar</th>
                            <th>Jabatan &amp; Peran</th>
                            <th>Rombel / Unit</th>
                            <th>Delegasi Menu Modul</th>
                            <th>SK Penugasan</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penugasanList as $item)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle p-1 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi {{ $activeTab === 'guru' ? 'bi-person-fill' : 'bi-mortarboard' }} fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $item->nama_pejabat }}</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $item->identitas_pejabat }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-bold small">
                                        {{ $item->masterJabatan->nama_jabatan ?? '-' }}
                                    </span>
                                    <div class="text-muted" style="font-size: 0.70rem;">{{ $item->masterJabatan->kode_jabatan ?? '' }}</div>
                                </td>
                                <td>
                                    @if($item->rombel)
                                        <span class="badge bg-light text-secondary border small">{{ $item->rombel->nama_rombel }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->masterJabatan && $item->masterJabatan->delegasi_modul)
                                        @foreach(explode(',', $item->masterJabatan->delegasi_modul) as $mod)
                                            <span class="badge bg-secondary-subtle text-secondary small me-1 mb-1" style="font-size: 0.68rem;">{{ trim($mod) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $item->nomor_sk ?? 'SK.800/01/2026' }}</div>
                                    <div class="text-muted" style="font-size: 0.70rem;">TA: {{ $item->tahun_ajaran }}</div>
                                </td>
                                <td class="text-center">
                                    <button type="button" wire:click="toggleStatus('{{ $item->id }}')" class="btn btn-sm rounded-pill px-2.5 py-0.5 border-0 {{ $item->status_aktif ? 'bg-success text-white' : 'bg-secondary text-white' }}" style="font-size: 0.70rem;">
                                        {{ $item->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                    </button>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="edit('{{ $item->id }}')" class="btn btn-outline-secondary rounded-circle p-1 me-1" style="width: 32px; height: 32px;" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="delete('{{ $item->id }}')" onclick="confirm('Hapus penugasan ini?') || event.stopImmediatePropagation()" class="btn btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted small">
                                    <i class="bi bi-inbox display-4 d-block mb-2 text-muted"></i>
                                    Belum ada data penugasan jabatan untuk kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $penugasanList->links() }}
            </div>
        </div>
    @endif

    {{-- TAB 3: KAMUS MASTER JABATAN & ACUAN FITUR BARU --}}
    @if($activeTab === 'kamus_jabatan')
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-3 p-3 text-white bg-dark">
                    <i class="bi bi-diagram-3-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Matriks Tata Kelola Jabatan &amp; Acuan Fitur Baru</h5>
                    <p class="text-muted small mb-0">
                        Standar arsitektur tanggung jawab modul (Role &amp; Duty Delegation Framework) SMKN 2 Indramayu. Setiap fitur aplikasi yang aktif maupun fitur baru yang akan datang wajib mengacu ke matriks ini untuk menentukan Penanggung Jawab Pendidik (PIC) dan Delegasi Siswa pelaksana.
                    </p>
                </div>
            </div>

            {{-- Tabel Kamus Jabatan --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr class="small text-uppercase">
                            <th>Kode Jabatan</th>
                            <th>Nama Jabatan Resmi</th>
                            <th>Kategori</th>
                            <th>Tanggung Jawab &amp; Delegasi Menu Modul</th>
                            <th>Hak Akses</th>
                            <th class="text-center">Jumlah Pejabat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kamusList as $kj)
                            <tr>
                                <td class="font-monospace fw-bold text-primary small">{{ $kj->kode_jabatan }}</td>
                                <td class="fw-bold text-dark small">{{ $kj->nama_jabatan }}</td>
                                <td>
                                    <span class="badge {{ $kj->kategori === 'guru' ? 'bg-primary' : 'bg-teal' }} rounded-pill px-2.5 py-1 small" style="{{ $kj->kategori === 'siswa' ? 'background: #0d9488;' : '' }}">
                                        {{ strtoupper($kj->kategori) }}
                                    </span>
                                </td>
                                <td>
                                    <p class="mb-1 text-muted small" style="font-size: 0.75rem;">{{ $kj->deskripsi_tugas }}</p>
                                    @if($kj->delegasi_modul)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(explode(',', $kj->delegasi_modul) as $m)
                                                <span class="badge bg-light text-dark border small" style="font-size: 0.65rem;">
                                                    <i class="bi bi-link-45deg me-0.5"></i>{{ trim($m) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary small text-uppercase">{{ $kj->hak_akses }}</span>
                                </td>
                                <td class="text-center fw-bold text-dark">{{ $kj->penugasan_count }} Orang</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- MODAL TAMBAH / EDIT PENUGASAN JABATAN --}}
    @if($showForm)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white p-3">
                        <h6 class="modal-title fw-bold mb-0">
                            <i class="bi bi-person-plus-fill me-1"></i> {{ $editing ? 'Edit Penugasan Jabatan' : 'Input Penugasan Jabatan Baru' }}
                        </h6>
                        <button type="button" wire:click="$set('showForm', false)" class="btn-close btn-close-white"></button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                {{-- Pilihan Tipe Subjek --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Tipe Personil <span class="text-danger">*</span></label>
                                    <select wire:model.live="tipeSubjek" class="form-select">
                                        <option value="guru">Pendidik / Tenaga Kependidikan (Guru)</option>
                                        <option value="siswa">Peserta Didik (Siswa / Taruna)</option>
                                    </select>
                                </div>

                                {{-- Pilihan Jabatan dari Kamus Master --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Pilih Jabatan Resmi <span class="text-danger">*</span></label>
                                    <select wire:model="masterJabatanId" class="form-select @error('masterJabatanId') is-invalid @enderror">
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach($masterOptions as $opt)
                                            <option value="{{ $opt->id }}">{{ $opt->nama_jabatan }} ({{ $opt->kode_jabatan }})</option>
                                        @endforeach
                                    </select>
                                    @error('masterJabatanId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Pilih Personil Guru atau Siswa --}}
                                @if($tipeSubjek === 'guru')
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark">Pilih Guru / PTK <span class="text-danger">*</span></label>
                                        <select wire:model="userId" class="form-select @error('userId') is-invalid @enderror">
                                            <option value="">-- Pilih Guru / User --</option>
                                            @foreach($guruOptions as $g)
                                                <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                                            @endforeach
                                        </select>
                                        @error('userId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @else
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark">Pilih Siswa / Taruna <span class="text-danger">*</span></label>
                                        <select wire:model="siswaId" class="form-select @error('siswaId') is-invalid @enderror">
                                            <option value="">-- Pilih Siswa --</option>
                                            @foreach($siswaOptions as $s)
                                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->rombel->nama_rombel ?? '-' }} - NIS: {{ $s->nis }})</option>
                                            @endforeach
                                        </select>
                                        @error('siswaId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                @endif

                                {{-- Rombel Binaan (Jika Ada) --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Rombel / Kelas Binaan (Opsional)</label>
                                    <select wire:model="rombelId" class="form-select">
                                        <option value="">-- Tidak Terikat Rombel Tertentu --</option>
                                        @foreach($rombelOptions as $r)
                                            <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Tahun Ajaran & SK --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="tahunAjaran" class="form-control" placeholder="2026/2027">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Nomor Surat Keputusan (SK)</label>
                                    <input type="text" wire:model="nomorSk" class="form-control" placeholder="misal: SK.800/015/SMKN2/2026">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Status Keaktifan</label>
                                    <select wire:model="statusAktif" class="form-select">
                                        <option value="1">Aktif Bertugas</option>
                                        <option value="0">Selesai / Non-Aktif</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark">Keterangan Penugasan</label>
                                    <input type="text" wire:model="keterangan" class="form-control" placeholder="Catatan tambahan mengenai ruang lingkup penugasan">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer p-3 bg-light">
                            <button type="button" wire:click="$set('showForm', false)" class="btn btn-secondary btn-sm rounded-pill px-3">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-pill px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Penugasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL IMPORT EXCEL --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-dark text-white p-3">
                    <h6 class="modal-title fw-bold mb-0">
                        <i class="bi bi-cloud-arrow-up-fill me-1 text-success"></i> Import Penugasan Jabatan dari Excel
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form wire:submit.prevent="importExcel">
                    <div class="modal-body p-4">
                        <div class="alert alert-info small rounded-3 mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i> Gunakan file template resmi <strong>Blanko_Penugasan_Jabatan_SMKN2.xlsx</strong>. Sistem akan mencocokkan Guru via Email dan Siswa via NIS secara otomatis.
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Pilih Berkas Excel (.xlsx, .xls)</label>
                            <input type="file" wire:model="importFile" class="form-control @error('importFile') is-invalid @enderror" accept=".xlsx,.xls">
                            @error('importFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div wire:loading wire:target="importFile" class="text-primary small mb-2">
                            <span class="spinner-border spinner-border-sm me-1"></span> Mengunggah berkas Excel...
                        </div>
                    </div>

                    <div class="modal-footer p-3 bg-light">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">
                            <i class="bi bi-upload me-1"></i> Proses Import Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
