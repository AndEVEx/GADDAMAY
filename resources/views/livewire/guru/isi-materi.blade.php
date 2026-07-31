<div>
    <div class="page-header">
        <h1><i class="bi bi-pencil-square me-2"></i>Isi Materi</h1>
        <p class="subtitle mb-0">
            {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} — {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
        </p>
    </div>

    <form wire:submit="simpan">
        {{-- Materi --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-book me-2"></i>Materi yang Diajarkan</div>
            <div class="card-body">
                <textarea wire:model="materi" class="form-control @error('materi') is-invalid @enderror"
                          rows="3" placeholder="Tuliskan materi yang diajarkan hari ini..."></textarea>
                @error('materi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Tujuan Pembelajaran (TP/KKTP) --}}
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-list-check me-2"></i>Tujuan Pembelajaran (TP/KKTP)
                <span class="badge bg-primary ms-2">{{ count($selectedTp) }} dipilih</span>
            </div>
            <div class="card-body p-0">
                @forelse($tpList as $tp)
                <label class="d-flex align-items-start gap-3 p-3 border-bottom cursor-pointer {{ $tp->is_taught ? 'bg-light' : '' }}"
                       style="min-height: 48px; cursor: pointer;">
                    <input type="checkbox" wire:model="selectedTp" value="{{ $tp->id }}"
                           class="form-check-input mt-1" style="min-width: 20px; min-height: 20px;">
                    <div class="flex-fill">
                        <div class="fw-bold small">{{ $tp->kode_tp }}</div>
                        <div class="small text-muted">{{ $tp->deskripsi_tp }}</div>
                        @if($tp->is_taught)
                            <span class="badge bg-success bg-opacity-10 text-success mt-1" style="font-size: 0.65rem;">
                                <i class="bi bi-check"></i> Sudah diajarkan
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning mt-1" style="font-size: 0.65rem;">
                                <i class="bi bi-clock"></i> Belum diajarkan
                            </span>
                        @endif
                    </div>
                </label>
                @empty
                <div class="p-3 text-center text-muted small">
                    <i class="bi bi-info-circle"></i> Belum ada TP untuk mapel ini. Hubungi Ketua MGMP.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary btn-lg w-100" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="bi bi-arrow-right me-2"></i>Simpan & Lanjut ke Kehadiran</span>
            <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
        </button>
    </form>

    <div class="mt-3">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary" wire:navigate>
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>
