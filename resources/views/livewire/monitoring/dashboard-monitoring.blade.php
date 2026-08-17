<div wire:poll.30s>
    {{-- Page Header --}}
    <div class="page-header shadow-sm mb-3">
        <div>
            <h1 class="text-white fw-bold mb-1"><i class="bi bi-graph-up me-2"></i>Monitoring KBM</h1>
            <div class="subtitle mb-0 text-white small fw-medium">
                {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                &bull; {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }} WIB
                &bull; Jam ke-{{ $currentJam ?? '?' }}
                @if(!empty($currentJamObj))
                    ({{ substr($currentJamObj->waktu_mulai, 0, 5) }} - {{ substr($currentJamObj->waktu_selesai, 0, 5) }} WIB
                    @if(!empty($currentJamObj->label))
                        &bull; {{ $currentJamObj->label }}
                    @endif
                    )
                @endif
            </div>
        </div>
        
        {{-- Jam Selector Controls --}}
        <div class="mt-3 d-flex align-items-center flex-wrap gap-2">
            <label class="me-1 fw-bold text-white" style="font-size: 0.95rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Pilih Jam:</label>
            
            <div class="dropdown">
                <button class="btn btn-light text-primary fw-extrabold dropdown-toggle shadow-sm px-3 py-2 d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 44px; border-radius: 12px; font-weight: 800; font-size: 0.95rem;">
                    <span>
                        @if(($selectedJam ?? null) === 0)
                            Jam ke-0 (Apel / Upacara)
                        @else
                            Jam ke-{{ $selectedJam ?? '?' }}
                        @endif
                        {{ $currentJam === $selectedJam ? '(LIVE Saat Ini)' : '' }}
                    </span>
                </button>
                <ul class="dropdown-menu shadow-lg py-1" style="border-radius: 12px; max-height: 340px; overflow-y: auto; min-width: 290px;">
                    @if(!empty($jamPelajaranList) && $jamPelajaranList->isNotEmpty())
                        @foreach($jamPelajaranList as $jp)
                            <li>
                                <button class="dropdown-item py-2 fw-semibold d-flex justify-content-between align-items-center {{ $selectedJam === $jp->jam_ke ? 'active bg-primary text-white' : '' }}" wire:click="setJam({{ $jp->jam_ke }})">
                                    <div>
                                        <div class="fw-bold">
                                            @if($jp->jam_ke === 0)
                                                Jam ke-0 (Apel Pagi / Upacara)
                                            @elseif($jp->jam_ke === 5)
                                                Jam ke-5 <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Istirahat 1 (09:45-10:00)</span>
                                            @elseif($jp->jam_ke === 9)
                                                Jam ke-9 <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Ishoma (12:15-12:45)</span>
                                            @elseif($jp->jam_ke === 12)
                                                Jam ke-12 <span class="badge bg-info text-white ms-1" style="font-size: 0.65rem;">Selesai KBM</span>
                                            @else
                                                Jam ke-{{ $jp->jam_ke }}
                                            @endif
                                        </div>
                                        <div class="small {{ $selectedJam === $jp->jam_ke ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.72rem;">
                                            pkl {{ substr($jp->waktu_mulai, 0, 5) }} - {{ substr($jp->waktu_selesai, 0, 5) }} WIB
                                        </div>
                                    </div>
                                    @if($currentJam === $jp->jam_ke)
                                        <span class="badge bg-success text-white ms-2" style="font-size: 0.65rem;">LIVE</span>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    @else
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
                    @endif
                </ul>
            </div>

            {{-- Interactive Live Badge Button --}}
            <button type="button" wire:click="resetToLive" class="btn btn-success text-white px-3 py-2 ms-2 d-inline-flex align-items-center gap-2 shadow-sm border-0" style="border-radius: 12px; font-size: 0.9rem !important; cursor: pointer;" title="Klik untuk kembali ke Jam Live saat ini">
                <i class="bi bi-broadcast fs-5 animate-pulse text-white"></i>
                <span class="fw-extrabold" style="letter-spacing: 0.5px;">LIVE (Kembali ke Jam Sekarang)</span>
            </button>
        </div>
    </div>

    {{-- Holiday Alert Banner for Monitoring --}}
    @if($todayHoliday)
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-3 animate-fade-in-up" style="border-radius: 14px; background: linear-gradient(135deg, #ef4444, #b91c1c); color: #fff;">
        <div class="bg-white bg-opacity-20 rounded-circle p-2 d-flex align-items-center justify-content-center">
            <i class="bi bi-brightness-alt-high-fill fs-3 text-white"></i>
        </div>
        <div>
            <div class="badge bg-white text-danger fw-bold mb-1">HARI INI LIBUR SEKOLAH ({{ $todayHoliday->tipe_label }})</div>
            <h5 class="fw-extrabold text-white mb-0">{{ $todayHoliday->nama_hari_libur }}</h5>
            <small class="text-white-50">KBM pada hari ini ditiadakan. Monitoring KBM non-aktif.</small>
        </div>
    </div>
    @endif

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
                        <div class="small text-primary fw-bold text-center w-100 mt-1" style="font-size: 0.72rem;">
                            <i class="bi bi-clock-history me-1"></i>Masuk: {{ $item['agenda']->waktu_mulai?->setTimezone('Asia/Jakarta')->format('H:i') ?? $item['agenda']->created_at?->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
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
