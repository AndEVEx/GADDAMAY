<div>
    {{-- Page Header --}}
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-house-fill me-2"></i>Dashboard</h1>
                <p class="subtitle mb-0">Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div class="text-end">
                <div class="small opacity-75">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <h5 class="fw-bold mb-3">
        <i class="bi bi-calendar-event me-1"></i> Jadwal Hari Ini
    </h5>

    @forelse($jadwals as $jadwal)
        <div class="card mb-3 animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    {{-- Jam --}}
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 text-center" style="min-width: 56px;">
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;">Jam {{ $jadwal->jam_ke_mulai }}</div>
                            @if($jadwal->jam_ke_mulai !== $jadwal->jam_ke_selesai)
                                <div class="text-muted" style="font-size: 0.65rem;">s/d {{ $jadwal->jam_ke_selesai }}</div>
                            @endif
                        </div>

                        <div>
                            @if($jadwal->isKegiatanKhusus())
                                <span class="badge-kegiatan">{{ $jadwal->kegiatan_khusus }}</span>
                            @else
                                <div class="fw-bold" style="font-size: 0.95rem;">
                                    {{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-door-open"></i>
                                    {{ $jadwal->rombel?->nama_kelas ?? '-' }}
                                </div>
                            @endif

                            @if($jadwal->keterangan)
                                <span class="badge-keterangan mt-1 d-inline-block">{{ $jadwal->keterangan }}</span>
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
                @if(!$jadwal->isKegiatanKhusus() && $jadwal->jadwalGuru->count() > 1)
                    <div class="small text-muted mb-2">
                        <i class="bi bi-people-fill"></i> Team Teaching:
                        {{ $jadwal->jadwalGuru->pluck('guru.name')->join(', ') }}
                    </div>
                @endif

                {{-- Action Buttons --}}
                @if($jadwal->can_start)
                    <a href="{{ route('guru.mulai', $jadwal->id) }}" class="btn btn-primary btn-sm w-100" wire:navigate>
                        <i class="bi bi-play-fill"></i> Mulai Kelas
                    </a>
                @elseif($jadwal->agenda)
                    @if($jadwal->agenda->status === 'menunggu_token')
                        <a href="{{ route('guru.mulai', $jadwal->id) }}" class="btn btn-warning btn-sm w-100 text-white" wire:navigate>
                            <i class="bi bi-hourglass-split"></i> Lihat Token
                        </a>
                    @elseif($jadwal->agenda->status === 'token_terverifikasi')
                        <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-primary btn-sm w-100" wire:navigate>
                            <i class="bi bi-pencil-square"></i> Isi Materi
                        </a>
                    @elseif($jadwal->agenda->status === 'berjalan')
                        <a href="{{ route('guru.stopwatch', $jadwal->agenda->id) }}" class="btn btn-success btn-sm w-100" wire:navigate>
                            <i class="bi bi-stopwatch"></i> Lanjut
                        </a>
                    @elseif($jadwal->agenda->status === 'selesai')
                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-outline-primary btn-sm flex-fill" wire:navigate>
                                <i class="bi bi-pencil"></i> Detail
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2 mb-0">Tidak ada jadwal hari ini.</p>
            </div>
        </div>
    @endforelse
</div>
