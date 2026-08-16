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
            <div>
                <input type="month" wire:model.live="bulan" class="form-control fw-semibold" style="min-height: 44px; border-radius: 10px;">
            </div>
        </div>
    </div>

    {{-- Rombel List --}}
    <h6 class="fw-bold text-dark mb-2">Daftar Kelas Mengajar ({{ $rombels->count() }} Kelas):</h6>

    @forelse($rombels as $rombel)
    <div class="card mb-2 animate-fade-in-up border shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('guru.jurnal-kelas', ['rombel' => $rombel->id, 'bulan' => $bulan]) }}" 
               class="text-decoration-none flex-grow-1" wire:navigate>
                <div class="fw-bold text-dark fs-6 mb-1">{{ $rombel->nama_kelas }}</div>
                <div class="small text-muted d-flex align-items-center gap-2">
                    <span><i class="bi bi-clock me-1 text-primary"></i>{{ $rombel->merged_sesi_minggu }} sesi/minggu</span>
                    <span>&bull;</span>
                    <span class="text-primary fw-semibold">{{ $namaBulan }}</span>
                    <span>&bull;</span>
                    <span class="text-success fw-bold">{{ $rombel->agenda_selesai_count }} pertemuan</span>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('guru.jurnal.export-pdf', ['rombel' => $rombel->id, 'bulan' => $bulan, 'tipe' => 'gabungan']) }}" 
                   target="_blank" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" style="border-radius: 8px; min-height: 38px;" title="Export PDF Jurnal Gabungan">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                    <span class="d-none d-sm-inline small fw-bold">PDF</span>
                </a>
                <a href="{{ route('guru.jurnal-kelas', ['rombel' => $rombel->id, 'bulan' => $bulan]) }}" 
                   class="btn btn-sm btn-primary d-flex align-items-center gap-1" style="border-radius: 8px; min-height: 38px;" wire:navigate>
                    <span class="small fw-semibold">Buka</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="card border shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
            <p class="mt-2 mb-0 fw-medium">Belum ada jadwal mengajar pada bulan ini.</p>
        </div>
    </div>
    @endforelse
</div>
