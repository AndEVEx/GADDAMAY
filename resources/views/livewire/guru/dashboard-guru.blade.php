<div wire:poll.60s>
    {{-- Clean Welcome Header Card (No Blue Background) --}}
    <div class="card mb-3 border-0 bg-light shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h5>
                <div class="text-muted small">Agenda Digital SMKN 2 Indramayu</div>
            </div>
            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fw-semibold" style="font-size: 0.85rem; border-radius: 8px;">
                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </div>

    {{-- Jadwal Hari Ini Header --}}
    <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
        <span><i class="bi bi-journal-check me-2 text-primary"></i>Jadwal Mengajar Hari Ini</span>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3" style="font-size: 0.75rem;">{{ $jadwals->count() }} Sesi</span>
    </h5>

    @forelse($jadwals as $jadwal)
        <div class="card mb-3 animate-fade-in-up border shadow-sm" style="animation-delay: {{ $loop->index * 0.05 }}s; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    
                    {{-- Jam & Status Icon Box Container --}}
                    <div class="d-flex align-items-center gap-2 flex-fill min-w-0" style="overflow: hidden;">
                        @php
                            $boxBg = match($jadwal->agenda?->status) {
                                'selesai' => 'bg-success bg-opacity-10 text-success',
                                'berjalan' => 'bg-primary bg-opacity-10 text-primary',
                                'menunggu_token', 'token_terverifikasi' => 'bg-warning bg-opacity-10 text-warning',
                                default => 'bg-light text-muted border',
                            };
                            $statusIcon = match($jadwal->agenda?->status) {
                                'selesai' => 'bi-check-circle-fill',
                                'berjalan' => 'bi-play-circle-fill',
                                'menunggu_token' => 'bi-hourglass-split',
                                'token_terverifikasi' => 'bi-shield-check',
                                default => 'bi-clock',
                            };
                        @endphp

                        <div class="rounded-3 p-2 text-center flex-shrink-0 {{ $boxBg }}" style="min-width: 62px;">
                            <i class="bi {{ $statusIcon }} fs-5 d-block mb-1"></i>
                            <div class="fw-bold" style="font-size: 0.78rem;">Jam {{ $jadwal->jam_ke_mulai }}</div>
                            @if($jadwal->jam_ke_mulai !== $jadwal->jam_ke_selesai)
                                <div style="font-size: 0.65rem;">s/d {{ $jadwal->jam_ke_selesai }}</div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-fill overflow-hidden" style="min-width: 0;">
                            @if(!empty($jadwal->is_kegiatan_khusus))
                                <span class="badge bg-secondary bg-opacity-15 text-dark px-2 py-1 text-truncate" style="font-size: 0.8rem; max-width: 100%;">{{ $jadwal->kegiatan_khusus }}</span>
                            @else
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem; line-height: 1.2;" title="{{ $jadwal->mataPelajaran?->nama_mapel }}">
                                    {{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}
                                </div>
                                <div class="text-muted small d-flex align-items-center gap-1 flex-wrap mt-1">
                                    <div class="d-flex align-items-center gap-1 text-truncate" style="max-width: 100%;">
                                        <i class="bi bi-door-open text-primary flex-shrink-0"></i>
                                        <span class="fw-semibold text-truncate">{{ $jadwal->rombel?->nama_kelas ?? '-' }}</span>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($jadwal->keterangan))
                                <span class="badge bg-light text-muted border mt-1 text-truncate d-inline-block" style="font-size: 0.7rem; max-width: 100%;">{{ $jadwal->keterangan }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status Text Badge (Fixed Right Boundary) --}}
                    <div class="flex-shrink-0 ms-1 text-end">
                        <span class="status-badge {{ $jadwal->status_label['class'] }} text-nowrap" style="font-size: 0.7rem; padding: 0.25rem 0.55rem;">
                            {{ $jadwal->status_label['text'] }}
                        </span>
                    </div>
                </div>

                {{-- Team Teaching Info --}}
                @if(empty($jadwal->is_kegiatan_khusus) && !empty($jadwal->jadwalGuru) && $jadwal->jadwalGuru->count() > 1)
                    <div class="small text-muted mb-2">
                        <i class="bi bi-people-fill text-primary"></i> Team Teaching:
                        {{ $jadwal->jadwalGuru->pluck('guru.name')->join(', ') }}
                    </div>
                @endif

                {{-- Action Buttons --}}
                @if(!empty($jadwal->can_start))
                    <a href="{{ route('guru.mulai', $jadwal->primary_id ?? $jadwal->id) }}" class="btn btn-primary btn-sm w-100 py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                        <i class="bi bi-play-fill me-1 fs-6"></i> Mulai Kelas
                    </a>
                @elseif(!empty($jadwal->is_late) && empty($jadwal->agenda) && empty($jadwal->is_kegiatan_khusus))
                    <button disabled class="btn btn-danger text-white btn-sm w-100 py-2 fw-semibold border-0" style="border-radius: 8px; cursor: not-allowed;" title="Batas waktu mulai kelas 30 menit terlewat">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Anda Telat
                    </button>
                @elseif(empty($jadwal->agenda) && empty($jadwal->is_kegiatan_khusus))
                    <button disabled class="btn btn-light text-muted btn-sm w-100 py-2 fw-medium border" style="border-radius: 8px; cursor: not-allowed;" title="Waktu mengajar belum tiba">
                        <i class="bi bi-lock-fill me-1 text-secondary"></i> Mulai Kelas (Belum Jamnya &bull; pkl {{ $jadwal->waktu_mulai_str ?? '06:45' }})
                    </button>
                @elseif(!empty($jadwal->agenda))
                    @if($jadwal->agenda->status === 'menunggu_token')
                        <a href="{{ route('guru.mulai', $jadwal->primary_id ?? $jadwal->id) }}" class="btn btn-warning btn-sm w-100 text-white py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                            <i class="bi bi-hourglass-split me-1"></i> Lihat Token OTP
                        </a>
                    @elseif($jadwal->agenda->status === 'token_terverifikasi')
                        <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-primary btn-sm w-100 py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                            <i class="bi bi-pencil-square me-1"></i> Isi Materi
                        </a>
                    @elseif($jadwal->agenda->status === 'berjalan')
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-primary btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-pencil-square me-1"></i> Isi Materi / Edit
                            </a>
                            <a href="{{ route('guru.stopwatch', $jadwal->agenda->id) }}" class="btn btn-success btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-stopwatch me-1"></i> Stopwatch
                            </a>
                        </div>
                    @elseif($jadwal->agenda->status === 'selesai')
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('guru.detail-agenda', $jadwal->agenda->id) }}" class="btn btn-outline-primary btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-eye me-1"></i> Detail Agenda
                            </a>
                            <a href="{{ route('guru.kehadiran', $jadwal->agenda->id) }}" class="btn btn-outline-secondary btn-sm py-2 fw-semibold" style="border-radius: 8px;" wire:navigate title="Presensi Siswa">
                                <i class="bi bi-person-check me-1"></i> Presensi
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <div class="card border shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2 mb-0 fw-medium">Tidak ada jadwal mengajar hari ini.</p>
            </div>
        </div>
    @endforelse
</div>
