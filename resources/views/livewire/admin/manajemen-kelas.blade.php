<div>
    <div class="page-header">
        <h1><i class="bi bi-door-open-fill me-2"></i>Manajemen Kelas</h1>
    </div>

    {{-- Import Section --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-header bg-success bg-opacity-10">
            <h6 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Import Data Kelas</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button wire:click="downloadTemplate" class="btn btn-outline-info" style="min-height: 48px;">
                    <i class="bi bi-download me-2"></i>Download Template (.xlsx)
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

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-8">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama kelas..." style="min-height: 48px;">
                </div>
                <div class="col-4">
                    <select wire:model.live="filterTingkat" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Tingkat</option>
                        <option value="10">Kelas 10 (X)</option>
                        <option value="11">Kelas 11 (XI)</option>
                        <option value="12">Kelas 12 (XII)</option>
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
                    <input type="text" wire:model="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" placeholder="Contoh: X PPLG 1" style="min-height: 48px;">
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
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($rombels as $rombel)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="fw-bold fs-6">{{ $rombel->nama_kelas }}</div>
                    <div class="text-muted small">
                        Tingkat: <span class="badge bg-secondary">{{ $rombel->tingkat_label }}</span>
                        &bull; Siswa: <span class="badge bg-info text-dark">{{ $rombel->siswa_count }} siswa</span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $rombel->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteRombel('{{ $rombel->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data kelas.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $rombels->links() }}</div>
</div>
