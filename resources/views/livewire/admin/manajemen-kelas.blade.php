<div>
    <div class="page-header">
        <h1><i class="bi bi-door-open-fill me-2"></i>Manajemen Kelas</h1>
    </div>

    {{-- Import & Export Section --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-header bg-success bg-opacity-10">
            <h6 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Import & Export Data Kelas</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 48px;">
                    <i class="bi bi-download me-2"></i>Download Template (.xlsx)
                </button>
                <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export Excel (.xlsx)
                </button>
                <button wire:click="exportPdf" class="btn btn-outline-danger" style="min-height: 48px;">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF (Kop Surat)
                </button>
            </div>
            <div class="mb-3">
                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="form-control" style="min-height: 48px;">
            </div>
            <button wire:click="importData" class="btn btn-success w-100" style="min-height: 48px;" {{ !$importFile ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="importData"><i class="bi bi-cloud-upload me-2"></i>Import Kelas</span>
                <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-2"></span>Mengimport...</span>
            </button>
        </div>
    </div>

    {{-- PKL Quick Batch Settings --}}
    <div class="card mb-3 border-0 shadow-sm animate-fade-in-up" style="border-radius: 12px; background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-building-check fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Pengaturan Cepat Status PKL Siswa</h6>
                        <small class="text-muted">Tandai jurusan yang sedang melaksanakan PKL di Industri</small>
                    </div>
                </div>
                <button wire:click="resetAllPkl" wire:confirm="Yakin ingin mereset semua kelas PKL menjadi Reguler?" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua PKL
                </button>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                <span class="small fw-bold text-secondary me-1">Jurusan Cepat:</span>
                @foreach($jurusanList as $jurusan)
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" wire:click="setJurusanPkl('{{ $jurusan }}', true)" class="btn btn-outline-success fw-semibold" title="Set semua kelas {{ $jurusan }} menjadi PKL">
                            + {{ $jurusan }} PKL
                        </button>
                        <button type="button" wire:click="setJurusanPkl('{{ $jurusan }}', false)" class="btn btn-outline-secondary" title="Set {{ $jurusan }} ke Reguler">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-5">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama kelas (e.g. NKPI, TP)..." style="min-height: 48px;">
                </div>
                <div class="col-6 col-md-3">
                    <select wire:model.live="filterTingkat" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Tingkat</option>
                        <option value="10">Kelas 10 (X)</option>
                        <option value="11">Kelas 11 (XI)</option>
                        <option value="12">Kelas 12 (XII)</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <select wire:model.live="filterPkl" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Status (PKL & Reguler)</option>
                        <option value="1">🏢 Sedang PKL (Industri)</option>
                        <option value="0">🏫 Kelas Reguler (Aktif KBM)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Button --}}
    <button wire:click="create" class="btn btn-primary mb-3" style="min-height: 48px;">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
    </button>

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Kelas
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Nama Kelas</label>
                    <input type="text" wire:model="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" placeholder="Contoh: XII NKPI 1" style="min-height: 48px;">
                    @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Tingkat</label>
                    <select wire:model="tingkat" class="form-select @error('tingkat') is-invalid @enderror" style="min-height: 48px;">
                        <option value="10">10 (X)</option>
                        <option value="11">11 (XI)</option>
                        <option value="12">12 (XII)</option>
                    </select>
                    @error('tingkat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- PKL Toggle --}}
                <div class="card p-3 mb-3 bg-light border-0" style="border-radius: 10px;">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="isPklSwitch" wire:model="is_pkl" style="width: 2.5em; height: 1.3em; cursor: pointer;">
                        <label class="form-check-label fw-bold ms-2" for="isPklSwitch" style="cursor: pointer;">
                            <i class="bi bi-building-check text-success me-1"></i> Sedang Melaksanakan PKL (Praktik Kerja Lapangan)
                        </label>
                    </div>
                    <small class="text-muted d-block ms-4">
                        Jika diaktifkan, jadwal kelas ini otomatis berstatus PKL (Hijau). Guru pengajar tidak perlu handshake token OTP dan langsung dapat konfirmasi monitoring PKL.
                    </small>

                    @if($is_pkl)
                    <div class="mt-2 ms-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Keterangan PKL (Opsional)</label>
                        <input type="text" wire:model="pkl_keterangan" class="form-control form-control-sm" placeholder="Contoh: PKL Industri Kapal Penangkap Ikan">
                    </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="min-height: 48px;"><i class="bi bi-check me-1"></i> Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($confirmDelete)
    <div class="alert alert-danger animate-fade-in-up">
        <strong>Hapus kelas "{{ $deleteName }}"?</strong>
        <p class="small mb-2">Data siswa dan jadwal pelajaran di kelas ini juga akan dihapus (cascade).</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteRombel" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Rombel List --}}
    <div class="card animate-fade-in-up shadow-sm">
        <div class="card-body p-0">
            @forelse($rombels as $rombel)
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-wrap gap-2 {{ $rombel->is_pkl ? 'bg-success bg-opacity-10' : '' }}">
                <div class="d-flex align-items-center gap-3 flex-fill min-w-0">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0 {{ $rombel->is_pkl ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width: 44px; height: 44px;">
                        <i class="bi {{ $rombel->is_pkl ? 'bi-building-check' : 'bi-door-closed' }} fs-5"></i>
                    </div>
                    <div class="min-w-0 flex-fill">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-bold fs-6 text-dark">{{ $rombel->nama_kelas }}</span>
                            @if($rombel->is_pkl)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-building-check me-1"></i>PKL (Industri)
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">
                            Tingkat: <span class="badge bg-secondary">{{ $rombel->tingkat_label }}</span>
                            &bull; Siswa: <span class="badge bg-info text-dark">{{ $rombel->siswa_count }} siswa</span>
                            @if($rombel->is_pkl && $rombel->pkl_keterangan)
                                &bull; <span class="text-success"><i class="bi bi-info-circle me-1"></i>{{ $rombel->pkl_keterangan }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- PKL Quick Switch & Actions --}}
                <div class="d-flex align-items-center gap-2">
                    <button wire:click="togglePkl('{{ $rombel->id }}')" class="btn btn-sm {{ $rombel->is_pkl ? 'btn-success text-white' : 'btn-outline-secondary' }} px-3 py-2 fw-semibold" style="border-radius: 8px; min-height: 42px;" title="{{ $rombel->is_pkl ? 'Klik untuk nonaktifkan PKL' : 'Klik untuk jadikan kelas PKL' }}">
                        <i class="bi {{ $rombel->is_pkl ? 'bi-toggle-on fs-5' : 'bi-toggle-off fs-5' }} me-1 align-middle"></i>
                        <span>{{ $rombel->is_pkl ? 'PKL Aktif' : 'Set PKL' }}</span>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light border btn-sm" data-bs-toggle="dropdown" style="min-height: 42px; min-width: 42px; border-radius: 8px;">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><button class="dropdown-item py-2" wire:click="edit('{{ $rombel->id }}')"><i class="bi bi-pencil me-2 text-primary"></i>Edit Kelas</button></li>
                            <li><button class="dropdown-item py-2" wire:click="togglePkl('{{ $rombel->id }}')"><i class="bi bi-building-check me-2 text-success"></i>{{ $rombel->is_pkl ? 'Ubah ke Reguler' : 'Ubah ke PKL' }}</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteRombel('{{ $rombel->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                        </ul>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data kelas yang sesuai filter.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $rombels->links() }}</div>
</div>
