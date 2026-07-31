<div>
    <div class="page-header">
        <h1><i class="bi bi-qr-code-scan me-2"></i>Verifikasi Token</h1>
        <p class="subtitle mb-0">Masukkan kode OTP dari guru</p>
    </div>

    @if(!$agenda || !$showMotivasi)
    {{-- Token Input Form --}}
    <div class="card mb-3">
        <div class="card-body py-4">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold">Masukkan Token OTP</h5>
                <p class="text-muted small">Minta kode 6 digit dari guru pengajar</p>
            </div>

            <form wire:submit="verifikasi">
                <div class="mb-3">
                    <input type="text" class="form-control text-center fw-bold @error('token') is-invalid @enderror"
                           wire:model="token" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                           placeholder="______" style="font-size: 2rem; letter-spacing: 0.5rem; padding: 1rem;">
                    @error('token')
                        <div class="invalid-feedback text-center">{{ $message }}</div>
                    @enderror
                </div>

                @if($errorMessage)
                    <div class="alert alert-danger small py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $errorMessage }}
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100 btn-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-check-circle me-2"></i>Verifikasi</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...</span>
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Motivasi Popup --}}
    @if($showMotivasi && $agenda)
    <div class="motivasi-popup" wire:click.self="tutupMotivasi">
        <div class="motivasi-content">
            <div class="motivasi-emoji">{{ $motivasiTipe === 'pantun' ? '📜' : '✨' }}</div>
            <div class="mb-2">
                <span class="status-badge status-hijau">
                    <i class="bi bi-check-circle-fill"></i> Verifikasi Berhasil!
                </span>
            </div>
            <div class="mb-2 small text-muted">
                {{ $agenda->guru?->name }} — {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}
                <br>{{ $agenda->jadwalPelajaran?->rombel?->nama_kelas }}
            </div>
            <div class="motivasi-text">{!! nl2br(e($motivasiText)) !!}</div>
            <button class="btn btn-primary" wire:click="goToFoto">
                <i class="bi bi-camera-fill me-2"></i>Ambil Foto Bukti
            </button>
            <button class="btn btn-outline-secondary btn-sm mt-2 d-block w-100" wire:click="tutupMotivasi">
                Lewati Foto
            </button>
        </div>
    </div>
    @endif

    {{-- Success Card (after verifikasi, motivasi dismissed) --}}
    @if($agenda && !$showMotivasi && $agenda->status === 'berjalan')
    <div class="card animate-fade-in-up">
        <div class="card-body text-center py-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-3">Kelas Sedang Berjalan</h5>
            <p class="text-muted">{{ $agenda->guru?->name }} — {{ $agenda->jadwalPelajaran?->mataPelajaran?->nama_mapel }}</p>
            <a href="{{ route('ketua.foto', $agenda->id) }}" class="btn btn-primary" wire:navigate>
                <i class="bi bi-camera-fill me-2"></i>Ambil Foto Bukti
            </a>
        </div>
    </div>
    @endif
</div>
