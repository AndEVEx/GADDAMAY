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

        {{-- Quick Sub-Navigation Pills (Hidden on mobile) --}}
        <div class="mt-3 pt-2 border-top border-white border-opacity-25 d-none d-md-flex gap-2 flex-wrap">
            <a href="{{ route('monitoring.dashboard') }}" class="btn btn-sm btn-light text-primary fw-bold px-3 py-1" style="border-radius: 8px;">
                <i class="bi bi-speedometer2 me-1"></i>Realtime Live
            </a>
            <a href="{{ route('monitoring.harian') }}" class="btn btn-sm btn-outline-light fw-semibold px-3 py-1" style="border-radius: 8px;" wire:navigate>
                <i class="bi bi-calendar2-day me-1"></i>Monitoring Harian (Tabel)
            </a>
            <a href="{{ route('monitoring.mingguan') }}" class="btn btn-sm btn-outline-light fw-semibold px-3 py-1" style="border-radius: 8px;" wire:navigate>
                <i class="bi bi-calendar-range me-1"></i>Monitoring Mingguan
            </a>
            <a href="{{ route('monitoring.izin-guru') }}" class="btn btn-sm btn-outline-light fw-semibold px-3 py-1" style="border-radius: 8px;" wire:navigate>
                <i class="bi bi-patch-check me-1"></i>Tabel Manajemen Izin
            </a>
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
            <div class="card text-center py-3 shadow-sm border-start border-4" style="border-color: #7c3aed !important;">
                <div class="fw-extrabold fs-3 mb-0" style="color: #7c3aed; line-height: 1.1;">{{ $summary['ungu'] ?? 0 }}</div>
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

    {{-- Search Filter --}}
    <div class="mb-3 animate-fade-in-up">
        <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="search" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Cari kelas, guru, atau mata pelajaran..." style="min-height: 48px;">
            @if(!empty($search))
                <button type="button" wire:click="$set('search', '')" class="btn btn-outline-secondary border-start-0 border-end" title="Hapus pencarian">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- Rombel Grid --}}
    <div class="row g-2">
        @forelse($monitoringData as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="monitoring-card card-{{ $item['status'] }} text-center p-3 d-flex flex-column align-items-center justify-content-center h-100 shadow-sm position-relative" 
                 style="border-radius: 12px; cursor: pointer; transition: transform 0.15s;"
                 wire:click="openDetailModal('{{ $item['rombel']->id }}', '{{ $item['jadwal']?->id }}', '{{ $item['agenda']?->id }}')"
                 title="Klik untuk melihat detail pembelajaran">
                
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

                {{-- Alasan Keterlambatan Guru (Item 2) --}}
                @if(!empty($item['agenda']?->alasan_terlambat))
                    <div class="mt-2 text-center w-100" onclick="event.stopPropagation();">
                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 text-truncate d-inline-block px-2 py-1" style="max-width: 100%; font-size: 0.68rem;" title="Alasan: {{ $item['agenda']->alasan_terlambat }}">
                            <i class="bi bi-chat-quote-fill me-1 text-warning"></i>{{ Str::limit($item['agenda']->alasan_terlambat, 18) }}
                        </span>
                    </div>
                @endif

                {{-- 2 Tombol Lihat Foto (Foto Murid & Foto Guru) --}}
                @if($item['agenda'])
                <div class="mt-2 d-flex flex-wrap gap-1 justify-content-center w-100" onclick="event.stopPropagation();">
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

                {{-- Quick hint --}}
                <div class="mt-2 text-muted" style="font-size: 0.62rem;">
                    <i class="bi bi-info-circle me-1"></i>Klik kartu untuk rincian
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="card p-4 border-0 shadow-sm" style="border-radius: 12px; background: #f8fafc;">
                <i class="bi bi-search fs-1 text-muted mb-2 d-block"></i>
                <h6 class="fw-bold text-dark mb-1">Tidak Ada Hasil Pencarian</h6>
                <p class="text-muted small mb-3">Tidak ditemukan kelas, guru, atau mapel yang cocok dengan kata kunci "<strong>{{ $search }}</strong>".</p>
                <div>
                    <button type="button" wire:click="$set('search', '')" class="btn btn-primary btn-sm px-3" style="border-radius: 8px;">
                        <i class="bi bi-arrow-repeat me-1"></i> Reset Pencarian
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- ============================================================ --}}
    {{-- POP-UP MODAL DETAIL PEMBELAJARAN (Item 3)                    --}}
    {{-- ============================================================ --}}
    @if($showDetailModal && $modalDetail)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.55); z-index: 1060;" wire:keydown.escape="closeDetailModal">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                {{-- Modal Header --}}
                <div class="modal-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            <i class="bi bi-door-open-fill"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">
                                Detail Pembelajaran — {{ $modalDetail['rombel']?->nama_kelas ?? 'Kelas' }}
                            </h5>
                            <small class="text-white-50">
                                Jam ke-{{ $modalDetail['jamSelected'] ?? $currentJam }} &bull; {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDetailModal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">
                    @php
                        $agenda = $modalDetail['agenda'];
                        $jadwal = $modalDetail['jadwal'];
                        $rombel = $modalDetail['rombel'];
                        $tps = $modalDetail['tps'];
                        $stats = $modalDetail['presensiStats'];
                    @endphp

                    {{-- 1. Ringkasan Guru, Mapel, & Waktu --}}
                    <div class="card border-0 bg-light p-3 mb-3" style="border-radius: 12px;">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="text-muted small">Mata Pelajaran</div>
                                <div class="fw-bold text-dark fs-6">
                                    {{ $jadwal?->mataPelajaran?->nama_mapel ?? ($jadwal?->kegiatan_khusus ?? 'Tidak Ada Jadwal') }}
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-muted small">Guru Pengajar</div>
                                <div class="fw-bold text-dark fs-6">
                                    {{ $agenda?->guru?->name ?? ($jadwal?->jadwalGuru?->pluck('guru.name')->join(', ') ?: '-') }}
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-muted small">Jadwal Jam Mengajar</div>
                                <div class="fw-semibold text-dark">
                                    Jam {{ $jadwal?->jam_ke_mulai ?? '-' }} s/d {{ $jadwal?->jam_ke_selesai ?? '-' }}
                                    <span class="text-muted small">({{ $jadwal?->waktu_mulai_str ?? '-' }} - {{ $jadwal?->waktu_selesai_str ?? '-' }} WIB)</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="text-muted small">Waktu Handshake Masuk</div>
                                <div class="fw-semibold text-primary">
                                    @if($agenda?->waktu_mulai)
                                        <i class="bi bi-clock-check me-1"></i>{{ \Carbon\Carbon::parse($agenda->waktu_mulai)->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                                    @else
                                        <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Belum Masuk</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="text-muted small">Status Sesi</div>
                                <div>
                                    @if($agenda)
                                        <span class="badge bg-success text-uppercase">{{ str_replace('_', ' ', $agenda->status) }}</span>
                                        <span class="badge bg-primary text-uppercase">{{ $agenda->status_kehadiran_guru }}</span>
                                    @else
                                        <span class="badge bg-danger">BELUM HANDSHAKE</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Alasan Keterlambatan Guru (Jika Ada) --}}
                    @if(!empty($agenda?->alasan_terlambat))
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-2 p-3 mb-3" style="border-radius: 12px; background-color: #fffbeb; border-left: 4px solid #f59e0b !important;">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5 flex-shrink-0 mt-1"></i>
                        <div>
                            <div class="fw-bold text-dark">Keterangan / Alasan Guru Terlambat:</div>
                            <div class="text-dark" style="font-style: italic;">"{{ $agenda->alasan_terlambat }}"</div>
                        </div>
                    </div>
                    @endif

                    {{-- 3. Materi Pembelajaran & KKTP / TP --}}
                    <div class="card border mb-3" style="border-radius: 12px;">
                        <div class="card-header bg-light py-2 fw-bold text-dark">
                            <i class="bi bi-journal-text me-2 text-primary"></i>Materi & Tujuan Pembelajaran (TP)
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="text-muted small fw-bold d-block mb-1">Materi yang Diajarkan:</label>
                                @if(!empty($agenda?->materi_diajarkan))
                                    <div class="p-2 bg-light rounded text-dark fw-medium">{{ $agenda->materi_diajarkan }}</div>
                                @else
                                    <span class="text-muted small fst-italic">Belum diisi oleh guru.</span>
                                @endif
                            </div>

                            <div>
                                <label class="text-muted small fw-bold d-block mb-1">Capaian KKTP / Tujuan Pembelajaran (TP):</label>
                                @if($tps->isNotEmpty())
                                    <ul class="list-group list-group-flush border rounded">
                                        @foreach($tps as $tp)
                                        <li class="list-group-item py-2 d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold me-2">{{ $tp->kode_tp }}</span>
                                                <span class="small">{{ $tp->deskripsi_tp }}</span>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted small fst-italic">Belum ada Tujuan Pembelajaran yang diatur untuk mapel ini.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. Presensi Kehadiran Siswa --}}
                    <div class="card border mb-3" style="border-radius: 12px;">
                        <div class="card-header bg-light py-2 fw-bold text-dark">
                            <i class="bi bi-people me-2 text-info"></i>Rekap Presensi Siswa di Kelas
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <div class="p-2 rounded bg-success bg-opacity-10 border border-success border-opacity-25">
                                        <div class="fs-5 fw-bold text-success">{{ $stats['hadir'] }}</div>
                                        <div class="small text-muted" style="font-size: 0.72rem;">Hadir</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 rounded bg-info bg-opacity-10 border border-info border-opacity-25">
                                        <div class="fs-5 fw-bold text-info">{{ $stats['sakit'] }}</div>
                                        <div class="small text-muted" style="font-size: 0.72rem;">Sakit</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25">
                                        <div class="fs-5 fw-bold text-warning">{{ $stats['izin'] }}</div>
                                        <div class="small text-muted" style="font-size: 0.72rem;">Izin</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25">
                                        <div class="fs-5 fw-bold text-danger">{{ $stats['alpa'] }}</div>
                                        <div class="small text-muted" style="font-size: 0.72rem;">Alpa / Belum</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Foto Bukti Guru & Murid --}}
                    @if($agenda && ($agenda->foto_bukti_path || $agenda->foto_guru_path))
                    <div class="card border" style="border-radius: 12px;">
                        <div class="card-header bg-light py-2 fw-bold text-dark">
                            <i class="bi bi-camera me-2 text-success"></i>Foto Dokumentasi KBM
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                @if($agenda->foto_bukti_path)
                                <div class="col-6 text-center">
                                    <div class="small text-muted mb-1 fw-semibold">Foto Murid (Ketua Kelas)</div>
                                    <img src="{{ Storage::url($agenda->foto_bukti_path) }}" alt="Foto Murid" 
                                         class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: cover; width: 100%; cursor: pointer;"
                                         onclick="window.openPhotoModal('{{ Storage::url($agenda->foto_bukti_path) }}', 'Foto Murid - {{ $rombel?->nama_kelas }}')">
                                </div>
                                @endif
                                @if($agenda->foto_guru_path)
                                <div class="col-6 text-center">
                                    <div class="small text-muted mb-1 fw-semibold">Foto Guru & Kelas</div>
                                    <img src="{{ Storage::url($agenda->foto_guru_path) }}" alt="Foto Guru" 
                                         class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: cover; width: 100%; cursor: pointer;"
                                         onclick="window.openPhotoModal('{{ Storage::url($agenda->foto_guru_path) }}', 'Foto Guru - {{ $rombel?->nama_kelas }}')">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary px-4" wire:click="closeDetailModal" style="border-radius: 8px;">Tutup</button>
                    @if(auth()->user()->canOverride() && $modalDetail['agenda'])
                        <a href="{{ route('admin.koreksi') }}" class="btn btn-warning text-dark fw-bold px-3" style="border-radius: 8px;" wire:navigate>
                            <i class="bi bi-pencil-square me-1"></i>Koreksi Agenda Ini
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- TABEL PERSENTASE KEHADIRAN GURU BULANAN (Item 5)             --}}
    {{-- ============================================================ --}}
    <div class="card border-0 shadow-sm mt-4 mb-3 animate-fade-in-up" style="border-radius: 14px;">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-radius: 14px 14px 0 0;">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>Persentase Kehadiran & Jam Mengajar Guru (Bulanan)
                </h6>
                <span class="text-muted small">Perhitungan total realisasi jam masuk kelas dibanding target jam semestinya</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="searchGuruRekap" class="form-control bg-light border-start-0" placeholder="Cari nama guru...">
                </div>
                <div class="d-flex align-items-center gap-1">
                    <input type="month" wire:model.live="rekapBulan" class="form-control form-control-sm" style="width: 140px;">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-3 py-2 text-center" style="width: 50px;">No</th>
                            <th class="py-2">Nama Guru</th>
                            <th class="py-2 text-center">Target Jam (JP)</th>
                            <th class="py-2 text-center">Realisasi Jam (JP)</th>
                            <th class="py-2 text-center">Izin / Sakit</th>
                            <th class="py-2 pe-3" style="width: 220px;">Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapGuruKehadiran as $index => $row)
                        @php
                            $barColor = $row->persentase >= 90 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning' : 'bg-danger');
                            $badgeColor = $row->persentase >= 90 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger');
                        @endphp
                        <tr>
                            <td class="ps-3 py-2 text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td class="py-2">
                                <span class="fw-bold text-dark d-block">{{ $row->guru->name }}</span>
                                <span class="text-muted small" style="font-size: 0.72rem;">{{ $row->guru->email }}</span>
                            </td>
                            <td class="py-2 text-center fw-semibold text-muted">{{ $row->target_jp }} JP</td>
                            <td class="py-2 text-center fw-bold text-success">{{ $row->realisasi_jp }} JP</td>
                            <td class="py-2 text-center small text-muted">
                                @if($row->izin_jp > 0 || $row->sakit_jp > 0)
                                    <span class="badge bg-warning bg-opacity-10 text-dark">{{ $row->izin_jp }} Izin</span>
                                    <span class="badge bg-info bg-opacity-10 text-dark">{{ $row->sakit_jp }} Sakit</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-2 pe-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ min(100, $row->persentase) }}%;"></div>
                                    </div>
                                    <span class="badge {{ $badgeColor }} fw-bold" style="font-size: 0.75rem; min-width: 50px;">
                                        {{ $row->persentase }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-people fs-3 d-block mb-1 opacity-50"></i>
                                Tidak ada data guru yang cocok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="mt-4 mb-3">
        <a href="{{ route('monitoring.progress') }}" class="btn btn-outline-primary w-100 py-2 fw-semibold" style="min-height: 48px;" wire:navigate>
            <i class="bi bi-bar-chart me-2"></i>Lihat Progress TP
        </a>
    </div>
</div>
