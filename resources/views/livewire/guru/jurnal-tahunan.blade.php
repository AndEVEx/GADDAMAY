<div>
    <div class="page-header">
        <h1><i class="bi bi-journal-text me-2"></i>Jurnal Mengajar</h1>
    </div>

    {{-- Month Picker --}}
    <div class="card mb-3">
        <div class="card-body p-3">
            <label class="form-label small">Bulan</label>
            <input type="month" wire:model.live="bulan" class="form-control">
        </div>
    </div>

    {{-- Rombel List --}}
    @forelse($rombels as $rombel)
    <a href="{{ route('guru.jurnal-kelas', $rombel->id) }}" class="card mb-2 text-decoration-none" wire:navigate>
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-bold">{{ $rombel->nama_kelas }}</div>
                <div class="small text-muted">{{ $rombel->total_jadwal_count }} jadwal/minggu</div>
            </div>
            <div class="text-end">
                <div class="fw-bold text-success">{{ $rombel->agenda_selesai_count }}</div>
                <div class="small text-muted">selesai</div>
            </div>
        </div>
    </a>
    @empty
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x" style="font-size: 2rem;"></i>
            <p class="mt-2 mb-0">Belum ada jadwal mengajar.</p>
        </div>
    </div>
    @endforelse
</div>
