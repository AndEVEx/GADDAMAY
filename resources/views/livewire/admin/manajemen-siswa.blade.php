<div>
    <div class="page-header">
        <h1><i class="bi bi-person-badge-fill me-2"></i>Manajemen Siswa</h1>
    </div>

    {{-- Import Section --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-header bg-success bg-opacity-10">
            <h6 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Import Data Siswa (.xlsx / .csv)</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 48px;">
                    <i class="bi bi-download me-2"></i>Download Template (.xlsx)
                </button>
                <a href="{{ route('admin.import-siswa') }}" class="btn btn-outline-primary" style="min-height: 48px; display: inline-flex; align-items: center;" wire:navigate>
                    <i class="bi bi-file-earmark-excel me-2"></i>Halaman Import Khusus
                </a>
            </div>
            <div class="mb-3">
                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="form-control" style="min-height: 48px;">
                <div class="form-text">Pastikan nama kelas di file Excel sesuai dengan nama kelas yang sudah terdaftar di sistem.</div>
            </div>
            <button wire:click="importData" class="btn btn-success w-100" style="min-height: 48px;" wire:loading.attr="disabled" {{ !$importFile ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="importData"><i class="bi bi-cloud-upload me-2"></i>Import Data Siswa</span>
                <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-2"></span>Mengimport...</span>
            </button>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Cari nama atau NIS siswa..." style="min-height: 48px;">
                    </div>
                </div>
                <div class="col-md-5">
                    <select wire:model.live="filterRombel" class="form-select fw-semibold" style="min-height: 48px;">
                        <option value="">-- Tampilkan Semua Kelas (Total: {{ $rombels->sum('siswa_count') }} Siswa) --</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }} ({{ $rombel->siswa_count }} Siswa)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($selectedRombel)
            <div class="alert alert-info py-2 px-3 mt-3 mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-radius: 8px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-door-open-fill fs-5 text-primary"></i>
                    <div>
                        <span class="fw-bold">Kelas: {{ $selectedRombel->nama_kelas }}</span>
                        <span class="badge bg-primary ms-1">{{ $selectedRombel->siswa_count }} Siswa</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="openSwapModal" class="btn btn-warning btn-sm text-dark fw-bold">
                        <i class="bi bi-arrow-left-right me-1"></i> Tukar Siswa Kelas Ini
                    </button>
                    <button wire:click="$set('filterRombel', '')" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i> Reset Filter
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center justify-content-between">
        <div class="d-flex gap-2 flex-wrap">
            <button wire:click="create" class="btn btn-primary" style="min-height: 48px;">
                <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
            </button>
            <button wire:click="openSwapModal" class="btn btn-warning text-dark fw-bold" style="min-height: 48px;">
                <i class="bi bi-arrow-left-right me-1"></i> Tukar Siswa Antar Rombel
            </button>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </button>
            <button wire:click="exportPdf" class="btn btn-outline-danger" style="min-height: 48px;">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF (Kop Surat)
            </button>
        </div>
    </div>

    {{-- Modal Swap Siswa Antar Rombel --}}
    @if($showSwapModal)
    <div class="card mb-3 border-warning shadow animate-fade-in-up" style="border-radius: 12px;">
        <div class="card-header bg-warning text-dark d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2"></i>Tukar Seluruh Data Siswa Antar Dua Rombel</h6>
            <button type="button" wire:click="closeSwapModal" class="btn-close"></button>
        </div>
        <div class="card-body">
            <div class="alert alert-light border small mb-3">
                <i class="bi bi-info-circle-fill text-primary me-1"></i>
                Fitur ini berguna ketika jadwal pelajaran sudah benar, namun seluruh daftar muridnya tertukar antara dua kelas (misal tertukar antara <strong>Rombel 1</strong> dan <strong>Rombel 2</strong>). Seluruh siswa di Kelas A akan dipindahkan ke Kelas B, dan sebaliknya, secara atomic & aman.
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pilih Kelas A</label>
                    <select wire:model.live="swapRombelA" class="form-select @error('swapRombelA') is-invalid @enderror" style="min-height: 48px;">
                        <option value="">-- Pilih Rombel A --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_kelas }} ({{ $r->siswa_count }} Siswa)</option>
                        @endforeach
                    </select>
                    @error('swapRombelA') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pilih Kelas B (Tujuan Tukar)</label>
                    <select wire:model.live="swapRombelB" class="form-select @error('swapRombelB') is-invalid @enderror" style="min-height: 48px;">
                        <option value="">-- Pilih Rombel B --</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}">{{ $r->nama_kelas }} ({{ $r->siswa_count }} Siswa)</option>
                        @endforeach
                    </select>
                    @error('swapRombelB') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            @if($swapRombelA && $swapRombelB && $swapRombelA !== $swapRombelB)
                @php
                    $rA = $rombels->firstWhere('id', $swapRombelA);
                    $rB = $rombels->firstWhere('id', $swapRombelB);
                @endphp
                @if($rA && $rB)
                <div class="alert alert-warning py-2 mb-3 small d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Akan menukar: <strong>{{ $rA->nama_kelas }}</strong> ({{ $rA->siswa_count }} siswa) $\leftrightarrow$ <strong>{{ $rB->nama_kelas }}</strong> ({{ $rB->siswa_count }} siswa).
                    </span>
                </div>
                @endif
            @endif

            <div class="d-flex gap-2">
                <button type="button" wire:click="swapSiswaRombel" wire:confirm="Yakin ingin menukar seluruh siswa antara kedua kelas yang dipilih?" class="btn btn-warning text-dark fw-bold flex-fill" style="min-height: 48px;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="swapSiswaRombel"><i class="bi bi-arrow-left-right me-1"></i> Eksekusi Tukar Siswa</span>
                    <span wire:loading wire:target="swapSiswaRombel"><span class="spinner-border spinner-border-sm me-1"></span>Memproses penukaran...</span>
                </button>
                <button type="button" wire:click="closeSwapModal" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Siswa
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Nama Lengkap</label>
                    <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama siswa" style="min-height: 48px;">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">NIS (Nomor Induk Siswa)</label>
                    <input type="text" wire:model="nis" class="form-control @error('nis') is-invalid @enderror" placeholder="Nomor NIS (opsional)" style="min-height: 48px;">
                    @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Kelas / Rombel</label>
                    <select wire:model="rombel_id" class="form-select @error('rombel_id') is-invalid @enderror" style="min-height: 48px;">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }} ({{ $rombel->tingkat_label }})</option>
                        @endforeach
                    </select>
                    @error('rombel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        <strong>Hapus data siswa "{{ $deleteName }}"?</strong>
        <p class="small mb-2">Riwayat kehadiran murid ini juga akan dihapus.</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteSiswa" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Siswa List --}}
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($siswas as $siswa)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="fw-bold fs-6">{{ $siswa->nama }}</div>
                    <div class="text-muted small">
                        NIS: {{ $siswa->nis ?? '-' }}
                        &bull; Kelas: <span class="badge bg-primary bg-opacity-10 text-primary">{{ $siswa->rombel->nama_kelas ?? '-' }}</span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $siswa->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteSiswa('{{ $siswa->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data siswa.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $siswas->links() }}</div>
</div>
