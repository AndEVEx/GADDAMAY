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
        <div class="card" wire:poll.5s="refreshStatus">
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
                <button wire:click="generateToken" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-clockwise"></i> Generate Ulang
                </button>
            </div>
        </div>
    @elseif($agenda->status === 'berjalan')
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="status-badge status-hijau mx-auto mb-3">
                    <i class="bi bi-check-circle-fill"></i> Token Terverifikasi!
                </div>
                <a href="{{ route('guru.stopwatch', $agenda->id) }}" class="btn btn-success btn-lg" wire:navigate>
                    <i class="bi bi-stopwatch me-2"></i>Lanjut ke Stopwatch
                </a>
            </div>
        </div>
    @endif

    {{-- Back Button --}}
    <div class="mt-3">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary" wire:navigate>
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>
