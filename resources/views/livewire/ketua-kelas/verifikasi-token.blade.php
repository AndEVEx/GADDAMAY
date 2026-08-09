<div>
    <div class="page-header">
        <h1><i class="bi bi-qr-code-scan me-2"></i>Verifikasi Token</h1>
        <p class="subtitle mb-0">Masukkan kode OTP dari guru pengajar</p>
    </div>

    {{-- Token Input Form --}}
    <div class="card mb-3 animate-fade-in-up">
        <div class="card-body py-4">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 72px; height: 72px;">
                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <h5 class="fw-bold">Masukkan Token OTP</h5>
                <p class="text-muted small">Minta kode 6 digit dari guru pengajar di depan kelas</p>
            </div>

            <form wire:submit="verifikasi">
                <div class="mb-3">
                    <input type="text" class="form-control text-center fw-bold @error('token') is-invalid @enderror"
                           wire:model="token" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                           placeholder="______" style="font-size: 2.2rem; letter-spacing: 0.5rem; padding: 1rem; border-radius: 12px;">
                    @error('token')
                        <div class="invalid-feedback text-center">{{ $message }}</div>
                    @enderror
                </div>

                @if($errorMessage)
                    <div class="alert alert-danger small py-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $errorMessage }}
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm" style="min-height: 52px; border-radius: 12px;" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-check-circle me-2"></i>Verifikasi Token</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...</span>
                </button>
            </form>
        </div>
    </div>
</div>
