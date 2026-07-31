<div wire:poll.30s>
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-graph-up me-2"></i>Monitoring</h1>
                <p class="subtitle mb-0">
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>
        
        <div class="mt-3 d-flex align-items-center">
            <label class="me-2 fw-bold">Pilih Jam:</label>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 48px;">
                    Jam ke-{{ $selectedJam ?? '?' }}
                </button>
                <ul class="dropdown-menu">
                    @for($i = 1; $i <= 10; $i++)
                        <li><button class="dropdown-item" wire:click="setJam({{ $i }})">Jam ke-{{ $i }}</button></li>
                    @endfor
                </ul>
            </div>
            <span class="badge bg-light text-dark">
                <i class="bi bi-broadcast animate-pulse text-success"></i> Live
            </span>
        </div>
    </div>

    {{-- Summary --}}
    <div class="row g-2 mb-3">
        <div class="col">
            <div class="card text-center py-2 border-start border-3 border-success">
                <div class="fw-bold text-success">{{ $summary['hijau'] ?? 0 }}</div>
                <div class="small text-muted">Lengkap</div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center py-2 border-start border-3 border-warning">
                <div class="fw-bold text-warning">{{ $summary['kuning'] ?? 0 }}</div>
                <div class="small text-muted">Belum Foto</div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center py-2 border-start border-3 border-danger">
                <div class="fw-bold text-danger">{{ $summary['merah'] ?? 0 }}</div>
                <div class="small text-muted">Belum Mulai</div>
            </div>
        </div>
        <div class="col">
            <div class="card text-center py-2 border-start border-3" style="border-color: #ea580c !important;">
                <div class="fw-bold" style="color: #ea580c;">{{ $summary['oranye'] ?? 0 }}</div>
                <div class="small text-muted">Izin</div>
            </div>
        </div>
    </div>

    {{-- Rombel Grid --}}
    <div class="row g-2">
        @foreach($monitoringData as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="monitoring-card card-{{ $item['status'] }}" data-bs-toggle="modal" data-bs-target="#detailModal"
                 wire:click="$dispatch('show-detail', { rombelId: '{{ $item['rombel']->id }}' })">
                <div class="fw-bold small mb-1">{{ $item['rombel']->nama_kelas }}</div>

                @if($item['jadwal'] && !$item['jadwal']->isKegiatanKhusus())
                    <div class="small text-muted mb-1">{{ $item['jadwal']->mataPelajaran?->nama_mapel }}</div>
                    @if($item['agenda'])
                        <div class="small text-muted">{{ $item['agenda']->guru?->name }}</div>
                    @else
                        <div class="small text-muted">{{ $item['jadwal']->jadwalGuru->first()?->guru?->name ?? '-' }}</div>
                    @endif
                @endif

                <div class="mt-2">
                    <span class="status-badge status-{{ $item['status'] }}">
                        {{ $item['label'] }}
                    </span>
                </div>

                @if($item['status'] === 'hijau' && $item['agenda']?->foto_bukti_path)
                    <div class="mt-2">
                        <span class="badge bg-success bg-opacity-10 text-success small">
                            <i class="bi bi-camera-fill"></i> Lihat Foto
                        </span>
                    </div>
                @endif

                @if($item['status'] === 'merah')
                    <div class="mt-2">
                        @if(auth()->user()->canOverride())
                        <a href="{{ route('admin.koreksi') }}" class="btn btn-outline-danger btn-sm" style="min-height: 32px; font-size: 0.7rem;"
                           wire:navigate onclick="event.stopPropagation()">
                            <i class="bi bi-pencil"></i> Koreksi
                        </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Links --}}
    <div class="mt-4">
        <a href="{{ route('monitoring.progress') }}" class="btn btn-outline-primary w-100" wire:navigate>
            <i class="bi bi-bar-chart me-2"></i>Lihat Progress TP
        </a>
    </div>
</div>
