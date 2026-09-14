<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('guru.kktp-hub') }}" wire:navigate class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0 fw-bold"><i class="bi bi-clipboard-check text-primary me-2"></i>Nilai KKTP Murid</h4>
        </div>
    </div>

    <div class="row g-4">
        @forelse($kelasData as $item)
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm animate-fade-in-up h-100" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">{{ $item->rombel->nama_kelas }}</h5>
                        <p class="text-muted mb-3">{{ $item->mapel->nama_mapel }}</p>

                        <div class="d-flex justify-content-between mb-3 text-sm">
                            <span><i class="bi bi-list-check me-1"></i> {{ $item->tp_count }} TP</span>
                            <span><i class="bi bi-people me-1"></i> {{ $item->siswa_count }} Siswa</span>
                        </div>

                        @if($item->tp_count == 0)
                            <div class="alert alert-warning mb-0 border-0" style="border-radius: 8px;">
                                <i class="bi bi-exclamation-triangle me-2"></i> Belum ada TP. 
                                <a href="{{ route('guru.kktp-setting') }}" wire:navigate class="alert-link">Silakan setting terlebih dahulu</a>.
                            </div>
                        @else
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">{{ $item->percentage }}% Tercapai</span>
                                <span class="text-muted small">({{ $item->tercapai_count }}/{{ $item->total_cells }})</span>
                            </div>
                            <div class="progress mb-4" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item->percentage }}%" aria-valuenow="{{ $item->percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <a href="{{ route('guru.kktp-nilai-detail', ['rombel' => $item->rombel->id, 'mapel' => $item->mapel->id]) }}" wire:navigate class="btn btn-primary w-100" style="border-radius: 8px;">
                                <i class="bi bi-pencil-square me-2"></i> Input Nilai
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted animate-fade-in-up">
                    <i class="bi bi-clipboard-x display-4 mb-3 d-block"></i>
                    <p>Belum ada jadwal mengajar yang tersedia.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
