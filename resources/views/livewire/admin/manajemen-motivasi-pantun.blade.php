<div>
    <div class="page-header">
        <h1><i class="bi bi-chat-quote-fill me-2"></i>Manajemen Motivasi & Pantun</h1>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari isi teks motivasi / pantun..." style="min-height: 48px;">
                </div>
                <div class="col-6 col-md-3">
                    <select wire:model.live="filterTipe" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Tipe</option>
                        <option value="pantun">Pantun</option>
                        <option value="kata_mutiara">Kata Mutiara</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select wire:model.live="filterKategori" class="form-select" style="min-height: 48px;">
                        <option value="">Semua Kategori</option>
                        <option value="sebelum_mengajar">Sebelum Mengajar</option>
                        <option value="siap_mengajar">Siap Mengajar</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Button --}}
    <button wire:click="create" class="btn btn-primary mb-3" style="min-height: 48px;">
        <i class="bi bi-plus-circle me-1"></i> Tambah Motivasi / Pantun
    </button>

    {{-- Form Modal / Card --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-{{ $editing ? 'pencil' : 'plus-circle' }} me-2"></i>{{ $editing ? 'Edit' : 'Tambah' }} Motivasi / Pantun
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Isi Teks Motivasi / Pantun</label>
                    <textarea wire:model="isi" class="form-control @error('isi') is-invalid @enderror" rows="4" placeholder="Tuliskan bait pantun atau kalimat kata mutiara..." style="min-height: 100px;"></textarea>
                    @error('isi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Tipe</label>
                        <select wire:model="tipe" class="form-select @error('tipe') is-invalid @enderror" style="min-height: 48px;">
                            <option value="pantun">Pantun</option>
                            <option value="kata_mutiara">Kata Mutiara</option>
                        </select>
                        @error('tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Kategori Prompt</label>
                        <select wire:model="kategori" class="form-select @error('kategori') is-invalid @enderror" style="min-height: 48px;">
                            <option value="sebelum_mengajar">Sebelum Mengajar</option>
                            <option value="siap_mengajar">Siap Mengajar</option>
                        </select>
                        @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
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
        <strong>Hapus motivasi/pantun "{{ $deleteContent }}"?</strong>
        <p class="small mb-2">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="d-flex gap-2">
            <button wire:click="deleteMotivasi" class="btn btn-danger" style="min-height: 48px;"><i class="bi bi-trash me-1"></i> Hapus</button>
            <button wire:click="$set('confirmDelete', false)" class="btn btn-outline-secondary" style="min-height: 48px;">Batal</button>
        </div>
    </div>
    @endif

    {{-- Motivasi List --}}
    <div class="card animate-fade-in-up">
        <div class="card-body p-0">
            @forelse($items as $item)
            <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                <div class="flex-fill">
                    <div class="fst-italic text-dark mb-2" style="white-space: pre-line;">"{!! e($item->isi) !!}"</div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-{{ $item->tipe === 'pantun' ? 'info' : 'warning' }} text-dark" style="font-size: 0.75rem;">
                            {{ $item->tipe === 'pantun' ? 'Pantun' : 'Kata Mutiara' }}
                        </span>
                        <span class="badge bg-secondary bg-opacity-20 text-dark" style="font-size: 0.75rem;">
                            {{ $item->kategori === 'sebelum_mengajar' ? 'Sebelum Mengajar' : 'Siap Mengajar' }}
                        </span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown" style="min-height: 48px; min-width: 48px;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item py-2" wire:click="edit('{{ $item->id }}')"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger py-2" wire:click="confirmDeleteMotivasi('{{ $item->id }}')"><i class="bi bi-trash me-2"></i>Hapus</button></li>
                    </ul>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada data motivasi atau pantun.
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $items->links() }}</div>
</div>
