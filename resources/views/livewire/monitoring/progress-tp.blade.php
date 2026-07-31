<div>
    <div class="page-header">
        <h1><i class="bi bi-bar-chart me-2"></i>Progress TP</h1>
        <p class="subtitle mb-0">Pantau ketercapaian Tujuan Pembelajaran</p>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small">Mata Pelajaran</label>
                    <select wire:model.live="selectedMapel" class="form-select">
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mapelList as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small">Kelas (Opsional)</label>
                    <select wire:model.live="selectedRombel" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($rombelList as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($selectedMapel && $progressData->count())
    {{-- Progress Bar --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold">Ketercapaian TP</span>
                <span class="fw-bold text-{{ $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}">
                    {{ $taughtCount }}/{{ $totalTp }} ({{ $percentage }}%)
                </span>
            </div>
            <div class="progress" style="height: 12px; border-radius: 6px;">
                <div class="progress-bar bg-{{ $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}"
                     style="width: {{ $percentage }}%; transition: width 0.6s ease-in-out;">
                </div>
            </div>
        </div>
    </div>

    {{-- TP List --}}
    <div class="card">
        <div class="card-body p-0">
            @foreach($progressData as $item)
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="flex-shrink-0">
                    @if($item['taught'])
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.2rem;"></i>
                    @else
                        <i class="bi bi-circle text-muted" style="font-size: 1.2rem;"></i>
                    @endif
                </div>
                <div class="flex-fill">
                    <div class="fw-bold small">{{ $item['tp']->kode_tp }}</div>
                    <div class="small text-muted">{{ $item['tp']->deskripsi_tp }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif($selectedMapel)
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-clipboard-x" style="font-size: 2rem;"></i>
            <p class="mt-2 mb-0">Belum ada TP terdaftar untuk mapel ini.</p>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-funnel" style="font-size: 2rem;"></i>
            <p class="mt-2 mb-0">Pilih mata pelajaran untuk melihat progress TP.</p>
        </div>
    </div>
    @endif
</div>
