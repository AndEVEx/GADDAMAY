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
            <button wire:click="importData" class="btn btn-success w-100" style="min-height: 48px;" {{ !$importFile ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="importData"><i class="bi bi-cloud-upload me-2"></i>Import Data Siswa</span>
                <span wire:loading wire:target="importData"><span class="spinner-border spinner-border-sm me-2"></span>Mengimport...</span>
            </button>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-8">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama atau NIS..." style="min-height: 48px;">
                </div>
                <div class="col-4">
                    <select wire:model.live="filterRombel" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Kelas</option>
                        @foreach($rombels as $rombel)
                            <option value="{{ $rombel->id }}">{{ $rombel->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <button wire:click="create" class="btn btn-primary" style="min-height: 48px;">
            <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
        </button>
        <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </button>
    </div>

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
