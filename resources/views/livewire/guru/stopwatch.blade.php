<div x-data="{
    elapsed: 0,
    interval: null,
    running: true,
    startTime: Date.now(),
    init() {
        @if($agenda->waktu_mulai)
        this.startTime = new Date('{{ $agenda->waktu_mulai->toIso8601String() }}').getTime();
        this.elapsed = Date.now() - this.startTime;
        @endif
        this.interval = setInterval(() => { if(this.running) this.elapsed = Date.now() - this.startTime; }, 100);
    },
    get formatted() {
        const s = Math.floor(this.elapsed / 1000);
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;
        return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
    }
}">
    <div class="page-header">
        <h1><i class="bi bi-stopwatch me-2"></i>Kelas Berjalan</h1>
        <p class="subtitle mb-0">
            {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }} — {{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
        </p>
    </div>

    {{-- Stopwatch Display --}}
    <div class="stopwatch-display mb-4" x-text="formatted">00:00:00</div>

    {{-- Status Badge --}}
    <div class="text-center mb-4">
        <span class="status-badge status-hijau">
            <i class="bi bi-broadcast animate-pulse"></i> Kelas Sedang Berjalan
        </span>
    </div>

    {{-- Prompter / Notes --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-sticky me-2"></i>Catatan Prompter</span>
            <button wire:click="savePrompter" class="btn btn-outline-primary btn-sm" style="min-height: 36px; min-width: 36px;">
                <i class="bi bi-save"></i>
            </button>
        </div>
        <div class="card-body p-2">
            <textarea wire:model.blur="prompter" class="form-control border-0" rows="4"
                      placeholder="Tulis catatan atau materi yang sedang diajarkan..."
                      style="resize: vertical;"></textarea>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 small">
                <div class="col-6"><span class="text-muted">Guru:</span> <strong>{{ auth()->user()->name }}</strong></div>
                <div class="col-6"><span class="text-muted">Tanggal:</span> <strong>{{ $agenda->tanggal?->format('d/m/Y') }}</strong></div>
                <div class="col-6"><span class="text-muted">Mulai:</span> <strong>{{ $agenda->waktu_mulai?->format('H:i') }}</strong></div>
                <div class="col-6">
                    <span class="text-muted">Foto:</span>
                    @if($agenda->foto_bukti_path)
                        <span class="text-success"><i class="bi bi-check-circle"></i> Ada</span>
                    @else
                        <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Belum</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- End Class Button --}}
    <button wire:click="akhiriPembelajaran" class="btn btn-danger btn-lg w-100"
            wire:confirm="Yakin ingin mengakhiri pembelajaran?" wire:loading.attr="disabled">
        <span wire:loading.remove><i class="bi bi-stop-circle me-2"></i>Akhiri Pembelajaran</span>
        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Mengakhiri...</span>
    </button>
</div>
