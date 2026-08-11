<div wire:poll.30s>
    {{-- Page Header --}}
    <div class="page-header shadow-sm mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="text-white fw-bold mb-1"><i class="bi bi-graph-up me-2"></i>Monitoring</h1>
                <p class="subtitle mb-0 text-white-50 small">
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>
        
        {{-- Jam Selector Controls --}}
        <div class="mt-3 d-flex align-items-center flex-wrap gap-2">
            <label class="me-1 fw-bold text-white" style="font-size: 0.95rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Pilih Jam:</label>
            
            <div class="dropdown">
                <button class="btn btn-light text-primary fw-extrabold dropdown-toggle shadow-sm px-3 py-2 d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 44px; border-radius: 12px; font-weight: 800; font-size: 0.95rem;">
                    <span>Jam ke-{{ $selectedJam ?? '?' }} {{ $currentJam === $selectedJam ? '(LIVE Saat Ini)' : '' }}</span>
                </button>
                <ul class="dropdown-menu shadow-lg py-1" style="border-radius: 12px; max-height: 320px; overflow-y: auto;">
                    @for($i = 0; $i <= $maxJam; $i++)
                        <li>
                            <button class="dropdown-item py-2 fw-semibold d-flex justify-content-between align-items-center {{ $selectedJam === $i ? 'active bg-primary text-white' : '' }}" wire:click="setJam({{ $i }})">
                                <span>Jam ke-{{ $i }}</span>
                                @if($currentJam === $i)
                                    <span class="badge bg-success text-white ms-2" style="font-size: 0.65rem;">LIVE</span>
                                @endif
                            </button>
                        </li>
                    @endfor
                </ul>
            </div>

            {{-- Interactive Live Badge Button --}}
            <button type="button" wire:click="resetToLive" class="btn btn-success text-white px-3 py-2 ms-2 d-inline-flex align-items-center gap-2 shadow-sm border-0" style="border-radius: 12px; font-size: 0.9rem !important; cursor: pointer;" title="Klik untuk kembali ke Jam Live saat ini">
                <i class="bi bi-broadcast fs-5 animate-pulse text-white"></i>
                <span class="fw-extrabold" style="letter-spacing: 0.5px;">LIVE (Kembali ke Jam Sekarang)</span>
            </button>
        </div>
    </div>

    {{-- Summary Statistics Grid (2x2 Grid) --}}
    <div class="row g-2 mb-3">
        {{-- Row 1: Lengkap & Izin --}}
        <div class="col-6">
            <div class="card text-center py-3 shadow-sm border-start border-4 border-success">
                <div class="fw-extrabold fs-3 text-success mb-0" style="line-height: 1.1;">{{ $summary['hijau'] ?? 0 }}</div>
                <div class="fw-semibold small text-muted">Lengkap</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card text-center py-3 shadow-sm border-start border-4" style="border-color: #ea580c !important;">
                <div class="fw-extrabold fs-3 mb-0" style="color: #ea580c; line-height: 1.1;">{{ $summary['oranye'] ?? 0 }}</div>
                <div class="fw-semibold small text-muted">Izin</div>
            </div>
        </div>

        {{-- Row 2: Belum Foto & Belum Mulai --}}
        <div class="col-6">
            <div class="card text-center py-3 shadow-sm border-start border-4 border-warning">
                <div class="fw-extrabold fs-3 text-warning mb-0" style="line-height: 1.1;">{{ $summary['kuning'] ?? 0 }}</div>
                <div class="fw-semibold small text-muted">Belum Foto</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card text-center py-3 shadow-sm border-start border-4 border-danger">
                <div class="fw-extrabold fs-3 text-danger mb-0" style="line-height: 1.1;">{{ $summary['merah'] ?? 0 }}</div>
                <div class="fw-semibold small text-muted">Belum Mulai</div>
            </div>
        </div>
    </div>

    {{-- Rombel Grid --}}
    <div class="row g-2">
        @foreach($monitoringData as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="monitoring-card card-{{ $item['status'] }} text-center p-3 d-flex flex-column align-items-center justify-content-center h-100 shadow-sm" style="border-radius: 12px;">
                
                {{-- Nama Kelas --}}
                <div class="fw-bold fs-6 mb-1 text-center w-100 text-dark">{{ $item['rombel']->nama_kelas }}</div>

                @if($item['jadwal'] && !$item['jadwal']->isKegiatanKhusus())
                    <div class="small text-muted text-center w-100 mb-1" style="font-size: 0.8rem; line-height: 1.2;">
                        {{ $item['jadwal']->mataPelajaran?->nama_mapel }}
                    </div>
                    @if($item['agenda'])
                        <div class="small text-muted text-center w-100" style="font-size: 0.75rem;">
                            <i class="bi bi-person me-1"></i>{{ $item['agenda']->guru?->name }}
                        </div>
                    @else
                        <div class="small text-muted text-center w-100" style="font-size: 0.75rem;">
                            <i class="bi bi-person me-1"></i>{{ $item['jadwal']->jadwalGuru->first()?->guru?->name ?? '-' }}
                        </div>
                    @endif
                @endif

                {{-- Status Badge --}}
                <div class="mt-2 text-center w-100">
                    <span class="status-badge status-{{ $item['status'] }} mx-auto">
                        {{ $item['label'] }}
                    </span>
                </div>

                {{-- 2 Tombol Lihat Foto (Foto Murid & Foto Guru) --}}
                @if($item['agenda'])
                <div class="mt-2 d-flex flex-wrap gap-1 justify-content-center w-100">
                    @if($item['agenda']->foto_bukti_path)
                        <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 text-nowrap shadow-sm" style="font-size: 0.68rem; border-radius: 6px;"
                                onclick="window.openPhotoModal('{{ Storage::url($item['agenda']->foto_bukti_path) }}', 'Foto Murid (Ketua Kelas) - {{ $item['rombel']->nama_kelas }}')">
                            <i class="bi bi-camera-fill me-1"></i>Foto Murid
                        </button>
                    @endif

                    @if($item['agenda']->foto_guru_path)
                        <button type="button" class="btn btn-outline-success btn-sm py-1 px-2 text-nowrap shadow-sm" style="font-size: 0.68rem; border-radius: 6px;"
                                onclick="window.openPhotoModal('{{ Storage::url($item['agenda']->foto_guru_path) }}', 'Foto Guru & Suasana Kelas - {{ $item['rombel']->nama_kelas }}')">
                            <i class="bi bi-person-bounding-box me-1"></i>Foto Guru
                        </button>
                    @endif
                </div>
                @endif

                @if($item['status'] === 'merah')
                    <div class="mt-2 text-center w-100">
                        @if(auth()->user()->canOverride())
                        <a href="{{ route('admin.koreksi') }}" class="btn btn-outline-danger btn-sm mx-auto" style="min-height: 32px; font-size: 0.7rem;" wire:navigate>
                            <i class="bi bi-pencil me-1"></i> Koreksi
                        </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Links --}}
    <div class="mt-4 mb-3">
        <a href="{{ route('monitoring.progress') }}" class="btn btn-outline-primary w-100 py-2 fw-semibold" style="min-height: 48px;" wire:navigate>
            <i class="bi bi-bar-chart me-2"></i>Lihat Progress TP
        </a>
    </div>
</div>
