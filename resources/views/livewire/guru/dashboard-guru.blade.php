<div>
    {{-- Page Header --}}
    <div class="page-header mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1"><i class="bi bi-house-fill me-2"></i>Dashboard</h1>
                <p class="subtitle mb-0 text-muted small">Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div class="text-end">
                <div class="small fw-semibold text-primary">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
        <span><i class="bi bi-calendar-event me-2 text-primary"></i>Jadwal Mengajar Hari Ini</span>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3" style="font-size: 0.75rem;">{{ $jadwals->count() }} Sesi</span>
    </h5>

    @forelse($jadwals as $jadwal)
        <div class="card mb-3 animate-fade-in-up border shadow-sm" style="animation-delay: {{ $loop->index * 0.05 }}s; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    {{-- Jam --}}
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 text-center" style="min-width: 62px;">
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;">Jam {{ $jadwal->jam_ke_mulai }}</div>
                            @if($jadwal->jam_ke_mulai !== $jadwal->jam_ke_selesai)
                                <div class="text-muted" style="font-size: 0.68rem;">s/d {{ $jadwal->jam_ke_selesai }}</div>
                            @endif
                        </div>

                        <div>
                            @if(!empty($jadwal->is_kegiatan_khusus))
                                <span class="badge bg-secondary bg-opacity-15 text-dark px-2 py-1" style="font-size: 0.8rem;">{{ $jadwal->kegiatan_khusus }}</span>
                            @else
                                <div class="fw-bold text-dark" style="font-size: 0.98rem;">
                                    {{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}
                                </div>
                                <div class="text-muted small d-flex align-items-center gap-1">
                                    <i class="bi bi-door-open text-primary"></i>
                                    <span class="fw-semibold">{{ $jadwal->rombel?->nama_kelas ?? '-' }}</span>
                                    @if(!empty($jadwal->is_split_by_break))
                                        <span class="badge bg-info bg-opacity-15 text-info ms-1" style="font-size: 0.65rem;">Terpotong Istirahat</span>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($jadwal->keterangan))
                                <span class="badge bg-light text-muted border mt-1 d-inline-block" style="font-size: 0.7rem;">{{ $jadwal->keterangan }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span class="status-badge {{ $jadwal->status_label['class'] }}">
                        <i class="bi {{ $jadwal->status_label['icon'] }}"></i>
                        {{ $jadwal->status_label['text'] }}
                    </span>
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
                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-primary btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-pencil-square me-1"></i> Isi Materi / Edit
                            </a>
                            <a href="{{ route('guru.stopwatch', $jadwal->agenda->id) }}" class="btn btn-success btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-stopwatch me-1"></i> Stopwatch
                            </a>
                        </div>
                    @elseif($jadwal->agenda->status === 'selesai')
                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-outline-primary btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-pencil me-1"></i> Detail / Edit Agenda
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
