<div>
    <div class="page-header">
        <h1><i class="bi bi-play-circle me-2"></i>Mulai Kelas</h1>
        <p class="subtitle mb-0">
            {{ $jadwal->mataPelajaran?->nama_mapel }} — {{ $jadwal->rombel?->nama_kelas }}
        </p>
    </div>

    {{-- Info Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6">
                    <div class="small text-muted">Mata Pelajaran</div>
                    <div class="fw-bold">{{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Kelas</div>
                    <div class="fw-bold">{{ $jadwal->rombel?->nama_kelas ?? '-' }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Hari</div>
                    <div class="fw-bold">{{ $jadwal->hari_label }}</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Jam</div>
                    <div class="fw-bold">Jam {{ $jadwal->jam_ke_mulai }} - {{ $jadwal->jam_ke_selesai }}</div>
                </div>
                @if($jadwal->jadwalGuru->count() > 1)
                <div class="col-12">
                    <div class="small text-muted">Team Teaching</div>
                    <div class="fw-bold">{{ $jadwal->jadwalGuru->pluck('guru.name')->join(', ') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Token Section --}}
    @if(!$agenda || $agenda->status === 'dibatalkan')
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-3">Siap Memulai Kelas?</h5>
                <p class="text-muted">Klik tombol di bawah untuk generate token OTP. Berikan kode kepada Ketua Kelas untuk verifikasi.</p>
                <button wire:click="generateToken" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-key-fill me-2"></i>Generate Token OTP</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Membuat token...</span>
                </button>
            </div>
        </div>
    @elseif($agenda->status === 'menunggu_token')
        <div class="card" wire:poll.3s="refreshStatus">
            <div class="card-body text-center py-4">
                <div class="small text-muted mb-2">Token OTP</div>
                <div class="otp-display mb-3">{{ $token }}</div>
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle"></i>
                    Berikan kode ini kepada <strong>Ketua Kelas</strong> untuk verifikasi handshake.
                </p>
                <div class="status-badge status-kuning mx-auto mb-3">
                    <i class="bi bi-hourglass-split"></i> Menunggu verifikasi Ketua Kelas...
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <button wire:click="refreshStatus" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i> Cek Status
                    </button>
                    <button wire:click="generateToken" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-key me-1"></i> Generate Ulang
                    </button>
                </div>
            </div>
        </div>
    @elseif($agenda->status === 'berjalan' || $agenda->status === 'token_terverifikasi')
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="status-badge status-hijau mx-auto mb-3">
                    <i class="bi bi-check-circle-fill"></i> Token Terverifikasi!
                </div>
                <h5 class="fw-bold mb-2 text-dark">Ketua Kelas Berhasil Memverifikasi Token</h5>
                <p class="text-muted small mb-3">Silakan lanjutkan untuk mengisi materi pembelajaran dan tujuan pembelajaran (TP).</p>
                <a href="{{ route('guru.materi', $agenda->id) }}" class="btn btn-primary btn-lg" wire:navigate>
                    <i class="bi bi-pencil-square me-2"></i>Lanjut ke Isi Materi & TP
                </a>
            </div>
        </div>
    @elseif($agenda->status === 'selesai')
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="status-badge status-hijau mx-auto mb-3">
                    <i class="bi bi-check-circle-fill"></i> Sesi Telah Ditutup
                </div>
                <h5 class="fw-bold mb-2 text-dark">Agenda Sesi Ini Sudah Selesai</h5>
                <p class="text-muted small mb-3">Anda dapat melihat rincian agenda atau melengkapi data materi, foto, dan presensi siswa.</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('guru.detail-agenda', $agenda->id) }}" class="btn btn-primary" wire:navigate>
                        <i class="bi bi-eye me-1"></i> Lihat Detail Agenda
                    </a>
                    <a href="{{ route('guru.materi', $agenda->id) }}" class="btn btn-outline-primary" wire:navigate>
                        <i class="bi bi-pencil me-1"></i> Edit Materi / Presensi
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Back & Cancel Buttons --}}
    <div class="mt-3 d-flex flex-wrap gap-2">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary flex-fill" wire:navigate style="min-height: 44px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
        @if($agenda)
        <button type="button" wire:click="batalkanAgenda" 
                wire:confirm="Yakin ingin membatalkan dan mereset agenda jam ini? (Gunakan ini jika Anda salah memilih jam pelajaran)"
                class="btn btn-outline-danger flex-fill" style="min-height: 44px;">
            <i class="bi bi-trash me-1"></i> Batalkan Sesi Ini (Salah Jam)
        </button>
        @endif
    </div>
</div>
