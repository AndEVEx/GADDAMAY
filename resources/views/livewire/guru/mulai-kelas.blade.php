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
            <div class="card-body text-center py-4">
                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-2">Siap Memulai Kelas?</h5>
                <p class="text-muted small">Klik tombol di bawah untuk generate token OTP. Berikan kode kepada Ketua Kelas untuk verifikasi.</p>

                {{-- Keterangan / Alasan Terlambat Input (Item 2) --}}
                <div class="card border-0 bg-light p-3 mb-3 text-start mx-auto" style="max-width: 500px; border-radius: 10px;">
                    <label class="form-label fw-bold small text-dark d-flex align-items-center gap-1 mb-1">
                        <i class="bi bi-chat-left-text text-primary"></i>
                        Keterangan / Alasan Keterlambatan (Opsional jika telat):
                    </label>
                    <input type="text" wire:model="alasan_terlambat" 
                           class="form-control form-control-sm" 
                           placeholder="Contoh: Mengikuti rapat dinas / piket pagi...">
                    <div class="form-text small text-muted" style="font-size: 0.72rem;">
                        Alasan ini akan tercatat di sistem & card monitoring.
                    </div>
                </div>

                <button wire:click="generateToken" class="btn btn-primary btn-lg px-4" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-key-fill me-2"></i>Generate Token OTP</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Membuat token...</span>
                </button>
            </div>
        </div>
    @elseif($agenda->status === 'menunggu_token')
        <div class="card" wire:poll.3s="refreshStatus">
            <div class="card-body text-center py-4">
                <div class="small text-muted mb-2">Token OTP (Rahasia &bull; Dilarang Screenshot)</div>

                {{-- Anti-Screenshot Protected Token Display (Item 7) --}}
                <div id="tokenProtectionContainer" class="position-relative mx-auto mb-3" style="width: 100%; max-width: 440px;">
                    <div id="tokenOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none bg-dark text-white rounded-3 d-flex flex-column align-items-center justify-content-center p-2 text-center" style="z-index: 10; backdrop-filter: blur(8px);">
                        <i class="bi bi-shield-slash-fill text-warning fs-3 mb-1"></i>
                        <span class="fw-bold small">Screenshot Tidak Diizinkan</span>
                        <span class="text-white-50" style="font-size: 0.68rem;">Token bersifat rahasia & realtime</span>
                    </div>

                    <div id="tokenDisplayBox" class="otp-display protected-token" style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; -webkit-touch-callout: none; white-space: nowrap !important;">
                        @if(!empty($token))
                            @foreach(str_split(trim((string)$token)) as $digit)
                                <span class="otp-digit">{{ $digit }}</span>
                            @endforeach
                        @else
                            <span>------</span>
                        @endif
                    </div>
                </div>

                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    Berikan kode ini secara langsung kepada <strong>Ketua Kelas</strong> untuk verifikasi handshake.
                </p>

                {{-- Keterangan / Alasan Terlambat Input saat menunggu token --}}
                <div class="card border-0 bg-light p-3 mb-3 text-start mx-auto" style="max-width: 500px; border-radius: 10px;">
                    <label class="form-label fw-bold small text-dark d-flex align-items-center gap-1 mb-1">
                        <i class="bi bi-chat-left-text text-primary"></i>
                        Keterangan / Alasan Keterlambatan:
                    </label>
                    <input type="text" wire:model.live.debounce.500ms="alasan_terlambat" 
                           class="form-control form-control-sm" 
                           placeholder="Contoh: Mengikuti rapat dinas / piket pagi...">
                    <div class="form-text small text-muted" style="font-size: 0.72rem;">
                        Tersimpan otomatis dan tampil pada monitoring jika ada keterlambatan.
                    </div>
                </div>

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

    {{-- Script Anti-Screenshot Khusus Halaman Token (Item 7) --}}
    <script>
    (function() {
        function setupTokenProtection() {
            const overlay = document.getElementById('tokenOverlay');
            const tokenBox = document.getElementById('tokenDisplayBox');

            if (!tokenBox) return;

            // 1. Hide token when window loses focus (e.g. Snipping tool, screen grabber active)
            window.addEventListener('blur', function() {
                if (overlay) overlay.classList.remove('d-none');
                if (tokenBox) tokenBox.style.filter = 'blur(12px)';
            });

            window.addEventListener('focus', function() {
                if (overlay) overlay.classList.add('d-none');
                if (tokenBox) tokenBox.style.filter = 'none';
            });

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (overlay) overlay.classList.remove('d-none');
                    if (tokenBox) tokenBox.style.filter = 'blur(12px)';
                } else {
                    if (overlay) overlay.classList.add('d-none');
                    if (tokenBox) tokenBox.style.filter = 'none';
                }
            });

            // 2. Intercept keyboard screenshot shortcuts (PrintScreen, Ctrl+P, Win+Shift+S)
            window.addEventListener('keydown', function(e) {
                if (
                    e.key === 'PrintScreen' || 
                    e.keyCode === 44 || 
                    (e.ctrlKey && e.key === 'p') || 
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'S')) ||
                    (e.metaKey && e.shiftKey && (e.key === '4' || e.key === '3' || e.key === 's'))
                ) {
                    e.preventDefault();
                    if (overlay) overlay.classList.remove('d-none');
                    if (tokenBox) tokenBox.style.filter = 'blur(12px)';
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(''); // Clear clipboard
                    }
                    setTimeout(function() {
                        if (overlay) overlay.classList.add('d-none');
                        if (tokenBox) tokenBox.style.filter = 'none';
                    }, 2000);
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupTokenProtection);
        } else {
            setupTokenProtection();
        }

        document.addEventListener('livewire:navigated', setupTokenProtection);
    })();
    </script>
</div>
