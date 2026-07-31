<div>
    <div class="page-header">
        <h1><i class="bi bi-book-fill me-2"></i>Manajemen Mata Pelajaran</h1>
    </div>

    {{-- Search --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama atau kode mata pelajaran..." style="min-height: 48px;">
        </div>
    </div>

    {{-- Add Button --}}
    <button wire:click="create" class="btn btn-primary mb-3" style="min-height: 48px;">
        <i class="bi bi-plus-circle me-1"></i> Tambah Mata Pelajaran
    </button>

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Mata Pelajaran
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Nama Mata Pelajaran</label>
                    <input type="text" wire:model="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror" placeholder="Contoh: Pemrograman Web dan Perangkat Bergerak" style="min-height: 48px;">
                    @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Kode Mapel</label>
                    <input type="text" wire:model="kode_mapel" class="form-control @error('kode_mapel') is-invalid @enderror" placeholder="Contoh: PWPB-10" style="min-height: 48px;">
                    @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        <strong>Hapus mata pelajaran "{{ $deleteName }}"?</strong>
        <p class="small mb-2">Tujuan Pembelajaran dan Jadwal Pelajaran terkait mapel ini juga akan dihapus (cascade).</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteMapel" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Mapel List --}}
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($mapels as $mapel)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="fw-bold fs-6">{{ $mapel->nama_mapel }}</div>
                    <div class="text-muted small">
                        Kode: <span class="badge bg-primary bg-opacity-10 text-primary">{{ $mapel->kode_mapel ?? '-' }}</span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $mapel->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteMapel('{{ $mapel->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data mata pelajaran.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $mapels->links() }}</div>
</div>
