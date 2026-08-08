<div>
    <div class="page-header">
        <h1><i class="bi bi-list-check me-2"></i>Manajemen TP/KKTP</h1>
        <p class="subtitle mb-0">Kelola Tujuan Pembelajaran per mata pelajaran</p>
    </div>

    {{-- Mapel Selector --}}
    <div class="card mb-3">
        <div class="card-body p-3">
            <label class="form-label small">Mata Pelajaran</label>
            <select wire:model.live="selectedMapel" class="form-select">
                <option value="">— Pilih Mata Pelajaran —</option>
                @foreach($mapelList as $m)
                <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($selectedMapel)
    {{-- Action Buttons --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <button wire:click="create" class="btn btn-primary" style="min-height: 48px;">
            <i class="bi bi-plus-circle me-1"></i>Tambah TP
        </button>
        <button wire:click="exportExcel" class="btn btn-outline-success" style="min-height: 48px;">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </button>
    </div>

    {{-- Form --}}
    @if($showForm)
    <div class="card mb-3 border-primary animate-fade-in-up">
        <div class="card-header bg-primary text-white">{{ $editing ? 'Edit' : 'Tambah' }} TP</div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label small">Kode TP</label>
                        <input type="text" wire:model="kodeTp" class="form-control @error('kodeTp') is-invalid @enderror">
                        @error('kodeTp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-4">
                        <label class="form-label small">Urutan</label>
                        <input type="number" wire:model="orderSequence" class="form-control" min="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Deskripsi TP</label>
                    <textarea wire:model="deskripsiTp" class="form-control @error('deskripsiTp') is-invalid @enderror" rows="3"></textarea>
                    @error('deskripsiTp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i> Simpan</button>
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-outline-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- TP List --}}
    <div class="card">
        <div class="card-header">TP terdaftar ({{ $tpList->count() }})</div>
        <div class="card-body p-0">
            @forelse($tpList as $tp)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="bg-primary bg-opacity-10 rounded-3 text-center fw-bold text-primary px-2 py-1" style="min-width: 56px; font-size: 0.8rem;">
                    {{ $tp->kode_tp }}
                </div>
                <div class="flex-fill small">{{ $tp->deskripsi_tp }}</div>
                <div class="d-flex gap-1">
                    <button wire:click="edit('{{ $tp->id }}')" class="btn btn-outline-primary btn-sm" style="min-height: 36px;"><i class="bi bi-pencil"></i></button>
                    <button wire:click="deletetp('{{ $tp->id }}')" wire:confirm="Hapus {{ $tp->kode_tp }}?" class="btn btn-outline-danger btn-sm" style="min-height: 36px;"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            @empty
            <div class="p-3 text-center text-muted small">Belum ada TP. Klik "Tambah TP" untuk menambahkan.</div>
            @endforelse
        </div>
    </div>
    @endif
</div>
