<div>
    <div class="page-header mb-3">
        <h1><i class="bi bi-journal-text me-2"></i>Jurnal Mengajar Guru</h1>
        <p class="subtitle mb-0">Pilih bulan & kelas untuk melihat riwayat mengajar per pertemuan</p>
    </div>

    {{-- Month Picker --}}
    <div class="card mb-3 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3">
            <label class="form-label fw-bold text-dark small mb-1">
                <i class="bi bi-calendar-month text-primary me-1"></i>Pilih Bulan Jurnal Mengajar:
            </label>
            <div class="d-flex align-items-center gap-2">
                <input type="month" wire:model.live="bulan" class="form-control fw-semibold" style="min-height: 44px; border-radius: 10px;">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 text-nowrap fw-bold" style="font-size: 0.85rem; border-radius: 8px;">
                    {{ $namaBulan }}
                </span>
            </div>
        </div>
    </div>

    {{-- Rombel List --}}
    <h6 class="fw-bold text-dark mb-2">Daftar Kelas Mengajar ({{ $rombels->count() }} Kelas):</h6>

    @forelse($rombels as $rombel)
    <a href="{{ route('guru.jurnal-kelas', ['rombel' => $rombel->id, 'bulan' => $bulan]) }}" 
       class="card mb-2 text-decoration-none animate-fade-in-up border shadow-sm hover-shadow" 
       style="border-radius: 12px;" wire:navigate>
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-bold text-dark fs-6 mb-1">{{ $rombel->nama_kelas }}</div>
                <div class="small text-muted d-flex align-items-center gap-2">
                    <span><i class="bi bi-clock me-1 text-primary"></i>{{ $rombel->total_jadwal_count }} sesi/minggu</span>
                    <span>&bull;</span>
                    <span class="text-primary fw-semibold">{{ $namaBulan }}</span>
                </div>
            </div>
            <div class="text-end">
                <div class="fw-extrabold text-success fs-5">{{ $rombel->agenda_selesai_count }}</div>
                <div class="small text-muted" style="font-size: 0.72rem;">pertemuan selesai</div>
            </div>
        </div>
    </a>
    @empty
    <div class="card border shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
            <p class="mt-2 mb-0 fw-medium">Belum ada jadwal mengajar pada bulan ini.</p>
        </div>
    </div>
    @endforelse
</div>
