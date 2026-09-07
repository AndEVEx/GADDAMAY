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

    {{-- Holiday Alert Banner --}}
    @if($todayHoliday)
    <div class="card mb-3 border-0 shadow-sm animate-fade-in-up text-white" style="border-radius: 14px; background: linear-gradient(135deg, #ef4444, #b91c1c);">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="bg-white bg-opacity-20 rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                <i class="bi bi-brightness-alt-high-fill fs-3 text-white"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white text-danger fw-bold" style="font-size: 0.7rem;">HARI LIBUR SEKOLAH</span>
                    <span class="badge bg-white bg-opacity-20 text-white" style="font-size: 0.7rem;">{{ $todayHoliday->tipe_label }}</span>
                </div>
                <h6 class="fw-bold mb-0 text-white">{{ $todayHoliday->nama_hari_libur }}</h6>
                <div class="text-white-50 small" style="font-size: 0.75rem;">
                    KBM hari ini ditiadakan. @if($todayHoliday->keterangan) ({{ $todayHoliday->keterangan }}) @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Access Shortcut Cards --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <a href="{{ route('guru.performa.jadwal-mingguan') }}" class="card border-0 shadow-sm text-decoration-none text-center p-2 h-100 bg-white" style="border-radius: 12px; transition: transform 0.15s;" wire:navigate>
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-1" style="width: 36px; height: 36px;">
                    <i class="bi bi-calendar-week fs-5"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 0.76rem;">Jam Mingguan</div>
                <div class="text-muted" style="font-size: 0.65rem;">Beban Mengajar</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('guru.performa.siswa-diajar') }}" class="card border-0 shadow-sm text-decoration-none text-center p-2 h-100 bg-white" style="border-radius: 12px; transition: transform 0.15s;" wire:navigate>
                <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-1" style="width: 36px; height: 36px;">
                    <i class="bi bi-people fs-5"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 0.76rem;">Siswa Diajar</div>
                <div class="text-muted" style="font-size: 0.65rem;">Per Kelas & Mapel</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('guru.performa.kehadiran-bulanan') }}" class="card border-0 shadow-sm text-decoration-none text-center p-2 h-100 bg-white" style="border-radius: 12px; transition: transform 0.15s;" wire:navigate>
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-1" style="width: 36px; height: 36px;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 0.76rem;">Realisasi Jam</div>
                <div class="text-muted" style="font-size: 0.65rem;">Kinerja Bulanan</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('guru.performa.export-iki') }}" class="card border-0 shadow-sm text-decoration-none text-center p-2 h-100 bg-white" style="border-radius: 12px; transition: transform 0.15s;" wire:navigate>
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-1" style="width: 36px; height: 36px;">
                    <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                </div>
                <div class="fw-bold text-dark" style="font-size: 0.76rem;">Export IKI</div>
                <div class="text-muted" style="font-size: 0.65rem;">Cetak Dokumen PDF</div>
            </a>
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
                            $boxBg = 'bg-light text-muted border';
                            $statusIcon = 'bi-clock';
                            
                            if (!empty($jadwal->agenda)) {
                                $agendaStatus = $jadwal->agenda->status;
                                $isIzin = in_array($jadwal->agenda->status_kehadiran_guru ?? '', ['izin', 'cuti', 'sakit', 'dinas', 'tugas_luar']);
                                
                                if ($isIzin) {
                                    $boxBg = 'text-white';
                                    $statusIcon = 'bi-info-circle-fill';
                                } elseif (in_array($agendaStatus, ['berjalan', 'selesai'])) {
                                    if (!empty($jadwal->handshake_on_time) && !empty($jadwal->has_foto)) {
                                        // ≤45min AND has foto = GREEN
                                        $boxBg = 'bg-success bg-opacity-10 text-success';
                                    } else {
                                        // >45min OR no foto yet = YELLOW
                                        $boxBg = 'bg-warning bg-opacity-10 text-warning';
                                    }
                                    $statusIcon = $agendaStatus === 'berjalan' ? 'bi-play-circle-fill' : 'bi-check-circle-fill';
                                } elseif (in_array($agendaStatus, ['menunggu_token', 'token_terverifikasi'])) {
                                    $boxBg = 'bg-warning bg-opacity-10 text-warning';
                                    $statusIcon = $agendaStatus === 'menunggu_token' ? 'bi-hourglass-split' : 'bi-shield-check';
                                }
                            }
                        @endphp

                        <div class="rounded-3 p-2 text-center flex-shrink-0 {{ $boxBg }}" style="min-width: 62px; {{ $isIzin ?? false ? 'background-color: rgba(124, 58, 237, 0.1); color: #7c3aed !important;' : '' }}">
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

                            @if(!empty($jadwal->otp_time) && empty($jadwal->is_kegiatan_khusus))
                                <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-stopwatch text-primary me-1"></i>Handshake: {{ $jadwal->otp_time }} WIB
                                    @if($jadwal->handshake_on_time === true)
                                        <span class="badge bg-success bg-opacity-10 text-success ms-1" style="font-size: 0.6rem;">Tepat Waktu</span>
                                    @elseif($jadwal->handshake_on_time === false)
                                        <span class="badge bg-warning bg-opacity-10 text-warning ms-1" style="font-size: 0.6rem;">Terlambat (>45 mnt)</span>
                                    @endif
                                </div>
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
                @elseif(!empty($jadwal->is_period_over) && (empty($jadwal->agenda) || $jadwal->agenda->status === 'menunggu_token' || $jadwal->agenda->status === 'dibatalkan') && empty($jadwal->is_kegiatan_khusus))
                    <button disabled class="btn btn-secondary text-white btn-sm w-100 py-2 fw-semibold border-0" style="border-radius: 8px; cursor: not-allowed;">
                        <i class="bi bi-clock-history me-1"></i> Jam Pelajaran Sudah Selesai
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
                            <i class="bi bi-pencil-square me-1"></i> Langkah 1: Isi Materi & TP
                        </a>
                    @elseif($jadwal->agenda->status === 'berjalan')
                        @php
                            $hasMateri = !empty($jadwal->agenda->materi_diajarkan);
                            $hasFotoGuru = !empty($jadwal->agenda->foto_guru_path);
                            $hasKehadiran = $jadwal->agenda->kehadiranMurid()->exists();
                        @endphp
                        <div class="d-flex flex-wrap gap-2">
                            @if(!$hasMateri)
                                <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-primary btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                    <i class="bi bi-pencil-square me-1"></i> Langkah 1: Isi Materi & TP
                                </a>
                            @elseif(!$hasFotoGuru)
                                <a href="{{ route('guru.foto-guru', $jadwal->agenda->id) }}" class="btn btn-success btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                    <i class="bi bi-camera me-1"></i> Langkah 2: Foto Guru & Kelas
                                </a>
                            @elseif(!$hasKehadiran)
                                <a href="{{ route('guru.kehadiran', $jadwal->agenda->id) }}" class="btn btn-warning text-dark btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                    <i class="bi bi-person-check me-1"></i> Langkah 3: Presensi Siswa
                                </a>
                            @else
                                <a href="{{ route('guru.stopwatch', $jadwal->agenda->id) }}" class="btn btn-success btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                    <i class="bi bi-stopwatch me-1"></i> Stopwatch & KKTP
                                </a>
                            @endif
                            <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-outline-secondary btn-sm py-2" style="border-radius: 8px;" wire:navigate title="Edit Materi / TP">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    @elseif($jadwal->agenda->status === 'selesai')
                        @php
                            $hasMateri = !empty($jadwal->agenda->materi_diajarkan);
                            $hasFotoGuru = !empty($jadwal->agenda->foto_guru_path);
                            $hasKehadiran = $jadwal->agenda->kehadiranMurid()->exists();
                            $isComplete = $hasMateri && $hasFotoGuru && $hasKehadiran;
                        @endphp
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('guru.detail-agenda', $jadwal->agenda->id) }}" class="btn btn-outline-primary btn-sm flex-fill py-2 fw-semibold" style="border-radius: 8px;" wire:navigate>
                                <i class="bi bi-eye me-1"></i> Detail Agenda
                            </a>
                            @if(!$isComplete)
                                @if(!$hasMateri)
                                    <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-warning btn-sm py-2 fw-semibold text-dark" style="border-radius: 8px;" wire:navigate title="Materi Belum Terisi">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Lengkapi Materi
                                    </a>
                                @elseif(!$hasFotoGuru)
                                    <a href="{{ route('guru.foto-guru', $jadwal->agenda->id) }}" class="btn btn-warning btn-sm py-2 fw-semibold text-dark" style="border-radius: 8px;" wire:navigate title="Foto Belum Diambil">
                                        <i class="bi bi-camera me-1"></i> Ambil Foto
                                    </a>
                                @elseif(!$hasKehadiran)
                                    <a href="{{ route('guru.kehadiran', $jadwal->agenda->id) }}" class="btn btn-warning btn-sm py-2 fw-semibold text-dark" style="border-radius: 8px;" wire:navigate title="Presensi Belum Terisi">
                                        <i class="bi bi-person-check me-1"></i> Isi Presensi
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('guru.kehadiran', $jadwal->agenda->id) }}" class="btn btn-outline-secondary btn-sm py-2 fw-semibold" style="border-radius: 8px;" wire:navigate title="Presensi Siswa">
                                    <i class="bi bi-person-check me-1"></i> Presensi
                                </a>
                                <a href="{{ route('guru.materi', $jadwal->agenda->id) }}" class="btn btn-outline-secondary btn-sm py-2" style="border-radius: 8px;" wire:navigate title="Edit Materi / TP">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
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
